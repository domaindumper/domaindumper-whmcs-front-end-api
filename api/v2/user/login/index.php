<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';

$ca = new ClientArea();

// *** CORS Headers ***
$allowedOrigins = [
    'http://localhost:3000', 
    'https://www.whoisextractor.com'
];

$origin = $_SERVER['HTTP_ORIGIN']; 

if (in_array($origin, $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $origin);
}

header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit; // Terminate the request for OPTIONS
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $data = json_decode(file_get_contents('php://input'), true);

    $email = !empty($data['email']) ? $data['email'] : '';
    $password2 = !empty($data['password']) ? $data['password'] : '';
    $rememberMe = !empty($data['rememberMe']) ? $data['rememberMe'] : 0; 

    $command = 'ValidateLogin';
    $postData = array(
        'email' => $email,
        'password2' => $password2,
    );

    $results = localAPI($command, $postData);

    $ResponseCode = 200;

    if ($results['result'] == 'success') {

        $command = 'GetClientsDetails';
        $postData = array(
            'clientid' => $results['userid'],
            'stats' => true,
        );

        $UserResults = localAPI($command, $postData);

        $Userdata = $UserResults['client'];

        unset($Userdata['users']);

        // Generate JWT Auth Token
        $ExpireTime = time() + ($rememberMe ? (3600 * 24 * 30) : 3600); 

        $payload = [
            'iss' => JWT_ISS,
            'aud' => JWT_AUD,
            'iat' => time(),
            'exp' => $ExpireTime, 
            'data' => $Userdata,
        ];

        $authToken = JWT::encode($payload, JWT_SECRET, JWT_ALGORITHM);

        StoreSession($authToken, $Userdata['client_id'], $ExpireTime);

        $Userdata = refineUserInformation($Userdata); 

        // *** Set the JWT as an HTTP-only cookie (with dynamic domain and Secure attribute) ***
        $serialized = serialize($authToken);

        // Determine the domain dynamically
        $domain = ($_SERVER['HTTP_ORIGIN'] === 'http://localhost:3000') ? 'localhost' : '.whoisextractor.com'; 

        // Determine if the connection is secure (HTTPS) - using $domain check
        $isSecure = ($domain === 'localhost') ? false : true; 

        // Conditionally add the Domain attribute
        $cookieHeader = 'Set-Cookie: authToken=' . $serialized . '; HttpOnly; ' . ($isSecure ? 'Secure; Domain=' . $domain . ';' : '') . ' SameSite=Lax; Max-Age=' . ($ExpireTime - time()) . '; Path=/'; 

        header($cookieHeader);

        $response = [
            'status' => $results['result'],
            'code' => 200,
            'authToken' => $authToken,
            'Userdata' => $Userdata
        ]; 

    } else {
        $response = [
            'status' => $results['result'],
            'code' => 200, 
            'message' => $results['message']
        ];
    }

} else { 

    // Handle invalid request method
    $ResponseCode = 405;
    $response = [
        'status' => 'error',
        'code' => 405,
        'message' => 'Method not allowed'
    ];
}

http_response_code($ResponseCode);
header('Content-Type: application/json');
echo json_encode($response);
?>