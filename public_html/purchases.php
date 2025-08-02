<?php
/**
 * User Purchase History Page
 * Used Car Purchase Website - Display user's purchase history
 */

// Include required files
require_once '../php/session.php';
require_once '../php/config.php';
require_once '../php/navigation.php';
require_once '../php/user_features.php';

// Require login to access this page
requireLogin();

// Get current user
$user = getCurrentUser();
if (!$user) {
    logoutUser();
    header("Location: login.php");
    exit();
}

// Get user's purchase history
$purchases = getUserPurchaseHistory($user['id']);
$purchasesCount = count($purchases);

// Calculate total spent
$totalSpent = 0;
foreach ($purchases as $purchase) {
    $totalSpent += $purchase['purchase_price'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Purchases - Used Car Purchase Website</title>
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
        <?php echo generateHeaderGreeting(); ?>
    </header>

    <?php echo generateNavigation('purchases.php'); ?>

    <main>
        <div class="purchases-container">
            <h2>My Purchase History</h2>
            <p>Here are the cars you've purchased through our platform.</p>
            
            <div class="purchase-stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo $purchasesCount; ?></span>
                    <span class="stat-label">Car<?php echo $purchasesCount !== 1 ? 's' : ''; ?> Purchased</span>
                </div>
                <?php if ($purchasesCount > 0): ?>
                <div class="stat-item">
                    <span class="stat-number"><?php echo formatPrice($totalSpent); ?></span>
                    <span class="stat-label">Total Spent</span>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($purchases)): ?>
                <div class="purchases-grid">
                    <?php foreach ($purchases as $purchase): ?>
                    <div class="purchase-card">
                        <img src="<?php echo sanitizeOutput($purchase['image']); ?>" alt="<?php echo sanitizeOutput($purchase['name']); ?>" class="car-image">
                        <div class="purchase-details">
                            <h3 class="car-name"><?php echo sanitizeOutput($purchase['name']); ?></h3>
                            
                            <div class="purchase-info">
                                <div class="purchase-price">
                                    <span class="label">Purchase Price:</span>
                                    <span class="value"><?php echo formatPrice($purchase['purchase_price']); ?></span>
                                </div>
                                <div class="purchase-date">
                                    <span class="label">Purchase Date:</span>
                                    <span class="value"><?php echo date('F j, Y', strtotime($purchase['purchase_date'])); ?></span>
                                </div>
                                <div class="purchase-status">
                                    <span class="label">Status:</span>
                                    <span class="value status-<?php echo strtolower($purchase['status']); ?>">
                                        <?php echo ucfirst($purchase['status']); ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="car-specs">
                                <span class="spec"><?php echo sanitizeOutput($purchase['mileage']); ?></span>
                                <span class="spec"><?php echo sanitizeOutput($purchase['transmission']); ?></span>
                                <span class="spec"><?php echo sanitizeOutput($purchase['body_type']); ?></span>
                                <?php if (!empty($purchase['color'])): ?>
                                    <span class="spec"><?php echo sanitizeOutput($purchase['color']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($purchase['fuel_type']) && $purchase['fuel_type'] !== 'Gasoline'): ?>
                                    <span class="spec fuel-type"><?php echo sanitizeOutput($purchase['fuel_type']); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="car-meta">
                                <span><?php echo formatYear($purchase['year']); ?> <?php echo sanitizeOutput($purchase['make']); ?> <?php echo sanitizeOutput($purchase['model']); ?></span>
                            </div>
                            
                            <p class="car-description"><?php echo sanitizeOutput($purchase['description']); ?></p>
                            
                            <div class="purchase-actions">
                                <button class="view-details-btn" data-car-id="<?php echo $purchase['id']; ?>">View Details</button>
                                <button class="purchased-btn" disabled>
                                    ✓ Purchased
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="purchase-summary">
                    <h3>Purchase Summary</h3>
                    <div class="summary-stats">
                        <div class="summary-item">
                            <span class="summary-label">Total Vehicles:</span>
                            <span class="summary-value"><?php echo $purchasesCount; ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Total Amount:</span>
                            <span class="summary-value"><?php echo formatPrice($totalSpent); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Average Price:</span>
                            <span class="summary-value"><?php echo formatPrice($totalSpent / $purchasesCount); ?></span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">First Purchase:</span>
                            <span class="summary-value">
                                <?php 
                                $firstPurchase = end($purchases); // Last in array (oldest by date)
                                echo date('F j, Y', strtotime($firstPurchase['purchase_date'])); 
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
                
            <?php else: ?>
                <div class="no-purchases-message">
                    <h3>🛒 No Purchases Yet</h3>
                    <p>You haven't purchased any cars yet.</p>
                    <p><a href="cars.php" class="cta-link">Browse our car inventory</a> to find your perfect vehicle!</p>
                    <p>You can also check your <a href="favorites.php" class="cta-link">saved favorites</a> for cars you're interested in.</p>
                </div>
            <?php endif; ?>
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