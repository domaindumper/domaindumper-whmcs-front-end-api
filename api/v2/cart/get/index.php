<?php
// api/v2/cart/get/index.php 

use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/product/products.php';

$ca = new ClientArea();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$authToken = $data['authToken'] ?? null;
$sessionId = $data['sessionId'] ?? null;

// 1. Get user ID from token (if provided)
$userId = null;
if ($authToken) {
    $userId = GetSession($authToken);
}

// 2. Get cart items
try {
    // Find the cart
    $cart = Capsule::table('carts')
        ->where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })
        ->first();

    if (!$cart) {
        http_response_code(404); // Not Found
        echo json_encode(['status' => 'error', 'message' => 'Cart not found']);
        exit;
    }

    // Get cart items with product details
    $cartItems = Capsule::table('cart_items')
        ->where('cart_id', $cart->id)
        ->get();

    foreach ($cartItems as &$item) {
        $command = 'GetProducts';
        $postData = [
            'pid' => $item->product_id,
        ];
        $productDetails = localAPI($command, $postData);

        // Extract product name and pricing details
        $productName = $productDetails['products']['product'][0]['name'];
        $pricing = $productDetails['products']['product'][0]['pricing'];

        $price = [];
        foreach (['INR', 'USD'] as $currency) {
            $price[$currency] = [
                'monthly' => $pricing[$currency]['monthly'],
                'annually' => $pricing[$currency]['annually']
            ];
        }

        // Get product image from $Products array
        $productImage = '';
        foreach ($Products as $product) {
            if ($product['id'] == $item->product_id) {
                $productImage = $product['images'][0];
                break;
            }
        }

        $item->productDetails = [
            'id' => $item->product_id,
            'name' => $productName,
            'image' => $productImage,
            'price' => $price
        ];
    }
    unset($item);

    // Get total product count
    $totalProducts = count($cartItems);

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Cart details retrieved', 
        'cartItems' => $cartItems,
        'totalProducts' => $totalProducts
    ]);

} catch (Exception $e) {
    http_response_code(500);
    print_r($e->getMessage());
}