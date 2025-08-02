# Admin Panel Setup Guide
## Used Car Purchase Website - Step 10

This guide explains how to set up and use the comprehensive admin panel for managing cars, users, themes, and site settings.

## Overview

The admin panel provides:
- **Secure Admin Authentication** with role-based access control
- **Car Management** with full CRUD operations for inventory
- **User Management** with role assignment and account control
- **Theme Settings** with site-wide default theme configuration
- **Site Settings** for system configuration and maintenance
- **Activity Logging** with comprehensive audit trails
- **Dashboard Analytics** with real-time statistics

## Installation Steps

### Step 1: Create Admin Database Setup

1. **Run the SQL script:**
```bash
mysql -u root -p used_cars < sql/admin_setup.sql
```

Or execute in phpMyAdmin:
```sql
-- Copy and paste contents of sql/admin_setup.sql
```

### Step 2: Verify Admin Accounts

The setup creates two admin accounts:
- **Primary Admin:** username `admin`, password `admin123` 
- **Super Admin:** username `superadmin`, password `admin123`

### Step 3: Access Admin Panel

1. **Visit admin login:**
```
http://localhost/finalproject/public_html/admin/login.php
```

2. **Login with admin credentials**
3. **Verify dashboard access and functionality**

## File Structure

```
finalproject/
├── sql/
│   └── admin_setup.sql              # Admin database setup
├── php/
│   └── admin-session.php            # Admin authentication & session management
├── public_html/
│   ├── admin-styles.css             # Admin panel styling
│   └── admin/
│       ├── login.php                # Admin login page
│       ├── dashboard.php            # Main admin dashboard
│       ├── cars.php                 # Car management interface
│       ├── users.php                # User management interface
│       ├── themes.php               # Theme settings management
│       ├── settings.php             # Site settings configuration
│       └── logs.php                 # Activity logs viewer
```

## Features Explained

### 1. Admin Authentication (`admin/login.php`)

**Security Features:**
- **Role-based access** - Only users with `admin` role can access
- **Secure login form** with CSRF protection
- **Session management** with activity logging
- **Failed login protection** with error logging
- **Professional UI** with security indicators

**Access Control:**
- Redirects non-admins to main site
- Requires active admin account status
- Logs all login attempts for security audit

### 2. Admin Dashboard (`admin/dashboard.php`)

**Statistics Display:**
- **User Statistics** - Total users, active count, new registrations
- **Car Inventory** - Total cars, average price, inventory metrics
- **User Activity** - Favorites count, purchase statistics
- **Revenue Analytics** - Total sales, customer counts

**Quick Actions:**
- Direct links to add new cars
- User management shortcuts
- Theme and settings access
- System monitoring tools

**Recent Activity:**
- Real-time feed of admin activities
- Detailed action descriptions
- Timestamp and user tracking
- Filterable activity log

### 3. Car Management (`admin/cars.php`)

**Full CRUD Operations:**
- **Create:** Add new cars with comprehensive details
- **Read:** List all cars with pagination and search
- **Update:** Edit existing car information
- **Delete:** Remove cars with confirmation dialogs

**Car Form Fields:**
- Basic info: Name, make, model, year, price
- Technical specs: Mileage, transmission, body type, fuel type
- Additional: Color, description, image path
- Validation: Required fields, data type checking

**Management Features:**
- **Pagination** for large inventories
- **Search functionality** across car details
- **Image management** with path validation
- **Bulk operations** and quick actions

### 4. User Management (`admin/users.php`)

**User Operations:**
- **View all users** with detailed information
- **Role management** - Promote/demote admin privileges
- **Account status** - Activate/deactivate accounts
- **User deletion** with cascade handling

**Advanced Filtering:**
- **Search users** by username, email, or name
- **Filter by role** (admin/user)
- **Filter by status** (active/inactive)
- **Date-based filtering** and sorting

**User Analytics:**
- **Activity tracking** - Favorites and purchases count
- **Login history** - Last login timestamps
- **Account creation** - Registration dates
- **Engagement metrics** - User interaction data

### 5. Theme Settings (`admin/themes.php`)

**Theme Management:**
- **Visual theme selector** with live previews
- **Default theme setting** for new visitors
- **Theme-specific customization** options
- **Site branding** - Name and tagline configuration

**Available Themes:**
- **Default Theme** - Professional blue color scheme
- **Dark Theme** - Dark mode with orange accents
- **Light Theme** - Minimal light styling

**Theme Features:**
- **Live preview** functionality
- **Instant switching** with JavaScript
- **Responsive design** across all themes
- **User preference** override system

### 6. Site Settings (`admin/settings.php`)

**General Configuration:**
- **Cars per page** - Pagination settings
- **Admin email** - Contact information
- **File upload limits** - Size restrictions
- **User registration** - Enable/disable new signups

**System Controls:**
- **Maintenance mode** - Site-wide access control
- **System information** - PHP, MySQL, server details
- **Database statistics** - Table record counts
- **Performance monitoring** - System health checks

### 7. Activity Logs (`admin/logs.php`)

**Comprehensive Logging:**
- **All admin actions** with detailed descriptions
- **User identification** - Who performed each action
- **Timestamp tracking** - When actions occurred
- **IP address logging** - Security and audit trails

**Log Filtering:**
- **Filter by action** type (create, update, delete, etc.)
- **Filter by admin** user
- **Date-based filtering** 
- **Advanced search** capabilities

**Audit Trail:**
- **Data changes** - What was modified
- **Target tracking** - Which records were affected
- **User agent** - Browser and system information
- **Security events** - Login attempts and access control

## Security Features

### Authentication & Authorization
- **Role-based access control** with admin-only restrictions
- **Secure password hashing** with bcrypt algorithm
- **Session management** with regeneration and timeout
- **CSRF token protection** on all forms and actions

### Input Validation & Sanitization
- **Server-side validation** for all form inputs
- **Input sanitization** with htmlspecialchars()
- **Prepared statements** for SQL injection prevention
- **Data type validation** and range checking

### Activity Monitoring
- **Comprehensive logging** of all admin actions
- **IP address tracking** for security monitoring
- **Failed login detection** and alert systems
- **Audit trail** for compliance and investigation

### Data Protection
- **Foreign key constraints** for data integrity
- **Cascade deletion** handling for related records
- **Backup and recovery** considerations
- **Access control** at database and application levels

## Usage Guide

### Daily Administration Tasks

1. **Check Dashboard:**
   - Review user statistics and activity
   - Monitor recent admin actions
   - Check system status indicators

2. **Manage Inventory:**
   - Add new car listings
   - Update pricing and descriptions
   - Remove sold or unavailable vehicles

3. **User Support:**
   - Activate new user accounts
   - Handle role change requests
   - Resolve account issues

4. **System Maintenance:**
   - Review activity logs for security
   - Update site settings as needed
   - Monitor system performance

### Emergency Procedures

**Maintenance Mode:**
1. Access Site Settings
2. Enable Maintenance Mode
3. Site becomes inaccessible to regular users
4. Admins retain full access

**User Account Issues:**
1. Search for user in User Management
2. Check account status and role
3. Activate/deactivate as needed
4. Review user activity logs

**Security Incidents:**
1. Check Activity Logs for suspicious activity
2. Review IP addresses and timestamps
3. Disable affected accounts if necessary
4. Change admin passwords if compromised

## Advanced Configuration

### Database Optimization
```sql
-- Add indexes for better performance
CREATE INDEX idx_admin_activity_date ON admin_activity_log(created_at);
CREATE INDEX idx_cars_price ON cars(price);
CREATE INDEX idx_users_role_status ON users(role, is_active);
```

### Custom Site Settings
```php
// Add new site settings
updateSiteSetting('feature_flag_new_search', '1', 'Enable new search functionality');
updateSiteSetting('max_favorites_per_user', '50', 'Maximum favorites per user');
```

### Theme Customization
```css
/* Custom admin theme modifications */
.admin-sidebar {
    background: linear-gradient(180deg, #your-color 0%, #your-color-dark 100%);
}

.admin-btn-primary {
    background: your-brand-color;
}
```

## API Reference

### Admin Session Functions
```php
isAdmin()                           // Check if current user is admin
requireAdmin($redirect)             // Redirect if not admin
getCurrentAdmin()                   // Get current admin user data
logAdminActivity($action, $table, $id, $details) // Log admin action
```

### Site Settings Functions
```php
getSiteSetting($key, $default)     // Get setting value
updateSiteSetting($key, $value, $desc) // Update setting
getAllSiteSettings($category)      // Get settings by category
```

### Statistics Functions
```php
getAdminStats()                     // Get dashboard statistics
hasAdminPermission($action)         // Check admin permissions
isMaintenanceMode()                 // Check maintenance status
```

## Troubleshooting

### Common Issues

**Cannot access admin panel:**
- Verify user has admin role in database
- Check if account is active
- Confirm correct login credentials
- Review error logs for authentication issues

**Dashboard not loading statistics:**
- Check database connections
- Verify table structure matches schema
- Review PHP error logs
- Test individual database queries

**Activity logs not recording:**
- Ensure logAdminActivity() calls are present
- Check admin_activity_log table exists
- Verify foreign key constraints
- Review database permissions

**Theme changes not applying:**
- Clear browser cache
- Check CSS file permissions
- Verify theme files exist
- Test theme switcher JavaScript

### Debug Mode

Enable debugging in admin pages:
```php
// Add to admin page headers for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
```

### Database Issues

Check admin database setup:
```sql
-- Verify admin users
SELECT username, role, is_active FROM users WHERE role = 'admin';

-- Check activity logs
SELECT COUNT(*) FROM admin_activity_log;

-- Verify site settings
SELECT * FROM site_settings ORDER BY category, setting_key;
```

## Customization Examples

### Adding New Admin Functions

1. **Create new admin page:**
```php
<?php
require_once '../../php/admin-session.php';
requireAdmin();
// Your admin functionality here
?>
```

2. **Add to navigation:**
```php
// In sidebar navigation
<li><a href="custom.php">🔧 Custom Feature</a></li>
```

3. **Log activities:**
```php
logAdminActivity('custom_action', 'target_table', $id, 'Description of action');
```

### Custom Dashboard Widgets

```php
// Add to dashboard.php
<div class="admin-stat-card">
    <div class="stat-icon">📈</div>
    <div class="stat-content">
        <div class="stat-number"><?php echo $custom_metric; ?></div>
        <div class="stat-label">Custom Metric</div>
    </div>
</div>
```

### Extended User Management

```php
// Add custom user fields
function updateUserCustomField($userId, $field, $value) {
    // Implementation
    logAdminActivity('update_user_custom', 'users', $userId, "Updated $field");
}
```

## Security Best Practices

1. **Regular Updates:**
   - Review and update admin passwords regularly
   - Monitor activity logs for unusual patterns
   - Keep database and PHP versions current

2. **Access Control:**
   - Limit admin role assignments
   - Use strong passwords for admin accounts
   - Enable two-factor authentication if available

3. **Monitoring:**
   - Review activity logs weekly
   - Monitor failed login attempts
   - Set up alerts for suspicious activity

4. **Backup:**
   - Regular database backups
   - Test backup restoration procedures
   - Secure backup storage

## Support and Maintenance

### Regular Maintenance Tasks

**Weekly:**
- Review activity logs for security
- Check system performance metrics
- Update car inventory as needed
- Monitor user account status

**Monthly:**
- Backup database and files
- Review and clean old log entries
- Update site settings as needed
- Check for system updates

**Quarterly:**
- Security audit of admin accounts
- Performance optimization review
- Feature usage analysis
- Documentation updates

The admin panel provides comprehensive control over your Used Car Purchase Website with enterprise-level security and functionality!