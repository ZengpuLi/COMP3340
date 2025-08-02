-- Admin Panel Database Setup
-- Used Car Purchase Website - Step 10
-- Add admin roles, settings, and admin account

-- USE lizengp_car;
-- Note: Make sure you have selected the lizengp_car database in phpMyAdmin before running this script

-- Add role column to users table (check if exists first)
SET @column_exists = (SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = 'lizengp_car' 
    AND TABLE_NAME = 'users' 
    AND COLUMN_NAME = 'role');

SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE users ADD COLUMN role ENUM(''user'', ''admin'') DEFAULT ''user'' AFTER phone;', 
    'SELECT "Column role already exists" AS message;');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

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

-- Create the first admin user (update existing user or create new)
UPDATE users SET role = 'admin' WHERE username = 'admin' OR email = 'admin@autodeals.com';

-- If no admin user exists, create one
INSERT IGNORE INTO users (username, email, password, first_name, last_name, role, created_at) 
VALUES (
    'admin', 
    'admin@autodeals.com', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'Admin', 
    'User', 
    'admin', 
    NOW()
);

-- Log the admin setup
INSERT INTO admin_activity_log (admin_user_id, action, details, ip_address, created_at)
SELECT id, 'admin_setup', 'Initial admin panel setup completed', '127.0.0.1', NOW()
FROM users WHERE role = 'admin' LIMIT 1;