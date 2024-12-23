<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';

$ca = new ClientArea();

// Custom Products array (populate with your actual data)
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
    [
        'id' => 2,
        'title' => 'Domain Backordering',
        'description_long' => 'Domain Backordering long description...',
        'description_short' => 'Domain Backordering short description',
        'images' => [
            'image3.jpg',
            'image4.jpg',
        ],
        'slug_page' => '/domain-backordering/',
        'sku' => '2026',
        'related' => [1, 3],
     ],
// ... more products
];

// Get all product IDs from the custom product array
$customProductIds = array_column($Products, 'id');

// Determine if a specific product ID is requested
// Check for pid in POST data (Prioritize POST over GET/REQUEST)
$data = json_decode(file_get_contents('php://input'), true);  // Get POST data
$requestedProductId = isset($data['pid']) ? (int)$data['pid'] : null;

// If not found in POST, check GET/REQUEST
if ($requestedProductId === null) {
    $requestedProductId = isset($_REQUEST['pid']) ? (int)$_REQUEST['pid'] : null;
}


// If a specific product ID is requested, check if it exists in your custom products
if ($requestedProductId) {
    $productExists = in_array($requestedProductId, $customProductIds, true);
    if (!$productExists) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Product not found.']);
        exit;
    }
    $apiProductIds = [$requestedProductId];
} else {
    // If no specific product ID is requested, use all IDs from custom products
    $apiProductIds = $customProductIds;
}


$command = 'GetProducts';

// If the $apiProductIds array is empty, it means there are no custom products to retrieve.
if (empty($apiProductIds)) {
    http_response_code(404); // 404 Not Found if there are no products
    echo json_encode(['status' => 'error', 'message' => 'No products found.']);
    exit;
}

$postData = ['pid' => implode(',', $apiProductIds)];
$results = localAPI($command, $postData);


if (!$results || !isset($results['products']['product']) || !is_array($results['products']['product'])) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Error fetching products.']);
    exit;
}

$apiProducts = $results['products']['product'];
$mergedProducts = [];

foreach ($apiProducts as $apiProduct) {
    $productId = (int)$apiProduct['pid'];
    $customProduct = array_filter($Products, fn($p) => (int)$p['id'] === $productId);

    $mergedProduct = !empty($customProduct) ? array_merge($apiProduct, reset($customProduct)) : $apiProduct;
    $mergedProducts[] = $mergedProduct;
}



// Determine the response data based on whether a specific product was requested
if ($requestedProductId) {
    $response = ['status' => 'success', 'code' => 200, 'data' => ['product' => $mergedProducts[0]]];
} else {
    $response = ['status' => 'success', 'code' => 200, 'data' => ['products' => $mergedProducts]];
}


http_response_code($response['code']);
header('Content-Type: application/json');
echo json_encode($response);

?>
