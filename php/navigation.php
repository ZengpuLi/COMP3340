<?php
/**
 * Dynamic Navigation Component
 * Used Car Purchase Website - Shows different options based on login status
 */

// Include session management if not already included
if (!function_exists('isLoggedIn')) {
    require_once dirname(__FILE__) . '/session.php';
}

/**
 * Generate navigation menu HTML based on login status
 * @param string $current_page Current page filename for active highlighting
 * @return string Navigation HTML
 */
function generateNavigation($current_page = '') {
    $loggedIn = isLoggedIn();
    $displayName = $loggedIn ? getUserDisplayName() : '';
    
    // Base navigation items (always shown)
            $nav_items = [
            'index.html' => 'Home',
            'about.html' => 'About Us',
            'cars.php' => 'Cars',
            'locations.html' => 'Locations',
            'market-trends.html' => 'Market Trends',
            'calculator.php' => 'Loan Calculator',
            'contact.html' => 'Contact Us',
            'help.php' => 'Help',
            'privacy.html' => 'Privacy Policy'
        ];
    
    $nav_html = '<nav>
        <button class="menu-toggle">☰</button>
        <ul>';
    
    // Add base navigation items
    foreach ($nav_items as $page => $title) {
        $active_class = ($current_page === $page) ? ' class="active"' : '';
        $nav_html .= "<li><a href=\"$page\"$active_class>$title</a></li>";
    }
    
    // Add authentication-specific items
    if ($loggedIn) {
        // Logged in - show user features, profile and logout
        $active_favorites = ($current_page === 'favorites.php') ? ' class="active"' : '';
        $active_purchases = ($current_page === 'purchases.php') ? ' class="active"' : '';
        $active_profile = ($current_page === 'profile.php') ? ' class="active"' : '';
        
        $nav_html .= "<li><a href=\"favorites.php\"$active_favorites>Favorites</a></li>";
        $nav_html .= "<li><a href=\"purchases.php\"$active_purchases>Purchases</a></li>";
        $nav_html .= "<li><a href=\"profile.php\"$active_profile>My Profile</a></li>";
        $nav_html .= '<li><a href="logout.php">Logout</a></li>';
        
        // Add user greeting on mobile
        $nav_html .= '<li class="user-greeting mobile-only"><span>Welcome, ' . sanitizeOutput($displayName) . '!</span></li>';
    } else {
        // Not logged in - show login and register
        $active_login = ($current_page === 'login.php') ? ' class="active"' : '';
        $active_register = ($current_page === 'register.php') ? ' class="active"' : '';
        $nav_html .= "<li><a href=\"login.php\"$active_login>Login</a></li>";
        $nav_html .= "<li><a href=\"register.php\"$active_register>Register</a></li>";
    }
    
    $nav_html .= '</ul>
    </nav>';
    
    return $nav_html;
}

/**
 * Generate header with user greeting
 * @return string Header greeting HTML
 */
function generateHeaderGreeting() {
    if (isLoggedIn()) {
        $displayName = getUserDisplayName();
        return '<p>Welcome back, ' . sanitizeOutput($displayName) . '!</p>';
    } else {
        return '<p>Find Your Perfect Pre-Owned Vehicle</p>';
    }
}

/**
 * Check if current user has admin privileges
 * @return bool
 */
function isAdmin() {
    if (!isLoggedIn()) {
        return false;
    }
    
    $user = getCurrentUser();
    return $user && $user['username'] === 'admin';
}
?>