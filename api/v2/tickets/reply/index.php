<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);
require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Authorization.php';

$ca = new ClientArea();

// Define constants
define('MAX_FILE_SIZE', 2 * 1024 * 1024); // 2MB
define('ALLOWED_FILE_TYPES', ['jpg', 'gif', 'jpeg', 'png']);

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Better input validation
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON payload', 400);
    }

    if (!isset($input['ticketId']) || !isset($input['message'])) {
        throw new Exception('Ticket ID and message are required', 400);
    }

    // Initialize authorization
    $auth = new Authorization();
    $userId = $auth->validateRequest();

    // Handle file attachments with validation
    $attachments = [];
    if (isset($input['attachments']) && is_array($input['attachments'])) {
        foreach ($input['attachments'] as $attachment) {
            if (!isset($attachment['name']) || !isset($attachment['data'])) {
                continue;
            }

            // Validate file type
            $ext = strtolower(pathinfo($attachment['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, ALLOWED_FILE_TYPES)) {
                throw new Exception('Invalid file type. Only jpg, gif, jpeg, png files are allowed', 415);
            }

            // Validate base64 data
            $decodedData = base64_decode($attachment['data']);
            if ($decodedData === false) {
                throw new Exception('Invalid file data', 400);
            }

            // Check file size
            if (strlen($decodedData) > MAX_FILE_SIZE) {
                throw new Exception('File size exceeds 2MB limit', 413);
            }

            $attachments[] = [
                'name' => $attachment['name'],
                'data' => $attachment['data']
            ];
        }
    }

    // Prepare API call
    $command = 'AddTicketReply';
    $postData = [
        'ticketid' => $input['ticketId'],
        'message' => $input['message'],
        'clientid' => $userId,
        'markdown' => true
    ];

    // Add attachments if present
    if (!empty($attachments)) {
        $postData['attachments'] = base64_encode(json_encode($attachments));
    }

    $results = localAPI($command, $postData);

    if ($results['result'] == 'success') {
        $response = [
            'status' => 'success',
            'code' => 200,
            'data' => [
                'ticket_id' => (int)$input['ticketId'],
                'reply_id' => isset($results['replyid']) ? (int)$results['replyid'] : null,
                'message' => 'Reply added successfully'
            ]
        ];
    } else {
        throw new Exception($results['message'] ?? 'Failed to add reply', 400);
    }

} catch (Exception $e) {
    $statusCode = ($e->getCode() >= 400 && $e->getCode() < 600) ? $e->getCode() : 500;
    
    $response = [
        'status' => 'error',
        'code' => $statusCode,
        'message' => $e->getMessage()
    ];
}

header('Content-Type: application/json; charset=utf-8');
http_response_code($response['code']);
echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
exit();