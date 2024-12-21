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
$resetToken = $data['token'] ?? null;

if (!$resetToken) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing reset token.']);
    exit;
}

$client = Capsule::table('tblclients')
    ->where('password_reset_token', $resetToken)
    ->where('password_reset_token_expiry', '>', date('Y-m-d H:i:s'))
    ->first();

if ($client) {
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Token is valid.']);
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid or expired reset token.']);
}

?>