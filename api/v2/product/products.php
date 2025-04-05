<?php 

//api/v2/product/products.php

// Custom Products array (populate with your actual data)

$Products = [
    [
        'id' => 1,
        'title' => 'India WHOIS Database: Daily Updates',
        'description_short' => 'Daily WHOIS data for Indian domains with accurate details.',
        'description_long' => 'Get daily updates of WHOIS data for Indian domains, including registrant details, nameservers, and expiration dates.',
        'images' => [
            '/images/product/1/india-whois-database.jpg',
            '/images/product/1/more.jpg',
        ],
        'featuredImage' => '/images/product/featuredimage.webp',
        'slug_page' => '/whois-database/whois-by-country/india-whois/',
        'sku' => 'DB-WHOIS-IN-01',
        'mpn' => 'DB-WHOIS-IN-01-ID',
        'related' => [2, 3, 4],
        'col' => 'col-lg-4',
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
            'Daily updates for .IN domains',
            'Accurate registrant details',
            'Nameserver and DNS data',
            'Domain registration and expiry dates',
            'Bulk data export options'
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
        'description_short' => 'Daily updates of newly registered and removed domains.',
        'description_long' => 'Get daily updates of newly registered and removed domains across all zones, including classic, gTLD, and ccTLD.',
        'images' => [
            '/images/product/2/daily-registered-domains.jpg',
            '/images/product/2/more.jpg',
        ],
        'featuredImage' => '/images/product/featuredimage.webp',
        'slug_page' => '/domain-name-list/daily-registered-domains/',
        'sku' => 'DB-DOM-DAILY-02',
        'mpn' => 'DB-DOM-DAILY-02-ID',
        'related' => [4],
        'col' => 'col-lg-6',
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'new',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Daily updates of registered and removed domains',
            'Covers all zones: classic, gTLD, ccTLD',
            'Search and monitor domain names',
            'Free access with no API or WHOIS data',
            'CSV format for easy download'
        ],
        'api_details' => [
            'support' => 'Not available for free plans'
        ],
        'delivery_method' => 'digital download',
        'return_policy' => 'https://www.whoisextractor.com/tos/refund-and-cancellation-policy/',
        'terms_of_service' => 'https://www.whoisextractor.com/tos/terms-and-conditions/'
    ],
    [
        'id' => 3,
        'title' => 'Daily WHOIS Database Downloads - Fresh, Accurate Data',
        'description_short' => 'Global WHOIS data updated daily for 1500+ TLDs.',
        'description_long' => 'Comprehensive WHOIS data for domains worldwide, updated daily with ownership and technical details.',
        'images' => [
            '/images/product/3/whois-database-download.jpg',
            '/images/product/3/more.jpg',
        ],
        'featuredImage' => '/images/product/featuredimage.webp',
        'slug_page' => '/whois-database/worldwide-whois/',
        'sku' => 'DB-WHOIS-GLB-03',
        'mpn' => 'DB-WHOIS-GLB-03-ID',
        'related' => [1, 4],
        'col' => 'col-lg-6',
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'new',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Daily updates for 1500+ TLDs',
            'Complete ownership details',
            'Technical contact and DNS info',
            'Exportable in CSV format',
            'Free API access included'
        ],
        'api_details' => [
            'support' => 'Email and chat support',
            'request_limit' => 'Unlimited for paid plans'
        ],
        'delivery_method' => 'digital download',
        'return_policy' => 'https://www.whoisextractor.com/tos/refund-and-cancellation-policy/',
        'terms_of_service' => 'https://www.whoisextractor.com/tos/terms-and-conditions/'
    ],
    [
        'id' => 4,
        'title' => 'Monthly New Domain Lists',
        'description_short' => 'Comprehensive monthly list of all registered domains.',
        'description_long' => 'Access the most complete list of all registered domains, updated monthly, including domain names, zones, and administrator contact details.',
        'images' => [
            '/images/product/4/monthly-domain-lists.jpg',
            '/images/product/4/more.jpg',
        ],
        'featuredImage' => '/images/product/featuredimage.webp',
        'slug_page' => '/domain-name-list/all-domains/',
        'sku' => 'DB-DOM-GLB-04',
        'mpn' => 'DB-DOM-GLB-04-ID',
        'related' => [2],
        'col' => 'col-lg-6',
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'new',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Complete list of all registered domains',
            'Updated monthly with new and removed domains',
            'Covers all zones: classic, gTLD, ccTLD',
            'Includes administrator email and phone contacts',
            'API access for automated updates',
            'CSV format for easy integration'
        ],
        'api_details' => [
            'support' => 'Email and chat support',
            'request_limit' => 'Unlimited for paid plans'
        ],
        'delivery_method' => 'digital download',
        'return_policy' => 'https://www.whoisextractor.com/tos/refund-and-cancellation-policy/',
        'terms_of_service' => 'https://www.whoisextractor.com/tos/terms-and-conditions/'
    ],
    [
        'id' => 5,
        'title' => 'Website Technology Database',
        'description_short' => 'Daily website data in CSV format with domain and email details.',
        'description_long' => 'Access daily website data including domain names and email addresses extracted from website pages. [Not WHOIS].',
        'images' => [
            '/images/product/5/website-technology-database.jpg',
            '/images/product/5/more.jpg',
        ],
        'featuredImage' => '/images/product/featuredimage.webp',
        'slug_page' => '/website-database/technology-data/',
        'sku' => 'DB-WEB-TECH-05',
        'mpn' => 'DB-WEB-TECH-05-ID',
        'related' => [11],
        'col' => 'col-lg-6',
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'new',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Daily updates in CSV format',
            'Domain names and email addresses',
            'Extracted from website pages',
            'Upcoming: A records, NS, IP, and country data',
            'Free API access included'
        ],
        'api_details' => [
            'support' => 'Email and chat support',
            'request_limit' => 'Unlimited for paid plans'
        ],
        'delivery_method' => 'digital download',
        'return_policy' => 'https://www.whoisextractor.com/tos/refund-and-cancellation-policy/',
        'terms_of_service' => 'https://www.whoisextractor.com/tos/terms-and-conditions/'
    ],
    [
        'id' => 6,
        'title' => 'Test Drive Our WHOIS Database - 7 Days Trial',
        'description_short' => 'Experience the power of our WHOIS database with a 7-day trial.',
        'description_long' => 'Try our WHOIS database for 7 days and explore its features and benefits for your business.',
        'images' => [
            '/images/product/3/whois-database-download.jpg',
            '/images/product/3/more.jpg',
        ],
        'featuredImage' => '/images/product/featuredimage.webp',
        'slug_page' => '/domain-backordering/',
        'sku' => 'DB-WHOIS-GLB-06',
        'mpn' => 'DB-WHOIS-GLB-06-ID',
        'related' => [1, 3],
        'col' => 'col-lg-6',
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'NewCondition',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Full access to WHOIS data',
            'Daily updates for 7 days',
            'Explore domain ownership details',
            'Test bulk domain analysis tools',
            'No commitment required'
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
        'title' => 'USA WHOIS Database',
        'description_short' => 'Daily WHOIS data for US domains with detailed records.',
        'description_long' => 'Access daily-updated WHOIS data for US domains, including registrant details and DNS information.',
        'images' => [
            '/images/product/7/us-whois-database.jpg',
            '/images/product/7/more.jpg',
        ],
        'featuredImage' => '/images/product/featuredimage.webp',
        'slug_page' => '/whois-database/whois-by-country/us-whois/',
        'sku' => 'DB-WHOIS-US-04',
        'mpn' => 'DB-WHOIS-US-07-ID',
        'related' => [1, 3],
        'col' => 'col-lg-4',
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'new',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Daily updates for US domains',
            'Registrant and DNS details',
            'Domain registration and expiry dates',
            'Bulk data export options',
            'API access available'
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
        'title' => 'Global WHOIS Database',
        'description_short' => 'Comprehensive WHOIS data for domains worldwide.',
        'description_long' => 'Get access to WHOIS data for domains across the globe, updated daily with ownership and DNS details.',
        'images' => [
            '/images/product/8/more.jpg',
        ],
        'featuredImage' => '/images/product/featuredimage.webp',
        'slug_page' => '/domain-backordering/',
        'sku' => 'DB-WHOIS-AU-08',
        'mpn' => 'DB-WHOIS-AU-08-ID',
        'related' => [1, 3],
        'col' => 'col-lg-6',
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'new',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Daily updates for all domains',
            'Registrant and DNS details included',
            'Exportable in CSV format',
            'Free API access included'
        ],
        'api_details' => [
            'support' => 'Email and chat support',
            'request_limit' => 'Unlimited for paid plans'
        ],
        'delivery_method' => 'digital download',
        'return_policy' => 'https://www.whoisextractor.com/tos/refund-and-cancellation-policy/',
        'terms_of_service' => 'https://www.whoisextractor.com/tos/terms-and-conditions/'
    ],
    [
        'id' => 9,
        'title' => 'Global WHOIS Database - Historical Data',
        'description_short' => 'Access historical WHOIS data for domains worldwide.',
        'description_long' => 'Order the global WHOIS database from past months, including registrant details and DNS data.',
        'images' => [
            '/images/product/9/historical-whois-database.jpg',
            '/images/product/9/more.jpg',
        ],
        'featuredImage' => '/images/product/featuredimage.webp',
        'slug_page' => '/whois-database/historical-whois-data/',
        'sku' => 'DB-WHOIS-HIST-09',
        'mpn' => 'DB-WHOIS-HIST-09-ID',
        'related' => [3, 8],
        'col' => 'col-lg-6',
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'NewCondition',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Access WHOIS data from past months',
            'Covers 1500+ TLDs globally',
            'Registrant and DNS details included',
            'Exportable in CSV format',
            'Free API access included'
        ],
        'api_details' => [
            'support' => 'Email and chat support',
            'request_limit' => 'Unlimited for paid plans'
        ],
        'delivery_method' => 'digital download',
        'return_policy' => 'https://www.whoisextractor.com/tos/refund-and-cancellation-policy/',
        'terms_of_service' => 'https://www.whoisextractor.com/tos/terms-and-conditions/'
    ],
    [
        'id' => 10,
        'title' => 'Custom WHOIS Solutions',
        'description_short' => 'Tailored WHOIS data solutions for your business needs.',
        'description_long' => 'Get custom WHOIS data solutions designed to meet your specific business requirements.',
        'images' => [
            '/images/product/2/newly-registered-domains-free.jpg',
            '/images/product/2/more.jpg',
        ],
        'featuredImage' => '/images/product/featuredimage.webp',
        'slug_page' => '/domain-backordering/',
        'sku' => 'DB-DOM-GLB-10',
        'mpn' => 'DB-DOM-GLB-10-ID',
        'related' => [1, 3],
        'col' => 'col-lg-6',
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'new',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Custom data extraction',
            'Flexible API integration',
            'Dedicated support team',
            'Scalable for enterprise needs',
            'Secure and reliable'
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
        'title' => 'Website Contact Database',
        'description_short' => 'Daily website contact data in CSV format.',
        'description_long' => 'Get daily website contact data including domain names and email addresses extracted from website pages. [Not WHOIS].',
        'images' => [
            '/images/product/11/website-contact-database.jpg',
            '/images/product/11/more.jpg',
        ],
        'featuredImage' => '/images/product/featuredimage.webp',
        'slug_page' => '/website-database/contact-data/',
        'sku' => 'DB-WEB-CONTACT-11',
        'mpn' => 'DB-WEB-CONTACT-11-ID',
        'related' => [5],
        'col' => 'col-lg-6',
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'new',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Daily updates in CSV format',
            'Domain names and email addresses',
            'Extracted from website pages',
            'Upcoming: A records, NS, IP, and country data',
            'Free API access included'
        ],
        'api_details' => [
            'support' => 'Email and chat support',
            'request_limit' => 'Unlimited for paid plans'
        ],
        'delivery_method' => 'digital download',
        'return_policy' => 'https://www.whoisextractor.com/tos/refund-and-cancellation-policy/',
        'terms_of_service' => 'https://www.whoisextractor.com/tos/terms-and-conditions/'
    ],
    [
        'id' => 12,
        'title' => 'Domain Registration Trends',
        'description_short' => 'Analyze domain registration trends across TLDs.',
        'description_long' => 'Track and analyze domain registration trends to identify market opportunities and patterns.',
        'images' => [
            '/images/product/11/website-detailed-daily-data.jpg',
            '/images/product/11/more.jpg',
        ],
        'featuredImage' => '/images/product/featuredimage.webp',
        'slug_page' => '/domain-backordering/',
        'sku' => 'DB-WEB-GLB-12',
        'mpn' => 'DB-WEB-GLB-12-ID',
        'related' => [1, 3],
        'col' => 'col-lg-6',
        'google_product_category' => 'Software > Business & Productivity Software',
        'condition' => 'new',
        'availability' => 'in stock',
        'downloadable' => 'yes',
        'brand' => 'Whoisextractor',
        'identifier_exists' => 'yes',
        'product_type' => 'Database',
        'features' => [
            'Daily registration trends',
            'Insights across TLDs',
            'Exportable trend reports',
            'Ideal for market analysis',
            'Customizable filters'
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
        'featuredImage' => '/images/product/featuredimage.webp',
        'slug_page' => '/domain-backordering/',
        'sku' => 'DB-ALL-IN-13',
        'mpn' => 'DB-ALL-IN-13-ID',
        'related' => [1, 3],
        'col' => 'col-lg-6',
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