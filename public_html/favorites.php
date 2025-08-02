<?php
/**
 * User Favorites Page
 * Used Car Purchase Website - Display user's favorite cars
 */

// Include required files
require_once '../php/session.php';
require_once '../php/config.php';
require_once '../php/navigation.php';
require_once '../php/user_features.php';

// Require login to access this page
requireLogin();

// Handle AJAX requests for user features
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    handleUserFeatureAction();
}

// Get current user
$user = getCurrentUser();
if (!$user) {
    logoutUser();
    header("Location: login.php");
    exit();
}

// Get user's favorite cars
$favorites = getUserFavorites($user['id']);
$favoritesCount = count($favorites);

// Add user interaction data to favorites
$currentUserId = $user['id'];
foreach ($favorites as &$car) {
    $car['is_favorited'] = true; // Obviously true since it's in favorites
    $car['is_purchased'] = hasUserPurchasedCar($currentUserId, $car['id']);
}
unset($car); // Break reference
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorites - Used Car Purchase Website</title>
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

    <?php echo generateNavigation('favorites.php'); ?>

    <main>
        <div class="favorites-container">
            <h2>My Favorite Cars</h2>
            <p>Here are the cars you've saved to your favorites list.</p>
            
            <div class="favorites-stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo $favoritesCount; ?></span>
                    <span class="stat-label">Favorite<?php echo $favoritesCount !== 1 ? 's' : ''; ?></span>
                </div>
            </div>
            
            <?php if (!empty($favorites)): ?>
                <div class="cars-grid">
                    <?php foreach ($favorites as $car): ?>
                    <div class="car-card">
                        <img src="<?php echo sanitizeOutput($car['image']); ?>" alt="<?php echo sanitizeOutput($car['name']); ?>" class="car-image">
                        <div class="car-details">
                            <h3 class="car-name"><?php echo sanitizeOutput($car['name']); ?></h3>
                            <p class="car-price"><?php echo formatPrice($car['price']); ?></p>
                            <p class="car-description"><?php echo sanitizeOutput($car['description']); ?></p>
                            <div class="car-specs">
                                <span class="spec"><?php echo sanitizeOutput($car['mileage']); ?></span>
                                <span class="spec"><?php echo sanitizeOutput($car['transmission']); ?></span>
                                <span class="spec"><?php echo sanitizeOutput($car['body_type']); ?></span>
                                <?php if (!empty($car['color'])): ?>
                                    <span class="spec"><?php echo sanitizeOutput($car['color']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($car['fuel_type']) && $car['fuel_type'] !== 'Gasoline'): ?>
                                    <span class="spec fuel-type"><?php echo sanitizeOutput($car['fuel_type']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="car-meta" style="font-size: 0.85rem; color: var(--text-light); margin-bottom: 1rem;">
                                <span><?php echo formatYear($car['year']); ?> <?php echo sanitizeOutput($car['make']); ?> <?php echo sanitizeOutput($car['model']); ?></span>
                                <br>
                                <span style="font-style: italic;">Added to favorites: <?php echo date('M j, Y', strtotime($car['favorited_at'])); ?></span>
                            </div>
                            
                            <div class="car-actions">
                                <button class="view-details-btn" data-car-id="<?php echo $car['id']; ?>">View Details</button>
                                
                                <div class="user-actions">
                                    <?php if ($car['is_purchased']): ?>
                                        <button class="purchased-btn" disabled>
                                            ✓ Purchased
                                        </button>
                                    <?php else: ?>
                                        <button class="purchase-btn" 
                                                data-car-id="<?php echo $car['id']; ?>" 
                                                data-price="<?php echo $car['price']; ?>">
                                            💰 Mark as Purchased
                                        </button>
                                    <?php endif; ?>
                                    
                                    <button class="favorite-btn favorited" 
                                            data-car-id="<?php echo $car['id']; ?>">
                                        ❤️ Remove from Favorites
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-favorites-message">
                    <h3>🤍 No Favorites Yet</h3>
                    <p>You haven't saved any cars to your favorites yet.</p>
                    <p><a href="cars.php" class="cta-link">Browse our car inventory</a> to find vehicles you like!</p>
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
    <script>
        // User Features JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = '<?php echo generateCSRFToken(); ?>';
            
            // Handle favorite button clicks (remove only since these are favorites)
            document.querySelectorAll('.favorite-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const carId = this.getAttribute('data-car-id');
                    
                    if (confirm('Remove this car from your favorites?')) {
                        handleUserAction('remove_favorite', carId, null, this);
                    }
                });
            });
            
            // Handle purchase button clicks
            document.querySelectorAll('.purchase-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const carId = this.getAttribute('data-car-id');
                    const price = this.getAttribute('data-price');
                    
                    if (confirm('Are you sure you want to mark this car as purchased?')) {
                        handleUserAction('mark_purchased', carId, price, this);
                    }
                });
            });
            
            function handleUserAction(action, carId, price, buttonElement) {
                const formData = new FormData();
                formData.append('action', action);
                formData.append('car_id', carId);
                formData.append('csrf_token', csrfToken);
                
                if (price) {
                    formData.append('price', price);
                }
                
                // Disable button during request
                buttonElement.disabled = true;
                const originalText = buttonElement.innerHTML;
                buttonElement.innerHTML = '⏳ Loading...';
                
                fetch('favorites.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (action === 'remove_favorite') {
                            // Remove the entire car card from favorites page
                            const carCard = buttonElement.closest('.car-card');
                            carCard.style.animation = 'fadeOut 0.3s ease-out';
                            setTimeout(() => {
                                carCard.remove();
                                // Update favorites count
                                const countElement = document.querySelector('.stat-number');
                                if (countElement) {
                                    const newCount = parseInt(countElement.textContent) - 1;
                                    countElement.textContent = newCount;
                                    const labelElement = document.querySelector('.stat-label');
                                    if (labelElement) {
                                        labelElement.textContent = 'Favorite' + (newCount !== 1 ? 's' : '');
                                    }
                                }
                                
                                // Show "no favorites" message if this was the last one
                                if (document.querySelectorAll('.car-card').length === 0) {
                                    location.reload();
                                }
                            }, 300);
                        } else if (action === 'mark_purchased') {
                            buttonElement.outerHTML = '<button class="purchased-btn" disabled>✓ Purchased</button>';
                        }
                        
                        // Show success message
                        showMessage(data.message, 'success');
                    } else {
                        // Show error message
                        showMessage(data.message, 'error');
                        buttonElement.innerHTML = originalText;
                    }
                    buttonElement.disabled = false;
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('An error occurred. Please try again.', 'error');
                    buttonElement.innerHTML = originalText;
                    buttonElement.disabled = false;
                });
            }
            
            function showMessage(message, type) {
                // Create temporary message element
                const messageDiv = document.createElement('div');
                messageDiv.className = `message-toast ${type}`;
                messageDiv.innerHTML = message;
                messageDiv.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: ${type === 'success' ? '#d4edda' : '#f8d7da'};
                    color: ${type === 'success' ? '#155724' : '#721c24'};
                    padding: 1rem;
                    border-radius: 6px;
                    border: 1px solid ${type === 'success' ? '#c3e6cb' : '#f5c6cb'};
                    z-index: 1000;
                    animation: slideIn 0.3s ease-out;
                `;
                
                document.body.appendChild(messageDiv);
                
                // Remove after 3 seconds
                setTimeout(() => {
                    messageDiv.remove();
                }, 3000);
            }
        });
    </script>
    <style>
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes fadeOut {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(0.9); }
        }
    </style>
</body>
</html>