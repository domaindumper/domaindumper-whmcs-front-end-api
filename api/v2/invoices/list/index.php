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
    $userId = $auth->validateRequest(); // This handles token validation and expiry

    // Get invoices from WHMCS
    $command = 'GetInvoices';
    $postData = array(
        'userid' => $userId,
        'limitnum' => 100
    );

    $results = localAPI($command, $postData);

    if ($results['result'] == 'success' && isset($results['invoices']['invoice'])) {
        // Process invoices
        $invoices = array_map(function($invoice) {
            return [
                'id' => (int)$invoice['id'],
                'invoice_number' => htmlspecialchars(trim($invoice['invoicenum'])),
                'date_created' => date('Y-m-d H:i:s', strtotime($invoice['date'])),
                'date_due' => date('Y-m-d H:i:s', strtotime($invoice['duedate'])),
                'date_paid' => !empty($invoice['datepaid']) ? date('Y-m-d H:i:s', strtotime($invoice['datepaid'])) : null,
                'subtotal' => (float)$invoice['subtotal'],
                'credit' => (float)$invoice['credit'],
                'tax' => (float)$invoice['tax'],
                'tax2' => (float)$invoice['tax2'],
                'total' => (float)$invoice['total'],
                'balance' => (float)$invoice['balance'],
                'status' => htmlspecialchars(ucfirst(trim($invoice['status']))),
                'payment_method' => htmlspecialchars(trim($invoice['paymentmethod'])),
                'currency_code' => htmlspecialchars(trim($invoice['currencycode'])),
                'currency_prefix' => htmlspecialchars(trim($invoice['currency_prefix'])),
                'currency_suffix' => htmlspecialchars(trim($invoice['currency_suffix'])),
                'notes' => htmlspecialchars(trim($invoice['notes'])),
                'items' => isset($invoice['items']['item']) ? array_map(function($item) {
                    return [
                        'description' => htmlspecialchars(trim($item['description'])),
                        'amount' => (float)$item['amount']
                    ];
                }, $invoice['items']['item']) : []
            ];
        }, $results['invoices']['invoice']);

        // Calculate status counts
        $statusCounts = [
            'total' => count($invoices),
            'paid' => 0,
            'unpaid' => 0,
            'cancelled' => 0,
            'refunded' => 0,
            'collections' => 0
        ];

        foreach ($invoices as $invoice) {
            $status = strtolower($invoice['status']);
            if (isset($statusCounts[$status])) {
                $statusCounts[$status]++;
            }
        }

        $response = [
            'status' => 'success',
            'code' => 200,
            'data' => [
                'invoices' => $invoices,
                'status_counts' => $statusCounts,
                'total_records' => count($invoices)
            ]
        ];
    } else {
        throw new Exception($results['message'] ?? 'Failed to fetch invoices');
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