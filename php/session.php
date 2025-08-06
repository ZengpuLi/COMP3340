<?php
/**
 * Session Management System
 * Used Car Purchase Website - Authentication
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include database configuration
require_once __DIR__ . '/config.php';

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
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
        $query = "SELECT id, username, email, first_name, last_name, phone, created_at, last_login 
                  FROM users 
                  WHERE id = ? AND is_active = 1";
        
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
        
        // Update last login time in database
        $connection = getDatabaseConnection();
        $query = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        $stmt->close();
        $connection->close();
        
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
 * Get user display name
 * @return string
 */
function getUserDisplayName() {
    if (!isLoggedIn()) {
        return '';
    }
    
    $user = getCurrentUser();
    if ($user) {
        if (!empty($user['first_name']) && !empty($user['last_name'])) {
            return $user['first_name'] . ' ' . $user['last_name'];
        } elseif (!empty($user['first_name'])) {
            return $user['first_name'];
        } else {
            return $user['username'];
        }
    }
    
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
    
    if (!preg_match("/[A-Za-z]/", $password)) {
        $errors[] = "Password must contain at least one letter";
    }
    
    if (!preg_match("/[0-9]/", $password)) {
        $errors[] = "Password must contain at least one number";
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
?>