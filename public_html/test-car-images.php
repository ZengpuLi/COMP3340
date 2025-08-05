<?php
// Test to check car image paths
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'php/config.php';

echo "<h1>🔍 Car Image Path Test</h1>";

try {
    $conn = getDatabaseConnection();
    $result = $conn->query("SELECT id, name, image FROM cars LIMIT 5");
    
    if ($result) {
        echo "<h2>Database Image Paths:</h2>";
        while ($row = $result->fetch_assoc()) {
            echo "<div style='border: 1px solid #ccc; padding: 1rem; margin: 1rem 0;'>";
            echo "<h3>Car: " . $row['name'] . "</h3>";
            echo "<p><strong>Database Image Path:</strong> " . $row['image'] . "</p>";
            
            // Check if file exists
            $image_path = $row['image'];
            if (file_exists($image_path)) {
                echo "<p style='color: green;'>✅ File exists</p>";
                echo "<img src='" . $image_path . "' alt='" . $row['name'] . "' style='max-width: 200px; height: auto;'>";
            } else {
                echo "<p style='color: red;'>❌ File not found: " . $image_path . "</p>";
                
                // Check alternative paths
                $alt_path = "images/cars/" . basename($image_path);
                if (file_exists($alt_path)) {
                    echo "<p style='color: orange;'>🔄 Alternative path exists: " . $alt_path . "</p>";
                    echo "<img src='" . $alt_path . "' alt='" . $row['name'] . "' style='max-width: 200px; height: auto;'>";
                }
            }
            echo "</div>";
        }
    }
    $conn->close();
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>