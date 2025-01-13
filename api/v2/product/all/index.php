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
        'title' => 'India WHOIS Database: Daily Updates',
        'description_short' => 'Access the most up-to-date and comprehensive WHOIS data for Indian domains.',
        'description_long' => 'Our India WHOIS Database provides comprehensive and accurate WHOIS records for millions of domains registered in India. This database is updated daily, ensuring you have access to the freshest data available. Use this data for lead generation, market research, competitor analysis, and more.',
        'images' => [
            'image1.jpg',
            'image2.jpg',
        ],
        'slug_page' => '/whois-database/whois-by-country/india-whois/',
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
        'title' => 'Daily Registered Domains (Free) - No API, No WHOIS',
        'description_short' => 'Get a free daily list of newly registered domain names.',
        'description_long' => 'Stay ahead of the curve with our free Daily Registered Domains list. This list is updated every 24 hours and provides you with a snapshot of the latest domain name registrations. While it doesn\'t include WHOIS data or API access, it\'s a valuable resource for domain research, market analysis, and identifying potential new competitors.',
        'images' => [
            'image3.jpg',
            'image4.jpg',
        ],
        'slug_page' => '/domain-name-list/new-domains/',
        'sku' => '2026-2',
        'related' => [4],
    ],
    [
        'id' => 3,
        'title' => 'Daily WHOIS Database Downloads - Fresh, Accurate Data',
        'description_short' => 'Need reliable and up-to-date WHOIS data? Our Daily Full WHOIS Database provides the solution. Get access to the latest domain registration information and make informed decisions for your business.',
        'description_long' => 'Our Daily Full WHOIS Database provides comprehensive and up-to-date WHOIS records for millions of domains across the globe. This database is updated every day, ensuring you have access to the most current information available. Use this data for market research, lead generation, competitor analysis, and more.',
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
        'title' => '260M+ Registered Domains - Updated Daily',
        'description_short' => 'Get an updated list of all registered domains daily.',
        'description_long' => 'Our All Registered Domains List provides a comprehensive database of over 260 million active domain names across various TLDs. This list is updated daily, ensuring you have the most current information available. Use this data for domain research, market analysis, and identifying potential new competitors or trends. Please note that this list does not include WHOIS data.',
        'images' => [
            'image3.jpg',
            'image4.jpg',
        ],
        'slug_page' => '/domain-name-list/all-domains/',
        'sku' => '2026',
        'related' => [2],
    ],
    [
        'id' => 5,
        'title' => '320k+ Website Details - Comprehensive Data',
        'description_short' => 'Access a massive database of website details, including emails and tech stacks.',
        'description_long' => 'Our Website Details Database provides comprehensive information on over 320,000 websites, including email addresses, technology stacks, contact details, and more. This database is updated daily, ensuring you have access to the most current information available. Use this data for lead generation, market research, competitor analysis, and a variety of other business applications.',
        'images' => [
            'image3.jpg',
            'image4.jpg',
        ],
        'slug_page' => '/website-database/historical-website-data/',
        'sku' => '2026-5',
        'related' => [1, 3],
    ],
    [
        'id' => 6,
        'title' => 'Test Drive Our WHOIS Database - 7 Days Trial',
        'description_short' => 'Experience the power of our WHOIS database with a 7-day trial.',
        'description_long' => 'Try our Daily Full WHOIS Database for free and see how it can benefit your business. Our trial gives you full access to our comprehensive WHOIS records for 7 days, allowing you to explore the data, test our tools, and experience the value we offer. Sign up for your trial today!',
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
        'description_short' => 'Get daily updates on US domain ownership and registration details.',
        'description_long' => 'Stay ahead of the curve with our Daily US WHOIS Database. Get access to the latest domain registration information, including owner details, contact information, and historical records. Our data is accurate, reliable, and updated every day, giving you the insights you need to make informed decisions.',
        'images' => [
            'image3.jpg',
            'image4.jpg',
        ],
        'slug_page' => '/whois-database/whois-by-country/us-whois/',
        'sku' => '2025-7',
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
        'title' => 'Australia - Whois Database',
        'description_short' => 'Access the most up-to-date and comprehensive WHOIS data for Australian domains.',
        'description_long' => 'Our Australia WHOIS Database provides comprehensive and accurate WHOIS records for millions of domains registered in Australia. This database is updated daily, ensuring you have access to the freshest data available. Use this data for lead generation, market research, competitor analysis, and more.',
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
        'title' => 'Historical WHOIS Database - Access Past Domain Data',
        'description_short' => 'Need to research past domain ownership? Download our historical WHOIS database.',
        'description_long' => 'Step back in time with our Old WHOIS Database. This comprehensive archive allows you to access domain registration data from previous years, going all the way back to 2011. Select the year and month you\'re interested in and download the corresponding WHOIS records. This data is invaluable for historical domain research, investigating ownership changes, and understanding domain trends over time.',
        'images' => [
            'image3.jpg',
            'image4.jpg',
        ],
        'slug_page' => '/whois-database/historical-whois/',
        'sku' => '2026',
        'related' => [1, 3],
    ],
    [
        'id' => 10,
        'title' => 'Daily Registered Domains with API - No WHOIS',
        'description_short' => 'Access a daily updated list of new domain names via our API.',
        'description_long' => 'Streamline your domain research with our Daily Registered Domains API. This API provides access to a freshly updated list of newly registered domain names every 24 hours. Integrate it with your application to automate downloads and seamlessly incorporate the data into your workflows. While it doesn\'t include WHOIS data, it\'s a valuable resource for domain analysis, market research, and identifying potential new competitors.',
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