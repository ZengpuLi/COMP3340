# MySQL Database Setup Guide
## Used Car Purchase Website - Step 7

This guide will help you set up the MySQL database for the car listings functionality.

## Prerequisites

- **XAMPP**, **WAMP**, **MAMP**, or standalone **MySQL Server**
- **PHP** with MySQLi and PDO extensions enabled
- **Web server** (Apache/Nginx) with PHP support

## Step 1: Start MySQL Server

### Using XAMPP:
1. Open XAMPP Control Panel
2. Start **Apache** and **MySQL** services
3. Click **Admin** next to MySQL to open phpMyAdmin

### Using Command Line:
```bash
# Start MySQL service (varies by OS)
sudo systemctl start mysql  # Linux
brew services start mysql   # macOS with Homebrew
```

## Step 2: Create Database and Tables

### Option A: Using phpMyAdmin (Recommended)
1. Open phpMyAdmin in your browser (usually `http://localhost/phpmyadmin`)
2. Click **"SQL"** tab
3. Copy and paste the contents of `sql/database_setup.sql`
4. Click **"Go"** to execute

### Option B: Using MySQL Command Line
```bash
# Connect to MySQL
mysql -u root -p

# Run the setup script
mysql -u root -p < sql/database_setup.sql
```

## Step 3: Configure Database Connection

1. Open `php/config.php`
2. Update the database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'your_password_here');  // Set your MySQL password
define('DB_NAME', 'used_cars');
```

**Common Settings:**
- **XAMPP/WAMP**: Usually no password (`''`)
- **MAMP**: Username: `root`, Password: `root`
- **Production**: Use secure credentials

## Step 4: Test Database Connection

1. Open your browser
2. Navigate to: `http://localhost/finalproject/php/test_connection.php`
3. Check for green checkmarks ✅
4. If errors appear, follow the troubleshooting steps

## Step 5: View Car Listings

1. Navigate to: `http://localhost/finalproject/public_html/cars.php`
2. You should see 22 cars loaded from the database
3. Test different themes to ensure styling works

## Database Structure

The `cars` table includes these fields:
- `id` - Primary key (auto-increment)
- `name` - Car name (e.g., "Toyota Corolla 2018")
- `price` - Price in decimal format
- `year` - Manufacturing year
- `image` - Path to car image
- `description` - Detailed description
- `mileage` - Formatted mileage string
- `transmission` - Transmission type
- `body_type` - Vehicle body style
- `make` - Car manufacturer
- `model` - Car model
- `color` - Vehicle color
- `fuel_type` - Fuel type (Gasoline, Electric, etc.)

## Troubleshooting

### Connection Failed
- ✅ Check MySQL server is running
- ✅ Verify credentials in `config.php`
- ✅ Ensure database `used_cars` exists
- ✅ Check PHP has MySQLi/PDO extensions

### Table Doesn't Exist
- ✅ Run the SQL setup script completely
- ✅ Check for SQL errors in phpMyAdmin
- ✅ Verify user has CREATE/INSERT permissions

### Images Not Loading
- ✅ Ensure all car images exist in `images/cars/`
- ✅ Check file paths in database match actual files
- ✅ Verify web server can serve image files

### PHP Errors
- ✅ Check PHP error logs
- ✅ Ensure `php/config.php` is readable
- ✅ Verify include paths are correct

## Sample Data

The setup script includes 22 diverse vehicles:
- **Sedans**: Toyota Corolla, Honda Civic, Audi A4, etc.
- **SUVs**: BMW X3, Mazda CX-5, Jeep Wrangler, etc.
- **Trucks**: Ford F-150, Chevrolet Silverado
- **Electric**: Tesla Model 3
- **Luxury**: Mercedes C-Class, Lexus RX 350

## Security Notes

- Use strong database passwords in production
- The code uses prepared statements to prevent SQL injection
- All output is sanitized with `htmlspecialchars()`
- Database credentials should be kept secure

## Next Steps

Once the database is working:
1. **Test all functionality** - Browse cars, switch themes
2. **Customize data** - Add your own car listings
3. **Backup database** - Export the `used_cars` database
4. **Future enhancements** - Add search, filters, car details pages

## Support

If you encounter issues:
1. Check the test connection script output
2. Review MySQL and PHP error logs
3. Verify all files are in correct locations
4. Ensure web server has proper permissions