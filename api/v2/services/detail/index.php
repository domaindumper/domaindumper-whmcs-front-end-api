<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Authorization.php';

/**
 * Generate a secure download URL for the service
 */
function generateDownloadUrl($serviceId, $userId) {
    // TODO: Implement proper URL generation with security token
    $token = hash('sha256', $serviceId . $userId . time() . uniqid());
    return "/download/{$serviceId}?token=" . $token;
}

/**
 * Get the number of API calls made for this service
 */
function getServiceApiUsage($serviceId) {
    // TODO: Implement API usage tracking
    return [
        'today' => 0,
        'this_month' => 0,
        'total' => 0
    ];
}

/**
 * Get the number of downloads made for this service
 */
function getServiceDownloads($serviceId) {
    // TODO: Implement download tracking
    return [
        'today' => 0,
        'this_month' => 0,
        'total' => 0
    ];
}

/**
 * Get the date of the last download
 */
function getLastDownloadDate($serviceId) {
    // TODO: Implement last download tracking
    return null;
}

/**
 * Calculate remaining quota for the service
 */
function calculateQuotaRemaining($serviceId) {
    // TODO: Implement quota calculation
    return [
        'downloads' => [
            'used' => 0,
            'total' => 1000,
            'remaining' => 1000
        ],
        'api_calls' => [
            'used' => 0,
            'total' => 10000,
            'remaining' => 10000
        ]
    ];
}

$ca = new ClientArea();

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate JSON and required fields
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON payload', 400);
    }

    if (!isset($input['serviceId'])) {
        throw new Exception('Service ID is required', 400);
    }

    // Add authorization check at the beginning
    $auth = new Authorization();
    $userId = $auth->validateRequest();
    
    // Validate service ownership
    $service = Capsule::table('tblhosting')
        ->where('id', $input['serviceId'])
        ->where('userid', $userId)
        ->first();
        
    if (!$service) {
        throw new Exception('Service not found or access denied', 404);
    }

    // Get specific service details
    $command = 'GetClientsProducts';
    $postData = array(
        'clientid' => $userId,
        'serviceid' => $input['serviceId'],
        'stats' => true
    );

    $results = localAPI($command, $postData);

    if ($results['result'] == 'success' && isset($results['products']['product'][0])) {
        $service = $results['products']['product'][0];

        // Get client details for currency
        $command = 'GetClientsDetails';
        $clientPostData = array(
            'clientid' => $userId,
            'stats' => false
        );

        $clientResults = localAPI($command, $clientPostData);

        // Replace the caching section with direct database query
        $currencyDetails = null;
        if (isset($clientResults['currency_code'])) {
            $currencyDetails = Capsule::table('tblcurrencies')
                ->select(['prefix', 'suffix', 'code', 'format', 'rate'])
                ->where('code', $clientResults['currency_code'])
                ->first();
        }
        
        // Process service details
        $serviceDetails = [
            'id' => (int)$service['id'],
            'client_id' => (int)$service['clientid'],
            'order_id' => (int)$service['orderid'],
            'product_id' => (int)$service['pid'],
            'domain' => $service['domain'],
            'host_name' => $service['hostname'] ?? null,
            'dedicated_ip' => $service['dedicatedip'] ?? null,
            'name' => htmlspecialchars(trim($service['name'])),
            'group_name' => htmlspecialchars(trim($service['groupname'])),
            'status' => htmlspecialchars(ucfirst(trim($service['status']))),
            'registration_date' => date('Y-m-d H:i:s', strtotime($service['regdate'])),
            'next_due_date' => date('Y-m-d H:i:s', strtotime($service['nextduedate'])),
            'billing_cycle' => htmlspecialchars(trim($service['billingcycle'])),
            'first_payment_amount' => (float)$service['firstpaymentamount'],
            'recurring_amount' => (float)$service['recurringamount'],
            'currency_code' => $service['currency'],
            'subscription_id' => $service['subscriptionid'] ?? null,
            'promotion_id' => (int)$service['promoid'],
            'promotion_description' => $service['promocount'] > 0 ? $service['promodesc'] : null,
            'last_update' => date('Y-m-d H:i:s', strtotime($service['lastupdate'])),
            'notes' => htmlspecialchars(trim($service['notes'])),
            'config_options' => isset($service['configoptions']) ? array_map(function($option) {
                return [
                    'name' => htmlspecialchars(trim($option['name'])),
                    'value' => htmlspecialchars(trim($option['value'])),
                    'option_type' => $option['type'] ?? 'standard'
                ];
            }, $service['configoptions']) : [],
            'custom_fields' => isset($service['customfields']) ? array_map(function($field) {
                return [
                    'name' => htmlspecialchars(trim($field['name'])),
                    'value' => htmlspecialchars(trim($field['value']))
                ];
            }, $service['customfields']) : [],
            'download' => [
                'available' => $service['status'] === 'Active',
                'url' => $service['status'] === 'Active' ? 
                    generateDownloadUrl($service['id'], $userId) : null,
                'expires' => $service['status'] === 'Active' ? 
                    date('Y-m-d H:i:s', strtotime('+24 hours')) : null
            ],
            'documentation' => [
                'api' => 'https://www.whoisextractor.com/support/api-documents/',
                'user_guide' => 'https://www.whoisextractor.com/support/user-guide/',
                'sample_data' => 'https://www.whoisextractor.com/support/sample-data/'
            ],
            'usage' => [
                'api_calls' => getServiceApiUsage($service['id']),
                'downloads' => getServiceDownloads($service['id']),
                'last_download' => getLastDownloadDate($service['id']),
                'quota_remaining' => calculateQuotaRemaining($service['id'])
            ]
        ];

        $response = [
            'status' => 'success',
            'code' => 200,
            'data' => [
                'service' => $serviceDetails,
                'currency' => [
                    'code' => $currencyDetails ? $currencyDetails->code : null,
                    'prefix' => $currencyDetails ? $currencyDetails->prefix : '',
                    'suffix' => $currencyDetails ? $currencyDetails->suffix : '',
                    'format' => $currencyDetails ? $currencyDetails->format : 1,
                    'rate' => $currencyDetails ? (float)$currencyDetails->rate : 1.00000
                ]
            ]
        ];
    } else {
        throw new Exception('Service not found or access denied', 404);
    }

} catch (Exception $e) {
    $statusCode = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
    
    $response = [
        'status' => 'error',
        'code' => $statusCode,
        'message' => $e->getMessage()
    ];
}

header('Content-Type: application/json; charset=utf-8');
http_response_code($response['code']);
echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
exit();