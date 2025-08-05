<?php
/**
 * Cars Listing Page - Simple Version
 * Used Car Purchase Website
 */

// Include required files
require_once 'php/config.php';
require_once 'php/session-fixed.php';
require_once 'php/navigation-fixed.php';

// Get cars from database
try {
    $conn = getDatabaseConnection();
    $result = $conn->query("SELECT * FROM cars ORDER BY id ASC");
    $cars = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $cars[] = $row;
        }
    }
    $conn->close();
} catch (Exception $e) {
    $cars = [];
    $error_message = "Unable to load car inventory at this time.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Car Inventory - AutoDeals</title>
    <link rel="stylesheet" href="css/theme-default.css" id="theme-link">
    
    <style>
        .cars-hero {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 3rem 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .cars-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            padding: 2rem;
        }
        
        .car-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
        }
        
        .car-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .car-image {
            width: 100%;
            height: 200px;
            background: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            overflow: hidden;
        }
        
        .car-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .car-details {
            padding: 1.5rem;
        }
        
        .car-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }
        
        .car-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .car-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
            color: var(--text-light);
        }
        
        .car-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .btn {
            padding: 0.75rem 1rem;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background: var(--primary-color);
            color: white;
            flex: 1;
        }
        
        .btn-secondary {
            background: var(--secondary-color);
            color: white;
            flex: 1;
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
            margin-bottom: 2rem;
            border: 1px solid #f5c6cb;
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

    <?php echo generateNavigation('cars.php'); ?>

    <main>
        <section class="cars-hero">
            <h2>🚗 Our Car Inventory</h2>
            <p>Find your perfect pre-owned vehicle from our extensive collection</p>
        </section>

        <?php if (isset($error_message)): ?>
            <div class="error-message">
                <strong>⚠️ Notice:</strong> <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <div class="cars-grid">
            <?php if (empty($cars)): ?>
                <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; background-color: #f8f9fa; border-radius: 8px;">
                    <h3>🚗 No Cars Available</h3>
                    <p>We don't have any vehicles in our inventory right now. Please check back soon for new arrivals!</p>
                </div>
            <?php else: ?>
                <?php foreach ($cars as $car): ?>
                    <div class="car-card">
                        <div class="car-image">
                            <?php 
                            // Simple image path handling - compatible with older PHP
                            $image_path = $car['image'];
                            if (substr($image_path, 0, 7) !== 'images/') {
                                $image_path = 'images/cars/' . basename($image_path);
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($image_path); ?>" 
                                 alt="<?php echo htmlspecialchars($car['name']); ?>" 
                                 onerror="this.parentNode.innerHTML='🚗';">
                        </div>
                        <div class="car-details">
                            <div class="car-name"><?php echo htmlspecialchars($car['name']); ?></div>
                            <div class="car-price"><?php echo formatPrice($car['price']); ?></div>
                            <div class="car-info">
                                <div><strong>Year:</strong> <?php echo $car['year']; ?></div>
                                <div><strong>Mileage:</strong> <?php echo htmlspecialchars($car['mileage']); ?></div>
                                <div><strong>Make:</strong> <?php echo htmlspecialchars($car['make']); ?></div>
                                <div><strong>Model:</strong> <?php echo htmlspecialchars($car['model']); ?></div>
                            </div>
                            <p><?php echo htmlspecialchars(substr($car['description'], 0, 100)) . '...'; ?></p>
                            <div class="car-actions">
                                <a href="calculator.php?car_id=<?php echo $car['id']; ?>" class="btn btn-primary">Calculate Payment</a>
                                <a href="inquiry.php?car_id=<?php echo $car['id']; ?>" class="btn btn-secondary">Inquire</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
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