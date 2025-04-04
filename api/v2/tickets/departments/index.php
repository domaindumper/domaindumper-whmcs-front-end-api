<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';

$ca = new ClientArea();

try {
    // Get support departments
    $command = 'GetSupportDepartments';
    $postData = array();

    $results = localAPI($command, $postData);

    if ($results['result'] == 'success' && isset($results['departments'])) {
        // Process departments
        $departments = array_map(function($department) {
            return [
                'id' => (int)$department['id'],
                'name' => htmlspecialchars(trim($department['name'])),
                'description' => htmlspecialchars(trim($department['description'])),
                'email' => $department['email'],
                'hidden' => (bool)$department['hidden'],
                'host' => $department['host'] ?? null,
                'port' => (int)($department['port'] ?? 0),
                'encryption' => $department['encryption'] ?? null
            ];
        }, $results['departments']['department']);

        $response = [
            'status' => 'success',
            'code' => 200,
            'data' => [
                'departments' => $departments,
                'total_departments' => count($departments)
            ]
        ];
    } else {
        throw new Exception($results['message'] ?? 'Failed to fetch departments');
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