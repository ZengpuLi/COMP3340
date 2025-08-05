<?php
/**
 * Simple Registration Page
 * Used Car Purchase Website
 */

// Include required files
require_once 'php/config.php';
require_once 'php/session-fixed.php';
require_once 'php/navigation-fixed.php';

$error_message = '';
$success_message = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = 'Please fill in all fields.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password must be at least 6 characters long.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        try {
            $conn = getDatabaseConnection();
            
            // Check if username or email already exists
            $check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $check->bind_param("ss", $username, $email);
            $check->execute();
            $result = $check->get_result();
            
            if ($result->num_rows > 0) {
                $error_message = 'Username or email already exists.';
            } else {
                // Create new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert = $conn->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, 'user', NOW())");
                $insert->bind_param("sss", $username, $email, $hashed_password);
                
                if ($insert->execute()) {
                    $success_message = 'Registration successful! You can now log in.';
                    // Clear form data
                    $_POST = array();
                } else {
                    $error_message = 'Registration failed. Please try again.';
                }
            }
            $conn->close();
        } catch (Exception $e) {
            $error_message = 'Registration system temporarily unavailable.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - AutoDeals</title>
    <link rel="stylesheet" href="css/theme-default.css" id="theme-link">
    
    <style>
        .register-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .register-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .register-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-dark);
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .btn {
            background: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
        }
        
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            border: 1px solid #f5c6cb;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
            border: 1px solid #c3e6cb;
        }
        
        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e0e0e0;
        }
        
        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
        
        .password-requirements {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.25rem;
        }
    </style>
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
        <div class="logo-text">🚗 AutoDeals</div>
        <h1>Used Car Purchase Website</h1>
        <?php echo generateHeaderGreeting(); ?>
    </header>

    <?php echo generateNavigation('register.php'); ?>

    <main>
        <section class="register-hero">
            <h2>📝 Create Account</h2>
            <p>Join AutoDeals to save your favorite cars and track your purchases</p>
        </section>

        <div class="register-container">
            <div class="register-card">
                <h3>Create Your Account</h3>
                
                <?php if ($error_message): ?>
                    <div class="error-message">
                        <strong>⚠️ Error:</strong> <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success_message): ?>
                    <div class="success-message">
                        <strong>✅ Success:</strong> <?php echo htmlspecialchars($success_message); ?>
                        <br><a href="login.php">Click here to log in</a>
                    </div>
                <?php endif; ?>

                <form method="POST" action="register.php">
                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" 
                               value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                               required minlength="3" maxlength="50">
                        <div class="password-requirements">3-50 characters, letters and numbers only</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address:</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required minlength="6">
                        <div class="password-requirements">At least 6 characters</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password:</label>
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
                    </div>
                    
                    <button type="submit" class="btn">Create Account</button>
                </form>
                
                <div class="login-link">
                    <p>Already have an account? <a href="login.php">Sign in here</a></p>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Used Car Purchase Website. All rights reserved.</p>
        <p>123 Auto Sales Drive, Cartown, CA 12345 | Phone: (555) 123-4567</p>
    </footer>

    <script src="js/main.js"></script>
    <script src="js/theme-switcher.js"></script>
</body>
</html>