<?php
// api/v2/cart/remove/index.php

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
$productId = $data['productId'] ?? null; // Changed from $cartItemId
$configoptions = $data['configoptions'] ?? null; 
$customfields = $data['customfields'] ?? null;
$authToken = $data['authToken'] ?? null;
$sessionId = $data['sessionId'] ?? null;

// 1. Validate input data
if (empty($productId)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Product ID is required']); // Changed error message
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
        ->where('id', $productId) // Use $productId here
        ->where('cart_id', $cart->id)
        ->delete();

    if ($deleted) {
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
                $price[$currency] = [
                    'monthly' => $pricing[$currency]['monthly'],
                    'annually' => $pricing[$currency]['annually']
                ];
            }

            // Get product image and SKU from $Products array
            $productImage = '';
            $productSKU = '';
            foreach ($Products as $product) {
                if ($product['id'] == $item->product_id) {
                    $productImage = $product['images'][0]; 
                    $productSKU = $product['sku']; 
                    break;
                }
            }

            $item->productDetails = [
                'id' => $item->product_id, 
                'name' => $productName,
                'sku' => $productSKU,
                'image' => $productImage, 
                'price' => $price
            ];

            // Add config_options if they exist
            if (
                !empty($item->config_options) && 
                $item->config_options !== null && 
                $item->config_options !== ''
            ) {
                $configOptions = json_decode($item->config_options, true);
                $decodedConfigOptions = [];

                foreach ($configOptions['configoption'] as $optionId => $subOptionId) {
                    $optionName = Capsule::table('tblproductconfigoptions')
                        ->where('id', $optionId)
                        ->value('optionname');

                    $subOptionName = Capsule::table('tblproductconfigoptionssub')
                        ->where('id', $subOptionId)
                        ->value('optionname');

                    $decodedConfigOptions[] = [
                        'name' => $optionName,
                        'value' => $subOptionName
                    ];
                }

                $item->productDetails['config_options'] = $decodedConfigOptions;
            }
        }
        unset($item);

        // Get total product count
        $totalProducts = count($cartItems);

        // Calculate totals for each currency
        $totals = [
            'INR' => [
                'subtotal' => 0,
                'gst' => 0,
                'total' => 0
            ],
            'USD' => [
                'subtotal' => 0,
                'gst' => 0,
                'total' => 0
            ]
        ];

        foreach ($cartItems as $item) {
            foreach (['INR', 'USD'] as $currency) {
                if (isset($item->productDetails['price'][$currency])) {
                    $monthlyPrice = floatval($item->productDetails['price'][$currency]['monthly']);
                    $totals[$currency]['subtotal'] += $monthlyPrice;
                }
            }
        }

        // Calculate GST and total for each currency
        foreach ($totals as &$currencyTotal) {
            $currencyTotal['gst'] = round($currencyTotal['subtotal'] * 0.18, 2); // 18% GST
            $currencyTotal['total'] = $currencyTotal['subtotal'] + $currencyTotal['gst'];
            
            // Format numbers to 2 decimal places
            $currencyTotal['subtotal'] = number_format($currencyTotal['subtotal'], 2, '.', '');
            $currencyTotal['gst'] = number_format($currencyTotal['gst'], 2, '.', '');
            $currencyTotal['total'] = number_format($currencyTotal['total'], 2, '.', '');
        }
        unset($currencyTotal);

        Capsule::commit();

        // Return a success response with cart items, totalProducts, and totals
        http_response_code(200);
        echo json_encode([
            'status' => 'success', 
            'message' => 'Item removed from cart',
            'cartItems' => $cartItems,
            'totalProducts' => $totalProducts,
            'totals' => $totals
        ]); 
    } else {
        // Return the specific error message
        http_response_code(400); 
        echo json_encode(['status' => 'error', 'message' => 'Cart item ID is required']); 
    }

} catch (Exception $e) {
    Capsule::rollback();
    http_response_code(500);
    print_r($e->getMessage());
}