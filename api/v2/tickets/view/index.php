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
            // Fix field names to match WHMCS API response
            'urgency' => htmlspecialchars(ucfirst(trim($results['priority']))),         // Changed from urgency to priority
            'department' => htmlspecialchars(trim($results['deptname'])),               // Changed from department to deptname
            'last_reply_by' => htmlspecialchars(trim($results['lastreplier'])),        // Changed from last_reply_by to lastreplier
            'unread' => (bool)$results['unread'],
            'client' => [
                'id' => (int)$results['userid'],
                'name' => $results['requestor_name'],
                'email' => $results['requestor_email']
            ]
        ];

        // Process ticket messages
        $ticket['messages'] = [];
        
        // Add original ticket message as first message
        $ticket['messages'][] = [
            'id' => 0,
            'date' => $ticket['date_created'],
            'message' => htmlspecialchars(trim($results['message'])),
            'attachment' => isset($results['attachment']) ? $results['attachment'] : null,
            'admin' => null,
            'owner' => 'client',
            'email' => $results['requestor_email'],
            'name' => $results['requestor_name'],
            'rating' => 0,
            'editor' => 'plain'
        ];

        // Process additional replies
        if (isset($results['replies']) && is_array($results['replies'])) {
            foreach ($results['replies'] as $reply) {
                $ticket['messages'][] = [
                    'id' => (int)$reply['replyid'],
                    'date' => date('Y-m-d H:i:s', strtotime($reply['date'])),
                    'message' => htmlspecialchars(trim($reply['message'])),
                    'attachment' => isset($reply['attachment']) ? $reply['attachment'] : null,
                    'admin' => !empty($reply['admin']) ? htmlspecialchars(trim($reply['admin'])) : null,
                    'owner' => !empty($reply['admin']) ? 'admin' : 'client',
                    'email' => isset($reply['email']) ? $reply['email'] : null,
                    'name' => isset($reply['name']) ? $reply['name'] : ($reply['admin'] ?? null),
                    'rating' => (int)($reply['rating'] ?? 0),
                    'editor' => $reply['editor'] ?? 'plain'
                ];
            }
        }

        // Sort messages by date
        usort($ticket['messages'], function($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });

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