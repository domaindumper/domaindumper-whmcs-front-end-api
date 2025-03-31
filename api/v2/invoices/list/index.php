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
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
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
        'limitnum' => 100
    );

    $results = localAPI($command, $postData);

    if ($results['result'] == 'success' && isset($results['invoices']['invoice'])) {
        // Process invoices
        $invoices = array_map(function($invoice) {
            // Sanitize and format data
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

        header('Content-Type: application/json; charset=utf-8');
        http_response_code(200);
        echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);

    } else {
        throw new Exception($results['message'] ?? 'Failed to fetch invoices');
    }

} catch(Exception $e) {
    $errorMessage = $e->getMessage();
    $statusCode = 500;

    if (strpos($errorMessage, 'expired')) {
        $statusCode = 401;
        $errorMessage = 'Session expired, please login again';
    }

    header('Content-Type: application/json; charset=utf-8');
    http_response_code($statusCode);
    echo json_encode([
        'status' => 'error',
        'code' => $statusCode,
        'message' => $errorMessage
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
?>