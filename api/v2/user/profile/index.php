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
    $auth->validateRequest();
    $userId = $auth->getUserId();

    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        // Get PUT data
        $putData = json_decode(file_get_contents('php://input'), true);
        
        // Required fields validation
        $requiredFields = ['firstname', 'lastname', 'email', 'phonenumber'];
        foreach ($requiredFields as $field) {
            if (!isset($putData[$field]) || empty($putData[$field])) {
                throw new Exception("Field {$field} is required");
            }
        }

        // Update client details
        $command = 'UpdateClient';
        $postData = array(
            'clientid' => $userId,
            'firstname' => $putData['firstname'],
            'lastname' => $putData['lastname'],
            'email' => $putData['email'],
            'phonenumber' => $putData['phonenumber'],
            'companyname' => $putData['companyname'] ?? '',
            'address1' => $putData['address1'] ?? '',
            'address2' => $putData['address2'] ?? '',
            'city' => $putData['city'] ?? '',
            'state' => $putData['state'] ?? '',
            'postcode' => $putData['postcode'] ?? '',
            'country' => $putData['country'] ?? ''
        );

        $results = localAPI($command, $postData);

        if ($results['result'] == 'success') {
            $response = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Profile updated successfully',
                'data' => $results['client']
            ];
        } else {
            throw new Exception($results['message'] ?? 'Failed to update profile');
        }
    } else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        // Get client details
        $command = 'GetClientsDetails';
        $postData = array(
            'clientid' => $userId,
            'stats' => true,
        );

        $results = localAPI($command, $postData);
        
        if ($results['result'] == 'success') {
            $response = [
                'status' => 'success',
                'code' => 200,
                'data' => $results['client']
            ];
        } else {
            throw new Exception('Failed to fetch profile data');
        }
    }

} catch (Exception $e) {
    http_response_code(500);
    $response = [
        'status' => 'error',
        'code' => 500,
        'message' => $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
?>