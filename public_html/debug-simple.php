<?php
// Simple diagnostic for 500 errors
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>🔧 Simple PHP Diagnostic</h1>";

echo "<h2>PHP Version:</h2>";
echo "<p>PHP Version: " . phpversion() . "</p>";

echo "<h2>File Tests:</h2>";
$files_to_test = ['php/config.php', 'php/session.php', 'php/navigation.php'];

foreach ($files_to_test as $file) {
    if (file_exists($file)) {
        echo "<p style='color: green;'>✅ $file exists</p>";
        try {
            require_once $file;
            echo "<p style='color: green;'>✅ $file loaded successfully</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Error loading $file: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ $file not found</p>";
    }
}

echo "<h2>Function Tests:</h2>";
$functions_to_test = ['str_starts_with', 'sanitizeOutput', 'formatPrice', 'getDatabaseConnection'];

foreach ($functions_to_test as $func) {
    if (function_exists($func)) {
        echo "<p style='color: green;'>✅ Function $func exists</p>";
    } else {
        echo "<p style='color: red;'>❌ Function $func not available</p>";
    }
}

echo "<h2>Database Test:</h2>";
try {
    if (function_exists('getDatabaseConnection')) {
        $conn = getDatabaseConnection();
        echo "<p style='color: green;'>✅ Database connection successful</p>";
        $result = $conn->query("SELECT COUNT(*) as count FROM cars");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p style='color: green;'>✅ Found " . $row['count'] . " cars in database</p>";
        }
        $conn->close();
    } else {
        echo "<p style='color: orange;'>⚠️ getDatabaseConnection function not available</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<h2>Next Steps:</h2>";
echo "<p>1. Upload cars-simple.php</p>";
echo "<p>2. Rename cars-simple.php to cars.php</p>";
echo "<p>3. Test the simplified version</p>";
?>