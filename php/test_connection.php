<?php
/**
 * Test Database Connection
 * Run this to verify your database configuration is working
 */

require_once 'config.php';

echo "<h2>🔍 Database Connection Test</h2>";

try {
    // Test MySQLi connection
    echo "<h3>Testing MySQLi Connection...</h3>";
    $connection = getDatabaseConnection();
    echo "✅ <strong>MySQLi Connection: SUCCESS</strong><br>";
    echo "📊 Database: " . DB_NAME . "<br>";
    echo "👤 Username: " . DB_USERNAME . "<br>";
    echo "🏠 Host: " . DB_HOST . "<br>";
    
    // Test if we can query the database
    $result = $connection->query("SHOW TABLES");
    if ($result) {
        echo "📋 <strong>Database Query: SUCCESS</strong><br>";
        echo "📊 <strong>Available Tables:</strong><br>";
        echo "<ul>";
        while ($row = $result->fetch_array()) {
            echo "<li>" . $row[0] . "</li>";
        }
        echo "</ul>";
    } else {
        echo "❌ <strong>Database Query: FAILED</strong><br>";
        echo "Error: " . $connection->error . "<br>";
    }
    
    $connection->close();
    
    // Test PDO connection
    echo "<h3>Testing PDO Connection...</h3>";
    $pdo = getPDOConnection();
    echo "✅ <strong>PDO Connection: SUCCESS</strong><br>";
    
    // Test PDO query
    $stmt = $pdo->query("SELECT VERSION() as version");
    $version = $stmt->fetch();
    echo "🗄️ <strong>MySQL Version:</strong> " . $version['version'] . "<br>";
    
    echo "<h3>🎉 All Database Tests Passed!</h3>";
    echo "<p>Your database configuration is working correctly. You can now proceed with importing SQL files.</p>";
    
} catch (Exception $e) {
    echo "❌ <strong>Database Connection: FAILED</strong><br>";
    echo "Error: " . $e->getMessage() . "<br>";
    echo "<br><h3>🔧 Troubleshooting Tips:</h3>";
    echo "<ul>";
    echo "<li><strong>Check Username:</strong> Current setting is '" . DB_USERNAME . "'</li>";
    echo "<li><strong>Check Database Name:</strong> Current setting is '" . DB_NAME . "'</li>";
    echo "<li><strong>Check Password:</strong> Verify your MySQL password is correct</li>";
    echo "<li><strong>Check Host:</strong> Current setting is '" . DB_HOST . "'</li>";
    echo "<li><strong>Check Permissions:</strong> Make sure the user has access to the database</li>";
    echo "</ul>";
}
?>

<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    h2 { color: #2c3e50; }
    h3 { color: #3498db; }
    ul { background: #f8f9fa; padding: 15px; border-radius: 5px; }
    li { margin: 5px 0; }
</style>