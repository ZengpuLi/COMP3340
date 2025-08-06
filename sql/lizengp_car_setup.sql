-- Complete Database Setup for AutoDeals Used Car Website
-- This file sets up everything for the existing lizengp_car database
-- IMPORTANT: Select the lizengp_car database in phpMyAdmin BEFORE running this script
-- Do NOT include any CREATE DATABASE or USE statements

-- ============================================
-- STEP 1: CREATE CARS TABLE
-- ============================================

CREATE TABLE IF NOT EXISTS cars (
    id INT(11) NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    year INT(4) NOT NULL,
    image VARCHAR(500) NOT NULL,
    description TEXT NOT NULL,
    mileage VARCHAR(50) NOT NULL,
    transmission VARCHAR(50) NOT NULL,
    body_type VARCHAR(50) NOT NULL,
    make VARCHAR(100) NOT NULL,
    model VARCHAR(100) NOT NULL,
    color VARCHAR(50) DEFAULT NULL,
    fuel_type VARCHAR(50) DEFAULT 'Gasoline',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    INDEX idx_year (year),
    INDEX idx_price (price),
    INDEX idx_make (make)
);

-- Insert sample car data (only cars with working images from screenshot)
INSERT IGNORE INTO cars (id, name, price, year, image, description, mileage, transmission, body_type, make, model, color, fuel_type) VALUES
(1, '2020 Kia Rio LX', 14900.00, 2020, 'images/cars/kia-rio-2020.jpg', 'Subcompact with big value and long warranty. Perfect first car with modern safety features.', '31,000 miles', 'CVT Automatic', 'Sedan', 'Kia', 'Rio', 'Clear White', 'Gasoline'),
(2, '2020 Nissan Sentra SV', 18500.00, 2020, 'images/cars/nissan-altima-2020.jpg', 'Nearly new with warranty remaining. Features advanced driver assistance and premium interior amenities.', '25,000 miles', 'CVT Automatic', 'Sedan', 'Nissan', 'Sentra', 'Red', 'Gasoline'),
(3, '2020 Mazda3 Preferred', 21500.00, 2020, 'images/cars/mazda3-2020.jpg', 'Premium compact with luxury feel. Outstanding driving dynamics and upscale interior materials.', '22,000 miles', '6-Speed Automatic', 'Sedan', 'Mazda', 'Mazda3', 'Deep Crystal Blue', 'Gasoline'),
(4, '2020 Volkswagen Passat 2.0T', 22800.00, 2020, 'images/cars/toyota-corolla-2018.jpg', 'Full-size sedan with European sophistication. Spacious interior with advanced technology features.', '26,000 miles', '6-Speed Automatic', 'Sedan', 'Volkswagen', 'Passat', 'Pure White', 'Gasoline'),
(5, '2020 Honda Accord LX', 24500.00, 2020, 'images/cars/nissan-altima-2018.jpg', 'Top-rated mid-size sedan with advanced safety features. Spacious interior with premium materials.', '28,000 miles', 'CVT Automatic', 'Sedan', 'Honda', 'Accord', 'Radiant Red', 'Gasoline'),
(6, '2019 Volkswagen Jetta S', 17600.00, 2019, 'images/cars/audi-a4-2019.jpg', 'European engineering with spacious interior. Features modern design and efficient performance.', '35,000 miles', '8-Speed Automatic', 'Sedan', 'Volkswagen', 'Jetta', 'Tornado Red', 'Gasoline'),
(7, '2019 Toyota Corolla LE', 17800.00, 2019, 'images/cars/toyota-corolla-2018.jpg', 'Like-new condition with advanced safety features. Perfect for daily commuting with outstanding fuel economy.', '32,000 miles', 'CVT Automatic', 'Sedan', 'Toyota', 'Corolla', 'White', 'Gasoline'),
(8, '2019 Mazda CX-5 Sport', 23800.00, 2019, 'images/cars/jeep-wrangler-2018.jpg', 'Compact SUV with premium feel and excellent driving dynamics. All-wheel drive available.', '36,000 miles', '6-Speed Automatic', 'SUV', 'Mazda', 'CX-5', 'Soul Red Crystal', 'Gasoline'),
(9, '2018 Honda Civic LX', 16500.00, 2018, 'images/cars/honda-civic-2019.jpg', 'Reliable and fuel-efficient compact sedan with excellent safety ratings. One owner vehicle with complete maintenance records.', '45,000 miles', 'CVT Automatic', 'Sedan', 'Honda', 'Civic', 'Silver', 'Gasoline'),
(10, '2018 Nissan Altima 2.5 S', 19400.00, 2018, 'images/cars/nissan-altima-2020.jpg', 'Mid-size sedan with smooth ride and spacious cabin. Features modern infotainment system.', '48,000 miles', 'CVT Automatic', 'Sedan', 'Nissan', 'Altima', 'Gun Metallic', 'Gasoline'),
(11, '2018 Toyota Camry LE', 20800.00, 2018, 'images/cars/honda-accord-2020.jpg', 'Redesigned model with bold styling and improved fuel economy. Reliable mid-size sedan choice.', '42,000 miles', '8-Speed Automatic', 'Sedan', 'Toyota', 'Camry', 'Midnight Black', 'Gasoline'),
(12, '2018 Subaru Outback 2.5i', 22500.00, 2018, 'images/cars/subaru-outback-2018.jpg', 'Versatile crossover with standard all-wheel drive. Perfect for outdoor adventures and daily use.', '44,000 miles', 'CVT Automatic', 'Crossover', 'Subaru', 'Outback', 'Wilderness Green', 'Gasoline'),
(13, '2017 Subaru Impreza Premium', 18900.00, 2017, 'images/cars/subaru-impreza-2017.jpg', 'All-wheel drive standard with excellent safety scores. Perfect for all weather conditions.', '62,000 miles', 'CVT Automatic', 'Sedan', 'Subaru', 'Impreza', 'Ice Silver', 'Gasoline'),
(14, '2017 Ford Escape SE', 19200.00, 2017, 'images/cars/ford-escape-2018.jpg', 'Spacious SUV with all-wheel drive capability. Great for families with plenty of cargo space and modern technology.', '58,000 miles', '6-Speed Automatic', 'SUV', 'Ford', 'Escape', 'Blue', 'Gasoline');

-- ============================================
-- STEP 2: CREATE USERS TABLE
-- ============================================

CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50) DEFAULT NULL,
    last_name VARCHAR(50) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY unique_username (username),
    UNIQUE KEY unique_email (email),
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
);

-- Insert sample users including admin
INSERT IGNORE INTO users (id, username, email, password, first_name, last_name, phone, role, is_active) VALUES
(1, 'admin', 'admin@autodeals.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', '(555) 123-4567', 'admin', 1),
(2, 'johndoe', 'john.doe@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Doe', '(555) 234-5678', 'user', 1),
(3, 'janesmith', 'jane.smith@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Jane', 'Smith', '(555) 345-6789', 'user', 1),
(4, 'mikejohnson', 'mike.johnson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mike', 'Johnson', '(555) 456-7890', 'user', 1),
(5, 'sarahwilson', 'sarah.wilson@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah', 'Wilson', '(555) 567-8901', 'user', 1);

-- ============================================
-- STEP 3: CREATE USER FEATURE TABLES
-- ============================================

-- Create favorites table
CREATE TABLE IF NOT EXISTS favorites (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    car_id INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, car_id),
    INDEX idx_user_id (user_id),
    INDEX idx_car_id (car_id)
);

-- Create purchase_history table
CREATE TABLE IF NOT EXISTS purchase_history (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    car_id INT(11) NOT NULL,
    purchase_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    price DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50) DEFAULT 'Cash',
    financing_term INT(3) DEFAULT NULL,
    down_payment DECIMAL(10,2) DEFAULT NULL,
    monthly_payment DECIMAL(10,2) DEFAULT NULL,
    notes TEXT,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_car_id (car_id),
    INDEX idx_purchase_date (purchase_date)
);

-- Insert sample favorites
INSERT IGNORE INTO favorites (user_id, car_id, created_at) VALUES
(2, 1, '2024-01-15 10:30:00'),
(2, 8, '2024-01-15 11:45:00'),
(2, 12, '2024-01-16 09:20:00'),
(3, 3, '2024-01-16 14:15:00'),
(3, 7, '2024-01-17 16:30:00'),
(4, 5, '2024-01-18 08:45:00'),
(4, 11, '2024-01-18 13:20:00'),
(4, 15, '2024-01-19 10:10:00'),
(5, 2, '2024-01-20 12:30:00'),
(5, 9, '2024-01-20 15:45:00');

-- Insert sample purchase history
INSERT IGNORE INTO purchase_history (user_id, car_id, purchase_date, price, payment_method, financing_term, down_payment, monthly_payment, notes) VALUES
(2, 13, '2024-01-10 14:30:00', 13800.00, 'Financing', 48, 2760.00, 243.75, 'Excellent first car purchase with 4-year financing'),
(3, 16, '2024-01-08 11:15:00', 14900.00, 'Cash', NULL, 14900.00, NULL, 'Cash purchase, very satisfied with the vehicle'),
(4, 6, '2024-01-05 16:45:00', 16800.00, 'Financing', 36, 5040.00, 358.33, 'Great deal on certified pre-owned vehicle');

-- ============================================
-- STEP 4: CREATE ADMIN TABLES
-- ============================================

-- Create site_settings table for admin configuration
CREATE TABLE IF NOT EXISTS site_settings (
    id INT(11) NOT NULL AUTO_INCREMENT,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    setting_description VARCHAR(255),
    category VARCHAR(50) DEFAULT 'general',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unique_setting (setting_key),
    INDEX idx_category (category)
);

-- Create admin activity log table
CREATE TABLE IF NOT EXISTS admin_activity_log (
    id INT(11) NOT NULL AUTO_INCREMENT,
    admin_user_id INT(11) NOT NULL,
    action VARCHAR(100) NOT NULL,
    target_type VARCHAR(50),
    target_id INT(11),
    details TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (admin_user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_admin_user (admin_user_id),
    INDEX idx_action (action),
    INDEX idx_created_at (created_at)
);

-- Insert default site settings
INSERT IGNORE INTO site_settings (setting_key, setting_value, setting_description, category) VALUES
('site_name', 'AutoDeals', 'Website name displayed in header', 'general'),
('site_description', 'Quality Used Cars for Sale', 'Website description for SEO', 'general'),
('default_theme', 'default', 'Default theme for new visitors', 'appearance'),
('cars_per_page', '12', 'Number of cars to display per page', 'display'),
('enable_user_registration', '1', 'Allow new user registrations', 'user_management'),
('require_email_verification', '0', 'Require email verification for new accounts', 'user_management'),
('maintenance_mode', '0', 'Enable maintenance mode', 'system'),
('contact_email', 'support@autodeals.com', 'Main contact email address', 'contact'),
('contact_phone', '(555) 123-4567', 'Main contact phone number', 'contact'),
('business_hours', 'Mon-Sat 9AM-8PM, Sun 10AM-6PM', 'Business hours display', 'contact'),
('address', '123 Auto Sales Drive, Cartown, CA 12345', 'Business address', 'contact');

-- ============================================
-- STEP 5: CREATE FORMS TABLES
-- ============================================

-- Create inquiries table for car inquiry form submissions
CREATE TABLE IF NOT EXISTS inquiries (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NULL,
    car_id INT(11) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NULL,
    inquiry_type ENUM('general', 'test_drive', 'financing', 'trade_in') DEFAULT 'general',
    contact_preference ENUM('email', 'phone', 'either') DEFAULT 'either',
    message TEXT NOT NULL,
    status ENUM('new', 'contacted', 'in_progress', 'completed', 'closed') DEFAULT 'new',
    admin_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_car_id (car_id),
    INDEX idx_status (status),
    INDEX idx_inquiry_type (inquiry_type),
    INDEX idx_created_at (created_at)
);

-- Create calculator_sessions table for tracking loan calculations
CREATE TABLE IF NOT EXISTS calculator_sessions (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NULL,
    car_id INT(11) NULL,
    vehicle_price DECIMAL(10,2) NOT NULL,
    down_payment DECIMAL(10,2) NOT NULL,
    loan_term INT(3) NOT NULL,
    interest_rate DECIMAL(5,3) NOT NULL,
    monthly_payment DECIMAL(10,2) NOT NULL,
    total_payment DECIMAL(12,2) NOT NULL,
    total_interest DECIMAL(10,2) NOT NULL,
    session_id VARCHAR(100) NULL,
    ip_address VARCHAR(45) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_car_id (car_id),
    INDEX idx_session_id (session_id),
    INDEX idx_created_at (created_at)
);

-- Insert sample inquiry data
INSERT IGNORE INTO inquiries (user_id, car_id, name, email, phone, inquiry_type, contact_preference, message, status) VALUES
(2, 1, 'John Doe', 'john.doe@email.com', '(555) 234-5678', 'test_drive', 'phone', 'I am interested in scheduling a test drive for this Honda Civic. What times are available this weekend?', 'new'),
(3, 8, 'Jane Smith', 'jane.smith@email.com', '(555) 345-6789', 'financing', 'email', 'Could you provide financing options for this Mazda3? I have good credit and can put down 20%.', 'contacted'),
(NULL, 12, 'Robert Wilson', 'robert.wilson@email.com', '(555) 123-9876', 'general', 'either', 'Is this Honda Accord still available? What is the vehicle history?', 'new'),
(4, 3, 'Mike Johnson', 'mike.johnson@email.com', '(555) 456-7890', 'trade_in', 'phone', 'I have a 2015 Toyota Camry to trade in. Can you provide an estimate for trade value?', 'in_progress'),
(NULL, 20, 'Lisa Brown', 'lisa.brown@email.com', NULL, 'general', 'email', 'What warranty options are available for this Volkswagen Passat?', 'new');

-- Insert sample calculator sessions (using CONCAT for session_id to avoid function issues)
INSERT IGNORE INTO calculator_sessions (user_id, car_id, vehicle_price, down_payment, loan_term, interest_rate, monthly_payment, total_payment, total_interest, session_id, ip_address) VALUES
(2, 1, 16500.00, 3300.00, 48, 4.500, 300.45, 14421.60, 1221.60, 'sess_user2_car1_001', '192.168.1.100'),
(3, 8, 21500.00, 4300.00, 60, 5.200, 325.78, 19546.80, 2346.80, 'sess_user3_car8_001', '192.168.1.101'),
(4, 12, 24500.00, 4900.00, 36, 3.900, 578.92, 20841.12, 1241.12, 'sess_user4_car12_001', '192.168.1.102'),
(NULL, 5, 14900.00, 2980.00, 42, 6.100, 345.67, 14518.14, 2598.14, 'sess_guest_car5_001', '192.168.1.103'),
(2, 18, 23800.00, 7140.00, 48, 4.200, 380.25, 18252.00, 1592.00, 'sess_user2_car18_001', '192.168.1.100');

-- ============================================
-- FINAL STEP: LOG SETUP COMPLETION
-- ============================================

-- Log the complete setup (insert a record showing setup completion)
INSERT IGNORE INTO admin_activity_log (admin_user_id, action, details, ip_address, created_at)
VALUES (1, 'database_setup', 'Complete database setup completed with all tables and sample data', '127.0.0.1', NOW());

-- Show success message
SELECT 'AutoDeals Database Setup Completed Successfully!' AS setup_status,
       'All 8 tables created with sample data' AS tables_created,
       'Admin login: username=admin, password=password' AS admin_credentials,
       '20 sample cars, 5 users, admin panel ready' AS sample_data;