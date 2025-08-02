-- Forms and Calculator Database Setup
-- Used Car Purchase Website - Step 12
-- Create inquiries table for car inquiry forms

USE used_cars;

-- Create inquiries table for storing car inquiries
CREATE TABLE IF NOT EXISTS inquiries (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NULL,
    car_id INT(11) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    message TEXT NOT NULL,
    inquiry_type ENUM('general', 'test_drive', 'financing', 'trade_in') DEFAULT 'general',
    status ENUM('new', 'contacted', 'scheduled', 'completed', 'closed') DEFAULT 'new',
    preferred_contact ENUM('email', 'phone', 'both') DEFAULT 'email',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_car_id (car_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create calculator sessions table (optional - for saving calculations)
CREATE TABLE IF NOT EXISTS calculator_sessions (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NULL,
    car_id INT(11) NULL,
    car_price DECIMAL(10, 2) NOT NULL,
    down_payment DECIMAL(10, 2) NOT NULL,
    loan_term INT(2) NOT NULL,
    interest_rate DECIMAL(5, 2) NOT NULL,
    monthly_payment DECIMAL(10, 2) NOT NULL,
    total_payment DECIMAL(10, 2) NOT NULL,
    total_interest DECIMAL(10, 2) NOT NULL,
    session_ip VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_car_id (car_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample inquiry data for testing
INSERT IGNORE INTO inquiries (user_id, car_id, name, email, phone, message, inquiry_type, preferred_contact) VALUES 
(2, 1, 'John Smith', 'john.smith@email.com', '(555) 123-4567', 'I am interested in this vehicle and would like to schedule a test drive. Please contact me at your earliest convenience.', 'test_drive', 'both'),
(2, 3, 'Sarah Johnson', 'sarah.j@email.com', '(555) 987-6543', 'Can you provide more information about the maintenance history and any recent repairs? Also interested in financing options.', 'financing', 'email'),
(NULL, 5, 'Mike Wilson', 'mike.wilson@email.com', '(555) 456-7890', 'Hi, I saw this car online and I am very interested. I have a trade-in vehicle as well. When would be a good time to visit?', 'trade_in', 'phone'),
(3, 2, 'Lisa Chen', 'lisa.chen@email.com', NULL, 'This car looks perfect for my family. Could you tell me more about the safety features and fuel efficiency?', 'general', 'email'),
(NULL, 4, 'David Rodriguez', 'david.r@email.com', '(555) 234-5678', 'I would like to know if this vehicle is still available and if you offer any warranty or extended service plans.', 'general', 'both');

-- Insert sample calculator sessions for testing
INSERT IGNORE INTO calculator_sessions (user_id, car_id, car_price, down_payment, loan_term, interest_rate, monthly_payment, total_payment, total_interest, session_ip) VALUES 
(2, 1, 25000.00, 5000.00, 5, 4.50, 372.86, 22371.60, 2371.60, '127.0.0.1'),
(2, 3, 18500.00, 3000.00, 4, 5.25, 356.89, 17130.72, 1630.72, '127.0.0.1'),
(3, 2, 32000.00, 8000.00, 6, 3.75, 369.42, 26597.20, 2597.20, '127.0.0.1'),
(NULL, 5, 21000.00, 4000.00, 5, 5.50, 324.17, 19450.20, 2450.20, '192.168.1.100'),
(NULL, 4, 28000.00, 6000.00, 5, 4.25, 407.78, 24466.80, 2466.80, '192.168.1.101');

-- Display confirmation and statistics
SELECT 'Forms and Calculator setup completed successfully!' as Status;
SELECT 
    (SELECT COUNT(*) FROM inquiries) as 'Total Inquiries',
    (SELECT COUNT(*) FROM inquiries WHERE status = 'new') as 'New Inquiries',
    (SELECT COUNT(*) FROM calculator_sessions) as 'Calculator Sessions',
    (SELECT COUNT(DISTINCT car_id) FROM inquiries) as 'Cars with Inquiries';

-- Show recent inquiries
SELECT i.id, i.name, i.email, c.name as car_name, i.inquiry_type, i.status, i.created_at 
FROM inquiries i 
JOIN cars c ON i.car_id = c.id 
ORDER BY i.created_at DESC 
LIMIT 5;