<?php
/**
 * Admin Login Page
 * Used Car Purchase Website - Admin Panel
 */

// Include session management
require_once '../../php/session.php';
require_once '../../php/admin-session.php';
require_once '../../php/config.php';

// Redirect if already logged in as admin
if (isLoggedIn() && isAdmin()) {
    header("Location: dashboard.php");
    exit();
}

// Initialize variables
$errors = [];
$login_identifier = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = "Security token validation failed. Please try again.";
    } else {
        // Sanitize input
        $login_identifier = sanitizeInput($_POST['login_identifier'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // Validation
        if (empty($login_identifier)) {
            $errors[] = "Username or email is required";
        }
        
        if (empty($password)) {
            $errors[] = "Password is required";
        }
        
        // Authenticate admin user
        if (empty($errors)) {
            try {
                $connection = getDatabaseConnection();
                
                // Check if login identifier is email or username AND user is admin
                $query = "SELECT id, username, email, password, first_name, last_name, role, is_active 
                          FROM users 
                          WHERE (username = ? OR email = ?) AND role = 'admin' AND is_active = 1";
                
                $stmt = $connection->prepare($query);
                $stmt->bind_param("ss", $login_identifier, $login_identifier);
                $stmt->execute();
                
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                
                if ($user && password_verify($password, $user['password'])) {
                    // Login successful
                    if (loginUser($user['id'], $user['username'])) {
                        // Log admin login activity
                        logAdminActivity('admin_login', null, null, 'Admin panel login successful');
                        
                        // Redirect to admin dashboard
                        header("Location: dashboard.php");
                        exit();
                    } else {
                        $errors[] = "Login failed. Please try again.";
                    }
                } else {
                    $errors[] = "Invalid admin credentials or insufficient permissions.";
                    
                    // Log failed login attempt
                    if ($user && $user['role'] !== 'admin') {
                        error_log("Non-admin user attempted admin login: " . $login_identifier);
                    }
                }
                
                $stmt->close();
                $connection->close();
                
            } catch (Exception $e) {
                error_log("Admin login error: " . $e->getMessage());
                $errors[] = "Login failed. Please try again later.";
            }
        }
    }
}

// Generate CSRF token
$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - AutoDeals Administration</title>
    <link rel="stylesheet" href="../../css/theme-default.css" id="theme-link">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .admin-login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .admin-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .admin-logo {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        
        .admin-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        
        .admin-subtitle {
            color: #7f8c8d;
            font-size: 1rem;
        }
        
        .admin-form {
            margin-top: 2rem;
        }
        
        .admin-form .form-group {
            margin-bottom: 1.5rem;
        }
        
        .admin-form label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .admin-form input[type="text"],
        .admin-form input[type="email"],
        .admin-form input[type="password"] {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e1e8ed;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .admin-form input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: white;
        }
        
        .admin-btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        
        .admin-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 25px rgba(102, 126, 234, 0.3);
        }
        
        .admin-error {
            background: #ffebee;
            color: #c62828;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #c62828;
        }
        
        .admin-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e1e8ed;
        }
        
        .admin-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        
        .admin-footer a:hover {
            text-decoration: underline;
        }
        
        .security-notice {
            background: #e3f2fd;
            color: #1565c0;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #1565c0;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="admin-login-container">
        <div class="admin-header">
            <div class="admin-logo">🔐</div>
            <h1 class="admin-title">Admin Panel</h1>
            <p class="admin-subtitle">AutoDeals Administration</p>
        </div>
        
        <div class="security-notice">
            <strong>🛡️ Secure Access Required</strong><br>
            This area is restricted to authorized administrators only.
        </div>
        
        <?php if (!empty($errors)): ?>
            <div class="admin-error">
                <strong>⚠️ Access Denied</strong>
                <ul style="margin: 0.5rem 0 0 1rem;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo sanitizeOutput($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="login.php" class="admin-form">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
            
            <div class="form-group">
                <label for="login_identifier">Admin Username or Email</label>
                <input type="text" id="login_identifier" name="login_identifier" 
                       value="<?php echo sanitizeOutput($login_identifier); ?>" 
                       required autofocus>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit" class="admin-btn">
                🚀 Access Admin Panel
            </button>
        </form>
        
        <div class="admin-footer">
            <p><a href="../index.html">← Return to Main Site</a></p>
            <p style="font-size: 0.85rem; color: #95a5a6; margin-top: 0.5rem;">
                Protected by advanced security measures
            </p>
        </div>
    </div>
</body>
</html>