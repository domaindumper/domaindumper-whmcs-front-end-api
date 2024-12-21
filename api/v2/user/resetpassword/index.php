<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';

$ca = new ClientArea();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = json_decode(file_get_contents('php://input'), true);
    $email = !empty($data['email']) ? $data['email'] : '';

    $Client = Illuminate\Database\Capsule\Manager::table('tblclients')
        ->where('email ', $email)
        ->first();

    $ResponseCode = 200;

    if ($Client) {

        $response = [
            'status' => 'success',
            'code' => 200,
            'message' => 'If you are a registered user, you will be sent an email with a password reset link.'
        ];

    } else {
        $response = [
            'status' => 'error',
            'code' => 200,
            'message' => 'If you are a registered user, you will be sent an email with a password reset link.'
        ];
    }

} else {

    // Handle invalid request method
    $ResponseCode = 405;
    $response = [
        'status' => 'error',
        'code' => 405,
        'message' => 'Method not allowed'
    ];
}

http_response_code($ResponseCode);
header('Content-Type: application/json');
echo json_encode($response);
?>