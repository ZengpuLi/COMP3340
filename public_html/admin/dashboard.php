<?php
/**
 * Admin Dashboard
 * Used Car Purchase Website - Admin Panel Main Dashboard
 */

// Include admin session management
require_once '../../php/admin-session.php';
require_once '../../php/config.php';

// Require admin access
requireAdmin();

// Get current admin user
$admin = getCurrentAdmin();

// Get admin statistics
$stats = getAdminStats();

// Get recent admin activity
try {
    $connection = getDatabaseConnection();
    $query = "SELECT aal.*, u.username, u.first_name, u.last_name 
              FROM admin_activity_log aal 
              JOIN users u ON aal.admin_user_id = u.id 
              ORDER BY aal.created_at DESC 
              LIMIT 10";
    
    $result = $connection->query($query);
    $recentActivity = [];
    
    while ($row = $result->fetch_assoc()) {
        $recentActivity[] = $row;
    }
    
    $connection->close();
    
} catch (Exception $e) {
    error_log("Error getting admin activity: " . $e->getMessage());
    $recentActivity = [];
}

// Log dashboard access
logAdminActivity('dashboard_access', null, null, 'Admin dashboard accessed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - AutoDeals Administration</title>
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
                <li><a href="dashboard.php" class="active">📊 Dashboard</a></li>
                <li><a href="cars.php">🚗 Manage Cars</a></li>
                <li><a href="users.php">👥 Manage Users</a></li>
                <li><a href="themes.php">🎨 Theme Settings</a></li>
                <li><a href="settings.php">⚙️ Site Settings</a></li>
                <li><a href="logs.php">📋 Activity Logs</a></li>
                <li class="nav-divider"></li>
                <li><a href="../index.html">🌐 View Site</a></li>
                <li><a href="../logout.php">🚪 Logout</a></li>
            </ul>
        </nav>
        
        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1>Admin Dashboard</h1>
                <div class="admin-header-actions">
                    <span class="admin-time">Last login: <?php echo $admin['last_login'] ? date('M j, Y g:i A', strtotime($admin['last_login'])) : 'Never'; ?></span>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="admin-stats-grid">
                <div class="admin-stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo number_format($stats['users']['total_users'] ?? 0); ?></div>
                        <div class="stat-label">Total Users</div>
                        <div class="stat-detail">
                            <?php echo $stats['users']['active_users'] ?? 0; ?> active, 
                            <?php echo $stats['users']['new_users_30_days'] ?? 0; ?> new this month
                        </div>
                    </div>
                </div>
                
                <div class="admin-stat-card">
                    <div class="stat-icon">🚗</div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo number_format($stats['cars']['total_cars'] ?? 0); ?></div>
                        <div class="stat-label">Cars in Inventory</div>
                        <div class="stat-detail">
                            Avg: <?php echo formatPrice($stats['cars']['average_price'] ?? 0); ?>
                        </div>
                    </div>
                </div>
                
                <div class="admin-stat-card">
                    <div class="stat-icon">❤️</div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo number_format($stats['favorites']['total_favorites'] ?? 0); ?></div>
                        <div class="stat-label">Total Favorites</div>
                        <div class="stat-detail">
                            <?php echo $stats['favorites']['users_with_favorites'] ?? 0; ?> users, 
                            <?php echo $stats['favorites']['cars_favorited'] ?? 0; ?> cars
                        </div>
                    </div>
                </div>
                
                <div class="admin-stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-content">
                        <div class="stat-number"><?php echo formatPrice($stats['purchases']['total_revenue'] ?? 0); ?></div>
                        <div class="stat-label">Total Revenue</div>
                        <div class="stat-detail">
                            <?php echo $stats['purchases']['total_purchases'] ?? 0; ?> sales, 
                            <?php echo $stats['purchases']['customers'] ?? 0; ?> customers
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="admin-section">
                <h2>Quick Actions</h2>
                <div class="admin-quick-actions">
                    <a href="cars.php?action=add" class="quick-action-btn">
                        <span class="action-icon">➕</span>
                        <span class="action-text">Add New Car</span>
                    </a>
                    <a href="users.php" class="quick-action-btn">
                        <span class="action-icon">👤</span>
                        <span class="action-text">Manage Users</span>
                    </a>
                    <a href="themes.php" class="quick-action-btn">
                        <span class="action-icon">🎨</span>
                        <span class="action-text">Change Theme</span>
                    </a>
                    <a href="settings.php" class="quick-action-btn">
                        <span class="action-icon">⚙️</span>
                        <span class="action-text">Site Settings</span>
                    </a>
                </div>
            </div>
            
            <!-- Recent Activity -->
            <div class="admin-section">
                <h2>Recent Admin Activity</h2>
                <div class="admin-activity-list">
                    <?php if (!empty($recentActivity)): ?>
                        <?php foreach ($recentActivity as $activity): ?>
                        <div class="activity-item">
                            <div class="activity-icon">
                                <?php
                                $icon = '📝';
                                switch ($activity['action']) {
                                    case 'login':
                                    case 'admin_login':
                                        $icon = '🔐';
                                        break;
                                    case 'create':
                                        $icon = '➕';
                                        break;
                                    case 'update':
                                        $icon = '✏️';
                                        break;
                                    case 'delete':
                                        $icon = '🗑️';
                                        break;
                                    case 'dashboard_access':
                                        $icon = '📊';
                                        break;
                                }
                                echo $icon;
                                ?>
                            </div>
                            <div class="activity-content">
                                <div class="activity-description">
                                    <strong><?php echo sanitizeOutput($activity['first_name'] . ' ' . $activity['last_name']); ?></strong>
                                    <?php echo sanitizeOutput($activity['details'] ?: ucfirst(str_replace('_', ' ', $activity['action']))); ?>
                                </div>
                                <div class="activity-meta">
                                    <?php echo date('M j, Y g:i A', strtotime($activity['created_at'])); ?>
                                    <?php if ($activity['target_table']): ?>
                                        • <?php echo sanitizeOutput($activity['target_table']); ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="no-activity">
                            <span class="no-activity-icon">📝</span>
                            <span>No recent activity found</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- System Status -->
            <div class="admin-section">
                <h2>System Status</h2>
                <div class="system-status-grid">
                    <div class="status-item">
                        <span class="status-label">Maintenance Mode:</span>
                        <span class="status-value <?php echo isMaintenanceMode() ? 'status-warning' : 'status-success'; ?>">
                            <?php echo isMaintenanceMode() ? 'Enabled' : 'Disabled'; ?>
                        </span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">User Registration:</span>
                        <span class="status-value status-success">
                            <?php echo getSiteSetting('enable_user_registration', '1') === '1' ? 'Enabled' : 'Disabled'; ?>
                        </span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Default Theme:</span>
                        <span class="status-value"><?php echo ucfirst(getSiteSetting('default_theme', 'default')); ?></span>
                    </div>
                    <div class="status-item">
                        <span class="status-label">Admin Users:</span>
                        <span class="status-value"><?php echo $stats['users']['admin_users'] ?? 0; ?></span>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>