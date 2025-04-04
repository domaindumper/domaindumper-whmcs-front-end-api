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

    if ($results['result'] == 'success') {
        // Get client details using userid
        $command = 'GetClientsDetails';
        $clientPostData = array(
            'clientid' => $results['userid'],
            'stats' => false
        );

        $clientResults = localAPI($command, $clientPostData);

        // Get currency details directly from tblcurrencies table
        $currencyDetails = Capsule::table('tblcurrencies')
            ->select(['prefix', 'suffix', 'code', 'format', 'rate'])
            ->where('code', $clientResults['currency_code'])
            ->first();

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
            'currency_code' => $clientResults['currency_code'],
            'currency_prefix' => $currencyDetails ? $currencyDetails->prefix : '',
            'currency_suffix' => $currencyDetails ? $currencyDetails->suffix : '',
            'currency_format' => $currencyDetails ? $currencyDetails->format : 1,
            'currency_rate' => $currencyDetails ? (float)$currencyDetails->rate : 1.00000,
            'items' => array_map(function($item) {
                return [
                    'description' => htmlspecialchars(trim($item['description'])),
                    'amount' => (float)$item['amount']
                ];
            }, $results['items']['item'])
        ];

        // Include client details from the separate API call
        $invoice['client'] = [
            'id' => (int)$clientResults['userid'],
            'name' => $clientResults['firstname'] . ' ' . substr($clientResults['lastname'], 0, 1) . '.',
            'company' => !empty($clientResults['companyname']) ? $clientResults['companyname'] : null,
            'country_code' => $clientResults['countrycode'] ?? null
        ];

        // Get transaction history if invoice is paid
        if ($results['status'] === 'Paid') {
            // Get transactions from tblaccounts
            $transactions = Capsule::table('tblaccounts')
                ->select([
                    'id',
                    'date',
                    'gateway',
                    'description',
                    'amountin',
                    'amountout',
                    'fees',
                    'transid',
                    'currency',
                    'rate'
                ])
                ->where('invoiceid', $results['invoiceid'])
                ->orderBy('date', 'desc')
                ->get();

            if ($transactions->count() > 0) {
                $invoice['transactions'] = $transactions->map(function($transaction) use ($currencyDetails) {
                    return [
                        'id' => (int)$transaction->id,
                        'transaction_id' => $transaction->transid,
                        'gateway' => $transaction->gateway,
                        'description' => $transaction->description,
                        'amount_in' => (float)$transaction->amountin,
                        'amount_out' => (float)$transaction->amountout,
                        'fees' => (float)$transaction->fees,
                        'date' => $transaction->date,
                        'currency_code' => $currencyDetails->code ?? null,
                        'exchange_rate' => (float)$transaction->rate
                    ];
                })->toArray();
            } else {
                $invoice['transactions'] = [];
            }
        } else {
            $invoice['transactions'] = [];
        }

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