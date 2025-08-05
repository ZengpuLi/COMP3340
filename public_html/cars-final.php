<?php
/**
 * Cars Page (Final Safe Version)
 * Used Car Purchase Website
 */

// Include required files
require_once 'php/config.php';
require_once 'php/session-safe.php';
require_once 'php/navigation-safe.php';

$cars = [];
$error_message = '';

try {
    $conn = getDatabaseConnection();
    $result = $conn->query("SELECT id, name, price, year, image, description FROM cars ORDER BY id ASC");
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $cars[] = $row;
        }
    }
    
    $conn->close();
} catch (Exception $e) {
    error_log("Cars page error: " . $e->getMessage());
    $error_message = "Unable to load cars at the moment. Please try again later.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cars - AutoDeals</title>
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
        <section class="cars-section">
            <h2>Available Cars</h2>
            
            <?php if ($error_message): ?>
                <div class="error-message">
                    <p><?php echo sanitizeOutput($error_message); ?></p>
                </div>
            <?php elseif (empty($cars)): ?>
                <div class="no-cars-message">
                    <p>No cars available at the moment. Please check back later.</p>
                </div>
            <?php else: ?>
                <div class="cars-grid">
                    <?php foreach ($cars as $car): ?>
                        <?php
                        // Ensure image path is correct
                        $image_path = $car['image'];
                        if (substr($image_path, 0, 7) !== 'images/') {
                            $image_path = 'images/cars/' . basename($image_path);
                        }
                        ?>
                        <div class="car-card">
                            <img src="<?php echo sanitizeOutput($image_path); ?>" 
                                 alt="<?php echo sanitizeOutput($car['name']); ?>" 
                                 class="car-image"
                                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjBmMGYwIi8+CiAgPHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OTk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkNhciBJbWFnZTwvdGV4dD4KPC9zdmc+'">
                            <div class="car-info">
                                <h3 class="car-name"><?php echo sanitizeOutput($car['name']); ?></h3>
                                <p class="car-price"><?php echo formatPrice($car['price']); ?></p>
                                <p class="car-year">Year: <?php echo formatYear($car['year']); ?></p>
                                <p class="car-description"><?php echo sanitizeOutput($car['description']); ?></p>
                                <button class="btn btn-primary view-details-btn" onclick="viewCarDetails(<?php echo $car['id']; ?>)">
                                    View Details
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Used Car Purchase Website. All rights reserved.</p>
        <p>123 Auto Sales Drive, Cartown, CA 12345 | Phone: (555) 123-4567</p>
    </footer>

    <script src="js/main.js"></script>
    <script src="js/theme-switcher.js"></script>
    <script>
        function viewCarDetails(carId) {
            alert('Car details feature coming soon! Car ID: ' + carId);
        }
    </script>
</body>
</html>