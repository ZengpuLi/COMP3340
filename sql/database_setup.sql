-- MySQL Database Setup for Used Car Purchase Website
-- Run these commands in your MySQL environment

-- Create the database
CREATE DATABASE IF NOT EXISTS used_cars CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Use the database
USE used_cars;

-- Create the cars table
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert sample car data (20+ records)
INSERT INTO cars (name, price, year, image, description, mileage, transmission, body_type, make, model, color, fuel_type) VALUES
('Toyota Corolla 2018', 16995.00, 2018, '../images/cars/toyota-corolla-2018.svg', 'Reliable compact sedan with excellent fuel economy. Clean history, one owner.', '45,000 miles', 'Automatic', '4-Door', 'Toyota', 'Corolla', 'Silver', 'Gasoline'),
('Honda Civic 2019', 18750.00, 2019, '../images/cars/honda-civic-2019.svg', 'Sporty and efficient with advanced safety features. Excellent condition throughout.', '32,000 miles', 'Manual', '4-Door', 'Honda', 'Civic', 'Blue', 'Gasoline'),
('Ford F-150 2017', 24995.00, 2017, '../images/cars/ford-f150-2017.svg', 'Powerful pickup truck with 4WD capability. Perfect for work and family use.', '68,000 miles', 'Automatic', 'Crew Cab', 'Ford', 'F-150', 'Black', 'Gasoline'),
('BMW X3 2019', 31500.00, 2019, '../images/cars/bmw-x3-2019.svg', 'Luxury SUV with premium features and all-wheel drive. Meticulously maintained.', '28,000 miles', 'Automatic', 'SUV', 'BMW', 'X3', 'White', 'Gasoline'),
('Mazda CX-5 2020', 22750.00, 2020, '../images/cars/mazda-cx5-2020.svg', 'Stylish crossover SUV with excellent handling and fuel efficiency. Like new condition.', '18,000 miles', 'Automatic', 'SUV', 'Mazda', 'CX-5', 'Red', 'Gasoline'),
('Subaru Outback 2018', 19995.00, 2018, '../images/cars/subaru-outback-2018.svg', 'Versatile wagon with all-wheel drive standard. Great for adventure and daily driving.', '42,000 miles', 'CVT', 'Wagon', 'Subaru', 'Outback', 'Green', 'Gasoline'),
('Audi A4 2019', 26850.00, 2019, '../images/cars/audi-a4-2019.svg', 'Premium sedan with advanced technology and luxury appointments. Pristine condition.', '35,000 miles', 'Automatic', '4-Door', 'Audi', 'A4', 'Gray', 'Gasoline'),
('Chevrolet Malibu 2017', 14995.00, 2017, '../images/cars/chevrolet-malibu-2017.svg', 'Spacious midsize sedan with modern features and excellent value. Well maintained.', '52,000 miles', 'Automatic', '4-Door', 'Chevrolet', 'Malibu', 'White', 'Gasoline'),
('Tesla Model 3 2020', 35500.00, 2020, '../images/cars/tesla-model3-2020.svg', 'Electric luxury sedan with autopilot features. Incredible efficiency and performance.', '25,000 miles', 'Automatic', '4-Door', 'Tesla', 'Model 3', 'Black', 'Electric'),
('Jeep Wrangler 2018', 27900.00, 2018, '../images/cars/jeep-wrangler-2018.svg', 'Rugged off-road SUV with removable doors and roof. Adventure ready vehicle.', '38,000 miles', 'Manual', 'SUV', 'Jeep', 'Wrangler', 'Orange', 'Gasoline'),
('Volkswagen Jetta 2019', 17850.00, 2019, '../images/cars/volkswagen-jetta-2019.svg', 'German engineering with spacious interior and great fuel economy. Well equipped.', '29,000 miles', 'Automatic', '4-Door', 'Volkswagen', 'Jetta', 'Silver', 'Gasoline'),
('Nissan Altima 2020', 19500.00, 2020, '../images/cars/nissan-altima-2020.svg', 'Modern midsize sedan with advanced safety tech and comfortable ride quality.', '22,000 miles', 'CVT', '4-Door', 'Nissan', 'Altima', 'Blue', 'Gasoline'),
('Hyundai Tucson 2018', 18995.00, 2018, '../images/cars/hyundai-tucson-2018.svg', 'Compact SUV with excellent warranty coverage. Great value with lots of features.', '41,000 miles', 'Automatic', 'SUV', 'Hyundai', 'Tucson', 'White', 'Gasoline'),
('Mercedes C-Class 2017', 28750.00, 2017, '../images/cars/mercedes-c300-2017.svg', 'Luxury sedan with premium interior and smooth performance. Excellent condition.', '45,000 miles', 'Automatic', '4-Door', 'Mercedes-Benz', 'C-Class', 'Black', 'Gasoline'),
('Kia Sorento 2019', 21500.00, 2019, '../images/cars/kia-sorento-2019.svg', 'Three-row SUV with great value and comprehensive warranty. Family-friendly vehicle.', '33,000 miles', 'Automatic', 'SUV', 'Kia', 'Sorento', 'Gray', 'Gasoline'),
('Dodge Charger 2018', 23995.00, 2018, '../images/cars/dodge-charger-2018.svg', 'Powerful full-size sedan with aggressive styling and V6 performance.', '39,000 miles', 'Automatic', '4-Door', 'Dodge', 'Charger', 'Red', 'Gasoline'),
('Lexus RX 350 2017', 32900.00, 2017, '../images/cars/lexus-rx350-2017.svg', 'Luxury SUV with exceptional reliability and premium amenities. Very well maintained.', '48,000 miles', 'Automatic', 'SUV', 'Lexus', 'RX 350', 'White', 'Gasoline'),
('Chevrolet Silverado 2019', 26500.00, 2019, '../images/cars/chevrolet-silverado-2019.svg', 'Full-size pickup truck with towing capability and spacious cab. Work and play ready.', '31,000 miles', 'Automatic', 'Crew Cab', 'Chevrolet', 'Silverado', 'Blue', 'Gasoline'),
('Acura TLX 2018', 24850.00, 2018, '../images/cars/acura-tlx-2018.svg', 'Sport luxury sedan with advanced tech and performance features. Excellent value.', '36,000 miles', 'Automatic', '4-Door', 'Acura', 'TLX', 'Black', 'Gasoline'),
('Toyota RAV4 2020', 24995.00, 2020, '../images/cars/toyota-rav4-2020.svg', 'Popular compact SUV with all-wheel drive and excellent resale value. Nearly new.', '15,000 miles', 'CVT', 'SUV', 'Toyota', 'RAV4', 'Silver', 'Gasoline'),
('Honda Accord 2019', 21750.00, 2019, '../images/cars/honda-accord-2019.svg', 'Midsize sedan with spacious interior and excellent fuel economy. Reliable choice.', '27,000 miles', 'CVT', '4-Door', 'Honda', 'Accord', 'White', 'Gasoline'),
('Ford Escape 2018', 17995.00, 2018, '../images/cars/ford-escape-2018.svg', 'Compact SUV with good fuel economy and practical cargo space. Great for daily driving.', '44,000 miles', 'Automatic', 'SUV', 'Ford', 'Escape', 'Gray', 'Gasoline');

-- Display confirmation message
SELECT 'Database and table created successfully!' as Status;
SELECT COUNT(*) as 'Total Cars Inserted' FROM cars;