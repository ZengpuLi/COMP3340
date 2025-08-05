<?php
// Get car list for image replacement
require_once 'php/config.php';

echo "<h1>🚗 Car List for Image Replacement</h1>";
echo "<p>Here are all the cars in your database that need real photos:</p>";

try {
    $conn = getDatabaseConnection();
    $result = $conn->query("SELECT id, name, year, make, model FROM cars ORDER BY id ASC");
    
    if ($result) {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>ID</th><th>Car Name</th><th>Year</th><th>Make</th><th>Model</th><th>Search Terms</th>";
        echo "</tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id'] . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . $row['year'] . "</td>";
            echo "<td>" . htmlspecialchars($row['make']) . "</td>";
            echo "<td>" . htmlspecialchars($row['model']) . "</td>";
            echo "<td><strong>" . $row['year'] . " " . htmlspecialchars($row['make']) . " " . htmlspecialchars($row['model']) . "</strong></td>";
            echo "</tr>";
        }
        echo "</table>";
        
        echo "<h2>📋 Instructions:</h2>";
        echo "<ol>";
        echo "<li><strong>Download Images:</strong> Search for each car using the 'Search Terms' and download high-quality photos</li>";
        echo "<li><strong>Image Requirements:</strong> 800x600 pixels, JPG format, under 200KB each</li>";
        echo "<li><strong>Naming Convention:</strong> Use format like 'honda-civic-2018.jpg'</li>";
        echo "<li><strong>Upload Location:</strong> DirectAdmin → public_html/images/cars/</li>";
        echo "</ol>";
        
        echo "<h2>🌐 Recommended Free Image Sources:</h2>";
        echo "<ul>";
        echo "<li><a href='https://unsplash.com' target='_blank'>Unsplash.com</a> - High quality free photos</li>";
        echo "<li><a href='https://pixabay.com' target='_blank'>Pixabay.com</a> - Free images and photos</li>";
        echo "<li><a href='https://pexels.com' target='_blank'>Pexels.com</a> - Free stock photos</li>";
        echo "<li><strong>Search terms:</strong> '[Year] [Make] [Model] car exterior'</li>";
        echo "</ul>";
    }
    $conn->close();
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>