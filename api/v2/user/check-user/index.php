<?php
// api/v2/user/check-user/index.php

use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';

$ca = new ClientArea();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? null;

// 1. Validate email format (optional, but recommended)
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid email address']);
    exit;
}

// 2. Check if the user exists
try {
    $exists = Capsule::table('tblclients')->where('email', $email)->exists(); 

    if (!$exists) {
        // User does not exist, send OTP email

        // 1. Generate OTP
        $otp = mt_rand(100000, 999999);

        // 2. Insert or update OTP in email_verification table
        Capsule::table('email_verification')
            ->updateOrInsert(
                ['email' => $email], // Where clause (find by email)
                ['otp' => $otp, 'updated_at' => Capsule::raw('CURRENT_TIMESTAMP')] // Update or insert data
            );

        // 3. Send OTP email using localAPI
        $signature = Capsule::table('tblconfiguration')
            ->where('setting', 'Signature')
            ->first();

        $command = 'SendEmail';
        $postData = [
            'messagename' => 'OTP Verification',
            'customtype' => 'general',
            'id' => 504,
            'customsubject' => 'OTP Verification',
            'email' => $email,
            'custommessage' => "Your OTP for verification is: " . $otp . "\n" . $signature->value,
        ];

        $results = localAPI($command, $postData);

        if ($results['result'] !== 'success') {
            // Handle email sending error (e.g., log the error)
        }
    }

    http_response_code(200);
    echo json_encode(['exists' => $exists]); 

} catch (Exception $e) {
    http_response_code(500);
    print_r($e->getMessage()); 
}