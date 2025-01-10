<?php
// api/v2/user/forget-password/index.php

use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';

$ca = new ClientArea();

function sendPasswordResetEmail($client) {
    $token = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', strtotime('+2 hour'));

    Capsule::table('tblclients')
        ->where('id', $client->id)
        ->update([
            'password_reset_token' => $token,
            'password_reset_token_expiry' => $expiry,
        ]);

    $Domain = Capsule::table('tblconfiguration')
        ->where('setting', 'Domain')
        ->first();

    $resetLink = $Domain->value . "/account/forgot-password/" . $token;

    $Signature = $Client = Capsule::table('tblconfiguration')
    ->where('setting', 'Signature')
    ->first();

    $command = 'SendEmail';
    $postData = [
        'messagename' => 'Password Reset',
        'id' => $client->id,
        'customtype' => 'general',
        'customsubject' => 'Password Reset Request',
        'custommessage' => "Dear {$client->firstname},\n\nTo reset your password, please click on the link below.\n\n<a href=\"{$resetLink}\">Reset your password</a>\n\nIf you're having trouble, try copying and pasting the following URL into your browser:\n{$resetLink}\n\nIf you did not request this reset, you can ignore this email. It will expire in 2 hours.\n\n---\n{$Signature->value}",
    ];

    $results = localAPI($command, $postData);

    if ($results['result'] === 'success') {
        return [
            'status' => 'success',
            'code' => 200,
            'message' => 'If you are a registered user, you will receive an email with a password reset link.'
        ];
    } else {
        return [
            'status' => 'error',
            'code' => 500,
            'message' => 'Failed to send password reset email. Please try again later. Error: ' . $results['message'],
        ];
    }
}



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