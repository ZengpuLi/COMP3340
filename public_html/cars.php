<?php
/**
 * Cars Page - MySQL Database Integration
 * Used Car Purchase Website
 */

// Include database configuration
require_once '../php/config.php';
require_once '../php/navigation.php';
require_once '../php/user_features.php';

// Handle AJAX requests for user features
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    handleUserFeatureAction();
}

// Initialize variables
$cars = [];
$error_message = '';
$total_cars = 0;

try {
    // Get database connection
    $connection = getDatabaseConnection();
    
    // Prepare and execute query to fetch all cars
    $query = "SELECT id, name, price, year, image, description, mileage, transmission, body_type, make, model, color, fuel_type 
              FROM cars 
              ORDER BY year DESC, price ASC";
    
    $stmt = $connection->prepare($query);
    
    if (!$stmt) {
        throw new Exception("Failed to prepare statement: " . $connection->error);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
                       // Fetch all cars and check user interactions if logged in
               $currentUserId = isLoggedIn() ? $_SESSION['user_id'] : null;
               
               while ($row = $result->fetch_assoc()) {
                   // Add user-specific data if logged in
                   if ($currentUserId) {
                       $row['is_favorited'] = isCarFavorited($currentUserId, $row['id']);
                       $row['is_purchased'] = hasUserPurchasedCar($currentUserId, $row['id']);
                   }
                   $cars[] = $row;
               }
        $total_cars = count($cars);
    } else {
        $error_message = "No cars found in our inventory at the moment. Please check back later!";
    }
    
    $stmt->close();
    $connection->close();
    
} catch (Exception $e) {
    // Log error and display user-friendly message
    error_log("Database error in cars.php: " . $e->getMessage());
    $error_message = "We're experiencing technical difficulties. Please try again later.";
    $cars = []; // Ensure cars array is empty on error
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cars - Used Car Purchase Website</title>
    <link rel="stylesheet" href="css/theme-default.css" id="theme-link">
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

    <?php echo generateNavigation('cars.php'); ?>

    <main>
        <h2>Our Car Inventory</h2>
        <p>Browse our selection of quality pre-owned vehicles. All cars have been thoroughly inspected and come with detailed history reports.</p>
        
        <?php if ($error_message): ?>
            <div class="error-message" style="background-color: #f8d7da; color: #721c24; padding: 1rem; border-radius: 6px; margin-bottom: 2rem; border: 1px solid #f5c6cb;">
                <h3>⚠️ Notice</h3>
                <p><?php echo sanitizeOutput($error_message); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($total_cars > 0): ?>
            <div class="inventory-stats" style="background-color: var(--light-gray); padding: 1rem; border-radius: 6px; margin-bottom: 2rem; text-align: center;">
                <p><strong><?php echo $total_cars; ?> vehicles</strong> currently available in our inventory</p>
            </div>
        <?php endif; ?>
        
        <div class="cars-grid">
            <?php if (!empty($cars)): ?>
                <?php foreach ($cars as $car): ?>
                <div class="car-card">
                    <?php 
                    // Ensure image path is correct
                    $image_path = $car['image'];
                    if (!str_starts_with($image_path, 'images/')) {
                        $image_path = 'images/cars/' . basename($image_path);
                    }
                    ?>
                    <img src="<?php echo sanitizeOutput($image_path); ?>" alt="<?php echo sanitizeOutput($car['name']); ?>" class="car-image" 
                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjBmMGYwIi8+CiAgPHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OTk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkNhciBJbWFnZTwvdGV4dD4KPC9zdmc+'";
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
                               </div>
                               
                               <div class="car-actions">
                                   <button class="view-details-btn" data-car-id="<?php echo $car['id']; ?>">View Details</button>
                                   
                                   <?php if (isLoggedIn()): ?>
                                       <div class="user-actions">
                                           <?php if (isset($car['is_purchased']) && $car['is_purchased']): ?>
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
                                           
                                           <button class="favorite-btn <?php echo (isset($car['is_favorited']) && $car['is_favorited']) ? 'favorited' : ''; ?>" 
                                                   data-car-id="<?php echo $car['id']; ?>">
                                               <?php if (isset($car['is_favorited']) && $car['is_favorited']): ?>
                                                   ❤️ Remove from Favorites
                                               <?php else: ?>
                                                   🤍 Add to Favorites
                                               <?php endif; ?>
                                           </button>
                                       </div>
                                   <?php endif; ?>
                               </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-cars-message" style="text-align: center; padding: 3rem; background-color: var(--light-gray); border-radius: 8px;">
                    <h3>🚗 No Cars Available</h3>
                    <p>We don't have any vehicles in our inventory right now. Please check back soon for new arrivals!</p>
                    <p><a href="contact.html" style="color: var(--secondary-color);">Contact us</a> if you're looking for something specific.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Used Car Purchase Website. All rights reserved.</p>
        <p>123 Auto Sales Drive, Cartown, CA 12345 | Phone: (555) 123-4567</p>
    </footer>

    <script src="js/main.js"></script>
    <script src="js/theme-switcher.js"></script>
    <?php if (isLoggedIn()): ?>
    <script>
        // User Features JavaScript for logged-in users
        document.addEventListener('DOMContentLoaded', function() {
            const csrfToken = '<?php echo generateCSRFToken(); ?>';
            
            // Handle favorite button clicks
            document.querySelectorAll('.favorite-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const carId = this.getAttribute('data-car-id');
                    const isFavorited = this.classList.contains('favorited');
                    const action = isFavorited ? 'remove_favorite' : 'add_favorite';
                    
                    handleUserAction(action, carId, null, this);
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
                
                fetch('cars.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (action === 'add_favorite') {
                            buttonElement.classList.add('favorited');
                            buttonElement.innerHTML = '❤️ Remove from Favorites';
                        } else if (action === 'remove_favorite') {
                            buttonElement.classList.remove('favorited');
                            buttonElement.innerHTML = '🤍 Add to Favorites';
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
    </style>
    <?php endif; ?>
</body>
</html>