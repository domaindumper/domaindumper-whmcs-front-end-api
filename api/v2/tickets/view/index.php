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
        'ticketid' => $input['ticketId']
    );

    $results = localAPI($command, $postData);

    //print_r($results); // Debugging line to check the API response

    if ($results['result'] == 'success') {
        // Process ticket data
        $ticket = [
            'id' => (int)$results['ticketid'],
            'tid' => htmlspecialchars(trim($results['tid'])),
            'department_id' => (int)$results['deptid'],
            'department_name' => htmlspecialchars(trim($results['deptname'])),
            'user_id' => (int)$results['userid'],
            'contact_id' => (int)$results['contactid'],
            'date_created' => date('Y-m-d H:i:s', strtotime($results['date'])),
            'subject' => htmlspecialchars(trim($results['subject'])),
            'status' => htmlspecialchars(ucfirst(trim($results['status']))),
            'priority' => htmlspecialchars(ucfirst(trim($results['priority']))),
            'last_reply' => date('Y-m-d H:i:s', strtotime($results['lastreply'])),
            'flag' => (int)$results['flag'],
            'service' => $results['service'],
            'client' => [
                'id' => (int)$results['userid'],
                'name' => htmlspecialchars(trim($results['name'])),
                'email' => $results['email'],
                'cc' => $results['cc']
            ]
        ];

        // Process messages (original + replies)
        $ticket['messages'] = [];

        // Add original message
        if (isset($results['replies']['reply']) && is_array($results['replies']['reply'])) {
            foreach ($results['replies']['reply'] as $reply) {
                // Get the message and clean up code blocks
                $message = trim($reply['message']);
                
                // Remove only leading and trailing newlines in code blocks
                $message = preg_replace('/```\r?\n*(.*?)\r?\n*```/s', function($matches) {
                    $code = trim($matches[1]); // Remove leading/trailing whitespace
                    return "```$code```";
                }, $message);
                
                $message = html_entity_decode($message);

                $ticket['messages'][] = [
                    'id' => (int)$reply['replyid'],
                    'user_id' => (int)$reply['userid'],
                    'contact_id' => (int)$reply['contactid'],
                    'date' => date('Y-m-d H:i:s', strtotime($reply['date'])),
                    'message' => $message, // Use cleaned message
                    'requestor' => [
                        'name' => htmlspecialchars(trim($reply['requestor_name'])),
                        'email' => $reply['requestor_email'],
                        'type' => $reply['requestor_type']
                    ],
                    'admin' => !empty($reply['admin']) ? htmlspecialchars(trim($reply['admin'])) : null,
                    'rating' => (int)($reply['rating'] ?? 0),
                    'attachments' => isset($reply['attachments']) && is_array($reply['attachments']) 
                        ? array_filter(array_map(function($attachment) {
                            return !empty($attachment) ? [
                                'filename' => $attachment['filename'] ?? null,
                                'index' => $attachment['index'] ?? null
                            ] : null;
                        }, $reply['attachments']))
                        : []
                ];
            }
        }

        // Add ticket notes if available
        if (isset($results['notes']['note']) && is_array($results['notes']['note'])) {
            $ticket['notes'] = array_map(function($note) {
                return [
                    'id' => (int)$note['noteid'],
                    'date' => date('Y-m-d H:i:s', strtotime($note['date'])),
                    'message' => html_entity_decode(trim($note['message'])), // Changed from htmlspecialchars to html_entity_decode
                    'admin' => htmlspecialchars(trim($note['admin'])),
                    'attachments' => isset($note['attachments']) && is_array($note['attachments'])
                        ? array_filter($note['attachments'])
                        : []
                ];
            }, $results['notes']['note']);
        } else {
            $ticket['notes'] = [];
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