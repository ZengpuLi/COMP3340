<?php
/**
 * Admin Session Management System
 * Used Car Purchase Website - Admin Panel Authentication
 */

// Include regular session management
require_once 'session.php';
require_once 'config.php';

/**
 * Check if current user is an admin
 * @return bool
 */
function isAdmin() {
    if (!isLoggedIn()) {
        return false;
    }
    
    try {
        $connection = getDatabaseConnection();
        $query = "SELECT role FROM users WHERE id = ? AND is_active = 1";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        $stmt->close();
        $connection->close();
        
        return $user && $user['role'] === 'admin';
        
    } catch (Exception $e) {
        error_log("Error checking admin status: " . $e->getMessage());
        return false;
    }
}

/**
 * Require admin access - redirect if not admin
 * @param string $redirectTo URL to redirect non-admins to
 */
function requireAdmin($redirectTo = '../index.html') {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
    
    if (!isAdmin()) {
        header("Location: $redirectTo");
        exit();
    }
}

/**
 * Get current admin user information
 * @return array|null
 */
function getCurrentAdmin() {
    if (!isAdmin()) {
        return null;
    }
    
    return getCurrentUser();
}

/**
 * Log admin activity for audit trail
 * @param string $action Action performed
 * @param string $targetTable Table affected (optional)
 * @param int $targetId Record ID affected (optional)
 * @param string $details Additional details (optional)
 * @return bool
 */
function logAdminActivity($action, $targetTable = null, $targetId = null, $details = null) {
    if (!isAdmin()) {
        return false;
    }
    
    try {
        $connection = getDatabaseConnection();
        $query = "INSERT INTO admin_activity_log (admin_user_id, action, target_table, target_id, details, ip_address, user_agent) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $connection->prepare($query);
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $stmt->bind_param("ississs", 
            $_SESSION['user_id'], 
            $action, 
            $targetTable, 
            $targetId, 
            $details, 
            $ipAddress, 
            $userAgent
        );
        
        $success = $stmt->execute();
        
        $stmt->close();
        $connection->close();
        
        return $success;
        
    } catch (Exception $e) {
        error_log("Error logging admin activity: " . $e->getMessage());
        return false;
    }
}

/**
 * Get site setting value
 * @param string $key Setting key
 * @param string $default Default value if not found
 * @return string
 */
function getSiteSetting($key, $default = '') {
    try {
        $connection = getDatabaseConnection();
        $query = "SELECT setting_value FROM site_settings WHERE setting_key = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("s", $key);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $setting = $result->fetch_assoc();
        
        $stmt->close();
        $connection->close();
        
        return $setting ? $setting['setting_value'] : $default;
        
    } catch (Exception $e) {
        error_log("Error getting site setting: " . $e->getMessage());
        return $default;
    }
}

/**
 * Update site setting value
 * @param string $key Setting key
 * @param string $value Setting value
 * @param string $description Setting description (optional)
 * @return bool
 */
function updateSiteSetting($key, $value, $description = null) {
    if (!isAdmin()) {
        return false;
    }
    
    try {
        $connection = getDatabaseConnection();
        
        // Check if setting exists
        $checkQuery = "SELECT id FROM site_settings WHERE setting_key = ?";
        $checkStmt = $connection->prepare($checkQuery);
        $checkStmt->bind_param("s", $key);
        $checkStmt->execute();
        $exists = $checkStmt->get_result()->num_rows > 0;
        $checkStmt->close();
        
        if ($exists) {
            // Update existing setting
            $query = $description ? 
                "UPDATE site_settings SET setting_value = ?, setting_description = ?, updated_at = CURRENT_TIMESTAMP WHERE setting_key = ?" :
                "UPDATE site_settings SET setting_value = ?, updated_at = CURRENT_TIMESTAMP WHERE setting_key = ?";
            
            $stmt = $connection->prepare($query);
            
            if ($description) {
                $stmt->bind_param("sss", $value, $description, $key);
            } else {
                $stmt->bind_param("ss", $value, $key);
            }
        } else {
            // Insert new setting
            $query = "INSERT INTO site_settings (setting_key, setting_value, setting_description) VALUES (?, ?, ?)";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("sss", $key, $value, $description);
        }
        
        $success = $stmt->execute();
        
        $stmt->close();
        $connection->close();
        
        // Log admin activity
        if ($success) {
            logAdminActivity('update_setting', 'site_settings', null, "Updated setting: $key = $value");
        }
        
        return $success;
        
    } catch (Exception $e) {
        error_log("Error updating site setting: " . $e->getMessage());
        return false;
    }
}

/**
 * Get all site settings by category
 * @param string $category Setting category (optional)
 * @return array
 */
function getAllSiteSettings($category = null) {
    try {
        $connection = getDatabaseConnection();
        
        if ($category) {
            $query = "SELECT * FROM site_settings WHERE category = ? ORDER BY setting_key";
            $stmt = $connection->prepare($query);
            $stmt->bind_param("s", $category);
        } else {
            $query = "SELECT * FROM site_settings ORDER BY category, setting_key";
            $stmt = $connection->prepare($query);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        
        $settings = [];
        while ($row = $result->fetch_assoc()) {
            $settings[] = $row;
        }
        
        $stmt->close();
        $connection->close();
        
        return $settings;
        
    } catch (Exception $e) {
        error_log("Error getting site settings: " . $e->getMessage());
        return [];
    }
}

/**
 * Get admin statistics for dashboard
 * @return array
 */
function getAdminStats() {
    try {
        $connection = getDatabaseConnection();
        
        $stats = [];
        
        // User statistics
        $userQuery = "SELECT 
            COUNT(*) as total_users,
            SUM(CASE WHEN role = 'admin' THEN 1 ELSE 0 END) as admin_users,
            SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_users,
            SUM(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as new_users_30_days
            FROM users";
        
        $result = $connection->query($userQuery);
        $stats['users'] = $result->fetch_assoc();
        
        // Car statistics
        $carQuery = "SELECT 
            COUNT(*) as total_cars,
            AVG(price) as average_price,
            MIN(price) as min_price,
            MAX(price) as max_price
            FROM cars";
        
        $result = $connection->query($carQuery);
        $stats['cars'] = $result->fetch_assoc();
        
        // Favorites statistics
        $favQuery = "SELECT 
            COUNT(*) as total_favorites,
            COUNT(DISTINCT user_id) as users_with_favorites,
            COUNT(DISTINCT car_id) as cars_favorited
            FROM favorites";
        
        $result = $connection->query($favQuery);
        $stats['favorites'] = $result->fetch_assoc();
        
        // Purchase statistics
        $purchaseQuery = "SELECT 
            COUNT(*) as total_purchases,
            SUM(purchase_price) as total_revenue,
            AVG(purchase_price) as average_sale_price,
            COUNT(DISTINCT user_id) as customers
            FROM purchase_history";
        
        $result = $connection->query($purchaseQuery);
        $stats['purchases'] = $result->fetch_assoc();
        
        $connection->close();
        
        return $stats;
        
    } catch (Exception $e) {
        error_log("Error getting admin stats: " . $e->getMessage());
        return [];
    }
}

/**
 * Check if maintenance mode is enabled
 * @return bool
 */
function isMaintenanceMode() {
    return getSiteSetting('maintenance_mode', '0') === '1';
}

/**
 * Validate admin permissions for specific action
 * @param string $action Action to validate
 * @return bool
 */
function hasAdminPermission($action) {
    if (!isAdmin()) {
        return false;
    }
    
    // For now, all admins have all permissions
    // In the future, this could be expanded for role-based permissions
    $allowedActions = [
        'manage_cars',
        'manage_users', 
        'manage_settings',
        'view_logs',
        'system_admin'
    ];
    
    return in_array($action, $allowedActions);
}
?>