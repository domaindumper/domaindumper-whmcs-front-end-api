<?php 

//api/v2/product/products.php

// Custom Products array (populate with your actual data)

$Products = [
    [
        'id' => 1,
        'title' => 'India WHOIS Database: Daily Updates',
        'description_short' => 'Access the most up-to-date and comprehensive WHOIS data for Indian domains.',
        'description_long' => 'Our India WHOIS Database provides comprehensive and accurate WHOIS records for millions of domains registered in India. This database is updated daily, ensuring you have access to the freshest data available. Use this data for lead generation, market research, competitor analysis, and more.',
        'images' => [
            '/images/product/1/india-whois-database.jpg',
            '/images/product/1/more.jpg',
        ],
        'slug_page' => '/whois-database/whois-by-country/india-whois/',
        'sku' => 'DB-WHOIS-IN-01',
        'mpn' => 'DB-WHOIS-IN-01-ID',
        'related' => [2, 3, 4],
        'col' => 'col-lg-6',
        'tags' => [
            ['title' => 'Daily Website Data', 'slug' => '/website-database/daily-website-data/'],
            ['title' => 'Historical Website Data', 'slug' => '/website-database/historical-website-data/']
        ],
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'new',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Real-time WHOIS lookups',
            'Historical WHOIS records',
            'Bulk domain analysis',
            'Domain ownership identification',
            'Domain expiry monitoring'
        ],
        'api_details' => [
            'documentation' => 'https://www.whoisextractor.com/support/api-documents/',
            'request_limit' => '500 requests/month',
            'support' => 'Email and chat support'
        ],
        'delivery_method' => 'digital download',
        'return_policy' => 'https://www.whoisextractor.com/tos/refund-and-cancellation-policy/',
        'terms_of_service' => 'https://www.whoisextractor.com/tos/terms-and-conditions/'
    ],
    [
        'id' => 2,
        'title' => 'Daily Registered Domains (Free) - No API, No WHOIS',
        'description_short' => 'Get a free daily list of newly registered domain names.',
        'description_long' => 'Stay ahead of the curve with our free Daily Registered Domains list. This list is updated every 24 hours and provides you with a snapshot of the latest domain name registrations. While it doesn\'t include WHOIS data or API access, it\'s a valuable resource for domain research, market analysis, and identifying potential new competitors.',
        'images' => [
            '/images/product/2/newly-registered-domains-free.jpg',
            '/images/product/2/more.jpg',
        ],
        'slug_page' => '/domain-name-list/new-domains/',
        'sku' => 'DB-DOM-GLB-02',
        'mpn' => 'DB-DOM-GLB-02-ID',
        'related' => [4],
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'new',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Real-time WHOIS lookups',
            'Historical WHOIS records',
            'Bulk domain analysis',
            'Domain ownership identification',
            'Domain expiry monitoring'
        ],
        'api_details' => [
            'documentation' => 'https://www.whoisextractor.com/support/api-documents/',
            'request_limit' => '500 requests/month',
            'support' => 'Email and chat support'
        ],
        'delivery_method' => 'digital download',
        'return_policy' => 'https://www.whoisextractor.com/tos/refund-and-cancellation-policy/',
        'terms_of_service' => 'https://www.whoisextractor.com/tos/terms-and-conditions/'
    ],
    [
        'id' => 3,
        'title' => 'Daily WHOIS Database Downloads - Fresh, Accurate Data',
        'description_short' => 'Need reliable and up-to-date WHOIS data? Our Daily Full WHOIS Database provides the solution. Get access to the latest domain registration information and make informed decisions for your business.',
        'description_long' => 'Our Daily Full WHOIS Database provides comprehensive and up-to-date WHOIS records for millions of domains across the globe. This database is updated every day, ensuring you have access to the most current information available. Use this data for market research, lead generation, competitor analysis, and more.',
        'images' => [
            '/images/product/3/whois-database-download.jpg',
            '/images/product/3/more.jpg',
        ],
        'slug_page' => '/whois-database/worldwide-whois/',
        'sku' => 'DB-WHOIS-GLB-03',
        'mpn' => 'DB-WHOIS-GLB-03-ID',
        'related' => [1, 4],
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'new',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Real-time WHOIS lookups',
            'Historical WHOIS records',
            'Bulk domain analysis',
            'Domain ownership identification',
            'Domain expiry monitoring'
        ],
        'api_details' => [
            'documentation' => 'https://www.whoisextractor.com/support/api-documents/',
            'request_limit' => '500 requests/month',
            'support' => 'Email and chat support'
        ],
        'delivery_method' => 'digital download',
        'return_policy' => 'https://www.whoisextractor.com/tos/refund-and-cancellation-policy/',
        'terms_of_service' => 'https://www.whoisextractor.com/tos/terms-and-conditions/'
    ],
    [
        'id' => 4,
        'title' => 'All Registered Domains (260M+) - Updated Daily',
        'description_short' => 'Get an updated list of all registered domains daily.',
        'description_long' => 'Our All Registered Domains List provides a comprehensive database of over 260 million active domain names across various TLDs. This list is updated daily, ensuring you have the most current information available. Use this data for domain research, market analysis, and identifying potential new competitors or trends. Please note that this list does not include WHOIS data.',
        'images' => [
            '/images/product/4/all-registered-domain-lists.jpg',
            '/images/product/4/more.jpg',
        ],
        'slug_page' => '/domain-name-list/all-domains/',
        'sku' => 'DB-DOM-GLB-04',
        'mpn' => 'DB-DOM-GLB-04-ID',
        'related' => [2],
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'new',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Real-time WHOIS lookups',
            'Historical WHOIS records',
            'Bulk domain analysis',
            'Domain ownership identification',
            'Domain expiry monitoring'
        ],
        'api_details' => [
            'documentation' => 'https://www.whoisextractor.com/support/api-documents/',
            'request_limit' => '500 requests/month',
            'support' => 'Email and chat support'
        ],
        'delivery_method' => 'digital download',
        'return_policy' => 'https://www.whoisextractor.com/tos/refund-and-cancellation-policy/',
        'terms_of_service' => 'https://www.whoisextractor.com/tos/terms-and-conditions/'
    ],
    [
        'id' => 5,
        'title' => '320k+ Website Details - Comprehensive Data',
        'description_short' => 'Access a massive database of website details, including emails and tech stacks.',
        'description_long' => 'Our Website Details Database provides comprehensive information on over 320,000 websites, including email addresses, technology stacks, contact details, and more. This database is updated daily, ensuring you have access to the most current information available. Use this data for lead generation, market research, competitor analysis, and a variety of other business applications.',
        'images' => [
            '/images/product/5/website-details-all-domains.jpg',
            '/images/product/5/more.jpg',
        ],
        'slug_page' => '/website-database/historical-website-data/',
        'sku' => 'DB-WEB-GLB-05',
        'mpn' => 'DB-WEB-GLB-05-ID',
        'related' => [1, 3],
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'new',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Real-time WHOIS lookups',
            'Historical WHOIS records',
            'Bulk domain analysis',
            'Domain ownership identification',
            'Domain expiry monitoring'
        ],
        'api_details' => [
            'documentation' => 'https://www.whoisextractor.com/support/api-documents/',
            'request_limit' => '500 requests/month',
            'support' => 'Email and chat support'
        ],
        'delivery_method' => 'digital download',
        'return_policy' => 'https://www.whoisextractor.com/tos/refund-and-cancellation-policy/',
        'terms_of_service' => 'https://www.whoisextractor.com/tos/terms-and-conditions/'
    ],
    [
        'id' => 6,
        'title' => 'Test Drive Our WHOIS Database - 7 Days Trial',
        'description_short' => 'Experience the power of our WHOIS database with a 7-day trial.',
        'description_long' => 'Try our Daily Full WHOIS Database for free and see how it can benefit your business. Our trial gives you full access to our comprehensive WHOIS records for 7 days, allowing you to explore the data, test our tools, and experience the value we offer. Sign up for your trial today!',
        'images' => [
            '/images/product/3/whois-database-download.jpg',
            '/images/product/3/more.jpg',
        ],
        'slug_page' => '/domain-backordering/',
        'sku' => 'DB-WHOIS-GLB-06',
        'mpn' => 'DB-WHOIS-GLB-06-ID',
        'related' => [1, 3],
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'NewCondition',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Real-time WHOIS lookups',
            'Historical WHOIS records',
            'Bulk domain analysis',
            'Domain ownership identification',
            'Domain expiry monitoring'
        ],
        'api_details' => [
            'documentation' => 'https://www.whoisextractor.com/support/api-documents/',
            'request_limit' => '500 requests/month',
            'support' => 'Email and chat support'
        ],
        'delivery_method' => 'digital download',
        'return_policy' => 'https://www.whoisextractor.com/tos/refund-and-cancellation-policy/',
        'terms_of_service' => 'https://www.whoisextractor.com/tos/terms-and-conditions/'
    ],
    [
        'id' => 7,
        'title' => 'US WHOIS Database: Daily Updates',
        'description_short' => 'Get daily updates on US domain ownership and registration details.',
        'description_long' => 'Stay ahead of the curve with our Daily US WHOIS Database. Get access to the latest domain registration information, including owner details, contact information, and historical records. Our data is accurate, reliable, and updated every day, giving you the insights you need to make informed decisions.',
        'images' => [
            '/images/product/7/us-whois-database.jpg',
            '/images/product/7/more.jpg',
        ],
        'slug_page' => '/whois-database/whois-by-country/us-whois/',
        'sku' => 'DB-WHOIS-US-07',
        'mpn' => 'DB-WHOIS-US-07-ID',
        'related' => [1, 3],
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'new',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Real-time WHOIS lookups',
            'Historical WHOIS records',
            'Bulk domain analysis',
            'Domain ownership identification',
            'Domain expiry monitoring'
        ],
        'api_details' => [
            'documentation' => 'https://www.whoisextractor.com/support/api-documents/',
            'request_limit' => '500 requests/month',
            'support' => 'Email and chat support'
        ],
        'delivery_method' => 'digital download',
        'return_policy' => 'https://www.whoisextractor.com/tos/refund-and-cancellation-policy/',
        'terms_of_service' => 'https://www.whoisextractor.com/tos/terms-and-conditions/'
    ],
    [
        'id' => 8,
        'title' => 'Australia - Whois Database',
        'description_short' => 'Access the most up-to-date and comprehensive WHOIS data for Australian domains.',
        'description_long' => 'Our Australia WHOIS Database provides comprehensive and accurate WHOIS records for millions of domains registered in Australia. This database is updated daily, ensuring you have access to the freshest data available. Use this data for lead generation, market research, competitor analysis, and more.',
        'images' => [
            '/images/product/8/australia-whois-database.jpg',
            '/images/product/8/more.jpg',
        ],
        'slug_page' => '/domain-backordering/',
        'sku' => 'DB-WHOIS-AU-08',
        'mpn' => 'DB-WHOIS-AU-08-ID',
        'related' => [1, 3],
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'new',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Real-time WHOIS lookups',
            'Historical WHOIS records',
            'Bulk domain analysis',
            'Domain ownership identification',
            'Domain expiry monitoring'
        ],
        'api_details' => [
            'documentation' => 'https://www.whoisextractor.com/support/api-documents/',
            'request_limit' => '500 requests/month',
            'support' => 'Email and chat support'
        ],
        'delivery_method' => 'digital download',
        'return_policy' => 'https://www.whoisextractor.com/tos/refund-and-cancellation-policy/',
        'terms_of_service' => 'https://www.whoisextractor.com/tos/terms-and-conditions/'
    ],
    [
        'id' => 9,
        'title' => 'Historical Old WHOIS Database - Access Past Domain Data',
        'description_short' => 'Need to research past domain ownership? Download our historical WHOIS database.',
        'description_long' => 'Step back in time with our Old WHOIS Database. This comprehensive archive allows you to access domain registration data from previous years, going all the way back to 2011. Select the year and month you\'re interested in and download the corresponding WHOIS records. This data is invaluable for historical domain research, investigating ownership changes, and understanding domain trends over time.',
        'images' => [
            '/images/product/9/old-whois-database.jpg',
            '/images/product/9/more.jpg',
        ],
        'slug_page' => '/whois-database/historical-whois/',
        'sku' => 'DB-WHOIS-OLD-09',
        'mpn' => 'DB-WHOIS-OLD-09-ID',
        'related' => [1, 3],
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'new',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Real-time WHOIS lookups',
            'Historical WHOIS records',
            'Bulk domain analysis',
            'Domain ownership identification',
            'Domain expiry monitoring'
        ],
        'api_details' => [
            'documentation' => 'https://www.whoisextractor.com/support/api-documents/',
            'request_limit' => '500 requests/month',
            'support' => 'Email and chat support'
        ],
        'delivery_method' => 'digital download',
        'return_policy' => 'https://www.whoisextractor.com/tos/refund-and-cancellation-policy/',
        'terms_of_service' => 'https://www.whoisextractor.com/tos/terms-and-conditions/'
    ],
    [
        'id' => 10,
        'title' => 'Daily Registered Domains with API - No WHOIS',
        'description_short' => 'Access a daily updated list of new domain names via our API.',
        'description_long' => 'Streamline your domain research with our Daily Registered Domains API. This API provides access to a freshly updated list of newly registered domain names every 24 hours. Integrate it with your application to automate downloads and seamlessly incorporate the data into your workflows. While it doesn\'t include WHOIS data, it\'s a valuable resource for domain analysis, market research, and identifying potential new competitors.',
        'images' => [
            '/images/product/2/newly-registered-domains-free.jpg',
            '/images/product/2/more.jpg',
        ],
        'slug_page' => '/domain-backordering/',
        'sku' => 'DB-DOM-GLB-10',
        'mpn' => 'DB-DOM-GLB-10-ID',
        'related' => [1, 3],
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'new',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Real-time WHOIS lookups',
            'Historical WHOIS records',
            'Bulk domain analysis',
            'Domain ownership identification',
            'Domain expiry monitoring'
        ],
        'api_details' => [
            'documentation' => 'https://www.whoisextractor.com/support/api-documents/',
            'request_limit' => '500 requests/month',
            'support' => 'Email and chat support'
        ],
        'delivery_method' => 'digital download',
        'return_policy' => 'https://www.whoisextractor.com/tos/refund-and-cancellation-policy/',
        'terms_of_service' => 'https://www.whoisextractor.com/tos/terms-and-conditions/'
    ],
    [
        'id' => 11,
        'title' => 'Website Detailed Daily Data - Accurate & Fresh',
        'description_short' => 'Get daily updates on website data, including emails and tech stacks.',
        'description_long' => 'Our Website Detailed Daily Data provides a wealth of information on over 320,000 websites. This database is updated daily, ensuring you have access to the freshest data available.  We scrape real data from live websites, including email addresses, technology stacks, contact details, social media links, and more. Use this data for lead generation, market research, competitor analysis, and a variety of other business applications.',
        'images' => [
            '/images/product/11/website-detailed-daily-data.jpg',
            '/images/product/11/more.jpg',
        ],
        'slug_page' => '/website-database/daily-website-data/',
        'sku' => 'DB-WEB-GLB-11',
        'mpn' => 'DB-WEB-GLB-11-ID',
        'related' => [1, 3],
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'new',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Real-time WHOIS lookups',
            'Historical WHOIS records',
            'Bulk domain analysis',
            'Domain ownership identification',
            'Domain expiry monitoring'
        ],
        'api_details' => [
            'documentation' => 'https://www.whoisextractor.com/support/api-documents/',
            'request_limit' => '500 requests/month',
            'support' => 'Email and chat support'
        ],
        'delivery_method' => 'digital download',
        'return_policy' => 'https://www.whoisextractor.com/tos/refund-and-cancellation-policy/',
        'terms_of_service' => 'https://www.whoisextractor.com/tos/terms-and-conditions/'
    ],
    [
        'id' => 12,
        'title' => 'Website Detailed Daily Data - Trial 7 Days',
        'description_long' => 'Domain Backordering long description...',
        'description_short' => 'Domain Backordering short description',
        'images' => [
            '/images/product/11/website-detailed-daily-data.jpg',
            '/images/product/11/more.jpg',
        ],
        'slug_page' => '/domain-backordering/',
        'sku' => 'DB-WEB-GLB-12',
        'mpn' => 'DB-WEB-GLB-12-ID',
        'related' => [1, 3],
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'new',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Real-time WHOIS lookups',
            'Historical WHOIS records',
            'Bulk domain analysis',
            'Domain ownership identification',
            'Domain expiry monitoring'
        ],
        'api_details' => [
            'documentation' => 'https://www.whoisextractor.com/support/api-documents/',
            'request_limit' => '500 requests/month',
            'support' => 'Email and chat support'
        ],
        'delivery_method' => 'digital download',
        'return_policy' => 'https://www.whoisextractor.com/tos/refund-and-cancellation-policy/',
        'terms_of_service' => 'https://www.whoisextractor.com/tos/terms-and-conditions/'
    ],
    [
        'id' => 13,
        'title' => 'India Data - 150 Crore Records - Comprehensive Coverage',
        'description_short' => 'Access the largest and most comprehensive Pan India Database with 150 crore records.',
        'description_long' => 'Our Pan India Database is the largest and most comprehensive collection of data on individuals and businesses across India. With 150 crore records, this database provides unparalleled coverage for your research, marketing, and analytical needs.  The data includes demographics, contact information, and other valuable insights.  Empower your business decisions with our Pan India Database.',
        'images' => [
            '/images/product/13/pan-india-database-150-crore-data.jpg',
            '/images/product/13/more.jpg',
        ],
        'slug_page' => '/domain-backordering/',
        'sku' => 'DB-ALL-IN-13',
        'mpn' => 'DB-ALL-IN-13-ID',
        'related' => [1, 3],
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'new',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Real-time WHOIS lookups',
            'Historical WHOIS records',
            'Bulk domain analysis',
            'Domain ownership identification',
            'Domain expiry monitoring'
        ],
        'api_details' => [
            'documentation' => 'https://www.whoisextractor.com/support/api-documents/',
            'request_limit' => '500 requests/month',
            'support' => 'Email and chat support'
        ],
        'delivery_method' => 'digital download',
        'return_policy' => 'https://www.whoisextractor.com/tos/refund-and-cancellation-policy/',
        'terms_of_service' => 'https://www.whoisextractor.com/tos/terms-and-conditions/'
    ],
];