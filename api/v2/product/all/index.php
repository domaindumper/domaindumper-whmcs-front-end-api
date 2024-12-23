<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';

$ca = new ClientArea();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'code' => 405, 'message' => 'Method not allowed']);
    exit;
}

// Products array (shortened and with example data)
$Products = [
    [
        'id' => 1,
        'title' => 'Whois Database',
        'description_long' => 'Whois Database long description...',
        'description_short' => 'Whois Database short description',
        'images' => [
            'image1.jpg',
            'image2.jpg',
        ],
        'slug_page' => '/whois-database/',
        'sku' => '2025',
        'related' => [2, 3, 4],
    ],
    // ... more products
];

// Dynamically get product IDs from the $Products array
$productIds = array_column($Products, 'id');

$command = 'GetProducts';
$postData = ['pid' => implode(',', $productIds)];

$results = localAPI($command, $postData);

if (!$results || !isset($results['products']['product']) || !is_array($results['products']['product'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'code' => 400, 'message' => 'Products not found or unexpected API response']);
    exit;
}

$apiProducts = $results['products']['product'];
$mergedProducts = [];

foreach ($apiProducts as $apiProduct) {
    $productId = (int)$apiProduct['pid'];

    $customProduct = array_filter($Products, function ($p) use ($productId) {
        return (int)$p['id'] === $productId; // Type casting for comparison
    });

    if (!empty($customProduct)) {
        $customProduct = reset($customProduct);
        $mergedProduct = array_merge($apiProduct, $customProduct);
    } else {
        $mergedProduct = $apiProduct;
    }

    $mergedProducts[] = $mergedProduct;
}

$response = [
    'status' => 'success',
    'code' => 200,
    'data' => $mergedProducts,
];

http_response_code(200);
header('Content-Type: application/json');
echo json_encode($response);

?>
