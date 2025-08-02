<?php
/**
 * Admin Car Management
 * Used Car Purchase Website - Admin Panel Car CRUD Operations
 */

// Include admin session management
require_once '../../php/admin-session.php';
require_once '../../php/config.php';

// Require admin access
requireAdmin();

// Initialize variables
$cars = [];
$error_message = '';
$success_message = '';
$action = $_GET['action'] ?? 'list';
$car_id = intval($_GET['id'] ?? 0);
$page = max(1, intval($_GET['page'] ?? 1));
$per_page = 20;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error_message = "Security token validation failed. Please try again.";
    } else {
        $post_action = $_POST['action'] ?? '';
        
        switch ($post_action) {
            case 'create':
                $result = createCar($_POST);
                if ($result['success']) {
                    $success_message = $result['message'];
                    $action = 'list';
                } else {
                    $error_message = $result['message'];
                }
                break;
                
            case 'update':
                $result = updateCar($_POST);
                if ($result['success']) {
                    $success_message = $result['message'];
                    $action = 'list';
                } else {
                    $error_message = $result['message'];
                }
                break;
                
            case 'delete':
                $result = deleteCar(intval($_POST['car_id'] ?? 0));
                if ($result['success']) {
                    $success_message = $result['message'];
                } else {
                    $error_message = $result['message'];
                }
                break;
        }
    }
}

// Car CRUD Functions
function createCar($data) {
    try {
        $connection = getDatabaseConnection();
        
        // Validate input
        $validation = validateCarData($data);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['message']];
        }
        
        // Insert car
        $query = "INSERT INTO cars (name, make, model, year, price, mileage, transmission, body_type, fuel_type, color, description, image) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $connection->prepare($query);
        $stmt->bind_param("sssiisssssss",
            $data['name'],
            $data['make'],
            $data['model'],
            $data['year'],
            $data['price'],
            $data['mileage'],
            $data['transmission'],
            $data['body_type'],
            $data['fuel_type'],
            $data['color'],
            $data['description'],
            $data['image']
        );
        
        if ($stmt->execute()) {
            $car_id = $connection->insert_id;
            $stmt->close();
            $connection->close();
            
            // Log admin activity
            logAdminActivity('create', 'cars', $car_id, "Added new car: {$data['name']}");
            
            return ['success' => true, 'message' => 'Car added successfully!'];
        } else {
            $stmt->close();
            $connection->close();
            return ['success' => false, 'message' => 'Failed to add car. Please try again.'];
        }
        
    } catch (Exception $e) {
        error_log("Error creating car: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred.'];
    }
}

function updateCar($data) {
    try {
        $connection = getDatabaseConnection();
        
        // Validate input
        $validation = validateCarData($data);
        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['message']];
        }
        
        $car_id = intval($data['car_id'] ?? 0);
        if ($car_id <= 0) {
            return ['success' => false, 'message' => 'Invalid car ID.'];
        }
        
        // Update car
        $query = "UPDATE cars SET name = ?, make = ?, model = ?, year = ?, price = ?, mileage = ?, 
                  transmission = ?, body_type = ?, fuel_type = ?, color = ?, description = ?, image = ?, 
                  updated_at = CURRENT_TIMESTAMP WHERE id = ?";
        
        $stmt = $connection->prepare($query);
        $stmt->bind_param("sssiisssssssi",
            $data['name'],
            $data['make'],
            $data['model'],
            $data['year'],
            $data['price'],
            $data['mileage'],
            $data['transmission'],
            $data['body_type'],
            $data['fuel_type'],
            $data['color'],
            $data['description'],
            $data['image'],
            $car_id
        );
        
        if ($stmt->execute()) {
            $stmt->close();
            $connection->close();
            
            // Log admin activity
            logAdminActivity('update', 'cars', $car_id, "Updated car: {$data['name']}");
            
            return ['success' => true, 'message' => 'Car updated successfully!'];
        } else {
            $stmt->close();
            $connection->close();
            return ['success' => false, 'message' => 'Failed to update car. Please try again.'];
        }
        
    } catch (Exception $e) {
        error_log("Error updating car: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred.'];
    }
}

function deleteCar($car_id) {
    try {
        $connection = getDatabaseConnection();
        
        if ($car_id <= 0) {
            return ['success' => false, 'message' => 'Invalid car ID.'];
        }
        
        // Get car name for logging
        $nameQuery = "SELECT name FROM cars WHERE id = ?";
        $nameStmt = $connection->prepare($nameQuery);
        $nameStmt->bind_param("i", $car_id);
        $nameStmt->execute();
        $nameResult = $nameStmt->get_result();
        $car = $nameResult->fetch_assoc();
        $carName = $car ? $car['name'] : "Car ID $car_id";
        $nameStmt->close();
        
        // Delete car (this will cascade to favorites and purchase_history due to foreign keys)
        $query = "DELETE FROM cars WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $car_id);
        
        if ($stmt->execute()) {
            $stmt->close();
            $connection->close();
            
            // Log admin activity
            logAdminActivity('delete', 'cars', $car_id, "Deleted car: $carName");
            
            return ['success' => true, 'message' => 'Car deleted successfully!'];
        } else {
            $stmt->close();
            $connection->close();
            return ['success' => false, 'message' => 'Failed to delete car. Please try again.'];
        }
        
    } catch (Exception $e) {
        error_log("Error deleting car: " . $e->getMessage());
        return ['success' => false, 'message' => 'Database error occurred.'];
    }
}

function validateCarData($data) {
    $errors = [];
    
    // Required fields
    if (empty($data['name'])) $errors[] = "Car name is required";
    if (empty($data['make'])) $errors[] = "Make is required";
    if (empty($data['model'])) $errors[] = "Model is required";
    if (empty($data['year']) || !is_numeric($data['year']) || $data['year'] < 1900 || $data['year'] > date('Y') + 1) {
        $errors[] = "Valid year is required";
    }
    if (empty($data['price']) || !is_numeric($data['price']) || $data['price'] < 0) {
        $errors[] = "Valid price is required";
    }
    if (empty($data['mileage'])) $errors[] = "Mileage is required";
    if (empty($data['transmission'])) $errors[] = "Transmission is required";
    if (empty($data['body_type'])) $errors[] = "Body type is required";
    if (empty($data['description'])) $errors[] = "Description is required";
    if (empty($data['image'])) $errors[] = "Image path is required";
    
    return [
        'valid' => empty($errors),
        'message' => empty($errors) ? '' : implode(', ', $errors)
    ];
}

// Get cars for listing
if ($action === 'list') {
    try {
        $connection = getDatabaseConnection();
        
        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM cars";
        $countResult = $connection->query($countQuery);
        $totalCars = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($totalCars / $per_page);
        
        // Get cars for current page
        $offset = ($page - 1) * $per_page;
        $query = "SELECT * FROM cars ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("ii", $per_page, $offset);
        $stmt->execute();
        
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $cars[] = $row;
        }
        
        $stmt->close();
        $connection->close();
        
    } catch (Exception $e) {
        error_log("Error getting cars: " . $e->getMessage());
        $error_message = "Failed to load cars.";
    }
}

// Get single car for editing
$editCar = null;
if ($action === 'edit' && $car_id > 0) {
    try {
        $connection = getDatabaseConnection();
        $query = "SELECT * FROM cars WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $car_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $editCar = $result->fetch_assoc();
        
        $stmt->close();
        $connection->close();
        
        if (!$editCar) {
            $error_message = "Car not found.";
            $action = 'list';
        }
        
    } catch (Exception $e) {
        error_log("Error getting car for edit: " . $e->getMessage());
        $error_message = "Failed to load car.";
        $action = 'list';
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
    <title>Manage Cars - AutoDeals Administration</title>
    <link rel="stylesheet" href="../../css/theme-default.css" id="theme-link">
    <link rel="stylesheet" href="../admin-styles.css">
</head>
<body class="admin-body">
    <div class="admin-layout">
        <!-- Admin Sidebar -->
        <nav class="admin-sidebar">
            <div class="admin-sidebar-header">
                <div class="admin-logo">
                    <span class="logo-icon">🚗</span>
                    <span class="logo-text">AutoDeals</span>
                </div>
                <div class="admin-subtitle">Administration</div>
            </div>
            
            <div class="admin-user-info">
                <div class="admin-avatar">👤</div>
                <div class="admin-details">
                    <div class="admin-name"><?php echo sanitizeOutput(getUserDisplayName()); ?></div>
                    <div class="admin-role">Administrator</div>
                </div>
            </div>
            
            <ul class="admin-nav">
                <li><a href="dashboard.php">📊 Dashboard</a></li>
                <li><a href="cars.php" class="active">🚗 Manage Cars</a></li>
                <li><a href="users.php">👥 Manage Users</a></li>
                <li><a href="themes.php">🎨 Theme Settings</a></li>
                <li><a href="settings.php">⚙️ Site Settings</a></li>
                <li><a href="logs.php">📋 Activity Logs</a></li>
                <li class="nav-divider"></li>
                <li><a href="../index.html">🌐 View Site</a></li>
                <li><a href="../logout.php">🚪 Logout</a></li>
            </ul>
        </nav>
        
        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1>
                    <?php 
                    switch ($action) {
                        case 'add': echo 'Add New Car'; break;
                        case 'edit': echo 'Edit Car'; break;
                        default: echo 'Manage Cars'; break;
                    }
                    ?>
                </h1>
                <div class="admin-header-actions">
                    <?php if ($action === 'list'): ?>
                        <a href="cars.php?action=add" class="admin-btn admin-btn-primary">
                            ➕ Add New Car
                        </a>
                    <?php else: ?>
                        <a href="cars.php" class="admin-btn admin-btn-secondary">
                            ← Back to Cars
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Success/Error Messages -->
            <?php if ($success_message): ?>
                <div class="admin-alert admin-alert-success">
                    ✅ <?php echo sanitizeOutput($success_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="admin-alert admin-alert-danger">
                    ❌ <?php echo sanitizeOutput($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($action === 'list'): ?>
                <!-- Cars List -->
                <div class="admin-section">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h2>Car Inventory (<?php echo number_format($totalCars ?? 0); ?> cars)</h2>
                    </div>
                    
                    <?php if (!empty($cars)): ?>
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Car Details</th>
                                    <th>Price</th>
                                    <th>Year</th>
                                    <th>Specs</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cars as $car): ?>
                                <tr>
                                    <td>
                                        <img src="<?php echo sanitizeOutput($car['image']); ?>" 
                                             alt="<?php echo sanitizeOutput($car['name']); ?>" 
                                             style="width: 80px; height: 60px; object-fit: cover; border-radius: 4px;">
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; margin-bottom: 0.25rem;">
                                            <?php echo sanitizeOutput($car['name']); ?>
                                        </div>
                                        <div style="color: #6c757d; font-size: 0.9rem;">
                                            <?php echo sanitizeOutput($car['make'] . ' ' . $car['model']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-weight: 600; color: #28a745;">
                                            <?php echo formatPrice($car['price']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo sanitizeOutput($car['year']); ?></td>
                                    <td>
                                        <div style="font-size: 0.85rem; color: #6c757d;">
                                            <?php echo sanitizeOutput($car['mileage']); ?><br>
                                            <?php echo sanitizeOutput($car['transmission']); ?><br>
                                            <?php echo sanitizeOutput($car['body_type']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <a href="cars.php?action=edit&id=<?php echo $car['id']; ?>" 
                                               class="admin-btn admin-btn-sm admin-btn-secondary">
                                                ✏️ Edit
                                            </a>
                                            <form method="POST" style="display: inline;" 
                                                  onsubmit="return confirm('Are you sure you want to delete this car? This action cannot be undone.');">
                                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
                                                <button type="submit" class="admin-btn admin-btn-sm admin-btn-danger">
                                                    🗑️ Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <div class="admin-pagination">
                                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <?php if ($i === $page): ?>
                                        <span class="current"><?php echo $i; ?></span>
                                    <?php else: ?>
                                        <a href="cars.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <div style="text-align: center; padding: 3rem; color: #6c757d;">
                            <div style="font-size: 3rem; margin-bottom: 1rem;">🚗</div>
                            <h3>No Cars Found</h3>
                            <p>Start by adding your first car to the inventory.</p>
                            <a href="cars.php?action=add" class="admin-btn admin-btn-primary">Add First Car</a>
                        </div>
                    <?php endif; ?>
                </div>
                
            <?php elseif ($action === 'add' || $action === 'edit'): ?>
                <!-- Add/Edit Car Form -->
                <form method="POST" class="admin-form">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    <input type="hidden" name="action" value="<?php echo $action === 'edit' ? 'update' : 'create'; ?>">
                    <?php if ($action === 'edit'): ?>
                        <input type="hidden" name="car_id" value="<?php echo $car_id; ?>">
                    <?php endif; ?>
                    
                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="name">Car Name *</label>
                            <input type="text" id="name" name="name" 
                                   value="<?php echo sanitizeOutput($editCar['name'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="admin-form-group">
                            <label for="year">Year *</label>
                            <input type="number" id="year" name="year" min="1900" max="<?php echo date('Y') + 1; ?>"
                                   value="<?php echo sanitizeOutput($editCar['year'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="make">Make *</label>
                            <input type="text" id="make" name="make" 
                                   value="<?php echo sanitizeOutput($editCar['make'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="admin-form-group">
                            <label for="model">Model *</label>
                            <input type="text" id="model" name="model" 
                                   value="<?php echo sanitizeOutput($editCar['model'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="price">Price *</label>
                            <input type="number" id="price" name="price" min="0" step="0.01"
                                   value="<?php echo sanitizeOutput($editCar['price'] ?? ''); ?>" required>
                        </div>
                        
                        <div class="admin-form-group">
                            <label for="mileage">Mileage *</label>
                            <input type="text" id="mileage" name="mileage" placeholder="e.g., 45,000 miles"
                                   value="<?php echo sanitizeOutput($editCar['mileage'] ?? ''); ?>" required>
                        </div>
                    </div>
                    
                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="transmission">Transmission *</label>
                            <select id="transmission" name="transmission" required>
                                <option value="">Select Transmission</option>
                                <option value="Manual" <?php echo ($editCar['transmission'] ?? '') === 'Manual' ? 'selected' : ''; ?>>Manual</option>
                                <option value="Automatic" <?php echo ($editCar['transmission'] ?? '') === 'Automatic' ? 'selected' : ''; ?>>Automatic</option>
                                <option value="CVT" <?php echo ($editCar['transmission'] ?? '') === 'CVT' ? 'selected' : ''; ?>>CVT</option>
                            </select>
                        </div>
                        
                        <div class="admin-form-group">
                            <label for="body_type">Body Type *</label>
                            <select id="body_type" name="body_type" required>
                                <option value="">Select Body Type</option>
                                <option value="Sedan" <?php echo ($editCar['body_type'] ?? '') === 'Sedan' ? 'selected' : ''; ?>>Sedan</option>
                                <option value="SUV" <?php echo ($editCar['body_type'] ?? '') === 'SUV' ? 'selected' : ''; ?>>SUV</option>
                                <option value="Hatchback" <?php echo ($editCar['body_type'] ?? '') === 'Hatchback' ? 'selected' : ''; ?>>Hatchback</option>
                                <option value="Coupe" <?php echo ($editCar['body_type'] ?? '') === 'Coupe' ? 'selected' : ''; ?>>Coupe</option>
                                <option value="Convertible" <?php echo ($editCar['body_type'] ?? '') === 'Convertible' ? 'selected' : ''; ?>>Convertible</option>
                                <option value="Pickup" <?php echo ($editCar['body_type'] ?? '') === 'Pickup' ? 'selected' : ''; ?>>Pickup</option>
                                <option value="Wagon" <?php echo ($editCar['body_type'] ?? '') === 'Wagon' ? 'selected' : ''; ?>>Wagon</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="admin-form-row">
                        <div class="admin-form-group">
                            <label for="fuel_type">Fuel Type</label>
                            <select id="fuel_type" name="fuel_type">
                                <option value="Gasoline" <?php echo ($editCar['fuel_type'] ?? 'Gasoline') === 'Gasoline' ? 'selected' : ''; ?>>Gasoline</option>
                                <option value="Hybrid" <?php echo ($editCar['fuel_type'] ?? '') === 'Hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                                <option value="Electric" <?php echo ($editCar['fuel_type'] ?? '') === 'Electric' ? 'selected' : ''; ?>>Electric</option>
                                <option value="Diesel" <?php echo ($editCar['fuel_type'] ?? '') === 'Diesel' ? 'selected' : ''; ?>>Diesel</option>
                            </select>
                        </div>
                        
                        <div class="admin-form-group">
                            <label for="color">Color</label>
                            <input type="text" id="color" name="color" 
                                   value="<?php echo sanitizeOutput($editCar['color'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <div class="admin-form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" required><?php echo sanitizeOutput($editCar['description'] ?? ''); ?></textarea>
                    </div>
                    
                    <div class="admin-form-group">
                        <label for="image">Image Path *</label>
                        <input type="text" id="image" name="image" placeholder="../../images/cars/car-name.svg"
                               value="<?php echo sanitizeOutput($editCar['image'] ?? ''); ?>" required>
                        <small style="color: #6c757d; font-size: 0.85rem;">
                            Enter the relative path to the car image (e.g., ../../images/cars/toyota-camry-2019.svg)
                        </small>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                        <button type="submit" class="admin-btn admin-btn-primary">
                            <?php echo $action === 'edit' ? '💾 Update Car' : '➕ Add Car'; ?>
                        </button>
                        <a href="cars.php" class="admin-btn admin-btn-secondary">Cancel</a>
                    </div>
                </form>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>