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

    // Initialize totals array
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
            $decodedConfigOptions = []; // Array to store decoded config options

            foreach ($configOptions['configoption'] as $optionId => $subOptionId) {
                // Get the option name from tblproductconfigoptions
                $optionName = Capsule::table('tblproductconfigoptions')
                    ->where('id', $optionId)
                    ->value('optionname');

                // Get the sub-option name (real value) from tblproductconfigoptionssub
                $subOptionName = Capsule::table('tblproductconfigoptionssub')
                    ->where('id', $subOptionId)
                    ->value('optionname');

                // Add the decoded config option to the array
                $decodedConfigOptions[] = [
                    'name' => $optionName,
                    'value' => $subOptionName
                ];
            }

            $item->productDetails['config_options'] = $decodedConfigOptions;
        }

        // Calculate totals for each currency
        foreach (['INR', 'USD'] as $currency) {
            // Assuming monthly price is being used - modify if using different billing cycle
            $price = (float)$price[$currency]['monthly'];
            $totals[$currency]['subtotal'] += $price;
        }
    }
    unset($item);

    // Get total product count
    $totalProducts = count($cartItems);

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

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'message' => 'Cart details retrieved', 
        'cartItems' => $cartItems,
        'totalProducts' => $totalProducts,
        'totals' => $totals
    ], JSON_PRETTY_PRINT);

} catch (Exception $e) {
    http_response_code(500);
    print_r($e->getMessage());
}