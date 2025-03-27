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
    $userId = $auth->validateRequest(); // This will handle validation and return userId

    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $command = 'GetClientsDetails';
            $postData = array(
                'clientid' => $userId,
                'stats' => true,
            );

            $results = localAPI($command, $postData);
            
            if ($results['result'] !== 'success') {
                throw new Exception('Failed to fetch profile data');
            }

            $response = [
                'status' => 'success',
                'code' => 200,
                'data' => refineUserInformation($results['client'])
            ];
            break;

        case 'PUT':
            $putData = json_decode(file_get_contents('php://input'), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON payload');
            }
            
            // Required fields validation
            $requiredFields = ['firstname', 'lastname', 'phonenumber'];
            $missingFields = [];
            
            foreach ($requiredFields as $field) {
                if (!isset($putData[$field]) || empty($putData[$field])) {
                    $missingFields[] = $field;
                }
            }
            
            if (!empty($missingFields)) {
                throw new Exception('Required fields missing: ' . implode(', ', $missingFields));
            }

            // Update client details
            $command = 'UpdateClient';
            $postData = array(
                'clientid' => $userId,
                'firstname' => trim($putData['firstname']),
                'lastname' => trim($putData['lastname']),
                'email' => trim($putData['email']),
                'phonenumber' => trim($putData['phonenumber']),
                'companyname' => trim($putData['companyname'] ?? ''),
                'address1' => trim($putData['address1'] ?? ''),
                'address2' => trim($putData['address2'] ?? ''),
                'city' => trim($putData['city'] ?? ''),
                'state' => trim($putData['state'] ?? ''),
                'postcode' => trim($putData['postcode'] ?? ''),
                'country' => trim($putData['country'] ?? '')
            );

            $results = localAPI($command, $postData);

            if ($results['result'] !== 'success') {
                throw new Exception($results['message'] ?? 'Failed to update profile');
            }

            $response = [
                'status' => 'success',
                'code' => 200,
                'message' => 'Profile updated successfully',
                'data' => refineUserInformation($results['client'])
            ];
            break;

        default:
            throw new Exception('Method not allowed', 405);
    }

} catch (Exception $e) {
    $statusCode = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
    
    $response = [
        'status' => 'error',
        'code' => $statusCode,
        'message' => $e->getMessage()
    ];
}

header('Content-Type: application/json');
echo json_encode($response);
exit();