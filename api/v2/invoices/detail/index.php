<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';

$ca = new ClientArea();

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate JSON
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON payload', 400);
    }

    // Check if Id exists in payload
    if (!isset($input['Id'])) {
        throw new Exception('Invoice ID is required', 400);
    }

    $invoiceId = (int)$input['Id'];
    if ($invoiceId <= 0) {
        throw new Exception('Invalid invoice ID', 400);
    }

    // Get invoice from WHMCS without user validation
    $command = 'GetInvoice';
    $postData = array(
        'invoiceid' => $invoiceId
    );

    $results = localAPI($command, $postData);

    echo '<pre>';
    print_r($results);
    echo '</pre>';

    if ($results['result'] == 'success') {
        // Get client details using userid
        $command = 'GetClientsDetails';
        $clientPostData = array(
            'clientid' => $results['userid'],
            'stats' => false
        );

        $clientResults = localAPI($command, $clientPostData);

        // Process invoice data
        $invoice = [
            'id' => (int)$results['invoiceid'],
            'invoice_number' => $results['invoicenum'],
            'date_created' => $results['date'],
            'date_due' => $results['duedate'],
            'date_paid' => $results['datepaid'],
            'subtotal' => (float)$results['subtotal'],
            'credit' => (float)$results['credit'],
            'tax' => (float)$results['tax'],
            'tax2' => (float)$results['tax2'],
            'total' => (float)$results['total'],
            'balance' => (float)$results['balance'],
            'status' => ucfirst($results['status']),
            'payment_method' => $results['paymentmethod'],
            'currency_code' => $results['currency'],
            'currency_prefix' => $results['currency_prefix'],
            'currency_suffix' => $results['currency_suffix'],
            'items' => array_map(function($item) {
                return [
                    'description' => htmlspecialchars(trim($item['description'])),
                    'amount' => (float)$item['amount']
                ];
            }, $results['items']['item'])
        ];

        // Include client details from the separate API call
        $invoice['client'] = [
            'name' => $clientResults['firstname'] . ' ' . substr($clientResults['lastname'], 0, 1) . '.',
            'company' => !empty($clientResults['companyname']) ? $clientResults['companyname'] : null,
        ];

        $response = [
            'status' => 'success',
            'code' => 200,
            'data' => $invoice
        ];
    } else {
        throw new Exception($results['message'] ?? 'Failed to fetch invoice', 404);
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