<?php
use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';

$ca = new ClientArea();

// Determine request method and handle accordingly
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $productId = isset($data['pid']) ? (int)$data['pid'] : null;

     if ($productId === null || $productId <= 0) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid product ID.']);
            exit;
     }

    $command = 'GetProducts';
    $postData = ['pid' => $productId];


    $results = localAPI($command, $postData);

        if (!$results || !isset($results['products']['product'][0])) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Product not found.']);
            exit;
        }

        $apiProduct = $results['products']['product'][0]; // Get the single product

        $response = [
            'status' => 'success',
            'code' => 200,
            'data' => ['product' => $apiProduct], // Include the 'product' key
        ];


} else if ($_SERVER['REQUEST_METHOD'] === 'GET'){


    // Products array (add your actual product data here)
    $Products = [
        // ... your product data ...
    ];


    $productIds = array_column($Products, 'id');


    $command = 'GetProducts';
    $postData = !empty($productIds) ? ['pid' => implode(',', $productIds)] : []; // Handle empty $productIds

    $results = localAPI($command, $postData);

    if (!$results || !isset($results['products']['product']) || !is_array($results['products']['product'])) {
        http_response_code(400); // Or 404 if no products are expected when empty.
        echo json_encode(['status' => 'error', 'message' => 'Products not found or unexpected API response.']);
        exit;
    }

    $apiProducts = $results['products']['product'];
    $mergedProducts = [];

    foreach ($apiProducts as $apiProduct) {
        $productId = (int)$apiProduct['pid'];

        $customProduct = array_filter($Products, function ($p) use ($productId) {
            return (int)$p['id'] === $productId;
        });

        $mergedProduct = !empty($customProduct) ? array_merge($apiProduct, reset($customProduct)) : $apiProduct;
        $mergedProducts[] = $mergedProduct;
    }

    $response = [
        'status' => 'success',
        'code' => 200,
        'data' => ['products' => $mergedProducts], // Use 'products' key for the array
    ];

} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}



http_response_code($response['code']);
header('Content-Type: application/json');
echo json_encode($response);
?>