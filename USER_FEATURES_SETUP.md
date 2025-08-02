# User Features Setup Guide (Favorites & Purchase History)
## Used Car Purchase Website - Step 9

This guide explains how to set up and use the private user features including favorites and purchase history functionality.

## Overview

The user features system provides:
- **Favorites System** - Users can save cars to a favorites list
- **Purchase History** - Users can mark cars as purchased and view purchase history
- **Interactive Buttons** - AJAX-powered favorite and purchase buttons on car cards
- **User-Specific Pages** - Dedicated favorites and purchases pages
- **Statistics & Analytics** - Purchase summaries and favorite counts

## Installation Steps

### Step 1: Create User Features Tables

1. **Run the SQL script:**
```bash
mysql -u root -p used_cars < sql/user_features.sql
```

Or execute in phpMyAdmin:
```sql
-- Copy and paste contents of sql/user_features.sql
```

### Step 2: Test User Features

1. **Visit the cars page while logged in:**
```
http://localhost/finalproject/public_html/cars.php
```

2. **Test functionality:**
   - Click "Add to Favorites" on any car
   - Click "Mark as Purchased" on any car
   - Notice the buttons change state appropriately

### Step 3: View User-Specific Pages

1. **Visit favorites page:**
```
http://localhost/finalproject/public_html/favorites.php
```

2. **Visit purchases page:**
```
http://localhost/finalproject/public_html/purchases.php
```

## File Structure

```
finalproject/
├── sql/
│   └── user_features.sql        # Database tables for favorites & purchases
├── php/
│   ├── config.php               # Database configuration
│   ├── session.php              # Session management functions
│   ├── navigation.php           # Updated with new nav links
│   └── user_features.php        # User features helper functions
└── public_html/
    ├── cars.php                 # Updated with interactive buttons
    ├── favorites.php            # User favorites page
    ├── purchases.php            # User purchase history page
    ├── login.php                # Login required for features
    ├── register.php             # Account creation
    └── profile.php              # User profile management
```

## Features Explained

### 1. Database Tables

**Favorites Table (`favorites`):**
- `id` - Primary key, auto increment
- `user_id` - Foreign key to users table
- `car_id` - Foreign key to cars table
- `created_at` - When favorited

**Purchase History Table (`purchase_history`):**
- `id` - Primary key, auto increment
- `user_id` - Foreign key to users table
- `car_id` - Foreign key to cars table
- `purchase_date` - Date of purchase
- `purchase_price` - Price paid
- `status` - Purchase status (completed)

### 2. Interactive Car Cards (`cars.php`)

**For Logged-In Users:**
- **Favorite Button:** Toggle between "Add to Favorites" and "Remove from Favorites"
- **Purchase Button:** "Mark as Purchased" or "✓ Purchased" if already bought
- **Real-time Updates:** Buttons update instantly via AJAX
- **Visual Feedback:** Loading states and success/error messages

**JavaScript Features:**
- CSRF token protection
- Fetch API for async requests
- Toast notifications for user feedback
- Button state management

### 3. Favorites Page (`favorites.php`)

**Display Features:**
- Grid layout of favorite cars
- Favorite statistics (total count)
- Date when each car was favorited
- Interactive buttons (remove from favorites, mark as purchased)

**User Experience:**
- Cars disappear from list when removed from favorites
- Real-time count updates
- Smooth animations for removals
- Empty state message with links to browse cars

### 4. Purchases Page (`purchases.php`)

**Display Features:**
- Grid layout of purchased cars
- Purchase information (date, price, status)
- Purchase statistics (total spent, average price, etc.)
- Purchase summary with analytics

**Analytics:**
- Total vehicles purchased
- Total amount spent
- Average price per vehicle
- First purchase date

### 5. Helper Functions (`php/user_features.php`)

**Core Functions:**
- `isCarFavorited($userId, $carId)` - Check if car is favorited
- `addToFavorites($userId, $carId)` - Add car to favorites
- `removeFromFavorites($userId, $carId)` - Remove from favorites
- `getUserFavorites($userId)` - Get user's favorite cars
- `hasUserPurchasedCar($userId, $carId)` - Check if car purchased
- `addPurchaseToHistory($userId, $carId, $price)` - Record purchase
- `getUserPurchaseHistory($userId)` - Get purchase history

**AJAX Handler:**
- `handleUserFeatureAction()` - Process AJAX requests
- CSRF token validation
- JSON response formatting
- Error handling and logging

### 6. Navigation Updates

**For Logged-In Users:**
- **Favorites** - Link to favorites page
- **Purchases** - Link to purchase history
- **My Profile** - User profile page
- **Logout** - Sign out

**Dynamic Display:**
- Shows user-specific links only when logged in
- Active page highlighting
- Mobile-friendly navigation

## User Interface Features

### Interactive Buttons

**Favorite Button States:**
- 🤍 Add to Favorites (not favorited)
- ❤️ Remove from Favorites (favorited)
- ⏳ Loading... (processing)

**Purchase Button States:**
- 💰 Mark as Purchased (not purchased)
- ✓ Purchased (already purchased, disabled)
- ⏳ Loading... (processing)

### Responsive Design

**Desktop Features:**
- Side-by-side button layout
- Hover effects and animations
- Large statistics display

**Mobile Features:**
- Stacked button layout
- Touch-friendly interface
- Simplified statistics view

### Theme Compatibility

**All Themes Supported:**
- **Default Theme** - Blue color scheme
- **Dark Theme** - Dark backgrounds with appropriate contrast
- **Light Theme** - Minimal styling with subtle shadows

## Security Features

### Database Security
- **Foreign Key Constraints** - Ensure data integrity
- **Prepared Statements** - Prevent SQL injection
- **User ID Validation** - Only allow users to manage their own data

### AJAX Security
- **CSRF Token Protection** - Prevent cross-site request forgery
- **Session Validation** - Require login for all actions
- **Input Sanitization** - Clean all user inputs

### Access Control
- **Login Required** - All features require authentication
- **User-Specific Data** - Users can only see their own favorites/purchases
- **Graceful Degradation** - Features hidden for non-logged-in users

## Usage Examples

### Check if Car is Favorited
```php
require_once '../php/user_features.php';

$userId = $_SESSION['user_id'];
$carId = 123;

if (isCarFavorited($userId, $carId)) {
    echo "This car is in favorites";
}
```

### Add Car to Favorites
```php
require_once '../php/user_features.php';

$userId = $_SESSION['user_id'];
$carId = 123;

if (addToFavorites($userId, $carId)) {
    echo "Added to favorites successfully";
}
```

### Get User's Favorites
```php
require_once '../php/user_features.php';

$userId = $_SESSION['user_id'];
$favorites = getUserFavorites($userId);

foreach ($favorites as $car) {
    echo $car['name'] . " - " . $car['price'];
}
```

### Mark Car as Purchased
```php
require_once '../php/user_features.php';

$userId = $_SESSION['user_id'];
$carId = 123;
$price = 25000.00;

if (addPurchaseToHistory($userId, $carId, $price)) {
    echo "Purchase recorded successfully";
}
```

## Customization Options

### 1. Purchase Status Options
Edit `sql/user_features.sql` to add more status types:
```sql
ALTER TABLE purchase_history 
MODIFY COLUMN status ENUM('pending', 'completed', 'cancelled', 'refunded') DEFAULT 'completed';
```

### 2. Additional Purchase Fields
```sql
ALTER TABLE purchase_history ADD COLUMN financing_terms TEXT;
ALTER TABLE purchase_history ADD COLUMN down_payment DECIMAL(10,2);
ALTER TABLE purchase_history ADD COLUMN monthly_payment DECIMAL(10,2);
```

### 3. Favorite Categories
```sql
CREATE TABLE favorite_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    name VARCHAR(50),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

ALTER TABLE favorites ADD COLUMN category_id INT;
```

### 4. Purchase Notes
```sql
ALTER TABLE purchase_history ADD COLUMN notes TEXT;
ALTER TABLE purchase_history ADD COLUMN dealer_contact VARCHAR(255);
```

## Performance Optimization

### Database Indexes
The tables include optimized indexes:
```sql
-- Favorites table indexes
INDEX idx_user_id (user_id)
INDEX idx_car_id (car_id)
UNIQUE KEY unique_user_car_favorite (user_id, car_id)

-- Purchase history table indexes
INDEX idx_user_id (user_id)
INDEX idx_purchase_date (purchase_date)
INDEX idx_status (status)
```

### AJAX Optimization
- **Minimal data transfer** - Only send necessary fields
- **Batch operations** - Multiple actions in single request
- **Caching** - Store user state in session
- **Error handling** - Graceful failure with retry options

## Troubleshooting

### Common Issues

**Buttons not working:**
- Check if user is logged in
- Verify CSRF tokens are being generated
- Check browser console for JavaScript errors
- Ensure AJAX endpoints are reachable

**Database errors:**
- Verify foreign key constraints
- Check user_features.sql was executed properly
- Ensure MySQL server has proper permissions
- Check error logs for SQL syntax issues

**Navigation links missing:**
- Clear browser cache
- Check session status
- Verify navigation.php includes are working
- Test login/logout functionality

**AJAX responses failing:**
- Check Content-Type headers
- Verify JSON response format
- Test CSRF token generation
- Check network tab in browser dev tools

### Debug Mode

Enable debugging in `php/user_features.php`:
```php
// Add at top of file for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
```

### Database Query Testing

Test database connections:
```sql
-- Check favorites
SELECT u.username, c.name, f.created_at 
FROM favorites f 
JOIN users u ON f.user_id = u.id 
JOIN cars c ON f.car_id = c.id;

-- Check purchases
SELECT u.username, c.name, p.purchase_date, p.purchase_price 
FROM purchase_history p 
JOIN users u ON p.user_id = u.id 
JOIN cars c ON p.car_id = c.id;
```

## Sample Data

The SQL script includes sample data for testing:

**Favorites:**
- Admin user has 3 favorite cars
- Test user has 3 favorite cars

**Purchases:**
- Admin user has 2 purchases ($57,000 total)
- Test user has 2 purchases ($52,000 total)

## Next Steps

1. **Test thoroughly** - Favorites, purchases, navigation
2. **Customize styling** - Match your design preferences
3. **Add features** - Purchase financing, favorite notes
4. **Implement search** - Filter favorites and purchases
5. **Analytics dashboard** - Admin view of user activity

## API Reference

### AJAX Endpoints

**Add to Favorites:**
```javascript
POST /cars.php
{
    action: 'add_favorite',
    car_id: 123,
    csrf_token: 'token_here'
}
```

**Remove from Favorites:**
```javascript
POST /favorites.php
{
    action: 'remove_favorite',
    car_id: 123,
    csrf_token: 'token_here'
}
```

**Mark as Purchased:**
```javascript
POST /cars.php
{
    action: 'mark_purchased',
    car_id: 123,
    price: 25000.00,
    csrf_token: 'token_here'
}
```

All endpoints return JSON:
```json
{
    "success": true,
    "message": "Operation completed successfully",
    "action": "added"
}
```