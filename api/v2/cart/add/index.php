<?php
// api/v2/cart/add/index.php

use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';

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
// Add more validation for $configoptions, $customfields as needed

// 2. Authenticate user (if logged in)
$userId = null;
if ($authToken) {
    if (isActiveSession($authToken)) {
        $userId = GetSession($authToken);
    } else {
        http_response_code(401); // Unauthorized
        echo json_encode(['status' => 'error', 'message' => 'Invalid or expired authentication token']);
        exit;
    }
}

// 3. Add to cart
try {

    // Find or create a cart
    $cart = Capsule::table('carts')
        ->where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })
        ->first();

        echo 'runing here1';

    if (!$cart) {
        $cart = Capsule::table('carts')->create([
            'user_id' => $userId,
            'session_id' => $sessionId,
        ]);
    }

    // Check for existing product in the cart
    $cartItem = Capsule::table('carts')
        ->where('id', $cart->id)
        ->where('product_id', $productId)
        ->first();

    if ($cartItem) {
        // Update existing cart item quantity
        Capsule::table('carts')
            ->where('id', $cartItem->id)
            ->update(['quantity' => $cartItem->quantity + 1]);
    } else {
        // Add new cart item
        Capsule::table('carts')->create([
            'id' => $cart->id, 
            'product_id' => $productId,
            'quantity' => 1,
            'configoptions' => json_encode($configoptions), // Store as JSON
            'customfields' => json_encode($customfields),   // Store as JSON
        ]);
    }

    

    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Product added to cart']);

} catch (Exception $e) {
    

    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to add product to cart']);
    error_log($e->getMessage()); 
}