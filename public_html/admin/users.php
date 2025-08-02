<?php
/**
 * Admin User Management
 * Used Car Purchase Website - Admin Panel User Management
 */

// Include admin session management
require_once '../../php/admin-session.php';
require_once '../../php/config.php';

// Require admin access
requireAdmin();

// Initialize variables
$users = [];
$error_message = '';
$success_message = '';
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 20;
$search = sanitizeInput($_GET['search'] ?? '');
$role_filter = $_GET['role'] ?? '';
$status_filter = $_GET['status'] ?? '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error_message = "Security token validation failed. Please try again.";
    } else {
        $action = $_POST['action'] ?? '';
        $user_id = intval($_POST['user_id'] ?? 0);
        
        switch ($action) {
            case 'toggle_status':
                $result = toggleUserStatus($user_id);
                if ($result['success']) {
                    $success_message = $result['message'];
                } else {
                    $error_message = $result['message'];
                }
                break;
                
            case 'change_role':
                $new_role = $_POST['new_role'] ?? '';
                $result = changeUserRole($user_id, $new_role);
                if ($result['success']) {
                    $success_message = $result['message'];
                } else {
                    $error_message = $result['message'];
                }
                break;
                
            case 'delete_user':
                $result = deleteUser($user_id);
                if ($result['success']) {
                    $success_message = $result['message'];
                } else {
                    $error_message = $result['message'];
                }
                break;
        }
    }
}

// User management functions
function toggleUserStatus($user_id) {
    try {
        $connection = getDatabaseConnection();
        
        // Get current status
        $query = "SELECT username, is_active FROM users WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }
        
        // Don't allow disabling self
        if ($user_id == $_SESSION['user_id']) {
            return ['success' => false, 'message' => 'You cannot deactivate your own account.'];
        }
        
        $new_status = $user['is_active'] ? 0 : 1;
        $action_text = $new_status ? 'activated' : 'deactivated';
        
        // Update status
        $updateQuery = "UPDATE users SET is_active = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $updateStmt = $connection->prepare($updateQuery);
        $updateStmt->bind_param("ii", $new_status, $user_id);
        
        if ($updateStmt->execute()) {
            $stmt->close();
            $updateStmt->close();
            $connection->close();
            
            // Log admin activity
            logAdminActivity('update', 'users', $user_id, "User {$user['username']} $action_text");
            
            return ['success' => true, 'message' => "User {$user['username']} has been $action_text."];
        } else {
            $stmt->close();
            $updateStmt->close();
            $connection->close();
            return ['success' => false, 'message' => 'Failed to update user status.'];
        }
        
    } catch (Exception $e) {
        error_log("Error toggling user status: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred.'];
    }
}

function changeUserRole($user_id, $new_role) {
    try {
        $connection = getDatabaseConnection();
        
        // Validate role
        if (!in_array($new_role, ['user', 'admin'])) {
            return ['success' => false, 'message' => 'Invalid role specified.'];
        }
        
        // Get current user info
        $query = "SELECT username, role FROM users WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }
        
        // Don't allow changing own role to user
        if ($user_id == $_SESSION['user_id'] && $new_role === 'user') {
            return ['success' => false, 'message' => 'You cannot remove your own admin privileges.'];
        }
        
        if ($user['role'] === $new_role) {
            return ['success' => false, 'message' => "User is already a $new_role."];
        }
        
        // Update role
        $updateQuery = "UPDATE users SET role = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        $updateStmt = $connection->prepare($updateQuery);
        $updateStmt->bind_param("si", $new_role, $user_id);
        
        if ($updateStmt->execute()) {
            $stmt->close();
            $updateStmt->close();
            $connection->close();
            
            // Log admin activity
            logAdminActivity('update', 'users', $user_id, "Changed role of {$user['username']} to $new_role");
            
            return ['success' => true, 'message' => "User {$user['username']} role changed to $new_role."];
        } else {
            $stmt->close();
            $updateStmt->close();
            $connection->close();
            return ['success' => false, 'message' => 'Failed to update user role.'];
        }
        
    } catch (Exception $e) {
        error_log("Error changing user role: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred.'];
    }
}

function deleteUser($user_id) {
    try {
        $connection = getDatabaseConnection();
        
        // Get user info
        $query = "SELECT username FROM users WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found.'];
        }
        
        // Don't allow deleting self
        if ($user_id == $_SESSION['user_id']) {
            return ['success' => false, 'message' => 'You cannot delete your own account.'];
        }
        
        // Delete user (this will cascade to favorites and purchase_history due to foreign keys)
        $deleteQuery = "DELETE FROM users WHERE id = ?";
        $deleteStmt = $connection->prepare($deleteQuery);
        $deleteStmt->bind_param("i", $user_id);
        
        if ($deleteStmt->execute()) {
            $stmt->close();
            $deleteStmt->close();
            $connection->close();
            
            // Log admin activity
            logAdminActivity('delete', 'users', $user_id, "Deleted user: {$user['username']}");
            
            return ['success' => true, 'message' => "User {$user['username']} has been deleted."];
        } else {
            $stmt->close();
            $deleteStmt->close();
            $connection->close();
            return ['success' => false, 'message' => 'Failed to delete user.'];
        }
        
    } catch (Exception $e) {
        error_log("Error deleting user: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred.'];
    }
}

// Build query with filters
$whereConditions = [];
$queryParams = [];
$paramTypes = '';

if (!empty($search)) {
    $whereConditions[] = "(username LIKE ? OR email LIKE ? OR first_name LIKE ? OR last_name LIKE ?)";
    $searchParam = "%$search%";
    $queryParams = array_merge($queryParams, [$searchParam, $searchParam, $searchParam, $searchParam]);
    $paramTypes .= 'ssss';
}

if (!empty($role_filter)) {
    $whereConditions[] = "role = ?";
    $queryParams[] = $role_filter;
    $paramTypes .= 's';
}

if (!empty($status_filter)) {
    $whereConditions[] = "is_active = ?";
    $queryParams[] = $status_filter === 'active' ? 1 : 0;
    $paramTypes .= 'i';
}

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get users with pagination
try {
    $connection = getDatabaseConnection();
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM users $whereClause";
    if (!empty($queryParams)) {
        $countStmt = $connection->prepare($countQuery);
        $countStmt->bind_param($paramTypes, ...$queryParams);
        $countStmt->execute();
        $countResult = $countStmt->get_result();
        $totalUsers = $countResult->fetch_assoc()['total'];
        $countStmt->close();
    } else {
        $countResult = $connection->query($countQuery);
        $totalUsers = $countResult->fetch_assoc()['total'];
    }
    
    $totalPages = ceil($totalUsers / $per_page);
    
    // Get users for current page
    $offset = ($page - 1) * $per_page;
    $query = "SELECT u.*, 
              (SELECT COUNT(*) FROM favorites f WHERE f.user_id = u.id) as favorites_count,
              (SELECT COUNT(*) FROM purchase_history ph WHERE ph.user_id = u.id) as purchases_count
              FROM users u 
              $whereClause 
              ORDER BY u.created_at DESC 
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
        $users[] = $row;
    }
    
    $stmt->close();
    $connection->close();
    
} catch (Exception $e) {
    error_log("Error getting users: " . $e->getMessage());
    $error_message = "Failed to load users.";
}

// Generate CSRF token
$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - AutoDeals Administration</title>
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
                <li><a href="users.php" class="active">👥 Manage Users</a></li>
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
                <h1>Manage Users</h1>
                <div class="admin-header-actions">
                    <span class="admin-time"><?php echo number_format($totalUsers ?? 0); ?> total users</span>
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
            
            <!-- Search and Filters -->
            <div class="admin-section">
                <form method="GET" class="admin-form">
                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="search">Search Users</label>
                            <input type="text" id="search" name="search" 
                                   value="<?php echo sanitizeOutput($search); ?>" 
                                   placeholder="Search by username, email, or name...">
                        </div>
                        
                        <div class="admin-form-group">
                            <label for="role">Filter by Role</label>
                            <select id="role" name="role">
                                <option value="">All Roles</option>
                                <option value="user" <?php echo $role_filter === 'user' ? 'selected' : ''; ?>>Users</option>
                                <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Admins</option>
                            </select>
                        </div>
                        
                        <div class="admin-form-group">
                            <label for="status">Filter by Status</label>
                            <select id="status" name="status">
                                <option value="">All Status</option>
                                <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                                <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 1rem;">
                        <button type="submit" class="admin-btn admin-btn-primary">🔍 Search</button>
                        <a href="users.php" class="admin-btn admin-btn-secondary">Clear Filters</a>
                    </div>
                </form>
            </div>
            
            <!-- Users List -->
            <div class="admin-section">
                <?php if (!empty($users)): ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>User Details</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Activity</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 600; margin-bottom: 0.25rem;">
                                        <?php echo sanitizeOutput($user['username']); ?>
                                        <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                            <span style="color: #667eea; font-size: 0.85rem;">(You)</span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="color: #6c757d; font-size: 0.9rem; margin-bottom: 0.25rem;">
                                        <?php echo sanitizeOutput($user['email']); ?>
                                    </div>
                                    <?php if (!empty($user['first_name']) || !empty($user['last_name'])): ?>
                                        <div style="color: #6c757d; font-size: 0.85rem;">
                                            <?php echo sanitizeOutput(trim($user['first_name'] . ' ' . $user['last_name'])); ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="status-value <?php echo $user['role'] === 'admin' ? 'status-warning' : 'status-success'; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-value <?php echo $user['is_active'] ? 'status-success' : 'status-danger'; ?>">
                                        <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div style="font-size: 0.85rem; color: #6c757d;">
                                        ❤️ <?php echo $user['favorites_count']; ?> favorites<br>
                                        💰 <?php echo $user['purchases_count']; ?> purchases<br>
                                        🕒 Last: <?php echo $user['last_login'] ? date('M j, Y', strtotime($user['last_login'])) : 'Never'; ?>
                                    </div>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <!-- Toggle Status -->
                                        <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                <input type="hidden" name="action" value="toggle_status">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" class="admin-btn admin-btn-sm <?php echo $user['is_active'] ? 'admin-btn-warning' : 'admin-btn-success'; ?>"
                                                        onclick="return confirm('Are you sure you want to <?php echo $user['is_active'] ? 'deactivate' : 'activate'; ?> this user?');">
                                                    <?php echo $user['is_active'] ? '🚫 Deactivate' : '✅ Activate'; ?>
                                                </button>
                                            </form>
                                            
                                            <!-- Change Role -->
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                <input type="hidden" name="action" value="change_role">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <input type="hidden" name="new_role" value="<?php echo $user['role'] === 'admin' ? 'user' : 'admin'; ?>">
                                                <button type="submit" class="admin-btn admin-btn-sm admin-btn-secondary"
                                                        onclick="return confirm('Are you sure you want to change this user\'s role to <?php echo $user['role'] === 'admin' ? 'user' : 'admin'; ?>?');">
                                                    <?php echo $user['role'] === 'admin' ? '👤 Make User' : '👑 Make Admin'; ?>
                                                </button>
                                            </form>
                                            
                                            <!-- Delete User -->
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                <input type="hidden" name="action" value="delete_user">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" class="admin-btn admin-btn-sm admin-btn-danger"
                                                        onclick="return confirm('Are you sure you want to DELETE this user? This action cannot be undone and will remove all their favorites and purchase history.');">
                                                    🗑️ Delete
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span style="color: #6c757d; font-size: 0.85rem; font-style: italic;">Your account</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="admin-pagination">
                            <?php
                            $baseUrl = "users.php?";
                            if ($search) $baseUrl .= "search=" . urlencode($search) . "&";
                            if ($role_filter) $baseUrl .= "role=" . urlencode($role_filter) . "&";
                            if ($status_filter) $baseUrl .= "status=" . urlencode($status_filter) . "&";
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
                        <div style="font-size: 3rem; margin-bottom: 1rem;">👥</div>
                        <h3>No Users Found</h3>
                        <p>No users match your current search criteria.</p>
                        <a href="users.php" class="admin-btn admin-btn-primary">View All Users</a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>