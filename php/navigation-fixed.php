<?php
/**
 * Dynamic Navigation Component - Fixed Version
 * Used Car Purchase Website - Stable navigation display
 */

// Include session management if not already included
if (!function_exists('isLoggedIn')) {
    require_once dirname(__FILE__) . '/session-fixed.php';
}

/**
 * Generate navigation menu HTML based on login status
 * @param string $current_page Current page filename for active highlighting
 * @return string Navigation HTML
 */
function generateNavigation($current_page = '') {
    // 简化登录状态检查
    $loggedIn = false;
    $displayName = '';
    
    try {
        $loggedIn = isLoggedIn();
        if ($loggedIn) {
            $displayName = getUserDisplayName();
        }
    } catch (Exception $e) {
        // 如果出错，默认为未登录状态
        $loggedIn = false;
    }
    
    // Base navigation items (always shown)
    $nav_items = [
        'index.html' => 'Home',
        'about.html' => 'About Us',
        'cars.php' => 'Cars',
        'locations.html' => 'Locations',
        'market-trends.html' => 'Market Trends',
        'calculator.php' => 'Loan Calculator',
        'contact.html' => 'Contact Us',
        'help.php' => 'Help'
    ];
    
    $nav_html = '<nav>
        <button class="menu-toggle">☰</button>
        <ul>';
    
    // Add base navigation items
    foreach ($nav_items as $page => $title) {
        $active_class = ($current_page === $page) ? ' class="active"' : '';
        $nav_html .= "<li><a href=\"$page\"$active_class>$title</a></li>";
    }
    
    // 总是显示Privacy Policy
    $nav_html .= '<li><a href="privacy.html">Privacy Policy</a></li>';
    
    // Add authentication-specific items
    if ($loggedIn && !empty($displayName)) {
        // 已登录 - 显示用户功能
        $nav_html .= '<li><a href="profile.php">My Profile</a></li>';
        $nav_html .= '<li><a href="logout.php">Logout</a></li>';
    } else {
        // 未登录 - 始终显示登录和注册
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
    try {
        if (isLoggedIn()) {
            $displayName = getUserDisplayName();
            if (!empty($displayName)) {
                return '<p>Welcome back, ' . sanitizeOutput($displayName) . '!</p>';
            }
        }
    } catch (Exception $e) {
        // 如果出错，显示默认消息
    }
    
    return '<p>Welcome to AutoDeals - Your trusted car dealer</p>';
}

/**
 * Check if current user has admin privileges
 * @return bool
 */
function isAdmin() {
    try {
        if (!isLoggedIn()) {
            return false;
        }
        
        $user = getCurrentUser();
        return $user && isset($user['role']) && $user['role'] === 'admin';
    } catch (Exception $e) {
        return false;
    }
}
?>