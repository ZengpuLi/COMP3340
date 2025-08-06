<?php
/**
 * User Login Page
 * Used Car Purchase Website - Authentication System
 */

// Include session management
require_once 'php/session.php';
require_once 'php/config.php';
require_once 'php/navigation.php';

// Redirect if already logged in
redirectIfLoggedIn();

// Initialize variables
$errors = [];
$login_identifier = '';
$redirect_url = $_GET['redirect'] ?? 'index.html';

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
        
        // Authenticate user
        if (empty($errors)) {
            try {
                $connection = getDatabaseConnection();
                
                // Check if login identifier is email or username
                $query = "SELECT id, username, email, password, first_name, last_name, is_active 
                          FROM users 
                          WHERE (username = ? OR email = ?) AND is_active = 1";
                
                $stmt = $connection->prepare($query);
                $stmt->bind_param("ss", $login_identifier, $login_identifier);
                $stmt->execute();
                
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                
                if ($user && password_verify($password, $user['password'])) {
                    // Login successful
                    if (loginUser($user['id'], $user['username'])) {
                        // Redirect to intended page or home
                        $redirect_url = !empty($redirect_url) ? $redirect_url : 'index.html';
                        header("Location: $redirect_url");
                        exit();
                    } else {
                        $errors[] = "Login failed. Please try again.";
                    }
                } else {
                    $errors[] = "Invalid username/email or password.";
                }
                
                $stmt->close();
                $connection->close();
                
            } catch (Exception $e) {
                error_log("Login error: " . $e->getMessage());
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
    <title>Login - Used Car Purchase Website</title>
    <link rel="stylesheet" href="../css/theme-default.css" id="theme-link">
</head>
<body>
    <header>
        <div class="theme-switcher">
            <select id="theme-select" class="theme-select">
                <option value="default">Default Theme</option>
                <option value="dark">Dark Theme</option>
                <option value="light">Light Theme</option>
            </select>
        </div>
        <div class="logo-text" style="font-size: 2rem; font-weight: bold; color: #3498db; margin-bottom: 0.5rem;">🚗 AutoDeals</div>
        <h1>Used Car Purchase Website</h1>
        <p>Welcome Back!</p>
    </header>

    <?php echo generateNavigation('login.php'); ?>

    <main>
        <div class="auth-container">
            <h2>Log In to Your Account</h2>
            <p>Access your saved cars and personalized recommendations.</p>
            
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <h3>⚠️ Login Failed</h3>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo sanitizeOutput($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="login.php<?php echo !empty($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label for="login_identifier">Username or Email</label>
                    <input type="text" id="login_identifier" name="login_identifier" value="<?php echo sanitizeOutput($login_identifier); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="auth-btn">Log In</button>
            </form>
            
            <div class="auth-links">
                <p>Don't have an account? <a href="register.php">Create one here</a></p>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Used Car Purchase Website. All rights reserved.</p>
        <p>123 Auto Sales Drive, Cartown, CA 12345 | Phone: (555) 123-4567</p>
    </footer>

    <script src="../js/main.js"></script>
    <script src="../js/theme-switcher.js"></script>
</body>
</html>