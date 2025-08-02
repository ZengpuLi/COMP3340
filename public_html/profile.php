<?php
/**
 * User Profile Page
 * Used Car Purchase Website - Authentication System
 */

// Include session management
require_once '../php/session.php';
require_once '../php/config.php';

// Require login to access this page
requireLogin();

// Get current user information
$user = getCurrentUser();

if (!$user) {
    // If user data couldn't be retrieved, logout and redirect
    logoutUser();
    header("Location: login.php");
    exit();
}

// Initialize variables for form processing
$errors = [];
$success_message = '';
$form_data = [
    'first_name' => $user['first_name'] ?? '',
    'last_name' => $user['last_name'] ?? '',
    'phone' => $user['phone'] ?? ''
];

// Process profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = "Security token validation failed. Please try again.";
    } else {
        // Sanitize input
        $form_data['first_name'] = sanitizeInput($_POST['first_name'] ?? '');
        $form_data['last_name'] = sanitizeInput($_POST['last_name'] ?? '');
        $form_data['phone'] = sanitizeInput($_POST['phone'] ?? '');
        
        // Validate phone number if provided
        if (!empty($form_data['phone']) && !preg_match('/^[\d\-\+\(\)\s]+$/', $form_data['phone'])) {
            $errors[] = "Please enter a valid phone number";
        }
        
        // Update profile if no errors
        if (empty($errors)) {
            try {
                $connection = getDatabaseConnection();
                
                $query = "UPDATE users SET first_name = ?, last_name = ?, phone = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
                $stmt = $connection->prepare($query);
                $stmt->bind_param("sssi", $form_data['first_name'], $form_data['last_name'], $form_data['phone'], $user['id']);
                
                if ($stmt->execute()) {
                    $success_message = "Profile updated successfully!";
                    // Refresh user data
                    $user = getCurrentUser();
                } else {
                    $errors[] = "Failed to update profile. Please try again.";
                }
                
                $stmt->close();
                $connection->close();
                
            } catch (Exception $e) {
                error_log("Profile update error: " . $e->getMessage());
                $errors[] = "Update failed. Please try again later.";
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
    <title>My Profile - Used Car Purchase Website</title>
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
        <p>Welcome, <?php echo sanitizeOutput(getUserDisplayName()); ?>!</p>
    </header>

    <nav>
        <button class="menu-toggle">☰</button>
        <ul>
            <li><a href="index.html">Home</a></li>
            <li><a href="about.html">About Us</a></li>
            <li><a href="cars.php">Cars</a></li>
            <li><a href="contact.html">Contact Us</a></li>
            <li><a href="help.php">Help</a></li>
            <li><a href="privacy.html">Privacy Policy</a></li>
            <li><a href="profile.php" class="active">My Profile</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>

    <main>
        <div class="profile-container">
            <h2>My Profile</h2>
            
            <?php if (!empty($success_message)): ?>
                <div class="success-message">
                    <h3>✅ Success!</h3>
                    <p><?php echo sanitizeOutput($success_message); ?></p>
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
            
            <div class="profile-info">
                <h3>Account Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Username:</label>
                        <span><?php echo sanitizeOutput($user['username']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Email:</label>
                        <span><?php echo sanitizeOutput($user['email']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Member Since:</label>
                        <span><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Last Login:</label>
                        <span><?php echo $user['last_login'] ? date('F j, Y g:i A', strtotime($user['last_login'])) : 'Never'; ?></span>
                    </div>
                </div>
            </div>
            
            <form method="POST" action="profile.php" class="profile-form">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="update_profile" value="1">
                
                <h3>Update Profile</h3>
                
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
                
                <button type="submit" class="auth-btn">Update Profile</button>
            </form>
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