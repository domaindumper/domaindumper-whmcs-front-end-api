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

    if (!isset($input['ticketId'])) {
        throw new Exception('Ticket ID is required', 400);
    }

    // Initialize authorization
    $auth = new Authorization();
    $userId = $auth->validateRequest();

    // Get ticket details
    $command = 'GetTicket';
    $postData = array(
        'ticketid' => $input['ticketId'],
        'clientid' => $userId
    );

    $results = localAPI($command, $postData);

    if ($results['result'] == 'success') {
        // Process ticket data
        $ticket = [
            'id' => (int)$results['ticketid'],
            'tid' => htmlspecialchars(trim($results['tid'])),
            'date_created' => date('Y-m-d H:i:s', strtotime($results['date'])),
            'date_updated' => date('Y-m-d H:i:s', strtotime($results['lastreply'])),
            'subject' => htmlspecialchars(trim($results['subject'])),
            'status' => htmlspecialchars(ucfirst(trim($results['status']))),
            'urgency' => htmlspecialchars(ucfirst(trim($results['urgency']))),
            'department' => htmlspecialchars(trim($results['department'])),
            'last_reply_by' => htmlspecialchars(trim($results['lastreplier'])),
            'unread' => (bool)$results['unread'],
            'client' => [
                'id' => (int)$results['userid'],
                'name' => $results['requestor_name'],
                'email' => $results['requestor_email']
            ]
        ];

        // Process replies
        if (isset($results['replies']) && is_array($results['replies'])) {
            $ticket['replies'] = array_map(function($reply) {
                return [
                    'id' => (int)$reply['id'],
                    'date' => date('Y-m-d H:i:s', strtotime($reply['date'])),
                    'message' => htmlspecialchars(trim($reply['message'])),
                    'attachment' => $reply['attachment'] ?? null,
                    'admin' => htmlspecialchars(trim($reply['admin'])),
                    'rating' => (int)$reply['rating'],
                    'editor' => $reply['editor'] ?? 'plain'
                ];
            }, $results['replies']);
        } else {
            $ticket['replies'] = [];
        }

        // Process attachments
        if (isset($results['attachments']) && is_array($results['attachments'])) {
            $ticket['attachments'] = array_map(function($attachment) {
                return [
                    'id' => (int)$attachment['id'],
                    'filename' => htmlspecialchars(trim($attachment['filename'])),
                    'size' => (int)$attachment['size'],
                    'url' => $attachment['url'] ?? null
                ];
            }, $results['attachments']);
        } else {
            $ticket['attachments'] = [];
        }

        $response = [
            'status' => 'success',
            'code' => 200,
            'data' => $ticket
        ];
    } else {
        throw new Exception($results['message'] ?? 'Failed to fetch ticket', 404);
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