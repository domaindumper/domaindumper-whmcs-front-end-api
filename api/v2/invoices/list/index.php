<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';

$ca = new ClientArea();

// Get the authorization header
$headers = apache_request_headers();
$authHeader = isset($headers['X-Authorization']) ? $headers['X-Authorization'] : '';

if (empty($authHeader)) {
    http_response_code(401);
    echo json_encode([
        'status' => 'error',
        'message' => 'No authorization token provided'
    ]);
    exit;
}

try {
    // Verify JWT token
    $decoded = JWT::decode($authHeader, JWT_SECRET, array(JWT_ALGORITHM));
    $userId = $decoded->data->client_id;

    // Get invoices from WHMCS
    $command = 'GetInvoices';
    $postData = array(
        'userid' => $userId,
        'limitnum' => 100 // Adjust limit as needed
    );

    $results = localAPI($command, $postData);

    if ($results['result'] == 'success' && isset($results['invoices']['invoice'])) {
        // Process invoices
        $invoices = array_map(function($invoice) {
            return [
                'id' => $invoice['id'],
                'invoice_number' => $invoice['invoicenum'],
                'date_created' => $invoice['date'],
                'date_due' => $invoice['duedate'],
                'date_paid' => $invoice['datepaid'],
                'subtotal' => $invoice['subtotal'],
                'credit' => $invoice['credit'],
                'tax' => $invoice['tax'],
                'tax2' => $invoice['tax2'],
                'total' => $invoice['total'],
                'balance' => $invoice['balance'],
                'status' => ucfirst($invoice['status']),
                'payment_method' => $invoice['paymentmethod'],
                'currency_code' => $invoice['currencycode'],
                'currency_prefix' => $invoice['currency_prefix'],
                'currency_suffix' => $invoice['currency_suffix'],
                'notes' => $invoice['notes'],
                'items' => isset($invoice['items']['item']) ? $invoice['items']['item'] : []
            ];
        }, $results['invoices']['invoice']);

        // Get status counts
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

        http_response_code(200);
        echo json_encode($response);

    } else {
        throw new Exception($results['message'] ?? 'Failed to fetch invoices');
    }

} catch(Exception $e) {
    $errorMessage = $e->getMessage();
    $statusCode = 500;

    // Handle JWT expiration
    if (strpos($errorMessage, 'expired')) {
        $statusCode = 401;
        $errorMessage = 'Session expired, please login again';
    }

    http_response_code($statusCode);
    echo json_encode([
        'status' => 'error',
        'code' => $statusCode,
        'message' => $errorMessage
    ]);
}
?>