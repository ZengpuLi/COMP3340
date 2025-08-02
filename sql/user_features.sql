-- User Features Database Tables
-- Used Car Purchase Website - Step 9
-- Favorites and Purchase History functionality

USE used_cars;

-- Create favorites table
CREATE TABLE IF NOT EXISTS favorites (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    car_id INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY unique_user_car_favorite (user_id, car_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_car_id (car_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create purchase_history table
CREATE TABLE IF NOT EXISTS purchase_history (
    id INT(11) NOT NULL AUTO_INCREMENT,
    user_id INT(11) NOT NULL,
    car_id INT(11) NOT NULL,
    purchase_date DATE NOT NULL,
    purchase_price DECIMAL(10, 2) NOT NULL,
    status VARCHAR(20) DEFAULT 'completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_car_id (car_id),
    INDEX idx_purchase_date (purchase_date),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample favorites for testing (admin user favorites)
INSERT IGNORE INTO favorites (user_id, car_id) VALUES 
(1, 1), -- admin likes Toyota Camry 2019
(1, 5), -- admin likes Ford F-150 2018  
(1, 12); -- admin likes Lexus RX350 2017

-- Insert sample favorites for testing (testuser favorites)
INSERT IGNORE INTO favorites (user_id, car_id) VALUES 
(2, 3), -- testuser likes Honda Civic 2020
(2, 8), -- testuser likes BMW X5 2017
(2, 15); -- testuser likes Audi A4 2019

-- Insert sample purchase history for testing
INSERT IGNORE INTO purchase_history (user_id, car_id, purchase_date, purchase_price) VALUES 
(1, 20, '2024-01-15', 22000.00), -- admin bought Subaru Outback
(1, 18, '2024-02-20', 35000.00), -- admin bought Mercedes C300
(2, 10, '2024-03-10', 28000.00), -- testuser bought Toyota RAV4
(2, 16, '2024-03-25', 24000.00); -- testuser bought Volkswagen Jetta

-- Display confirmation and counts
SELECT 'User features tables created successfully!' as Status;
SELECT 
    (SELECT COUNT(*) FROM favorites) as 'Total Favorites',
    (SELECT COUNT(*) FROM purchase_history) as 'Total Purchases',
    (SELECT COUNT(DISTINCT user_id) FROM favorites) as 'Users with Favorites',
    (SELECT COUNT(DISTINCT user_id) FROM purchase_history) as 'Users with Purchases';