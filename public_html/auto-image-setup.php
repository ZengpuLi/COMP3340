<?php
/**
 * Automated Car Image Setup Helper
 * This tool will help you quickly set up real car images
 */

// Include required files
require_once 'php/config.php';

echo "<h1>🚗 Automated Car Image Setup</h1>";

// Define the car image mappings with direct download URLs (placeholder images from free sources)
$car_images = [
    1 => [
        'name' => '2018 Honda Civic LX',
        'filename' => 'honda-civic-2018.jpg',
        'placeholder_url' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=800&h=600&fit=crop&crop=center'
    ],
    2 => [
        'name' => '2019 Toyota Corolla LE',
        'filename' => 'toyota-corolla-2019.jpg',
        'placeholder_url' => 'https://images.unsplash.com/photo-1621007947382-bb3c3994e3fb?w=800&h=600&fit=crop&crop=center'
    ],
    3 => [
        'name' => '2017 Ford Escape SE',
        'filename' => 'ford-escape-2017.jpg',
        'placeholder_url' => 'https://images.unsplash.com/photo-1552519507-da3b142c6e3d?w=800&h=600&fit=crop&crop=center'
    ],
    4 => [
        'name' => '2020 Nissan Altima',
        'filename' => 'nissan-altima-2020.jpg',
        'placeholder_url' => 'https://images.unsplash.com/photo-1494976388531-d1058494cdd8?w=800&h=600&fit=crop&crop=center'
    ],
    5 => [
        'name' => '2017 Chevrolet Malibu',
        'filename' => 'chevrolet-malibu-2017.jpg',
        'placeholder_url' => 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?w=800&h=600&fit=crop&crop=center'
    ],
    6 => [
        'name' => '2018 Hyundai Tucson',
        'filename' => 'hyundai-tucson-2018.jpg',
        'placeholder_url' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=800&h=600&fit=crop&crop=center'
    ]
];

echo "<h2>🎯 Quick Setup Options:</h2>";

echo "<div style='background: #e3f2fd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h3>Option 1: One-Click Database Update (Recommended)</h3>";
echo "<p>This will update your database to use optimized placeholder images immediately:</p>";

if (isset($_POST['update_database'])) {
    try {
        $conn = getDatabaseConnection();
        
        $updates = [
            1 => 'images/cars/honda-civic-2018.jpg',
            2 => 'images/cars/toyota-corolla-2019.jpg', 
            3 => 'images/cars/ford-escape-2017.jpg',
            4 => 'images/cars/nissan-altima-2020.jpg',
            5 => 'images/cars/chevrolet-malibu-2017.jpg',
            6 => 'images/cars/hyundai-tucson-2018.jpg',
            7 => 'images/cars/kia-sorento-2019.jpg',
            8 => 'images/cars/mazda-cx5-2020.jpg',
            9 => 'images/cars/subaru-outback-2018.jpg',
            10 => 'images/cars/volkswagen-jetta-2019.jpg',
            11 => 'images/cars/toyota-rav4-2020.jpg',
            12 => 'images/cars/honda-accord-2019.jpg',
            13 => 'images/cars/ford-f150-2017.jpg',
            14 => 'images/cars/chevrolet-silverado-2019.jpg',
            15 => 'images/cars/tesla-model3-2020.jpg',
            16 => 'images/cars/bmw-x3-2019.jpg',
            17 => 'images/cars/audi-a4-2019.jpg',
            18 => 'images/cars/mercedes-c300-2017.jpg',
            19 => 'images/cars/lexus-rx350-2017.jpg',
            20 => 'images/cars/acura-tlx-2018.jpg'
        ];
        
        $updated = 0;
        foreach ($updates as $id => $image_path) {
            $stmt = $conn->prepare("UPDATE cars SET image = ? WHERE id = ?");
            $stmt->bind_param("si", $image_path, $id);
            if ($stmt->execute()) {
                $updated++;
            }
        }
        
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px;'>";
        echo "<strong>✅ Success!</strong> Updated $updated car image paths in database.";
        echo "</div>";
        
        $conn->close();
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px;'>";
        echo "<strong>❌ Error:</strong> " . $e->getMessage();
        echo "</div>";
    }
}

echo "<form method='post' style='margin: 20px 0;'>";
echo "<button type='submit' name='update_database' style='background: #28a745; color: white; padding: 15px 30px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;'>";
echo "🚀 Update Database Now";
echo "</button>";
echo "</form>";
echo "</div>";

echo "<div style='background: #fff3cd; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h3>Option 2: Download Real Images Pack</h3>";
echo "<p>For the best results, I recommend downloading this curated pack of real car images:</p>";

echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin: 20px 0;'>";

foreach ($car_images as $id => $car) {
    echo "<div style='background: white; padding: 15px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);'>";
    echo "<strong>" . htmlspecialchars($car['name']) . "</strong><br>";
    echo "<code style='background: #f8f9fa; padding: 2px 5px; border-radius: 3px;'>" . $car['filename'] . "</code><br>";
    echo "<a href='" . $car['placeholder_url'] . "' target='_blank' style='color: #007bff; text-decoration: none;'>📥 Download Image</a>";
    echo "</div>";
}

echo "</div>";
echo "</div>";

echo "<div style='background: #d1ecf1; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h3>📋 Manual Setup Instructions:</h3>";
echo "<ol>";
echo "<li><strong>Create images folder:</strong> In DirectAdmin, create <code>public_html/images/cars/</code></li>";
echo "<li><strong>Download images:</strong> Right-click → Save As for each image above</li>";
echo "<li><strong>Upload images:</strong> Upload all JPG files to <code>public_html/images/cars/</code></li>";
echo "<li><strong>Test website:</strong> Visit your cars page to see real images</li>";
echo "</ol>";
echo "</div>";

echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
echo "<h3>🔧 Current Database Status:</h3>";

try {
    $conn = getDatabaseConnection();
    $result = $conn->query("SELECT id, name, image FROM cars ORDER BY id LIMIT 10");
    
    if ($result) {
        echo "<table style='width: 100%; border-collapse: collapse;'>";
        echo "<tr style='background: #e9ecef;'>";
        echo "<th style='padding: 10px; border: 1px solid #ddd;'>ID</th>";
        echo "<th style='padding: 10px; border: 1px solid #ddd;'>Car Name</th>";
        echo "<th style='padding: 10px; border: 1px solid #ddd;'>Current Image Path</th>";
        echo "<th style='padding: 10px; border: 1px solid #ddd;'>Status</th>";
        echo "</tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . $row['id'] . "</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd;'><code>" . htmlspecialchars($row['image']) . "</code></td>";
            
            $image_exists = file_exists($row['image']);
            if ($image_exists) {
                echo "<td style='padding: 10px; border: 1px solid #ddd; color: #28a745;'>✅ Exists</td>";
            } else {
                echo "<td style='padding: 10px; border: 1px solid #ddd; color: #dc3545;'>❌ Missing</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    }
    $conn->close();
} catch (Exception $e) {
    echo "<p style='color: #dc3545;'>Error checking database: " . $e->getMessage() . "</p>";
}

echo "</div>";

echo "<div style='text-align: center; margin: 30px 0; padding: 20px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px;'>";
echo "<h3>🎯 Next Steps:</h3>";
echo "<p>1. Click 'Update Database Now' above</p>";
echo "<p>2. Visit your cars page: <a href='cars.php' style='color: #ffd700;'>cars.php</a></p>";
echo "<p>3. Replace with real images when ready using the advanced image finder</p>";
echo "</div>";
?>