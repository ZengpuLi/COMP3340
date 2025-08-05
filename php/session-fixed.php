<?php
/**
 * Session Management System - Fixed Version
 * Used Car Purchase Website - Authentication
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration
require_once 'config.php';

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Get current user ID
 * @return int|null
 */
function getCurrentUserId() {
    return isLoggedIn() ? $_SESSION['user_id'] : null;
}

/**
 * Get current user information
 * @return array|null
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    try {
        $connection = getDatabaseConnection();
        // 简化查询，只查询存在的列
        $query = "SELECT id, username, email, created_at FROM users WHERE id = ?";
        
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        $stmt->close();
        $connection->close();
        
        return $user;
        
    } catch (Exception $e) {
        error_log("Error fetching current user: " . $e->getMessage());
        return null;
    }
}

/**
 * Login user and create session
 * @param int $userId
 * @param string $username
 * @return bool
 */
function loginUser($userId, $username) {
    try {
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        // Set session variables
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['login_time'] = time();
        
        return true;
        
    } catch (Exception $e) {
        error_log("Error logging in user: " . $e->getMessage());
        return false;
    }
}

/**
 * Logout user and destroy session
 */
function logoutUser() {
    // Unset all session variables
    $_SESSION = array();
    
    // Delete session cookie
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
 * Require login - redirect to login page if not logged in
 * @param string $redirectTo URL to redirect to after login
 */
function requireLogin($redirectTo = null) {
    if (!isLoggedIn()) {
        $redirect = $redirectTo ? urlencode($redirectTo) : '';
        $loginUrl = 'login.php' . ($redirect ? '?redirect=' . $redirect : '');
        header("Location: $loginUrl");
        exit();
    }
}

/**
 * Redirect if already logged in
 * @param string $redirectTo URL to redirect to
 */
function redirectIfLoggedIn($redirectTo = 'index.html') {
    if (isLoggedIn()) {
        header("Location: $redirectTo");
        exit();
    }
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
 * Verify CSRF token
 * @param string $token
 * @return bool
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Get user display name - simplified version
 * @return string
 */
function getUserDisplayName() {
    if (!isLoggedIn()) {
        return '';
    }
    
    // 直接从session获取用户名，避免数据库查询问题
    return $_SESSION['username'] ?? '';
}

/**
 * Validate password strength
 * @param string $password
 * @return array
 */
function validatePassword($password) {
    $errors = [];
    
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long";
    }
    
    return $errors;
}

/**
 * Sanitize user input
 * @param string $input
 * @return string
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Sanitize output for display
 * @param string $output
 * @return string
 */
function sanitizeOutput($output) {
    return htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
}
?>