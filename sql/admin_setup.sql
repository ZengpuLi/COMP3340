-- Admin Panel Database Setup
-- Used Car Purchase Website - Step 10
-- Add admin roles, settings, and admin account

USE used_cars;

-- Add role column to users table
ALTER TABLE users ADD COLUMN role ENUM('user', 'admin') DEFAULT 'user' AFTER phone;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Update existing admin user to have admin role
UPDATE users SET role = 'admin' WHERE username = 'admin';

-- Create additional admin account for testing
INSERT IGNORE INTO users (username, email, password, first_name, last_name, role, is_active) VALUES 
('superadmin', 'superadmin@autodeals.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super', 'Admin', 'admin', 1);

-- Insert default site settings
INSERT IGNORE INTO site_settings (setting_key, setting_value, setting_description, category) VALUES 
('default_theme', 'default', 'Default theme for new visitors', 'appearance'),
('site_name', 'AutoDeals', 'Website name displayed in header', 'general'),
('site_tagline', 'Find Your Perfect Pre-Owned Vehicle', 'Website tagline', 'general'),
('cars_per_page', '20', 'Number of cars to display per page', 'display'),
('enable_user_registration', '1', 'Allow new user registration', 'users'),
('maintenance_mode', '0', 'Enable maintenance mode', 'system'),
('admin_email', 'admin@autodeals.com', 'Primary admin email address', 'general'),
('max_file_upload_size', '5242880', 'Maximum file upload size in bytes (5MB)', 'files'),
('allowed_image_types', 'jpg,jpeg,png,gif,svg', 'Allowed image file types', 'files');

-- Create admin activity log table for audit trail
CREATE TABLE IF NOT EXISTS admin_activity_log (
    id INT(11) NOT NULL AUTO_INCREMENT,
    admin_user_id INT(11) NOT NULL,
    action VARCHAR(100) NOT NULL,
    target_table VARCHAR(50),
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample admin activity for testing
INSERT IGNORE INTO admin_activity_log (admin_user_id, action, target_table, details, ip_address) VALUES 
(1, 'login', NULL, 'Admin login successful', '127.0.0.1'),
(1, 'create', 'cars', 'Added new car: Toyota Camry 2019', '127.0.0.1'),
(1, 'update', 'users', 'Updated user role for user ID 2', '127.0.0.1');

-- Display confirmation and statistics
SELECT 'Admin setup completed successfully!' as Status;
SELECT 
    (SELECT COUNT(*) FROM users WHERE role = 'admin') as 'Admin Users',
    (SELECT COUNT(*) FROM users WHERE role = 'user') as 'Regular Users',
    (SELECT COUNT(*) FROM site_settings) as 'Site Settings',
    (SELECT COUNT(*) FROM admin_activity_log) as 'Admin Activities';

-- Show admin accounts
SELECT id, username, email, first_name, last_name, role, is_active, created_at 
FROM users 
WHERE role = 'admin' 
ORDER BY created_at;