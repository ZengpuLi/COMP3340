# User Authentication System Setup Guide
## Used Car Purchase Website - Step 8

This guide explains how to set up and use the user registration and login system.

## Overview

The authentication system provides:
- **User Registration** with validation and password hashing
- **Secure Login** with session management
- **User Profiles** with editable information
- **Dynamic Navigation** that changes based on login status
- **CSRF Protection** and secure password requirements

## Installation Steps

### Step 1: Create Users Table

1. **Run the SQL script:**
```bash
mysql -u root -p used_cars < sql/users_table.sql
```

Or execute in phpMyAdmin:
```sql
-- Copy and paste contents of sql/users_table.sql
```

### Step 2: Test Database Connection

1. **Visit test page:**
```
http://localhost/finalproject/php/test_connection.php
```

2. **Verify users table** exists and sample data is loaded

### Step 3: Test Authentication

1. **Visit registration page:**
```
http://localhost/finalproject/public_html/register.php
```

2. **Create a new account** or use demo accounts:
   - **Admin:** username `admin`, password `admin123`
   - **User:** username `testuser`, password `user123`

## File Structure

```
finalproject/
├── sql/
│   └── users_table.sql          # Database table creation
├── php/
│   ├── config.php               # Database configuration
│   ├── session.php              # Session management functions
│   └── navigation.php           # Dynamic navigation component
└── public_html/
    ├── register.php             # User registration page
    ├── login.php                # User login page
    ├── logout.php               # Logout handler
    ├── profile.php              # User profile page
    └── cars.php                 # Updated with dynamic navigation
```

## Features Explained

### 1. User Registration (`register.php`)

**Validation Features:**
- Username: 3+ characters, letters/numbers/underscores only
- Email: Valid email format, unique in database
- Password: 6+ characters with letters and numbers
- Confirm password must match
- Optional: First name, last name, phone number

**Security Features:**
- Password hashing with `password_hash()`
- CSRF token protection
- Input sanitization with `htmlspecialchars()`
- Prepared statements for database queries
- Duplicate username/email checking

### 2. User Login (`login.php`)

**Login Options:**
- Username OR email address
- Case-insensitive login
- Demo accounts for testing

**Security Features:**
- Password verification with `password_verify()`
- Session regeneration on login
- Failed login protection
- Redirect to intended page after login

### 3. Session Management (`php/session.php`)

**Core Functions:**
- `isLoggedIn()` - Check if user is authenticated
- `getCurrentUser()` - Get current user data
- `loginUser($id, $username)` - Create user session
- `logoutUser()` - Destroy session securely
- `requireLogin()` - Redirect if not logged in

**Security Features:**
- Session ID regeneration
- Secure session cookies
- Session timeout handling
- CSRF token generation and verification

### 4. Dynamic Navigation (`php/navigation.php`)

**Conditional Display:**
- **Logged Out:** Login, Register
- **Logged In:** My Profile, Logout, Welcome message
- **All Users:** Home, About, Cars, Contact, Help, Privacy

**Functions:**
- `generateNavigation($current_page)` - Create nav HTML
- `generateHeaderGreeting()` - Welcome message
- `isAdmin()` - Check admin privileges

### 5. User Profile (`profile.php`)

**Account Information Display:**
- Username and email (read-only)
- Member since date
- Last login timestamp

**Editable Fields:**
- First name and last name
- Phone number
- Profile update with validation

## Security Measures

### Password Security
- **Hashing:** Using PHP's `password_hash()` with default algorithm
- **Verification:** `password_verify()` for login
- **Requirements:** Minimum 6 characters with letters and numbers

### Input Validation
- **Server-side validation** for all forms
- **Sanitization** with `htmlspecialchars()`
- **Prepared statements** prevent SQL injection
- **CSRF tokens** prevent cross-site request forgery

### Session Security
- **Session regeneration** on login
- **Secure cookies** when possible
- **Session timeout** handling
- **Proper logout** with session destruction

## Usage Examples

### Check if User is Logged In
```php
require_once '../php/session.php';

if (isLoggedIn()) {
    echo "Welcome, " . getUserDisplayName();
} else {
    echo "Please log in";
}
```

### Require Login for Page
```php
require_once '../php/session.php';

// Redirect to login if not authenticated
requireLogin();

// Page content for authenticated users only
echo "This is a protected page";
```

### Get Current User Data
```php
require_once '../php/session.php';

$user = getCurrentUser();
if ($user) {
    echo "User ID: " . $user['id'];
    echo "Email: " . $user['email'];
    echo "Member since: " . $user['created_at'];
}
```

### Use Dynamic Navigation
```php
require_once '../php/navigation.php';

// In your HTML page
echo generateNavigation('current-page.php');
```

## Database Schema

### Users Table Structure
```sql
CREATE TABLE users (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    phone VARCHAR(20),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);
```

## Customization Options

### 1. Password Requirements
Edit `validatePassword()` in `php/session.php`:
```php
function validatePassword($password) {
    $errors = [];
    
    if (strlen($password) < 8) {  // Change minimum length
        $errors[] = "Password must be at least 8 characters long";
    }
    
    // Add special character requirement
    if (!preg_match("/[!@#$%^&*]/", $password)) {
        $errors[] = "Password must contain a special character";
    }
    
    return $errors;
}
```

### 2. Add User Roles
```sql
ALTER TABLE users ADD COLUMN role VARCHAR(20) DEFAULT 'user';
UPDATE users SET role = 'admin' WHERE username = 'admin';
```

### 3. Session Timeout
Edit `php/session.php`:
```php
// Check session timeout (30 minutes)
if (isset($_SESSION['login_time']) && 
    (time() - $_SESSION['login_time']) > 1800) {
    logoutUser();
    header("Location: login.php?timeout=1");
    exit();
}
```

## Troubleshooting

### Common Issues

**Sessions not working:**
- Check PHP session configuration
- Ensure `session_start()` is called
- Verify file permissions for session storage

**Database connection errors:**
- Check credentials in `php/config.php`
- Ensure MySQL server is running
- Verify users table exists

**Login always fails:**
- Check password hashing
- Verify user exists and is active
- Check for case sensitivity issues

**Navigation not updating:**
- Clear browser cache
- Check PHP includes are working
- Verify session functions are available

### Debug Mode

Enable debug output in `php/config.php`:
```php
// Temporary debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

## Next Steps

1. **Test thoroughly** - Register, login, logout, profile updates
2. **Customize styling** - Match your site's design
3. **Add features** - Password reset, email verification
4. **Implement roles** - Admin panel, user permissions
5. **Enhanced security** - Rate limiting, password reset tokens

## Demo Accounts

For testing purposes, use these accounts:

**Administrator:**
- Username: `admin`
- Password: `admin123`
- Access: Full system access

**Regular User:**
- Username: `testuser` 
- Password: `user123`
- Access: Standard user features

Both accounts are created automatically when running the SQL setup script.