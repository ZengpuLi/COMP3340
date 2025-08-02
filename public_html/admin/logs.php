<?php
/**
 * Admin Activity Logs
 * Used Car Purchase Website - Admin Panel Activity Monitoring
 */

// Include admin session management
require_once '../../php/admin-session.php';
require_once '../../php/config.php';

// Require admin access
requireAdmin();

// Initialize variables
$logs = [];
$error_message = '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 50;
$action_filter = $_GET['action'] ?? '';
$admin_filter = intval($_GET['admin'] ?? 0);
$date_filter = $_GET['date'] ?? '';

// Build query with filters
$whereConditions = [];
$queryParams = [];
$paramTypes = '';

if (!empty($action_filter)) {
    $whereConditions[] = "aal.action = ?";
    $queryParams[] = $action_filter;
    $paramTypes .= 's';
}

if ($admin_filter > 0) {
    $whereConditions[] = "aal.admin_user_id = ?";
    $queryParams[] = $admin_filter;
    $paramTypes .= 'i';
}

if (!empty($date_filter)) {
    $whereConditions[] = "DATE(aal.created_at) = ?";
    $queryParams[] = $date_filter;
    $paramTypes .= 's';
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get activity logs with pagination
try {
    $connection = getDatabaseConnection();
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total 
                   FROM admin_activity_log aal 
                   JOIN users u ON aal.admin_user_id = u.id 
                   $whereClause";
    
    if (!empty($queryParams)) {
        $countStmt = $connection->prepare($countQuery);
        $countStmt->bind_param($paramTypes, ...$queryParams);
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $totalLogs = $countResult->fetch_assoc()['total'];
        $countStmt->close();
    } else {
        $countResult = $connection->query($countQuery);
        $totalLogs = $countResult->fetch_assoc()['total'];
    }
    
    $totalPages = ceil($totalLogs / $per_page);
    
    // Get logs for current page
    $offset = ($page - 1) * $per_page;
    $query = "SELECT aal.*, u.username, u.first_name, u.last_name 
              FROM admin_activity_log aal 
              JOIN users u ON aal.admin_user_id = u.id 
              $whereClause 
              ORDER BY aal.created_at DESC 
              LIMIT ? OFFSET ?";
    
    $finalParams = array_merge($queryParams, [$per_page, $offset]);
    $finalParamTypes = $paramTypes . 'ii';
    
    $stmt = $connection->prepare($query);
    if (!empty($finalParams)) {
        $stmt->bind_param($finalParamTypes, ...$finalParams);
    }
    $stmt->execute();
    
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
    
    $stmt->close();
    
    // Get available admins for filter
    $adminQuery = "SELECT DISTINCT u.id, u.username, u.first_name, u.last_name 
                   FROM users u 
                   JOIN admin_activity_log aal ON u.id = aal.admin_user_id 
                   WHERE u.role = 'admin' 
                   ORDER BY u.username";
    $adminResult = $connection->query($adminQuery);
    $admins = [];
    while ($row = $adminResult->fetch_assoc()) {
        $admins[] = $row;
    }
    
    $connection->close();
    
} catch (Exception $e) {
    error_log("Error getting activity logs: " . $e->getMessage());
    $error_message = "Failed to load activity logs.";
}

// Function to get action icon
function getActionIcon($action) {
    $icons = [
        'login' => '🔐',
        'admin_login' => '🔐',
        'dashboard_access' => '📊',
        'create' => '➕',
        'update' => '✏️',
        'delete' => '🗑️',
        'update_setting' => '⚙️',
        'update_settings' => '⚙️',
    ];
    
    return $icons[$action] ?? '📝';
}

// Function to get action color
function getActionColor($action) {
    $colors = [
        'login' => '#28a745',
        'admin_login' => '#28a745', 
        'create' => '#17a2b8',
        'update' => '#ffc107',
        'delete' => '#dc3545',
        'update_setting' => '#6f42c1',
        'update_settings' => '#6f42c1',
    ];
    
    return $colors[$action] ?? '#6c757d';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs - AutoDeals Administration</title>
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
                <li><a href="settings.php">⚙️ Site Settings</a></li>
                <li><a href="logs.php" class="active">📋 Activity Logs</a></li>
                <li class="nav-divider"></li>
                <li><a href="../index.html">🌐 View Site</a></li>
                <li><a href="../logout.php">🚪 Logout</a></li>
            </ul>
        </nav>
        
        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1>Activity Logs</h1>
                <div class="admin-header-actions">
                    <span class="admin-time"><?php echo number_format($totalLogs ?? 0); ?> total activities</span>
                </div>
            </div>
            
            <!-- Error Messages -->
            <?php if ($error_message): ?>
                <div class="admin-alert admin-alert-danger">
                    ❌ <?php echo sanitizeOutput($error_message); ?>
                </div>
            <?php endif; ?>
            
            <!-- Filters -->
            <div class="admin-section">
                <form method="GET" class="admin-form">
                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="action">Filter by Action</label>
                            <select id="action" name="action">
                                <option value="">All Actions</option>
                                <option value="admin_login" <?php echo $action_filter === 'admin_login' ? 'selected' : ''; ?>>Admin Login</option>
                                <option value="create" <?php echo $action_filter === 'create' ? 'selected' : ''; ?>>Create</option>
                                <option value="update" <?php echo $action_filter === 'update' ? 'selected' : ''; ?>>Update</option>
                                <option value="delete" <?php echo $action_filter === 'delete' ? 'selected' : ''; ?>>Delete</option>
                                <option value="update_setting" <?php echo $action_filter === 'update_setting' ? 'selected' : ''; ?>>Settings Update</option>
                            </select>
                        </div>
                        
                        <div class="admin-form-group">
                            <label for="admin">Filter by Admin</label>
                            <select id="admin" name="admin">
                                <option value="">All Admins</option>
                                <?php foreach ($admins as $admin): ?>
                                    <option value="<?php echo $admin['id']; ?>" 
                                            <?php echo $admin_filter === $admin['id'] ? 'selected' : ''; ?>>
                                        <?php echo sanitizeOutput($admin['username']); ?>
                                        <?php if ($admin['first_name']): ?>
                                            (<?php echo sanitizeOutput($admin['first_name'] . ' ' . $admin['last_name']); ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="admin-form-group">
                            <label for="date">Filter by Date</label>
                            <input type="date" id="date" name="date" value="<?php echo sanitizeOutput($date_filter); ?>">
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="admin-btn admin-btn-primary">🔍 Filter</button>
                        <a href="logs.php" class="admin-btn admin-btn-secondary">Clear Filters</a>
                    </div>
                </form>
            </div>
            
            <!-- Activity Logs -->
            <div class="admin-section">
                <?php if (!empty($logs)): ?>
                    <div class="admin-activity-list">
                        <?php foreach ($logs as $log): ?>
                        <div class="activity-item">
                            <div class="activity-icon" style="background-color: <?php echo getActionColor($log['action']); ?>20;">
                                <?php echo getActionIcon($log['action']); ?>
                            </div>
                            <div class="activity-content">
                                <div class="activity-description">
                                    <strong><?php echo sanitizeOutput($log['first_name'] . ' ' . $log['last_name']); ?></strong>
                                    <span style="color: #6c757d;">(@<?php echo sanitizeOutput($log['username']); ?>)</span>
                                    <?php if ($log['details']): ?>
                                        <?php echo sanitizeOutput($log['details']); ?>
                                    <?php else: ?>
                                        <?php echo ucfirst(str_replace('_', ' ', $log['action'])); ?>
                                    <?php endif; ?>
                                </div>
                                <div class="activity-meta">
                                    <?php echo date('F j, Y g:i:s A', strtotime($log['created_at'])); ?>
                                    <?php if ($log['target_table']): ?>
                                        • Table: <?php echo sanitizeOutput($log['target_table']); ?>
                                    <?php endif; ?>
                                    <?php if ($log['target_id']): ?>
                                        • ID: <?php echo $log['target_id']; ?>
                                    <?php endif; ?>
                                    • IP: <?php echo sanitizeOutput($log['ip_address']); ?>
                                </div>
                            </div>
                            <div style="text-align: right; color: #6c757d; font-size: 0.8rem;">
                                <?php echo date('H:i:s', strtotime($log['created_at'])); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="admin-pagination">
                            <?php
                            $baseUrl = "logs.php?";
                            if ($action_filter) $baseUrl .= "action=" . urlencode($action_filter) . "&";
                            if ($admin_filter) $baseUrl .= "admin=" . urlencode($admin_filter) . "&";
                            if ($date_filter) $baseUrl .= "date=" . urlencode($date_filter) . "&";
                            ?>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <?php if ($i === $page): ?>
                                    <span class="current"><?php echo $i; ?></span>
                                <?php else: ?>
                                    <a href="<?php echo $baseUrl; ?>page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                    
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem; color: #6c757d;">
                        <div style="font-size: 3rem; margin-bottom: 1rem;">📋</div>
                        <h3>No Activity Found</h3>
                        <p>No admin activities match your current filter criteria.</p>
                        <a href="logs.php" class="admin-btn admin-btn-primary">View All Activities</a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>