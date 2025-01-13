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
        'sku' => '2026-1',
        'related' => [2, 3, 4],
        'col' => 'col-lg-6',
        'tags' => [
            ['title' => 'Daily Website Data', 'slug' => '/website-database/daily-website-data/'],
            ['title' => 'Historical Website Data', 'slug' => '/website-database/historical-website-data/']
        ],
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
        'sku' => '2026-2',
        'related' => [1, 3],
    ],
    [
        'id' => 3,
        'title' => 'Daily WHOIS Database Downloads - Fresh, Accurate Data',
        'description_long' => 'Our Daily Full WHOIS Database provides comprehensive and up-to-date WHOIS records for millions of domains across the globe. This database is updated every day, ensuring you have access to the most current information available. Use this data for market research, lead generation, competitor analysis, and more.',
        'description_short' => 'Need reliable and up-to-date WHOIS data? Our Daily Full WHOIS Database provides the solution. Get access to the latest domain registration information and make informed decisions for your business.',
        'images' => [
            'image3.jpg',
            'image4.jpg',
        ],
        'slug_page' => '/whois-database/worldwide-whois/',
        'sku' => '2025-3',
        'related' => [1, 4],
    ],
    [
        'id' => 4,
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
    [
        'id' => 5,
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
    [
        'id' => 6,
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
    [
        'id' => 7,
        'title' => 'US WHOIS Database: Daily Updates',
        'description_long' => 'Stay ahead of the curve with our Daily US WHOIS Database. Get access to the latest domain registration information, including owner details, contact information, and historical records. Our data is accurate, reliable, and updated every day, giving you the insights you need to make informed decisions.',
        'description_short' => 'Get daily updates on US domain ownership and registration details.',
        'images' => [
            'image3.jpg',
            'image4.jpg',
        ],
        'slug_page' => '/whois-database/whois-by-country/us-whois/',
        'sku' => '2024-7',
        'related' => [1, 3],
    ],
    [
        'id' => 7,
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
    [
        'id' => 8,
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
    [
        'id' => 9,
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
    [
        'id' => 10,
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
    [
        'id' => 11,
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
    [
        'id' => 12,
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
    [
        'id' => 13,
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
];

// Get all custom product IDs
$customProductIds = array_column($Products, 'id');

// Get requested product ID (prioritize POST)
$data = json_decode(file_get_contents('php://input'), true);
$requestedProductId = isset($data['pid']) ? (int)$data['pid'] : null;

// Fallback to GET/REQUEST if not in POST
if ($requestedProductId === null) {
    $requestedProductId = isset($_REQUEST['pid']) ? (int)$_REQUEST['pid'] : null;
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
    $productId = (int)$apiProduct['pid'];
    $customProduct = array_filter($Products, fn($p) => (int)$p['id'] === $productId);
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