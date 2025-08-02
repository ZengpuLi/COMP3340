<?php
/**
 * SEO and Metadata Helper Functions
 * Used Car Purchase Website - SEO Optimization System
 */

/**
 * Generate comprehensive SEO meta tags for a page
 */
function generateSEOMetaTags($page_config) {
    $site_name = "AutoDeals - Used Car Purchase Website";
    $site_url = "https://autodeals.com"; // Update with actual domain
    $default_image = "/images/logo-social.png"; // Social media logo
    
    // Ensure required fields have defaults
    $title = $page_config['title'] ?? 'Quality Used Cars for Sale';
    $description = $page_config['description'] ?? 'Find your perfect used car at AutoDeals. Browse our extensive inventory of quality pre-owned vehicles with transparent pricing and excellent customer service.';
    $keywords = $page_config['keywords'] ?? 'used cars, car sales, buy used car online, pre-owned vehicles, car dealership, auto sales, car financing';
    $canonical = $page_config['canonical'] ?? '';
    $og_image = $page_config['og_image'] ?? $default_image;
    $og_type = $page_config['og_type'] ?? 'website';
    $robots = $page_config['robots'] ?? 'index, follow';
    
    // Generate full title with site name
    $full_title = $title . ' | ' . $site_name;
    if (strlen($full_title) > 60) {
        $full_title = $title; // Use short title if combined is too long
    }
    
    $meta_html = '';
    
    // Basic Meta Tags
    $meta_html .= '<title>' . htmlspecialchars($full_title, ENT_QUOTES, 'UTF-8') . '</title>' . "\n";
    $meta_html .= '<meta name="description" content="' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    $meta_html .= '<meta name="keywords" content="' . htmlspecialchars($keywords, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    $meta_html .= '<meta name="robots" content="' . htmlspecialchars($robots, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    $meta_html .= '<meta name="author" content="AutoDeals Team">' . "\n";
    $meta_html .= '<meta name="viewport" content="width=device-width, initial-scale=1.0">' . "\n";
    
    // Canonical URL
    if ($canonical) {
        $meta_html .= '<link rel="canonical" href="' . htmlspecialchars($site_url . $canonical, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    }
    
    // Open Graph Tags for Social Media
    $meta_html .= '<meta property="og:title" content="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    $meta_html .= '<meta property="og:description" content="' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    $meta_html .= '<meta property="og:image" content="' . htmlspecialchars($site_url . $og_image, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    $meta_html .= '<meta property="og:url" content="' . htmlspecialchars($site_url . $canonical, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    $meta_html .= '<meta property="og:type" content="' . htmlspecialchars($og_type, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    $meta_html .= '<meta property="og:site_name" content="' . htmlspecialchars($site_name, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    
    // Twitter Card Tags
    $meta_html .= '<meta name="twitter:card" content="summary_large_image">' . "\n";
    $meta_html .= '<meta name="twitter:title" content="' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    $meta_html .= '<meta name="twitter:description" content="' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    $meta_html .= '<meta name="twitter:image" content="' . htmlspecialchars($site_url . $og_image, ENT_QUOTES, 'UTF-8') . '">' . "\n";
    
    // Additional SEO Tags
    $meta_html .= '<meta name="theme-color" content="#3498db">' . "\n";
    $meta_html .= '<meta name="msapplication-TileColor" content="#3498db">' . "\n";
    
    // Favicon Links
    $meta_html .= '<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">' . "\n";
    $meta_html .= '<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">' . "\n";
    $meta_html .= '<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">' . "\n";
    $meta_html .= '<link rel="manifest" href="/site.webmanifest">' . "\n";
    
    return $meta_html;
}

/**
 * SEO Configuration for each page
 */
function getSEOConfig($page_name, $additional_data = []) {
    $configs = [
        'index.html' => [
            'title' => 'Quality Used Cars for Sale - Best Deals on Pre-Owned Vehicles',
            'description' => 'Find your perfect used car at AutoDeals. Browse our extensive inventory of quality pre-owned vehicles with transparent pricing, financing options, and excellent customer service.',
            'keywords' => 'used cars, car sales, buy used car online, pre-owned vehicles, car dealership, auto sales, car financing, best used car deals',
            'canonical' => '/',
            'og_type' => 'website'
        ],
        
        'about.html' => [
            'title' => 'About AutoDeals - Your Trusted Used Car Dealership',
            'description' => 'Learn about AutoDeals, your trusted partner in finding quality used cars. With over 15 years of experience, we provide transparent pricing and exceptional customer service.',
            'keywords' => 'about autodeals, used car dealership, car sales experience, trusted car dealer, automotive history',
            'canonical' => '/about.html',
            'og_type' => 'website'
        ],
        
        'cars.php' => [
            'title' => 'Browse Used Cars Inventory - Quality Pre-Owned Vehicles',
            'description' => 'Browse our complete inventory of quality used cars. Filter by make, model, year, and price to find the perfect vehicle for your needs and budget.',
            'keywords' => 'used cars inventory, browse cars, car listings, pre-owned vehicles, car search, automotive inventory',
            'canonical' => '/cars.php',
            'og_type' => 'website'
        ],
        
        'locations.html' => [
            'title' => 'AutoDeals Locations - Find a Dealership Near You',
            'description' => 'Visit one of our convenient AutoDeals locations across the country. Find store hours, contact information, and directions to our car dealerships.',
            'keywords' => 'car dealership locations, autodeals stores, used car lots, dealership hours, car dealer near me',
            'canonical' => '/locations.html',
            'og_type' => 'website'
        ],
        
        'market-trends.html' => [
            'title' => 'Used Car Market Trends & Pricing Analysis',
            'description' => 'Stay informed with the latest used car market trends, pricing analysis, and automotive industry insights to make smart buying decisions.',
            'keywords' => 'car market trends, used car prices, automotive analysis, car value trends, market insights, pricing data',
            'canonical' => '/market-trends.html',
            'og_type' => 'article'
        ],
        
        'calculator.php' => [
            'title' => 'Car Loan Payment Calculator - Estimate Monthly Payments',
            'description' => 'Calculate your estimated monthly car loan payments with our easy-to-use calculator. Compare financing options and plan your car purchase budget.',
            'keywords' => 'car loan calculator, auto loan payment, monthly payment calculator, car financing, loan estimation, payment planning',
            'canonical' => '/calculator.php',
            'og_type' => 'website'
        ],
        
        'inquiry.php' => [
            'title' => 'Contact Us About a Vehicle - Car Inquiry Form',
            'description' => 'Interested in one of our vehicles? Send us an inquiry and our team will get back to you with all the details you need.',
            'keywords' => 'car inquiry, contact dealer, vehicle information, test drive request, car questions',
            'canonical' => '/inquiry.php',
            'og_type' => 'website'
        ],
        
        'contact.html' => [
            'title' => 'Contact AutoDeals - Get in Touch with Our Team',
            'description' => 'Contact AutoDeals for questions about our vehicles, services, or to schedule a visit. We\'re here to help you find your perfect used car.',
            'keywords' => 'contact autodeals, customer service, dealership contact, car dealer phone, get in touch',
            'canonical' => '/contact.html',
            'og_type' => 'website'
        ],
        
        'help.php' => [
            'title' => 'Help & Support - AutoDeals Customer Service',
            'description' => 'Find answers to frequently asked questions about buying used cars, financing, warranties, and our services at AutoDeals.',
            'keywords' => 'car buying help, used car FAQ, customer support, car buying guide, automotive help',
            'canonical' => '/help.php',
            'og_type' => 'website'
        ],
        
        'privacy.html' => [
            'title' => 'Privacy Policy - AutoDeals Data Protection',
            'description' => 'Read our privacy policy to understand how AutoDeals protects your personal information and data when you use our website and services.',
            'keywords' => 'privacy policy, data protection, customer privacy, information security, data handling',
            'canonical' => '/privacy.html',
            'og_type' => 'website'
        ],
        
        'login.php' => [
            'title' => 'Login to Your AutoDeals Account',
            'description' => 'Sign in to your AutoDeals account to access your favorites, purchase history, and personalized car recommendations.',
            'keywords' => 'login, user account, sign in, customer portal, account access',
            'canonical' => '/login.php',
            'og_type' => 'website',
            'robots' => 'noindex, nofollow'
        ],
        
        'register.php' => [
            'title' => 'Create Your AutoDeals Account - Join Today',
            'description' => 'Create a free AutoDeals account to save your favorite cars, track your purchase history, and get personalized recommendations.',
            'keywords' => 'create account, register, sign up, user registration, join autodeals',
            'canonical' => '/register.php',
            'og_type' => 'website'
        ],
        
        'favorites.php' => [
            'title' => 'Your Favorite Cars - Saved Vehicles',
            'description' => 'View and manage your saved favorite cars. Keep track of vehicles you\'re interested in and compare your options.',
            'keywords' => 'favorite cars, saved vehicles, car wishlist, bookmarked cars',
            'canonical' => '/favorites.php',
            'og_type' => 'website',
            'robots' => 'noindex, nofollow'
        ],
        
        'purchases.php' => [
            'title' => 'Your Purchase History - AutoDeals Account',
            'description' => 'View your vehicle purchase history and access important documents related to your AutoDeals transactions.',
            'keywords' => 'purchase history, car purchases, transaction history, account dashboard',
            'canonical' => '/purchases.php',
            'og_type' => 'website',
            'robots' => 'noindex, nofollow'
        ]
    ];
    
    // Get base config for the page
    $config = $configs[$page_name] ?? $configs['index.html'];
    
    // Merge with additional data (for dynamic content like specific cars)
    if (!empty($additional_data)) {
        $config = array_merge($config, $additional_data);
    }
    
    return $config;
}

/**
 * Generate JSON-LD structured data for SEO
 */
function generateStructuredData($type, $data = []) {
    $structured_data = [];
    
    switch ($type) {
        case 'organization':
            $structured_data = [
                "@context" => "https://schema.org",
                "@type" => "AutoDealer",
                "name" => "AutoDeals",
                "description" => "Quality used car dealership with transparent pricing and excellent customer service",
                "url" => "https://autodeals.com",
                "logo" => "https://autodeals.com/images/logo-social.png",
                "contactPoint" => [
                    "@type" => "ContactPoint",
                    "telephone" => "+1-555-123-4567",
                    "contactType" => "customer service",
                    "availableLanguage" => "English"
                ],
                "address" => [
                    "@type" => "PostalAddress",
                    "streetAddress" => "123 Auto Sales Drive",
                    "addressLocality" => "Cartown",
                    "addressRegion" => "CA",
                    "postalCode" => "12345",
                    "addressCountry" => "US"
                ],
                "openingHours" => [
                    "Mo-Sa 09:00-20:00",
                    "Su 10:00-18:00"
                ],
                "sameAs" => [
                    "https://www.facebook.com/autodeals",
                    "https://www.twitter.com/autodeals",
                    "https://www.instagram.com/autodeals"
                ]
            ];
            break;
            
        case 'vehicle':
            $structured_data = [
                "@context" => "https://schema.org",
                "@type" => "Vehicle",
                "name" => $data['name'] ?? '',
                "description" => $data['description'] ?? '',
                "brand" => [
                    "@type" => "Brand",
                    "name" => $data['make'] ?? ''
                ],
                "model" => $data['model'] ?? '',
                "vehicleModelDate" => $data['year'] ?? '',
                "mileageFromOdometer" => [
                    "@type" => "QuantitativeValue",
                    "value" => $data['mileage'] ?? '',
                    "unitCode" => "SMI"
                ],
                "offers" => [
                    "@type" => "Offer",
                    "price" => $data['price'] ?? '',
                    "priceCurrency" => "USD",
                    "availability" => "https://schema.org/InStock",
                    "seller" => [
                        "@type" => "AutoDealer",
                        "name" => "AutoDeals"
                    ]
                ]
            ];
            break;
            
        case 'breadcrumb':
            $structured_data = [
                "@context" => "https://schema.org",
                "@type" => "BreadcrumbList",
                "itemListElement" => $data['breadcrumbs'] ?? []
            ];
            break;
    }
    
    return '<script type="application/ld+json">' . json_encode($structured_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
}

/**
 * Generate breadcrumb structured data
 */
function generateBreadcrumbs($breadcrumbs) {
    $breadcrumb_items = [];
    $position = 1;
    
    foreach ($breadcrumbs as $name => $url) {
        $breadcrumb_items[] = [
            "@type" => "ListItem",
            "position" => $position,
            "name" => $name,
            "item" => "https://autodeals.com" . $url
        ];
        $position++;
    }
    
    return generateStructuredData('breadcrumb', ['breadcrumbs' => $breadcrumb_items]);
}

/**
 * Get optimized image alt text
 */
function getImageAlt($image_path, $context = '') {
    $filename = basename($image_path);
    
    // Generate descriptive alt text based on context and filename
    $alt_texts = [
        'logo' => 'AutoDeals Logo - Quality Used Car Dealership',
        'car-' => 'Used car for sale at AutoDeals',
        'placeholder' => 'Car image placeholder',
        'hero' => 'AutoDeals used car dealership showroom',
        'about' => 'AutoDeals team and facility',
        'contact' => 'AutoDeals contact and location information'
    ];
    
    foreach ($alt_texts as $key => $alt) {
        if (strpos($filename, $key) !== false) {
            return $alt . ($context ? ' - ' . $context : '');
        }
    }
    
    // Default alt text
    return 'AutoDeals - ' . ($context ?: 'Quality used cars and automotive services');
}

/**
 * Generate sitemap XML (basic version)
 */
function generateSitemap() {
    $base_url = "https://autodeals.com";
    $pages = [
        '' => ['priority' => '1.0', 'changefreq' => 'daily'],
        '/about.html' => ['priority' => '0.8', 'changefreq' => 'monthly'],
        '/cars.php' => ['priority' => '0.9', 'changefreq' => 'daily'],
        '/locations.html' => ['priority' => '0.7', 'changefreq' => 'monthly'],
        '/market-trends.html' => ['priority' => '0.6', 'changefreq' => 'weekly'],
        '/calculator.php' => ['priority' => '0.8', 'changefreq' => 'monthly'],
        '/inquiry.php' => ['priority' => '0.7', 'changefreq' => 'monthly'],
        '/contact.html' => ['priority' => '0.7', 'changefreq' => 'monthly'],
        '/help.php' => ['priority' => '0.6', 'changefreq' => 'monthly'],
        '/privacy.html' => ['priority' => '0.3', 'changefreq' => 'yearly']
    ];
    
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    foreach ($pages as $page => $data) {
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . $base_url . $page . '</loc>' . "\n";
        $xml .= '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
        $xml .= '    <changefreq>' . $data['changefreq'] . '</changefreq>' . "\n";
        $xml .= '    <priority>' . $data['priority'] . '</priority>' . "\n";
        $xml .= '  </url>' . "\n";
    }
    
    $xml .= '</urlset>';
    
    return $xml;
}

/**
 * Optimize image for web
 */
function optimizeImagePath($image_path, $context = '') {
    // Add webp support detection and appropriate alt text
    $alt = getImageAlt($image_path, $context);
    $webp_path = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $image_path);
    
    // Return optimized image HTML
    return [
        'src' => $image_path,
        'webp' => $webp_path,
        'alt' => $alt
    ];
}
?>