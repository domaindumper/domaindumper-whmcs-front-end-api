<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/product/products.php';

$ca = new ClientArea();


// Get all custom product IDs
$customProductIds = array_column($Products, 'id');

// Get requested product ID (prioritize POST)
$data = json_decode(file_get_contents('php://input'), true);
$requestedProductId = isset($data['pid']) ? (int) $data['pid'] : null;

// Fallback to GET/REQUEST if not in POST
if ($requestedProductId === null) {
    $requestedProductId = isset($_REQUEST['pid']) ? (int) $_REQUEST['pid'] : null;
}

// Determine API product IDs based on request
if ($requestedProductId) {
    $productExists = in_array($requestedProductId, $customProductIds, true);
    if (!$productExists) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Product not found.']);
        exit;
    }
    $apiProductIds = [$requestedProductId];
} else {
    $apiProductIds = $customProductIds;
}


$command = 'GetProducts';

if (empty($apiProductIds)) {
    http_response_code(404);
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
    $productId = (int) $apiProduct['pid'];
    $customProduct = array_filter($Products, fn($p) => (int) $p['id'] === $productId);
    $mergedProduct = !empty($customProduct) ? array_merge($apiProduct, reset($customProduct)) : $apiProduct;

    // Remove unwanted keys
    unset($mergedProduct['product_url']);

    ksort($mergedProduct); // Sort keys alphabetically within each product

    $mergedProducts[] = $mergedProduct;
}

// The usort block to sort by ID remains the same:
usort($mergedProducts, function ($a, $b) {
    return $a['id'] <=> $b['id'];
});



// Build response (no changes here)
$response = $requestedProductId
    ? ['status' => 'success', 'code' => 200, 'data' => ['product' => $mergedProducts[0]]]
    : ['status' => 'success', 'code' => 200, 'data' => ['products' => $mergedProducts]];

http_response_code($response['code']);
header('Content-Type: application/json');
echo json_encode($response);
?>