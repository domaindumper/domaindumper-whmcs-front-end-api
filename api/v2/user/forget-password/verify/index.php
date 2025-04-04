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
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$resetToken = $data['token'] ?? null;  // Null coalescing operator for cleaner code
$password = $data['password'] ?? null;
$password2 = $data['password2'] ?? null;

//print_r($data);

if (!$resetToken) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing reset token.']);
    exit;
}

$client = Capsule::table('tblclients')
    ->where('password_reset_token', $resetToken)
    ->where('password_reset_token_expiry', '>', date('Y-m-d H:i:s'))
    ->first();

if (!$client) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid or expired reset token.']);
    exit;
}

if (empty($password) || empty($password2)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Both passwords are required.']);
    exit;
}

if ($password !== $password2) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Passwords do not match.']);
    exit;
}

if (!isVaildPassword($password)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Password must meet the specified criteria.']); // Be specific about criteria in your front-end messaging
    exit;
}


$encryptedPassword = password_hash($password, PASSWORD_BCRYPT);

// echo $encryptedPassword;

// print_r($client); die();

try {
    Capsule::table('tblclients')
        ->where('id', $client->id)
        ->update([
            'password' => $encryptedPassword,
            'password_reset_token' => null,
            'password_reset_token_expiry' => null,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

    // Update tblusers as well (if necessary and if the structure is as described previously)
    $userId = Capsule::table('tblusers')->where('email', $client->email)->first();
    if ($userId) {
      Capsule::table('tblusers')
          ->where('id', $userId->id) // Use userId->id
          ->update([
              'password' => $encryptedPassword,
              'updated_at' => date('Y-m-d H:i:s')
          ]);
    }

        // Send password changed email notification
        $Signature = Capsule::table('tblconfiguration')
        ->where('setting', 'Signature')
        ->first();

    $command = 'SendEmail';
    $postData = [
        'messagename' => 'Password Changed',
        'id' => $client->id,
        'customtype' => 'general',
        'customsubject' => 'Your Password Has Been Changed',
        'custommessage' => "Dear {$client->firstname},\n\nYour password has been successfully updated.\n\nIf you did not initiate this password change, please contact support immediately.\n\n" . $Signature->value,
    ];
    $results = localAPI($command, $postData);


    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Password reset successful. Please log in to continue.']);

} catch (Exception $e) {
    http_response_code(500);
    print_r("Password Reset Error: ".$e->getMessage(), "error"); // Log the error for debugging.  Consider a more robust logging solution
    echo json_encode(['status' => 'error', 'message' => 'An error occurred during password reset.']); // Generic message for security
}

?>