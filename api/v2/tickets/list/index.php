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
    // Initialize authorization
    $auth = new Authorization();
    $userId = $auth->validateRequest(); // This handles token validation and expiry

    // Get tickets from WHMCS
    $command = 'GetTickets';
    $postData = array(
        'limitstart' => 0,
        'limitnum' => 100,
        'clientid' => $userId
    );

    $results = localAPI($command, $postData);

    if ($results['result'] == 'success' && isset($results['tickets']['ticket'])) {
        // Process tickets
        $tickets = array_map(function($ticket) {
            return [
                'id' => (int)$ticket['ticketid'],
                'tid' => htmlspecialchars(trim($ticket['tid'])),
                'date_created' => date('Y-m-d H:i:s', strtotime($ticket['date'])),
                'date_updated' => date('Y-m-d H:i:s', strtotime($ticket['lastreply'])),
                'subject' => htmlspecialchars(trim($ticket['subject'])),
                'message' => htmlspecialchars(trim($ticket['message'])),
                'status' => htmlspecialchars(ucfirst(trim($ticket['status']))),
                'urgency' => htmlspecialchars(ucfirst(trim($ticket['urgency']))),
                'department' => htmlspecialchars(trim($ticket['deptname'])),
                'last_reply_by' => htmlspecialchars(trim($ticket['last_reply_by'])),
                'unread' => (bool)$ticket['unread'],
                'replies_count' => (int)$ticket['replies'],
                'attachments_count' => isset($ticket['attachments']) ? count($ticket['attachments']) : 0
            ];
        }, $results['tickets']['ticket']);

        // Calculate status counts
        $statusCounts = [
            'total' => count($tickets),
            'open' => 0,
            'answered' => 0,
            'customer-reply' => 0,
            'closed' => 0,
            'in_progress' => 0
        ];

        foreach ($tickets as $ticket) {
            $status = strtolower($ticket['status']);
            if (isset($statusCounts[$status])) {
                $statusCounts[$status]++;
            }
        }

        $response = [
            'status' => 'success',
            'code' => 200,
            'data' => [
                'tickets' => $tickets,
                'status_counts' => $statusCounts,
                'total_records' => count($tickets)
            ]
        ];
    } else {
        throw new Exception($results['message'] ?? 'Failed to fetch tickets');
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