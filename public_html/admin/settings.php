<?php
/**
 * Admin Site Settings
 * Used Car Purchase Website - Admin Panel Site Configuration
 */

// Include admin session management
require_once '../../php/admin-session.php';
require_once '../../php/config.php';

// Require admin access
requireAdmin();

// Initialize variables
$error_message = '';
$success_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error_message = "Security token validation failed. Please try again.";
    } else {
        $action = $_POST['action'] ?? '';
        
        if ($action === 'update_settings') {
            $updated_count = 0;
            $settings_to_update = [
                'cars_per_page' => [
                    'value' => intval($_POST['cars_per_page'] ?? 20),
                    'description' => 'Number of cars to display per page',
                    'validation' => function($val) { return $val >= 5 && $val <= 100; }
                ],
                'enable_user_registration' => [
                    'value' => isset($_POST['enable_user_registration']) ? '1' : '0',
                    'description' => 'Allow new user registration'
                ],
                'maintenance_mode' => [
                    'value' => isset($_POST['maintenance_mode']) ? '1' : '0',
                    'description' => 'Enable maintenance mode'
                ],
                'admin_email' => [
                    'value' => sanitizeInput($_POST['admin_email'] ?? ''),
                    'description' => 'Primary admin email address',
                    'validation' => function($val) { return filter_var($val, FILTER_VALIDATE_EMAIL); }
                ],
                'max_file_upload_size' => [
                    'value' => intval($_POST['max_file_upload_size'] ?? 5242880),
                    'description' => 'Maximum file upload size in bytes',
                    'validation' => function($val) { return $val >= 1048576 && $val <= 52428800; } // 1MB to 50MB
                ]
            ];
            
            foreach ($settings_to_update as $key => $setting) {
                $value = $setting['value'];
                
                // Validate if validation function exists
                if (isset($setting['validation']) && !$setting['validation']($value)) {
                    continue; // Skip invalid values
                }
                
                if (updateSiteSetting($key, $value, $setting['description'])) {
                    $updated_count++;
                }
            }
            
            if ($updated_count > 0) {
                $success_message = "Successfully updated $updated_count settings.";
                logAdminActivity('update_settings', 'site_settings', null, "Updated $updated_count site settings");
            } else {
                $error_message = "No settings were updated.";
            }
        }
    }
}

// Get current settings
$settings = [];
$setting_keys = ['cars_per_page', 'enable_user_registration', 'maintenance_mode', 'admin_email', 'max_file_upload_size'];

foreach ($setting_keys as $key) {
    $settings[$key] = getSiteSetting($key, '');
}

// Generate CSRF token
$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Settings - AutoDeals Administration</title>
    <link rel="stylesheet" href="../../css/theme-default.css" id="theme-link">
    <link rel="stylesheet" href="../admin-styles.css">
</head>
<body class="admin-body">
    <div class="admin-layout">
        <!-- Admin Sidebar -->
        <nav class="admin-sidebar">
            <div class="admin-sidebar-header">
                <div class="admin-logo">
                    <span class="logo-icon">🚗</span>
                    <span class="logo-text">AutoDeals</span>
                </div>
                <div class="admin-subtitle">Administration</div>
            </div>
            
            <div class="admin-user-info">
                <div class="admin-avatar">👤</div>
                <div class="admin-details">
                    <div class="admin-name"><?php echo sanitizeOutput(getUserDisplayName()); ?></div>
                    <div class="admin-role">Administrator</div>
                </div>
            </div>
            
            <ul class="admin-nav">
                <li><a href="dashboard.php">📊 Dashboard</a></li>
                <li><a href="cars.php">🚗 Manage Cars</a></li>
                <li><a href="users.php">👥 Manage Users</a></li>
                <li><a href="themes.php">🎨 Theme Settings</a></li>
                <li><a href="settings.php" class="active">⚙️ Site Settings</a></li>
                <li><a href="logs.php">📋 Activity Logs</a></li>
                <li class="nav-divider"></li>
                <li><a href="../index.html">🌐 View Site</a></li>
                <li><a href="../logout.php">🚪 Logout</a></li>
            </ul>
        </nav>
        
        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1>Site Settings</h1>
                <div class="admin-header-actions">
                    <span class="admin-time">Configure website behavior</span>
                </div>
            </div>
            
            <!-- Success/Error Messages -->
            <?php if ($success_message): ?>
                <div class="admin-alert admin-alert-success">
                    ✅ <?php echo sanitizeOutput($success_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="admin-alert admin-alert-danger">
                    ❌ <?php echo sanitizeOutput($error_message); ?>
                </div>
            <?php endif; ?>
            
            <!-- Maintenance Mode Warning -->
            <?php if (isMaintenanceMode()): ?>
                <div class="admin-alert admin-alert-warning">
                    <strong>🚧 Maintenance Mode Active</strong><br>
                    Your website is currently in maintenance mode. Regular users cannot access the site.
                </div>
            <?php endif; ?>
            
            <!-- Site Settings Form -->
            <div class="admin-section">
                <h2>General Settings</h2>
                <form method="POST" class="admin-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="action" value="update_settings">
                    
                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="cars_per_page">Cars Per Page</label>
                            <input type="number" id="cars_per_page" name="cars_per_page" 
                                   value="<?php echo sanitizeOutput($settings['cars_per_page'] ?: '20'); ?>" 
                                   min="5" max="100">
                            <small style="color: #6c757d;">Number of cars to display per page (5-100)</small>
                        </div>
                        
                        <div class="admin-form-group">
                            <label for="admin_email">Admin Email</label>
                            <input type="email" id="admin_email" name="admin_email" 
                                   value="<?php echo sanitizeOutput($settings['admin_email']); ?>" 
                                   placeholder="admin@autodeals.com">
                            <small style="color: #6c757d;">Primary contact email for admin notifications</small>
                        </div>
                    </div>
                    
                    <div class="admin-form-group">
                        <label for="max_file_upload_size">Max File Upload Size (MB)</label>
                        <input type="number" id="max_file_upload_size" name="max_file_upload_size" 
                               value="<?php echo round(intval($settings['max_file_upload_size'] ?: 5242880) / 1024 / 1024); ?>" 
                               min="1" max="50">
                        <small style="color: #6c757d;">Maximum file upload size in megabytes (1-50 MB)</small>
                    </div>
                    
                    <h3 style="margin-top: 2rem; margin-bottom: 1rem;">User Management</h3>
                    
                    <div class="admin-form-group">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="enable_user_registration" 
                                   <?php echo $settings['enable_user_registration'] === '1' ? 'checked' : ''; ?>>
                            Enable User Registration
                        </label>
                        <small style="color: #6c757d;">Allow new users to create accounts on the website</small>
                    </div>
                    
                    <h3 style="margin-top: 2rem; margin-bottom: 1rem;">System Settings</h3>
                    
                    <div class="admin-form-group">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="maintenance_mode" 
                                   <?php echo $settings['maintenance_mode'] === '1' ? 'checked' : ''; ?>>
                            Enable Maintenance Mode
                        </label>
                        <small style="color: #6c757d;">
                            <strong>⚠️ Warning:</strong> This will make the site inaccessible to regular users
                        </small>
                    </div>
                    
                    <button type="submit" class="admin-btn admin-btn-primary" style="margin-top: 2rem;">
                        💾 Save Settings
                    </button>
                </form>
            </div>
            
            <!-- System Information -->
            <div class="admin-section">
                <h2>System Information</h2>
                <div class="system-status-grid">
                    <div class="status-item">
                        <span class="status-label">PHP Version:</span>
                        <span class="status-value"><?php echo PHP_VERSION; ?></span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Server Software:</span>
                        <span class="status-value"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">MySQL Version:</span>
                        <span class="status-value">
                            <?php 
                            try {
                                $connection = getDatabaseConnection();
                                $version = $connection->server_info;
                                $connection->close();
                                echo $version;
                            } catch (Exception $e) {
                                echo 'Connection Error';
                            }
                            ?>
                        </span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Max Upload Size:</span>
                        <span class="status-value"><?php echo ini_get('upload_max_filesize'); ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Database Status -->
            <div class="admin-section">
                <h2>Database Status</h2>
                <div class="system-status-grid">
                    <?php
                    try {
                        $connection = getDatabaseConnection();
                        
                        // Get table sizes
                        $tables = ['users', 'cars', 'favorites', 'purchase_history', 'site_settings', 'admin_activity_log'];
                        foreach ($tables as $table) {
                            $query = "SELECT COUNT(*) as count FROM $table";
                            $result = $connection->query($query);
                            $count = $result->fetch_assoc()['count'];
                            echo "<div class='status-item'>";
                            echo "<span class='status-label'>" . ucfirst(str_replace('_', ' ', $table)) . ":</span>";
                            echo "<span class='status-value'>" . number_format($count) . " records</span>";
                            echo "</div>";
                        }
                        
                        $connection->close();
                    } catch (Exception $e) {
                        echo "<div class='status-item'>";
                        echo "<span class='status-label'>Database:</span>";
                        echo "<span class='status-value status-danger'>Connection Error</span>";
                        echo "</div>";
                    }
                    ?>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="admin-section">
                <h2>Quick Actions</h2>
                <div class="admin-quick-actions">
                    <a href="logs.php" class="quick-action-btn">
                        <span class="action-icon">📋</span>
                        <span class="action-text">View Activity Logs</span>
                    </a>
                    <a href="../index.html" class="quick-action-btn">
                        <span class="action-icon">🌐</span>
                        <span class="action-text">View Public Site</span>
                    </a>
                    <a href="dashboard.php" class="quick-action-btn">
                        <span class="action-icon">📊</span>
                        <span class="action-text">Return to Dashboard</span>
                    </a>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Convert MB to bytes for max_file_upload_size
        document.querySelector('form').addEventListener('submit', function(e) {
            const mbField = document.getElementById('max_file_upload_size');
            const mbValue = parseInt(mbField.value);
            const bytesValue = mbValue * 1024 * 1024;
            
            // Create hidden field with bytes value
            const hiddenField = document.createElement('input');
            hiddenField.type = 'hidden';
            hiddenField.name = 'max_file_upload_size';
            hiddenField.value = bytesValue;
            
            this.appendChild(hiddenField);
            
            // Disable the MB field so it doesn't submit
            mbField.disabled = true;
        });
    </script>
</body>
</html>