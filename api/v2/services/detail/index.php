<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Authorization.php';

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

    // Initialize authorization
    $auth = new Authorization();
    $userId = $auth->validateRequest();

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
            }, $service['customfields']) : []
        ];

        $response = [
            'status' => 'success',
            'code' => 200,
            'data' => $serviceDetails
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