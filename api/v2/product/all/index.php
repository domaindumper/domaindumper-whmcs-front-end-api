<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';

$ca = new ClientArea();



if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'code' => 405, 'message' => 'Method not allowed']);
    exit;
}


$command = 'GetProducts';
$postData = array(
    'pid' => '1, 2, 3', // Product IDs
);

$results = localAPI($command, $postData);
print_r($results);


if (!$email) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'code' => 400, 'message' => 'Invalid email address.']);
    exit;
}

$Client = Capsule::table('tblclients')
    ->where('email', $email)
    ->first();

if ($Client) {
    if ($Client->password_reset_token && $Client->password_reset_token_expiry > date('Y-m-d H:i:s')) {
        $lastSent = strtotime($Client->password_reset_token_expiry) - (2 * 60 * 60);  // Correct calculation of last sent time
        $timeDiff = time() - $lastSent;
        if ($timeDiff > 60) {
            $response = sendPasswordResetEmail($Client);
        } else {
            http_response_code(429);
            $response = [
                'status' => 'error',
                'code' => 429,
                'message' => 'Please wait 60 seconds before requesting another password reset email.',
            ];
        }
    } else {
        $response = sendPasswordResetEmail($Client);
    }
} else {
    http_response_code(404);
    $response = [
        'status' => 'error',
        'code' => 404,
        'message' => 'Can\'t find that email. Try again?',
    ];
}

http_response_code($response['code'] ?? 200);
header('Content-Type: application/json');
echo json_encode($response);

?>