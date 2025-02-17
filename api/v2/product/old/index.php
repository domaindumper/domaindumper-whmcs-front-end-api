<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';

$ca = new ClientArea();
$responseCode = 200;

try {
    // Get Old database list with status=1 and type=1
    $oldDatabase = Capsule::table("whois_database_old")
        ->where('status', 1)
        ->where('type', 1)
        ->orderBy('id', 'desc')
        ->get();

    $formattedData = [];
    foreach ($oldDatabase as $record) {
        $formattedData[] = [
            'id' => $record->id,
            'period' => getConfigurableOptionName($record->month) . '-' . $record->year,
            'year' => $record->year,
            'month' => $record->month,
            'monthName' => getConfigurableOptionName($record->month),
            'dataCount' => is_numeric($record->data_count) ? number_format((float)$record->data_count) : '0',
            'size' => $record->size,
            'productId' => 9
        ];
    }

    $response = [
        'status' => 'success',
        'code' => 200,
        'data' => $formattedData,
        'total' => count($formattedData)
    ];

} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'code' => 500,
        'message' => 'Internal server error: ' . $e->getMessage()
    ];
    $responseCode = 500;
}

// Set response headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
http_response_code($responseCode);

echo json_encode($response, JSON_PRETTY_PRINT);

/**
 * Helper function to get configurable option name from database
 * @param int $id The option ID to look up
 * @return string The option name
 */
function getConfigurableOptionName($id) {
    try {
        $product = Illuminate\Database\Capsule\Manager::table("tblproductconfigoptionssub")
            ->where('id', $id)
            ->first();
            
        return $product ? $product->optionname : '';
    } catch (Exception $e) {
        // Return empty string or ID if query fails
        return '';
    }
}