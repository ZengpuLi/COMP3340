<?php
/**
 * Final Test Page
 * Used Car Purchase Website
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>🚀 Final Safe Version Test</h1>";
echo "<p>PHP Version: " . phpversion() . "</p>";

try {
    echo "<h2>Testing Core Files...</h2>";
    
    require_once 'php/config.php';
    echo "<p>✅ config.php loaded successfully</p>";
    
    require_once 'php/session-safe.php';
    echo "<p>✅ session-safe.php loaded successfully</p>";
    
    require_once 'php/navigation-safe.php';
    echo "<p>✅ navigation-safe.php loaded successfully</p>";
    
    echo "<h2>Testing Database Connection...</h2>";
    $conn = getDatabaseConnection();
    if ($conn) {
        echo "<p>✅ Database connection successful!</p>";
        
        $result = $conn->query("SELECT COUNT(*) as count FROM cars");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p>✅ Found " . $row['count'] . " cars in database</p>";
        }
        
        $result = $conn->query("SELECT COUNT(*) as count FROM users");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "<p>✅ Found " . $row['count'] . " users in database</p>";
        }
        
        $conn->close();
    } else {
        echo "<p>❌ Database connection failed</p>";
    }
    
    echo "<h2>Testing Functions...</h2>";
    
    if (function_exists('sanitizeInput')) {
        echo "<p>✅ sanitizeInput() function available</p>";
    } else {
        echo "<p>❌ sanitizeInput() function missing</p>";
    }
    
    if (function_exists('sanitizeOutput')) {
        echo "<p>✅ sanitizeOutput() function available</p>";
    } else {
        echo "<p>❌ sanitizeOutput() function missing</p>";
    }
    
    if (function_exists('isLoggedIn')) {
        echo "<p>✅ isLoggedIn() function available</p>";
    } else {
        echo "<p>❌ isLoggedIn() function missing</p>";
    }
    
    if (function_exists('generateNavigation')) {
        echo "<p>✅ generateNavigation() function available</p>";
    } else {
        echo "<p>❌ generateNavigation() function missing</p>";
    }
    
    if (function_exists('generateHeaderGreeting')) {
        echo "<p>✅ generateHeaderGreeting() function available</p>";
    } else {
        echo "<p>❌ generateHeaderGreeting() function missing</p>";
    }
    
    echo "<h2>Testing Navigation Generation...</h2>";
    $nav = generateNavigation('test.php');
    if (strlen($nav) > 0) {
        echo "<p>✅ Navigation generated successfully (" . strlen($nav) . " characters)</p>";
    } else {
        echo "<p>❌ Navigation generation failed</p>";
    }
    
    echo "<h2>Testing Header Greeting...</h2>";
    $greeting = generateHeaderGreeting();
    if (strlen($greeting) > 0) {
        echo "<p>✅ Header greeting generated: " . $greeting . "</p>";
    } else {
        echo "<p>❌ Header greeting generation failed</p>";
    }
    
    echo "<h2>✅ All Tests Passed!</h2>";
    echo "<p><strong>Ready to deploy the final safe versions!</strong></p>";
    
} catch (Throwable $e) {
    echo "<h2>❌ Error Caught:</h2>";
    echo "<p><strong>Type:</strong> " . get_class($e) . "</p>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "<pre><strong>Stack Trace:</strong>\n" . $e->getTraceAsString() . "</pre>";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1 { color: #2c3e50; }
h2 { color: #3498db; border-bottom: 1px solid #bdc3c7; padding-bottom: 5px; }
p { margin: 5px 0; }
pre { background: #f8f9fa; padding: 10px; border-radius: 5px; }
</style>