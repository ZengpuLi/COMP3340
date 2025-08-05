<?php
/**
 * Session Management (Safe Version)
 * Used Car Purchase Website
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Check if user is admin
 * @return bool
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Get current user ID
 * @return int|null
 */
function getCurrentUserId() {
    return isLoggedIn() ? (int)$_SESSION['user_id'] : null;
}

/**
 * Get current username
 * @return string|null
 */
function getCurrentUsername() {
    return isLoggedIn() ? $_SESSION['username'] : null;
}

/**
 * Login user
 * @param int $userId
 * @param string $username
 * @param string $role
 */
function loginUser($userId, $username, $role = 'user') {
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    $_SESSION['user_id'] = (int)$userId;
    $_SESSION['username'] = $username;
    $_SESSION['user_role'] = $role;
    $_SESSION['login_time'] = time();
}

/**
 * Logout user
 */
function logoutUser() {
    // Clear all session variables
    $_SESSION = array();
    
    // Destroy session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy session
    session_destroy();
}

/**
 * Require login - redirect if not logged in
 * @param string $redirectTo
 */
function requireLogin($redirectTo = 'login.php') {
    if (!isLoggedIn()) {
        header("Location: $redirectTo");
        exit;
    }
}

/**
 * Require admin - redirect if not admin
 * @param string $redirectTo
 */
function requireAdmin($redirectTo = 'login.php') {
    if (!isAdmin()) {
        header("Location: $redirectTo");
        exit;
    }
}

/**
 * Generate header greeting
 * @return string
 */
function generateHeaderGreeting() {
    if (isLoggedIn()) {
        $username = getCurrentUsername();
        return "<p class=\"user-greeting\">Welcome back, " . sanitizeOutput($username) . "!</p>";
    }
    return "<p class=\"user-greeting\">Find Your Perfect Pre-Owned Vehicle</p>";
}

/**
 * Sanitize user input (only if not already defined)
 * @param string $input
 * @return string
 */
if (!function_exists('sanitizeInput')) {
    function sanitizeInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Check for CSRF token
 * @param string $token
 * @return bool
 */
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Generate CSRF token
 * @return string
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Set flash message
 * @param string $message
 * @param string $type (success, error, warning, info)
 */
function setFlashMessage($message, $type = 'info') {
    $_SESSION['flash_message'] = array(
        'message' => $message,
        'type' => $type
    );
}

/**
 * Get and clear flash message
 * @return array|null
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

/**
 * Check session timeout (30 minutes)
 * @return bool
 */
function checkSessionTimeout() {
    $timeout = 30 * 60; // 30 minutes
    
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > $timeout) {
        logoutUser();
        return true;
    }
    
    // Update last activity time
    $_SESSION['login_time'] = time();
    return false;
}

// Auto-check session timeout for logged in users
if (isLoggedIn()) {
    checkSessionTimeout();
}
?>