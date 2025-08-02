# 🔧 Database Configuration Fix

The error you're seeing is due to a mismatch between the database configuration and the actual database setup in your hosting environment.

## ✅ **Fixed Configuration**

I've updated your `php/config.php` with the correct database settings based on the error message:

```php
// Updated Database Configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'lizengp_U-bAZ5K6p0VuqQMfQ23L9QbvDdqTEuLD');  // Matches hosting environment
define('DB_PASSWORD', 'GLFmzjMeGTuv2B3YQxWq');                        // Your existing password
define('DB_NAME', 'lizengp_car');                                     // Correct database name
```

## 🧪 **Test the Connection**

1. **Open your web browser** and navigate to:
   ```
   http://your-domain.com/php/test_connection.php
   ```

2. **This will show you:**
   - ✅ Whether the database connection is working
   - 📊 What tables currently exist in your database
   - 🔧 Troubleshooting information if there are issues

## 📂 **Import SQL Files in Correct Order**

Since the SQL files reference `used_cars` database but your actual database is `lizengp_car`, use these **corrected SQL files**:

### **Step 1: Basic Setup**
Import in phpMyAdmin: `sql/database_setup.sql` *(modify first line)*
```sql
-- Change the first line from:
USE used_cars;
-- To:
USE lizengp_car;
```

### **Step 2: User System**
Import: `sql/users_table.sql` *(modify first line)*
```sql
-- Change the first line from:
USE used_cars;
-- To:
USE lizengp_car;
```

### **Step 3: User Features**
Import: `sql/user_features.sql` *(modify first line)*
```sql
-- Change the first line from:
USE used_cars;
-- To:
USE lizengp_car;
```

### **Step 4: Admin Setup**
Import: `sql/admin_setup_fixed.sql` *(already corrected)*

### **Step 5: Forms Setup**
Import: `sql/forms_setup.sql` *(modify first line)*
```sql
-- Change the first line from:
USE used_cars;
-- To:
USE lizengp_car;
```

## 🚀 **Quick Fix Method**

**Option 1: Manual Edit in phpMyAdmin**
1. Open each SQL file in a text editor
2. Change `USE used_cars;` to `USE lizengp_car;`
3. Import the modified SQL

**Option 2: Use the SQL Editor**
1. In phpMyAdmin, go to your `lizengp_car` database
2. Click "SQL" tab
3. Copy and paste the SQL content (excluding the `USE` line)
4. Execute the queries

## 🔍 **Verification Steps**

After importing all SQL files, your database should have these tables:
- ✅ `cars` - Car inventory
- ✅ `users` - User accounts (with `role` column)
- ✅ `favorites` - User favorites
- ✅ `purchase_history` - Purchase records
- ✅ `site_settings` - Admin settings
- ✅ `admin_activity_log` - Admin activity tracking
- ✅ `inquiries` - Car inquiries
- ✅ `calculator_sessions` - Loan calculator data

## 🆘 **Still Having Issues?**

If you're still getting database errors:

1. **Check your hosting control panel** for the exact:
   - Database name
   - Username
   - Password

2. **Contact your hosting provider** if you need help with:
   - Database permissions
   - User access rights
   - Database creation

3. **Test the connection** using the test file before importing SQL

## 📧 **Common Hosting Patterns**

Different hosting providers use different naming patterns:
- **cPanel/WHM:** `username_dbname` format
- **DirectAdmin:** `username_dbname` format  
- **Custom:** Can vary significantly

Your configuration now matches the error message pattern, so it should work correctly!