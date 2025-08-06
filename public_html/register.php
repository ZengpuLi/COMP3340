<?php
/**
 * User Registration Page
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
$success_message = '';
$form_data = [
    'username' => '',
    'email' => '',
    'first_name' => '',
    'last_name' => '',
    'phone' => ''
];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = "Security token validation failed. Please try again.";
    } else {
        // Sanitize and validate input
        $form_data['username'] = sanitizeInput($_POST['username'] ?? '');
        $form_data['email'] = sanitizeInput($_POST['email'] ?? '');
        $form_data['first_name'] = sanitizeInput($_POST['first_name'] ?? '');
        $form_data['last_name'] = sanitizeInput($_POST['last_name'] ?? '');
        $form_data['phone'] = sanitizeInput($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Validation
        if (empty($form_data['username'])) {
            $errors[] = "Username is required";
        } elseif (strlen($form_data['username']) < 3) {
            $errors[] = "Username must be at least 3 characters long";
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $form_data['username'])) {
            $errors[] = "Username can only contain letters, numbers, and underscores";
        }
        
        if (empty($form_data['email'])) {
            $errors[] = "Email is required";
        } elseif (!filter_var($form_data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Please enter a valid email address";
        }
        
        if (empty($password)) {
            $errors[] = "Password is required";
        } else {
            $password_errors = validatePassword($password);
            $errors = array_merge($errors, $password_errors);
        }
        
        if ($password !== $confirm_password) {
            $errors[] = "Passwords do not match";
        }
        
        // Check for duplicate username and email
        if (empty($errors)) {
            try {
                $connection = getDatabaseConnection();
                
                // Check username
                $query = "SELECT id FROM users WHERE username = ?";
                $stmt = $connection->prepare($query);
                $stmt->bind_param("s", $form_data['username']);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $errors[] = "Username already exists. Please choose a different one.";
                }
                $stmt->close();
                
                // Check email
                $query = "SELECT id FROM users WHERE email = ?";
                $stmt = $connection->prepare($query);
                $stmt->bind_param("s", $form_data['email']);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    $errors[] = "Email address already registered. Please use a different email.";
                }
                $stmt->close();
                
                $connection->close();
                
            } catch (Exception $e) {
                error_log("Registration validation error: " . $e->getMessage());
                $errors[] = "Unable to validate registration. Please try again.";
            }
        }
        
        // If no errors, create the user
        if (empty($errors)) {
            try {
                $connection = getDatabaseConnection();
                
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Insert user
                $query = "INSERT INTO users (username, email, password, first_name, last_name, phone) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $connection->prepare($query);
                $stmt->bind_param("ssssss", 
                    $form_data['username'], 
                    $form_data['email'], 
                    $hashed_password,
                    $form_data['first_name'],
                    $form_data['last_name'],
                    $form_data['phone']
                );
                
                if ($stmt->execute()) {
                    $success_message = "Registration successful! You can now log in with your credentials.";
                    // Clear form data on success
                    $form_data = array_fill_keys(array_keys($form_data), '');
                } else {
                    $errors[] = "Registration failed. Please try again.";
                }
                
                $stmt->close();
                $connection->close();
                
            } catch (Exception $e) {
                error_log("Registration error: " . $e->getMessage());
                $errors[] = "Registration failed. Please try again later.";
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
    <title>Register - Used Car Purchase Website</title>
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
        <p>Create Your Account</p>
    </header>

    <?php echo generateNavigation('register.php'); ?>

    <main>
        <div class="auth-container">
            <h2>Create Your Account</h2>
            <p>Join AutoDeals to save your favorite cars and get personalized recommendations.</p>
            
            <?php if (!empty($success_message)): ?>
                <div class="success-message">
                    <h3>✅ Success!</h3>
                    <p><?php echo sanitizeOutput($success_message); ?></p>
                    <p><a href="login.php">Click here to log in</a></p>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <h3>⚠️ Please fix the following errors:</h3>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo sanitizeOutput($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="register.php" class="auth-form">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                
                <div class="form-group">
                    <label for="username">Username *</label>
                    <input type="text" id="username" name="username" value="<?php echo sanitizeOutput($form_data['username']); ?>" required>
                    <small>3+ characters, letters, numbers, and underscores only</small>
                </div>
                
                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" value="<?php echo sanitizeOutput($form_data['email']); ?>" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">First Name</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo sanitizeOutput($form_data['first_name']); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Last Name</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo sanitizeOutput($form_data['last_name']); ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo sanitizeOutput($form_data['phone']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" required>
                    <small>At least 6 characters with letters and numbers</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password *</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="auth-btn">Create Account</button>
            </form>
            
            <div class="auth-links">
                <p>Already have an account? <a href="login.php">Log in here</a></p>
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