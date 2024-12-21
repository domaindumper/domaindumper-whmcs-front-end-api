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

        // Generate a random password reset token and store password_reset_token and password_reset_token_expiry in tblclients table

        $password_reset_token = bin2hex(random_bytes(100));

        $password_reset_token_expiry = date('Y-m-d H:i:s', strtotime('+2 hour'));   // Token expires in 2 hours

        Illuminate\Database\Capsule\Manager::table('tblclients')
            ->where('email', $email)
            ->update([
                'password_reset_token' => $password_reset_token,
                'password_reset_token_expiry' => $password_reset_token_expiry
            ]);

        // Send an email to the user with a password reset link

        $command = 'SendEmail';
        $postData = array(
            'messagename' => 'Client Signup Email',
            'id' => $Client->id,
            'customtype' => 'general',
            'customsubject' => 'Product Welcome Email',
            'custommessage' => '<p>Thank you for choosing us</p><p>Your custom is appreciated</p><p>{$custommerge}<br /></p>',
            //'customvars' => base64_encode(serialize(array("custommerge"=>$populatedvar1, "custommerge2"=>$populatedvar2))),
        );

        $results = localAPI($command, $postData);
        print_r($results);

        // Prepare the response data

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