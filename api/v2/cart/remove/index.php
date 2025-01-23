<?php
// api/v2/cart/remove/index.php

use WHMCS\ClientArea;
use WHMCS\Database\Capsule;

define('CLIENTAREA', true);

require $_SERVER['DOCUMENT_ROOT'] . '../../init.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/vendor/autoload.php';
require $_SERVER['DOCUMENT_ROOT'] . '/v2/lib/Session.php';

$ca = new ClientArea();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // ... (handle invalid request method)
}

$data = json_decode(file_get_contents('php://input'), true);
$cartItemId = $data['cartItemId'] ?? null;
$authToken = $data['authToken'] ?? null;
$sessionId = $data['sessionId'] ?? null;

// 1. Validate input data
if (empty($cartItemId)) {
    // ... (return error: cart item ID is required)
}

// 2. Get user ID from token (if provided)
$userId = null;
if ($authToken) {
    $userId = GetSession($authToken);
}

// 3. Remove item from cart
try {
    Capsule::beginTransaction();

    // Find the cart
    $cart = Capsule::table('carts')
        ->where(function ($query) use ($userId, $sessionId) {
            // ... (find cart by user_id or session_id)
        })
        ->first();

    if (!$cart) {
        // ... (return error: cart not found)
    }

    // Remove the cart item from cart_items table
    $deleted = Capsule::table('cart_items')
        ->where('cart_item_id', $cartItemId) 
        ->where('cart_id', $cart->id) // Ensure the item belongs to the cart
        ->delete();

    if ($deleted) {
        // Update updated_at in the 'carts' table
        Capsule::table('carts')
            ->where('id', $cart->id)
            ->update(['updated_at' => Capsule::raw('CURRENT_TIMESTAMP')]);

        Capsule::commit();

        // ... (return success response)
    } else {
        // ... (return error: failed to remove item)
    }

} catch (Exception $e) {
    // ... (handle error)
}