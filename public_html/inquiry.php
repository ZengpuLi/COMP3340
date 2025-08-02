<?php
/**
 * Car Inquiry Form
 * Used Car Purchase Website - Contact Sellers Form
 */

// Include session management and configuration
require_once '../php/session.php';
require_once '../php/navigation.php';
require_once '../php/config.php';

// Initialize variables
$success_message = '';
$error_message = '';
$selected_car = null;
$car_id = intval($_GET['car_id'] ?? 0);

// Get car information if car_id is provided
if ($car_id > 0) {
    try {
        $connection = getDatabaseConnection();
        $query = "SELECT id, name, make, model, year, price, image, description FROM cars WHERE id = ?";
        $stmt = $connection->prepare($query);
        $stmt->bind_param("i", $car_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $selected_car = $result->fetch_assoc();
        
        $stmt->close();
        $connection->close();
    } catch (Exception $e) {
        error_log("Error fetching car for inquiry: " . $e->getMessage());
        $error_message = "Could not load car information.";
    }
}

// Get current user information for prefilling
$current_user = null;
if (isLoggedIn()) {
    $current_user = getCurrentUser();
}

// Get all cars for dropdown
$cars = [];
try {
    $connection = getDatabaseConnection();
    $query = "SELECT id, name, make, model, year, price FROM cars ORDER BY make, model, year";
    $result = $connection->query($query);
    
    while ($row = $result->fetch_assoc()) {
        $cars[] = $row;
    }
    
    $connection->close();
} catch (Exception $e) {
    error_log("Error fetching cars for dropdown: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error_message = "Security token validation failed. Please try again.";
    } else {
        // Sanitize and validate input
        $name = sanitizeInput($_POST['name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $inquiry_car_id = intval($_POST['car_id'] ?? 0);
        $message = sanitizeInput($_POST['message'] ?? '');
        $inquiry_type = sanitizeInput($_POST['inquiry_type'] ?? 'general');
        $preferred_contact = sanitizeInput($_POST['preferred_contact'] ?? 'email');
        
        // Validation
        $errors = [];
        
        if (empty($name) || strlen($name) < 2) {
            $errors[] = "Name must be at least 2 characters long";
        }
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Valid email address is required";
        }
        
        if (!empty($phone) && !preg_match('/^[\+]?[1-9][\d]{0,15}$/', preg_replace('/[\s\-\(\)]/', '', $phone))) {
            $errors[] = "Phone number format is invalid";
        }
        
        if ($inquiry_car_id <= 0) {
            $errors[] = "Please select a car";
        }
        
        if (empty($message) || strlen($message) < 10) {
            $errors[] = "Message must be at least 10 characters long";
        }
        
        if (!in_array($inquiry_type, ['general', 'test_drive', 'financing', 'trade_in'])) {
            $inquiry_type = 'general';
        }
        
        if (!in_array($preferred_contact, ['email', 'phone', 'both'])) {
            $preferred_contact = 'email';
        }
        
        // Save inquiry if no errors
        if (empty($errors)) {
            try {
                $connection = getDatabaseConnection();
                
                // Verify car exists
                $carCheck = $connection->prepare("SELECT name FROM cars WHERE id = ?");
                $carCheck->bind_param("i", $inquiry_car_id);
                $carCheck->execute();
                $carResult = $carCheck->get_result();
                $car = $carResult->fetch_assoc();
                $carCheck->close();
                
                if (!$car) {
                    $errors[] = "Selected car is no longer available";
                } else {
                    // Insert inquiry
                    $query = "INSERT INTO inquiries (user_id, car_id, name, email, phone, message, inquiry_type, preferred_contact) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    
                    $stmt = $connection->prepare($query);
                    $user_id = isLoggedIn() ? $_SESSION['user_id'] : null;
                    
                    $stmt->bind_param("iissssss", 
                        $user_id, $inquiry_car_id, $name, $email, $phone, $message, $inquiry_type, $preferred_contact
                    );
                    
                    if ($stmt->execute()) {
                        $inquiry_id = $connection->insert_id;
                        $stmt->close();
                        $connection->close();
                        
                        // Log admin activity if user is logged in
                        if (isLoggedIn()) {
                            logAdminActivity('create', 'inquiries', $inquiry_id, "Car inquiry submitted for: {$car['name']}");
                        }
                        
                        $success_message = "Your inquiry has been submitted successfully! We will contact you soon via " . 
                                         ($preferred_contact === 'both' ? 'email or phone' : $preferred_contact) . ".";
                        
                        // Clear form data on success
                        $_POST = [];
                        
                        // Optional: Send email notification to admin
                        // This would be implemented with a proper mail service
                        
                    } else {
                        $stmt->close();
                        $connection->close();
                        $error_message = "Failed to submit inquiry. Please try again.";
                    }
                }
            } catch (Exception $e) {
                error_log("Error saving inquiry: " . $e->getMessage());
                $error_message = "Database error occurred. Please try again later.";
            }
        } else {
            $error_message = implode('<br>', $errors);
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
    <title>Car Inquiry - Used Car Purchase Website</title>
    <link rel="stylesheet" href="../css/theme-default.css" id="theme-link">
    
    <style>
        /* Inquiry form specific styles */
        .inquiry-hero {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .inquiry-hero h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .inquiry-hero p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .inquiry-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-bottom: 3rem;
        }
        
        .inquiry-form {
            background: var(--white);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
        }
        
        .car-details {
            background: var(--white);
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: 1px solid var(--border-color);
        }
        
        .form-section {
            margin-bottom: 2rem;
        }
        
        .form-section h3 {
            color: var(--primary-color);
            margin-bottom: 1rem;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-dark);
        }
        
        .required {
            color: #e74c3c;
        }
        
        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="tel"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--border-color);
            border-radius: 6px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--secondary-color);
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        
        .radio-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-top: 0.5rem;
        }
        
        .radio-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem;
            background: var(--light-gray);
            border-radius: 6px;
            border: 2px solid var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .radio-item:hover {
            border-color: var(--secondary-color);
        }
        
        .radio-item input[type="radio"] {
            margin: 0;
        }
        
        .radio-item.selected {
            border-color: var(--secondary-color);
            background: rgba(52, 152, 219, 0.1);
        }
        
        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        
        .selected-car-info {
            background: var(--light-gray);
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
        }
        
        .car-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .car-image {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }
        
        .car-info {
            flex: 1;
        }
        
        .car-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.25rem;
        }
        
        .car-price {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--secondary-color);
        }
        
        .car-description {
            color: var(--text-dark);
            line-height: 1.5;
            margin-top: 1rem;
        }
        
        .contact-info {
            background: var(--light-gray);
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 2rem;
            border: 1px solid var(--border-color);
        }
        
        .contact-info h4 {
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
            color: var(--text-dark);
        }
        
        .contact-item:last-child {
            margin-bottom: 0;
        }
        
        .contact-icon {
            width: 20px;
            text-align: center;
            color: var(--secondary-color);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1.5rem;
            border: 1px solid transparent;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
        
        .form-note {
            background: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 6px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            color: #1565c0;
            font-size: 0.9rem;
        }
        
        /* Responsive design */
        @media (max-width: 768px) {
            .inquiry-hero {
                padding: 3rem 1rem;
            }
            
            .inquiry-hero h2 {
                font-size: 2rem;
            }
            
            .inquiry-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .radio-group {
                grid-template-columns: 1fr;
            }
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
        <div class="logo-text" style="font-size: 2rem; font-weight: bold; color: #3498db; margin-bottom: 0.5rem;">🚗 AutoDeals</div>
        <h1>Used Car Purchase Website</h1>
        <?php echo generateHeaderGreeting(); ?>
    </header>

    <?php echo generateNavigation('inquiry.php'); ?>

    <main>
        <section class="inquiry-hero">
            <h2>📧 Car Inquiry</h2>
            <p>Interested in one of our vehicles? Send us a message and we'll get back to you with all the details you need.</p>
        </section>

        <!-- Success/Error Messages -->
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                ✅ <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                ❌ <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="inquiry-container">
            <div class="inquiry-form">
                <form method="POST" action="inquiry.php<?php echo $car_id ? "?car_id=$car_id" : ''; ?>" id="inquiryForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                    
                    <div class="form-section">
                        <h3>👤 Your Information</h3>
                        
                        <?php if ($current_user): ?>
                        <div class="form-note">
                            ℹ️ <strong>Logged in as:</strong> <?php echo sanitizeOutput(getUserDisplayName($current_user)); ?>
                            <br>Your information has been pre-filled below.
                        </div>
                        <?php endif; ?>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Full Name <span class="required">*</span></label>
                                <input type="text" id="name" name="name" 
                                       value="<?php echo sanitizeOutput($_POST['name'] ?? ($current_user ? trim($current_user['first_name'] . ' ' . $current_user['last_name']) : '')); ?>" 
                                       required maxlength="100">
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address <span class="required">*</span></label>
                                <input type="email" id="email" name="email" 
                                       value="<?php echo sanitizeOutput($_POST['email'] ?? ($current_user['email'] ?? '')); ?>" 
                                       required maxlength="255">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" 
                                   value="<?php echo sanitizeOutput($_POST['phone'] ?? ($current_user['phone_number'] ?? '')); ?>" 
                                   placeholder="(555) 123-4567" maxlength="20">
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>🚗 Vehicle Interest</h3>
                        
                        <div class="form-group">
                            <label for="car_id">Select Vehicle <span class="required">*</span></label>
                            <select id="car_id" name="car_id" required>
                                <option value="">Choose a vehicle...</option>
                                <?php foreach ($cars as $car): ?>
                                    <option value="<?php echo $car['id']; ?>" 
                                            <?php echo ($car['id'] == ($car_id ?: ($_POST['car_id'] ?? 0))) ? 'selected' : ''; ?>>
                                        <?php echo sanitizeOutput($car['year'] . ' ' . $car['make'] . ' ' . $car['model'] . ' - ' . formatPrice($car['price'])); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Inquiry Type <span class="required">*</span></label>
                            <div class="radio-group">
                                <label class="radio-item">
                                    <input type="radio" name="inquiry_type" value="general" 
                                           <?php echo ($_POST['inquiry_type'] ?? 'general') === 'general' ? 'checked' : ''; ?>>
                                    <span>General Question</span>
                                </label>
                                <label class="radio-item">
                                    <input type="radio" name="inquiry_type" value="test_drive" 
                                           <?php echo ($_POST['inquiry_type'] ?? '') === 'test_drive' ? 'checked' : ''; ?>>
                                    <span>Schedule Test Drive</span>
                                </label>
                                <label class="radio-item">
                                    <input type="radio" name="inquiry_type" value="financing" 
                                           <?php echo ($_POST['inquiry_type'] ?? '') === 'financing' ? 'checked' : ''; ?>>
                                    <span>Financing Options</span>
                                </label>
                                <label class="radio-item">
                                    <input type="radio" name="inquiry_type" value="trade_in" 
                                           <?php echo ($_POST['inquiry_type'] ?? '') === 'trade_in' ? 'checked' : ''; ?>>
                                    <span>Trade-in Value</span>
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Your Message <span class="required">*</span></label>
                            <textarea id="message" name="message" required 
                                      placeholder="Please tell us about your interest in this vehicle, any questions you have, or additional information we should know..."><?php echo sanitizeOutput($_POST['message'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <div class="form-section">
                        <h3>📞 Contact Preferences</h3>
                        
                        <div class="form-group">
                            <label>Preferred Contact Method <span class="required">*</span></label>
                            <div class="radio-group">
                                <label class="radio-item">
                                    <input type="radio" name="preferred_contact" value="email" 
                                           <?php echo ($_POST['preferred_contact'] ?? 'email') === 'email' ? 'checked' : ''; ?>>
                                    <span>📧 Email</span>
                                </label>
                                <label class="radio-item">
                                    <input type="radio" name="preferred_contact" value="phone" 
                                           <?php echo ($_POST['preferred_contact'] ?? '') === 'phone' ? 'checked' : ''; ?>>
                                    <span>📞 Phone</span>
                                </label>
                                <label class="radio-item">
                                    <input type="radio" name="preferred_contact" value="both" 
                                           <?php echo ($_POST['preferred_contact'] ?? '') === 'both' ? 'checked' : ''; ?>>
                                    <span>📧📞 Either</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-btn" id="submitBtn">
                        📨 Send Inquiry
                    </button>
                </form>
            </div>
            
            <div class="car-details">
                <?php if ($selected_car): ?>
                <div class="selected-car-info">
                    <h3 style="color: var(--primary-color); margin-bottom: 1rem;">🎯 Selected Vehicle</h3>
                    <div class="car-header">
                        <img src="<?php echo sanitizeOutput($selected_car['image']); ?>" 
                             alt="<?php echo sanitizeOutput($selected_car['name']); ?>" 
                             class="car-image">
                        <div class="car-info">
                            <div class="car-name"><?php echo sanitizeOutput($selected_car['name']); ?></div>
                            <div class="car-price"><?php echo formatPrice($selected_car['price']); ?></div>
                        </div>
                    </div>
                    <div class="car-description">
                        <?php echo sanitizeOutput($selected_car['description']); ?>
                    </div>
                </div>
                
                <div style="text-align: center; margin: 2rem 0;">
                    <a href="calculator.php?car_id=<?php echo $car_id; ?>" class="action-btn btn-secondary" 
                       style="display: inline-block; padding: 0.75rem 1.5rem; background: transparent; color: var(--secondary-color); border: 2px solid var(--secondary-color); border-radius: 6px; text-decoration: none; font-weight: 600;">
                        💰 Calculate Loan Payment
                    </a>
                </div>
                <?php endif; ?>
                
                <div class="contact-info">
                    <h4>📞 Contact Information</h4>
                    
                    <div class="contact-item">
                        <span class="contact-icon">📞</span>
                        <span>(555) 123-4567</span>
                    </div>
                    
                    <div class="contact-item">
                        <span class="contact-icon">📧</span>
                        <span>sales@autodeals.com</span>
                    </div>
                    
                    <div class="contact-item">
                        <span class="contact-icon">📍</span>
                        <span>123 Auto Sales Drive, Cartown, CA 12345</span>
                    </div>
                    
                    <div class="contact-item">
                        <span class="contact-icon">🕒</span>
                        <span>Mon-Sat: 9AM-8PM, Sun: 10AM-6PM</span>
                    </div>
                </div>
                
                <div class="form-note" style="margin-top: 1.5rem;">
                    <strong>💡 Quick Response Guarantee</strong><br>
                    We typically respond to inquiries within 2 hours during business hours and within 24 hours on weekends.
                </div>
            </div>
        </div>
    </main>

    <footer>
        <p>&copy; 2024 Used Car Purchase Website. All rights reserved.</p>
        <p>123 Auto Sales Drive, Cartown, CA 12345 | Phone: (555) 123-4567</p>
    </footer>

    <script src="../js/main.js"></script>
    <script src="../js/theme-switcher.js"></script>
    
    <script>
        // Inquiry form JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('inquiryForm');
            const submitBtn = document.getElementById('submitBtn');
            
            // Radio button styling
            const radioButtons = document.querySelectorAll('input[type="radio"]');
            radioButtons.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Remove selected class from all items in this group
                    const groupName = this.name;
                    document.querySelectorAll(`input[name="${groupName}"]`).forEach(r => {
                        r.closest('.radio-item').classList.remove('selected');
                    });
                    
                    // Add selected class to current item
                    this.closest('.radio-item').classList.add('selected');
                });
                
                // Set initial state
                if (radio.checked) {
                    radio.closest('.radio-item').classList.add('selected');
                }
            });
            
            // Form validation
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let isValid = true;
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.style.borderColor = '#e74c3c';
                    } else {
                        field.style.borderColor = '';
                    }
                });
                
                // Check if at least one radio button is selected for required groups
                const requiredRadioGroups = ['inquiry_type', 'preferred_contact'];
                requiredRadioGroups.forEach(groupName => {
                    const checkedRadio = form.querySelector(`input[name="${groupName}"]:checked`);
                    if (!checkedRadio) {
                        isValid = false;
                        // Highlight the radio group
                        const radioGroup = form.querySelector(`input[name="${groupName}"]`).closest('.radio-group');
                        radioGroup.style.borderColor = '#e74c3c';
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                    return;
                }
                
                // Show loading state
                submitBtn.disabled = true;
                submitBtn.innerHTML = '⏳ Sending...';
            });
            
            // Phone number formatting
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                if (value.length >= 6) {
                    value = value.replace(/(\d{3})(\d{3})(\d{4})/, '($1) $2-$3');
                } else if (value.length >= 3) {
                    value = value.replace(/(\d{3})(\d{0,3})/, '($1) $2');
                }
                this.value = value;
            });
            
            // Auto-resize textarea
            const messageTextarea = document.getElementById('message');
            messageTextarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = this.scrollHeight + 'px';
            });
            
            // Character counter for message
            const messageCounter = document.createElement('div');
            messageCounter.style.cssText = 'font-size: 0.8rem; color: #6c757d; text-align: right; margin-top: 0.25rem;';
            messageTextarea.parentNode.appendChild(messageCounter);
            
            function updateMessageCounter() {
                const length = messageTextarea.value.length;
                messageCounter.textContent = `${length} characters (minimum 10)`;
                if (length < 10) {
                    messageCounter.style.color = '#e74c3c';
                } else {
                    messageCounter.style.color = '#28a745';
                }
            }
            
            messageTextarea.addEventListener('input', updateMessageCounter);
            updateMessageCounter();
        });
    </script>
</body>
</html>