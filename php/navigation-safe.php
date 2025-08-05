<?php
/**
 * Navigation Generation (Safe Version)
 * Used Car Purchase Website
 */

/**
 * Generate navigation menu HTML
 * @param string $currentPage
 * @return string
 */
function generateNavigation($currentPage = '') {
    // Get current page filename if not provided
    if (empty($currentPage)) {
        $currentPage = basename($_SERVER['PHP_SELF']);
    }
    
    // Define navigation items
    $navItems = [
        ['url' => 'index.html', 'label' => 'Home', 'public' => true],
        ['url' => 'about.html', 'label' => 'About Us', 'public' => true],
        ['url' => 'cars.php', 'label' => 'Cars', 'public' => true],
        ['url' => 'locations.html', 'label' => 'Locations', 'public' => true],
        ['url' => 'market-trends.html', 'label' => 'Market Trends', 'public' => true],
        ['url' => 'calculator.php', 'label' => 'Loan Calculator', 'public' => true],
        ['url' => 'contact.html', 'label' => 'Contact Us', 'public' => true],
        ['url' => 'help.php', 'label' => 'Help', 'public' => true]
    ];
    
    // User-specific navigation items
    $userItems = [];
    $authItems = [];
    
    if (function_exists('isLoggedIn') && isLoggedIn()) {
        // Logged in user items
        $userItems = [
            ['url' => 'favorites.php', 'label' => 'Favorites'],
            ['url' => 'purchases.php', 'label' => 'Purchases'],
            ['url' => 'profile.php', 'label' => 'Profile']
        ];
        $authItems = [
            ['url' => 'logout.php', 'label' => 'Logout']
        ];
        
        // Admin items
        if (function_exists('isAdmin') && isAdmin()) {
            $userItems[] = ['url' => 'admin/dashboard.php', 'label' => 'Admin'];
        }
    } else {
        // Not logged in items
        $authItems = [
            ['url' => 'login.php', 'label' => 'Login'],
            ['url' => 'register.php', 'label' => 'Register']
        ];
    }
    
    // Build navigation HTML
    $html = '<nav><ul>';
    
    // Add public navigation items
    foreach ($navItems as $item) {
        $isActive = (basename($item['url']) === basename($currentPage)) ? ' class="active"' : '';
        $html .= "<li><a href=\"{$item['url']}\"{$isActive}>{$item['label']}</a></li>";
    }
    
    // Add user-specific items
    foreach ($userItems as $item) {
        $isActive = (basename($item['url']) === basename($currentPage)) ? ' class="active"' : '';
        $html .= "<li><a href=\"{$item['url']}\"{$isActive}>{$item['label']}</a></li>";
    }
    
    // Add authentication items
    foreach ($authItems as $item) {
        $isActive = (basename($item['url']) === basename($currentPage)) ? ' class="active"' : '';
        $html .= "<li><a href=\"{$item['url']}\"{$isActive}>{$item['label']}</a></li>";
    }
    
    // Add privacy policy
    $isPrivacyActive = (basename('privacy.html') === basename($currentPage)) ? ' class="active"' : '';
    $html .= "<li><a href=\"privacy.html\"{$isPrivacyActive}>Privacy Policy</a></li>";
    
    $html .= '</ul></nav>';
    
    return $html;
}

/**
 * Generate mobile-friendly navigation
 * @param string $currentPage
 * @return string
 */
function generateMobileNavigation($currentPage = '') {
    $navigation = generateNavigation($currentPage);
    
    // Add mobile menu toggle
    $mobileNav = '<div class="mobile-menu-toggle" onclick="toggleMobileMenu()">☰</div>';
    $mobileNav .= str_replace('<nav>', '<nav class="mobile-nav">', $navigation);
    
    return $mobileNav;
}

/**
 * Get navigation breadcrumbs
 * @param array $breadcrumbs
 * @return string
 */
function generateBreadcrumbs($breadcrumbs = []) {
    if (empty($breadcrumbs)) {
        return '';
    }
    
    $html = '<nav class="breadcrumbs"><ul>';
    
    foreach ($breadcrumbs as $index => $breadcrumb) {
        if ($index === count($breadcrumbs) - 1) {
            // Last item (current page) - no link
            $html .= "<li class=\"current\">{$breadcrumb['label']}</li>";
        } else {
            // Linked items
            $html .= "<li><a href=\"{$breadcrumb['url']}\">{$breadcrumb['label']}</a></li>";
        }
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}
?>