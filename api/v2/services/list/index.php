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
    // Initialize authorization
    $auth = new Authorization();
    $userId = $auth->validateRequest();

    // Get client's products/services
    $command = 'GetClientsProducts';
    $postData = array(
        'clientid' => $userId,
        'stats' => true
    );

    $results = localAPI($command, $postData);

    if ($results['result'] == 'success' && isset($results['products']['product'])) {
        // Process services
        $services = array_map(function($service) {
            return [
                'id' => (int)$service['id'],
                'client_id' => (int)$service['clientid'],
                'order_id' => (int)$service['orderid'],
                'product_id' => (int)$service['pid'],
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
                }, $service['configoptions']) : []
            ];
        }, $results['products']['product']);

        // Calculate status counts
        $statusCounts = [
            'total' => count($services),
            'active' => 0,
            'pending' => 0,
            'suspended' => 0,
            'terminated' => 0,
            'cancelled' => 0
        ];

        foreach ($services as $service) {
            $status = strtolower($service['status']);
            if (isset($statusCounts[$status])) {
                $statusCounts[$status]++;
            }
        }

        $response = [
            'status' => 'success',
            'code' => 200,
            'data' => [
                'services' => $services,
                'status_counts' => $statusCounts,
                'total_records' => count($services)
            ]
        ];
    } else {
        throw new Exception($results['message'] ?? 'Failed to fetch services');
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