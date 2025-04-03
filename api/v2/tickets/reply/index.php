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
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    // Validate JSON and required fields
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON payload', 400);
    }

    if (!isset($input['ticketId']) || !isset($input['message'])) {
        throw new Exception('Ticket ID and message are required', 400);
    }

    // Initialize authorization
    $auth = new Authorization();
    $userId = $auth->validateRequest();

    // Handle file attachments if present
    $attachments = [];
    if (isset($input['attachments']) && is_array($input['attachments'])) {
        foreach ($input['attachments'] as $attachment) {
            if (isset($attachment['name']) && isset($attachment['data'])) {
                $attachments[] = [
                    'name' => $attachment['name'],
                    'data' => $attachment['data'] // Should be base64 encoded
                ];
            }
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

    // Add custom fields if present
    if (isset($input['customFields']) && is_array($input['customFields'])) {
        $postData['customfields'] = base64_encode(serialize($input['customFields']));
    }

    $results = localAPI($command, $postData);

    if ($results['result'] == 'success') {
        // Format the response
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