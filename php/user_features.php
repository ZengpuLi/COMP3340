<?php
/**
 * User Features Helper Functions
 * Used Car Purchase Website - Favorites and Purchase History
 */

// Include required dependencies
require_once 'config.php';
require_once 'session.php';

/**
 * Check if a car is in user's favorites
 * @param int $userId
 * @param int $carId
 * @return bool
 */
function isCarFavorited($userId, $carId) {
    try {
        $connection = getDatabaseConnection();
        $query = "SELECT id FROM favorites WHERE user_id = ? AND car_id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ii", $userId, $carId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $isFavorited = $result->num_rows > 0;
        
        $stmt->close();
        $connection->close();
        
        return $isFavorited;
        
    } catch (Exception $e) {
        error_log("Error checking favorite status: " . $e->getMessage());
        return false;
    }
}

/**
 * Add car to user's favorites
 * @param int $userId
 * @param int $carId
 * @return bool
 */
function addToFavorites($userId, $carId) {
    try {
        $connection = getDatabaseConnection();
        $query = "INSERT IGNORE INTO favorites (user_id, car_id) VALUES (?, ?)";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ii", $userId, $carId);
        
        $success = $stmt->execute();
        
        $stmt->close();
        $connection->close();
        
        return $success;
        
    } catch (Exception $e) {
        error_log("Error adding to favorites: " . $e->getMessage());
        return false;
    }
}

/**
 * Remove car from user's favorites
 * @param int $userId
 * @param int $carId
 * @return bool
 */
function removeFromFavorites($userId, $carId) {
    try {
        $connection = getDatabaseConnection();
        $query = "DELETE FROM favorites WHERE user_id = ? AND car_id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ii", $userId, $carId);
        
        $success = $stmt->execute();
        
        $stmt->close();
        $connection->close();
        
        return $success;
        
    } catch (Exception $e) {
        error_log("Error removing from favorites: " . $e->getMessage());
        return false;
    }
}

/**
 * Get user's favorite cars with full car details
 * @param int $userId
 * @return array
 */
function getUserFavorites($userId) {
    try {
        $connection = getDatabaseConnection();
        $query = "SELECT c.*, f.created_at as favorited_at 
                  FROM cars c 
                  INNER JOIN favorites f ON c.id = f.car_id 
                  WHERE f.user_id = ? 
                  ORDER BY f.created_at DESC";
        
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $favorites = [];
        
        while ($row = $result->fetch_assoc()) {
            $favorites[] = $row;
        }
        
        $stmt->close();
        $connection->close();
        
        return $favorites;
        
    } catch (Exception $e) {
        error_log("Error getting user favorites: " . $e->getMessage());
        return [];
    }
}

/**
 * Check if user has already purchased a car
 * @param int $userId
 * @param int $carId
 * @return bool
 */
function hasUserPurchasedCar($userId, $carId) {
    try {
        $connection = getDatabaseConnection();
        $query = "SELECT id FROM purchase_history WHERE user_id = ? AND car_id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ii", $userId, $carId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $hasPurchased = $result->num_rows > 0;
        
        $stmt->close();
        $connection->close();
        
        return $hasPurchased;
        
    } catch (Exception $e) {
        error_log("Error checking purchase status: " . $e->getMessage());
        return false;
    }
}

/**
 * Add car purchase to history
 * @param int $userId
 * @param int $carId
 * @param float $price
 * @return bool
 */
function addPurchaseToHistory($userId, $carId, $price) {
    try {
        $connection = getDatabaseConnection();
        $query = "INSERT INTO purchase_history (user_id, car_id, purchase_date, purchase_price) VALUES (?, ?, CURDATE(), ?)";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("iid", $userId, $carId, $price);
        
        $success = $stmt->execute();
        
        $stmt->close();
        $connection->close();
        
        return $success;
        
    } catch (Exception $e) {
        error_log("Error adding purchase to history: " . $e->getMessage());
        return false;
    }
}

/**
 * Get user's purchase history with full car details
 * @param int $userId
 * @return array
 */
function getUserPurchaseHistory($userId) {
    try {
        $connection = getDatabaseConnection();
        $query = "SELECT c.*, p.purchase_date, p.purchase_price, p.status, p.created_at as purchased_at 
                  FROM cars c 
                  INNER JOIN purchase_history p ON c.id = p.car_id 
                  WHERE p.user_id = ? 
                  ORDER BY p.purchase_date DESC, p.created_at DESC";
        
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $purchases = [];
        
        while ($row = $result->fetch_assoc()) {
            $purchases[] = $row;
        }
        
        $stmt->close();
        $connection->close();
        
        return $purchases;
        
    } catch (Exception $e) {
        error_log("Error getting user purchase history: " . $e->getMessage());
        return [];
    }
}

/**
 * Get user's favorites count
 * @param int $userId
 * @return int
 */
function getUserFavoritesCount($userId) {
    try {
        $connection = getDatabaseConnection();
        $query = "SELECT COUNT(*) as count FROM favorites WHERE user_id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count = $row['count'];
        
        $stmt->close();
        $connection->close();
        
        return $count;
        
    } catch (Exception $e) {
        error_log("Error getting favorites count: " . $e->getMessage());
        return 0;
    }
}

/**
 * Get user's purchases count
 * @param int $userId
 * @return int
 */
function getUserPurchasesCount($userId) {
    try {
        $connection = getDatabaseConnection();
        $query = "SELECT COUNT(*) as count FROM purchase_history WHERE user_id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $count = $row['count'];
        
        $stmt->close();
        $connection->close();
        
        return $count;
        
    } catch (Exception $e) {
        error_log("Error getting purchases count: " . $e->getMessage());
        return 0;
    }
}

/**
 * Handle AJAX requests for favorites and purchases
 */
function handleUserFeatureAction() {
    // Check if this is an AJAX request
    if (!isset($_POST['action']) || !isLoggedIn()) {
        return;
    }
    
    $userId = $_SESSION['user_id'];
    $action = $_POST['action'];
    $carId = intval($_POST['car_id'] ?? 0);
    
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        echo json_encode(['success' => false, 'message' => 'Security token validation failed']);
        exit();
    }
    
    $response = ['success' => false, 'message' => 'Unknown action'];
    
    switch ($action) {
        case 'add_favorite':
            if (addToFavorites($userId, $carId)) {
                $response = ['success' => true, 'message' => 'Added to favorites', 'action' => 'added'];
            } else {
                $response = ['success' => false, 'message' => 'Failed to add to favorites'];
            }
            break;
            
        case 'remove_favorite':
            if (removeFromFavorites($userId, $carId)) {
                $response = ['success' => true, 'message' => 'Removed from favorites', 'action' => 'removed'];
            } else {
                $response = ['success' => false, 'message' => 'Failed to remove from favorites'];
            }
            break;
            
        case 'mark_purchased':
            $price = floatval($_POST['price'] ?? 0);
            if ($price > 0 && addPurchaseToHistory($userId, $carId, $price)) {
                $response = ['success' => true, 'message' => 'Car marked as purchased', 'action' => 'purchased'];
            } else {
                $response = ['success' => false, 'message' => 'Failed to mark as purchased'];
            }
            break;
    }
    
    echo json_encode($response);
    exit();
}

// Handle AJAX requests if this file is called directly
if ($_SERVER['REQUEST_METHOD'] === 'POST' && basename($_SERVER['PHP_SELF']) === 'user_features.php') {
    handleUserFeatureAction();
}
?>