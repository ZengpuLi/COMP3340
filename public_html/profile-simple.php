<?php
/**
 * Simple Profile Page
 * Used Car Purchase Website
 */

// Include required files
require_once 'php/config.php';
require_once 'php/session-fixed.php';
require_once 'php/navigation-fixed.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = getCurrentUserId();
$user_info = null;

// Get user information
try {
    $conn = getDatabaseConnection();
    $stmt = $conn->prepare("SELECT username, email, created_at FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_info = $result->fetch_assoc();
    $conn->close();
} catch (Exception $e) {
    $error_message = "Unable to load profile information.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - AutoDeals</title>
    <link rel="stylesheet" href="css/theme-default.css" id="theme-link">
    
    <style>
        .profile-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .profile-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .profile-card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }
        
        .profile-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }
        
        .info-item {
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid var(--primary-color);
        }
        
        .info-label {
            font-weight: 600;
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
        }
        
        .info-value {
            font-size: 1.1rem;
            color: var(--text-dark);
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }
        
        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-secondary {
            background: var(--secondary-color);
            color: white;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        .welcome-message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 2rem;
            border: 1px solid #c3e6cb;
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

    <?php echo generateNavigation('profile.php'); ?>

    <main>
        <section class="profile-hero">
            <h2>👤 My Profile</h2>
            <p>Manage your account information and preferences</p>
        </section>

        <div class="profile-container">
            <div class="welcome-message">
                <strong>Welcome back, <?php echo htmlspecialchars($user_info['username'] ?? 'User'); ?>!</strong>
                <br>Thank you for being a valued member of AutoDeals.
            </div>

            <div class="profile-card">
                <h3>Account Information</h3>
                
                <?php if ($user_info): ?>
                    <div class="profile-info">
                        <div class="info-item">
                            <div class="info-label">Username</div>
                            <div class="info-value"><?php echo htmlspecialchars($user_info['username']); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Email Address</div>
                            <div class="info-value"><?php echo htmlspecialchars($user_info['email']); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Member Since</div>
                            <div class="info-value"><?php echo date('F j, Y', strtotime($user_info['created_at'])); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">Account Status</div>
                            <div class="info-value">✅ Active</div>
                        </div>
                    </div>
                <?php else: ?>
                    <p>Unable to load profile information at this time.</p>
                <?php endif; ?>
            </div>

            <div class="profile-card">
                <h3>Quick Actions</h3>
                <div class="action-buttons">
                    <a href="cars.php" class="btn btn-primary">Browse Cars</a>
                    <a href="calculator.php" class="btn btn-secondary">Loan Calculator</a>
                    <a href="logout.php" class="btn btn-danger">Sign Out</a>
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