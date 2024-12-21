<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';

$ca = new ClientArea();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'code' => 405, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$email = isset($data['email']) ? filter_var($data['email'], FILTER_VALIDATE_EMAIL) : null;

if (!$email) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'code' => 400, 'message' => 'Invalid email address.']);
    exit;
}


$Client = Illuminate\Database\Capsule\Manager::table('tblclients')
    ->where('email', $email)
    ->first();

if ($Client) {
    $token = bin2hex(random_bytes(32)); // Use a cryptographically secure method for token generation
    $expiry = date('Y-m-d H:i:s', strtotime('+2 hour'));

    Illuminate\Database\Capsule\Manager::table('tblclients')
        ->where('email', $email)
        ->update([
            'password_reset_token' => $token,
            'password_reset_token_expiry' => $expiry
        ]);

    // Construct the reset link (replace with your actual URL)
    $resetLink = "https://yourdomain.com/reset-password.php?token=" . $token;


    $command = 'SendEmail';
    $postData = [
        'messagename' => 'Password Reset', // Use a dedicated email template
        'id' => $Client->id,
        'customtype' => 'general',
        'customsubject' => 'Password Reset Request',
        'custommessage' => "Click the following link to reset your password: " . $resetLink,
    ];

    $results = localAPI($command, $postData);

    if ($results['result'] === 'success') {
        http_response_code(200);
        $response = [
            'status' => 'success',
            'code' => 200,
            'message' => 'If you are a registered user, you will receive an email with a password reset link.'
        ];
    } else {
        http_response_code(500); // Use appropriate error code
        $response = [
            'status' => 'error',
            'code' => 500,
            'message' => 'Failed to send password reset email. Please try again later.'
        ];
    }

} else {
    http_response_code(404);
    $response = [
        'status' => 'error',
        'code' => 404,
        'message' => 'Email address not found.'
    ];
}


header('Content-Type: application/json');
echo json_encode($response);

?>