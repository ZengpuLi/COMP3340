<?php
/**
 * Register Page (Final Safe Version)
 * Used Car Purchase Website
 */

// Include required files
require_once 'php/config.php';
require_once 'php/session-safe.php';
require_once 'php/navigation-safe.php';

$error_message = '';
$success_message = '';

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $email = sanitizeInput($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        try {
            $conn = getDatabaseConnection();
            
            // Check if username or email already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error_message = "Username or email already exists.";
            } else {
                // Hash password and insert user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->bind_param("sss", $username, $email, $hashed_password);
                
                if ($stmt->execute()) {
                    $success_message = "Registration successful! You can now log in.";
                } else {
                    $error_message = "Registration failed. Please try again.";
                }
            }
            
            $stmt->close();
            $conn->close();
        } catch (Exception $e) {
            error_log("Registration error: " . $e->getMessage());
            $error_message = "An unexpected error occurred. Please try again later.";
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
        .form-container {
            max-width: 400px;
            margin: 3rem auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        .form-container h2 {
            color: var(--primary-color);
            margin-bottom: 1.5rem;
        }
        .form-group {
            margin-bottom: 1rem;
            text-align: left;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-dark);
        }
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
        }
        .btn-submit {
            width: 100%;
            padding: 12px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .btn-submit:hover {
            background: var(--secondary-color);
        }
        .message {
            margin-top: 1rem;
            padding: 0.8rem;
            border-radius: 6px;
            font-weight: 600;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .form-footer {
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
        .form-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }
        .form-footer a:hover {
            text-decoration: underline;
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
        <div class="logo-text" style="font-size: 2rem; font-weight: bold; color: #3498db; margin-bottom: 0.5rem;">🚗 AutoDeals</div>
        <h1>Used Car Purchase Website</h1>
        <?php echo generateHeaderGreeting(); ?>
    </header>

    <?php echo generateNavigation('register.php'); ?>

    <main>
        <div class="form-container">
            <h2>Create Your Account</h2>
            <?php if ($error_message): ?>
                <div class="message error-message"><?php echo sanitizeOutput($error_message); ?></div>
            <?php endif; ?>
            <?php if ($success_message): ?>
                <div class="message success-message"><?php echo sanitizeOutput($success_message); ?></div>
            <?php endif; ?>
            <form action="register.php" method="POST">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn-submit">Register</button>
            </form>
            <div class="form-footer">
                Already have an account? <a href="login.php">Login here</a>
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