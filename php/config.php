<?php
/**
 * Database Configuration File
 * Used Car Purchase Website
 */

// Database configuration constants
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'lizengp_car');
define('DB_PASSWORD', 'GLFmzjMeGTuv2B3YQxWq');  // Change this to your MySQL password
define('DB_NAME', 'lizengp_car');
define('DB_CHARSET', 'utf8mb4');

// Error reporting settings
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone setting
date_default_timezone_set('America/New_York');

/**
 * Database connection function using MySQLi
 * Returns a MySQLi connection object
 */
function getDatabaseConnection() {
    try {
        $connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
        
        // Check connection
        if ($connection->connect_error) {
            throw new Exception("Database connection failed: " . $connection->connect_error);
        }
        
        // Set charset
        $connection->set_charset(DB_CHARSET);
        
        return $connection;
        
    } catch (Exception $e) {
        error_log("Database connection error: " . $e->getMessage());
        throw new Exception("Unable to connect to the database. Please try again later.");
    }
}

/**
 * PDO Database connection function (alternative)
 * Returns a PDO connection object
 */
function getPDOConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, $options);
        return $pdo;
        
    } catch (PDOException $e) {
        error_log("Database connection error: " . $e->getMessage());
        throw new Exception("Unable to connect to the database. Please try again later.");
    }
}

/**
 * Sanitize output for HTML display
 */
function sanitizeOutput($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

/**
 * Format price for display
 */
function formatPrice($price) {
    return '$' . number_format($price, 0);
}

/**
 * Format year for display
 */
function formatYear($year) {
    return (int)$year;
}
?>