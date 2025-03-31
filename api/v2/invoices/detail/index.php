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

    // Get invoice ID from URL
    $invoiceId = basename($_SERVER['REQUEST_URI']);
    if (!$invoiceId) {
        throw new Exception('Invoice ID is required', 400);
    }

    // Get invoice from WHMCS
    $command = 'GetInvoice';
    $postData = array(
        'invoiceid' => $invoiceId,
        'userid' => $userId
    );

    $results = localAPI($command, $postData);

    if ($results['result'] == 'success') {
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
            'client_name' => $results['client']['firstname'] . ' ' . $results['client']['lastname'],
            'billing_address' => $results['client']['address1'],
            'billing_city' => $results['client']['city'],
            'billing_state' => $results['client']['state'],
            'billing_zip' => $results['client']['postcode'],
            'country' => $results['client']['country'],
            'items' => array_map(function($item) {
                return [
                    'description' => htmlspecialchars(trim($item['description'])),
                    'amount' => (float)$item['amount']
                ];
            }, $results['items']['item'])
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