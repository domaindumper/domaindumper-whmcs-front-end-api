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

// Product More details for SEO and other purposes

$Products = array();

$Products[] = array(
    'id' => 1,
    'title' => 'Whois Database',
    'description_long' => 'Whois Database',
    'description_sort' => 'Whois Database',
    'images' => array(
        'https://www.example.com/images/product1.jpg',
        'https://www.example.com/images/product1-1.jpg',
        'https://www.example.com/images/product1-2.jpg',
    ),
    'slug_page' => '/whois-database/',
    'sku' => '2025',
    'mpn' => '2024',
    'related' => array(2, 3, 4),
    'col' => 'col-lg-6',
);

$Products[] = array(
    'id' => 2,
    'title' => 'Whois Database',
    'description_long' => 'Whois Database',
    'description_sort' => 'Whois Database',
    'images' => array(
        'https://www.example.com/images/product1.jpg',
        'https://www.example.com/images/product1-1.jpg',
        'https://www.example.com/images/product1-2.jpg',
    ),
    'slug_page' => '/whois-database/',
    'sku' => '2025',
    'mpn' => '2024',
    'related' => array(2, 3, 4),
    'col' => 'col-lg-6',
);


$command = 'GetProducts';
$postData = array(
    'pid' => '1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14', // Product IDs separated by commas
);

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
        return isset($p['id']) && (int)$p['id'] === $productId;
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
    'data' => $mergedProducts, // Use merged data
];

http_response_code(200);
header('Content-Type: application/json');
echo json_encode($response);

?>