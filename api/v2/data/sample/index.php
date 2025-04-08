<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);
require __DIR__ . '/../../../../init.php';
require __DIR__ . '/../../../../vendor/autoload.php';

$ca = new ClientArea();

try {
    // Get whois_type from request
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate JSON and required fields
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON payload', 400);
    }

    if (!isset($input['whois_type']) || !is_numeric($input['whois_type'])) {
        throw new Exception('Valid whois_type is required', 400);
    }

    // Get demo data from database
    $demoData = Capsule::table('whois_database_demo')
        ->select([
            'whois_id',
            'whois_md5',
            'whois_count',
            'whois_file',
            'whois_size',
            'whois_note',
            'created_at',
            'updated_at'
        ])
        ->where('whois_type', (int)$input['whois_type'])
        ->where('whois_status', 1) // Only active records
        ->orderBy('created_at', 'desc')
        ->get();

    if ($demoData->count() > 0) {
        $samples = $demoData->map(function($item) {
            return [
                'id' => (int)$item->whois_id,
                'md5' => $item->whois_md5,
                'count' => (int)$item->whois_count,
                'file_name' => $item->whois_file,
                'file_size' => $item->whois_size,
                'note' => $item->whois_note,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at
            ];
        })->toArray();

        $response = [
            'status' => 'success',
            'code' => 200,
            'data' => [
                'samples' => $samples,
                'total_records' => count($samples)
            ]
        ];
    } else {
        $response = [
            'status' => 'success',
            'code' => 200,
            'data' => [
                'samples' => [],
                'total_records' => 0
            ]
        ];
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