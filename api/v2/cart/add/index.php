<?php
// api/v2/cart/add/index.php

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
$productId = $data['productId'] ?? null;
$configoptions = $data['configoptions'] ?? null;
$customfields = $data['customfields'] ?? null;
$authToken = $data['authToken'] ?? null;
$sessionId = $data['sessionId'] ?? null;

// 1. Validate input data
if (empty($productId)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required']);
    exit;
}

// Check if the product ID exists in tblproducts
$productExists = Capsule::table('tblproducts')->where('id', $productId)->exists();
if (!$productExists) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Incorrect product ID']);
    exit;
}

// 2. Get user ID from token (if provided)
$userId = null;
if ($authToken) {
    $userId = GetSession($authToken); 
}

// 3. Add to cart
try {
    Capsule::beginTransaction(); 

    // Find or create a cart in the 'carts' table
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
        $cartId = Capsule::table('carts')->insertGetId([
            'user_id' => $userId,
            'session_id' => $sessionId,
        ]);
        $cart = Capsule::table('carts')->where('id', $cartId)->first(); 
    }

    // Check for existing product with the same configoptions in 'cart_items'
    $cartItem = Capsule::table('cart_items')
        ->where('cart_id', $cart->id)
        ->where('product_id', $productId)
        ->where('config_options', json_encode($configoptions))
        ->first();

    if (!$cartItem) { 
        // Add new cart item to 'cart_items'
        Capsule::table('cart_items')->insert([
            'cart_id' => $cart->id,  
            'product_id' => $productId, 
            'quantity' => 1, 
            'config_options' => json_encode($configoptions), 
            'custom_fields' => json_encode($customfields)
        ]);
    } 

    // Update updated_at in the 'carts' table
    Capsule::table('carts')
        ->where('id', $cart->id)
        ->update(['updated_at' => Capsule::raw('CURRENT_TIMESTAMP')]); 

    // Get all cart items with configoptions from 'cart_items'
    $cartItems = Capsule::table('cart_items')
        ->where('cart_id', $cart->id)
        ->get();

    // Get product details for each cart item
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
            // Include both monthly and annually prices, without the 'type' key
            $price[$currency] = [
                'monthly' => $pricing[$currency]['monthly'], 
                'annually' => $pricing[$currency]['annually']
            ];
        }

        // Add product details to the cart item
        $item->productDetails = [
            'id' => $item->product_id, 
            'name' => $productName,
            'price' => $price
        ];
    }
    unset($item); 

    // Get total product count
    $totalProducts = count($cartItems); 

    Capsule::commit();

    http_response_code(200);
    echo json_encode([
        'status' => 'success', 
        'message' => 'Product added to cart',
        'cartItems' => $cartItems,
        'totalProducts' => $totalProducts
    ]);

} catch (Exception $e) {
    Capsule::rollback();
    http_response_code(500);
    print_r($e->getMessage()); 
}