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
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method Not Allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$cartItemId = $data['cartItemId'] ?? null;
$authToken = $data['authToken'] ?? null;
$sessionId = $data['sessionId'] ?? null;

// 1. Validate input data
if (empty($cartItemId)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Cart item ID is required']);
    exit;
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
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })
        ->first();

    if (!$cart) {
        http_response_code(404); 
        echo json_encode(['status' => 'error', 'message' => 'Cart not found']);
        exit;
    }

    // Remove the cart item from cart_items table
    $deleted = Capsule::table('cart_items')
        ->where('cart_item_id', $cartItemId)
        ->where('cart_id', $cart->id)
        ->delete();

    if ($deleted) {
        // Update updated_at in the 'carts' table
        Capsule::table('carts')
            ->where('id', $cart->id)
            ->update(['updated_at' => Capsule::raw('CURRENT_TIMESTAMP')]);

        Capsule::commit();

        // Return a success response
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Item removed from cart']); 
    } else {
        Capsule::rollback();
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to remove item from cart']);
    }

} catch (Exception $e) {
    Capsule::rollback();
    http_response_code(500);
    print_r($e->getMessage());
}