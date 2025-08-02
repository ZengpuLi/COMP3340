# Interactive Forms & Loan Calculator Setup Guide
## Used Car Purchase Website - Step 12

This guide explains how to set up and use the comprehensive forms and calculator system that enables user interaction and loan payment calculations.

## Overview

The forms and calculator enhancement includes:
- **Loan/Payment Calculator** with real-time JavaScript calculations
- **Car Inquiry Form** with database integration and email preferences
- **Comprehensive Security** with CSRF protection and input validation
- **User Experience** with pre-filled forms for logged-in users
- **Database Integration** for storing inquiries and calculator sessions

## Features Implemented

### 1. Database Setup

#### **New Tables Created:**

##### **Inquiries Table:**
```sql
CREATE TABLE inquiries (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NULL,                           -- FK to users (nullable for guests)
    car_id INT(11) NOT NULL,                        -- FK to cars
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NULL,
    message TEXT NOT NULL,
    inquiry_type ENUM('general', 'test_drive', 'financing', 'trade_in') DEFAULT 'general',
    status ENUM('new', 'contacted', 'scheduled', 'completed', 'closed') DEFAULT 'new',
    preferred_contact ENUM('email', 'phone', 'both') DEFAULT 'email',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

##### **Calculator Sessions Table (Optional):**
```sql
CREATE TABLE calculator_sessions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NULL,                           -- FK to users (nullable for guests)
    car_id INT(11) NULL,                            -- FK to cars (nullable)
    car_price DECIMAL(10, 2) NOT NULL,
    down_payment DECIMAL(10, 2) NOT NULL,
    loan_term INT(2) NOT NULL,
    interest_rate DECIMAL(5, 2) NOT NULL,
    monthly_payment DECIMAL(10, 2) NOT NULL,
    total_payment DECIMAL(10, 2) NOT NULL,
    total_interest DECIMAL(10, 2) NOT NULL,
    session_ip VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### **Sample Data Included:**
- **5 sample inquiries** demonstrating different inquiry types
- **5 sample calculator sessions** with realistic loan calculations
- **Proper foreign key relationships** with cascade handling

### 2. Loan Payment Calculator

#### **Calculator Features (`calculator.php`):**

##### **Real-Time Calculations:**
```javascript
// Standard loan payment formula: M = P * [r(1+r)^n] / [(1+r)^n - 1]
const monthlyRate = (interestRate / 100) / 12;
const numPayments = loanTerm * 12;
const monthlyPayment = loanAmount * (monthlyRate * Math.pow(1 + monthlyRate, numPayments)) / 
                      (Math.pow(1 + monthlyRate, numPayments) - 1);
```

##### **Calculator Form Fields:**
- **Vehicle Price** - With dollar prefix, min/max validation
- **Down Payment** - Auto-calculated as 20% of vehicle price by default
- **Loan Term** - Years input with suffix, 1-10 year range
- **Interest Rate** - Percentage input with suffix, 0.1-20% range

##### **Instant Results Display:**
- **Monthly Payment** - Primary result in highlighted format
- **Total Payment** - Including down payment
- **Total Interest** - Amount paid over principal
- **Payment Breakdown** - Detailed calculation components

##### **Advanced Features:**
- **Car Integration** - Pre-fills price when accessed from car detail
- **Save Calculations** - Stores sessions for logged-in users
- **Financing Tips** - Educational content about loan terms
- **Responsive Design** - Mobile-optimized layout

#### **JavaScript Functionality:**
```javascript
// Auto-calculate on input change
inputs.forEach(input => {
    input.addEventListener('input', calculatePayment);
});

// Save calculation via AJAX
function saveCalculation() {
    const formData = new FormData();
    formData.append('action', 'save_calculation');
    // ... calculation data
    
    fetch('calculator.php', {
        method: 'POST',
        body: formData
    }).then(response => response.json())
      .then(data => showMessage(data.message, data.success ? 'success' : 'error'));
}
```

### 3. Car Inquiry Form

#### **Inquiry Form Features (`inquiry.php`):**

##### **User Information Section:**
- **Auto-prefill** for logged-in users from profile data
- **Full Name** - Required field with validation
- **Email Address** - Required with email format validation
- **Phone Number** - Optional with formatting and validation

##### **Vehicle Interest Section:**
- **Car Selection** - Dropdown populated from database
- **Inquiry Type** - Radio buttons for specific inquiry types:
  - General Question
  - Schedule Test Drive
  - Financing Options
  - Trade-in Value
- **Message** - Required textarea with character counter (minimum 10 chars)

##### **Contact Preferences:**
- **Preferred Contact Method** - Radio buttons:
  - Email
  - Phone
  - Either

#### **Database Integration:**
```php
// Insert inquiry with proper validation
$query = "INSERT INTO inquiries (user_id, car_id, name, email, phone, message, inquiry_type, preferred_contact) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $connection->prepare($query);
$user_id = isLoggedIn() ? $_SESSION['user_id'] : null;

$stmt->bind_param("iissssss", 
    $user_id, $inquiry_car_id, $name, $email, $phone, $message, $inquiry_type, $preferred_contact
);
```

#### **User Experience Features:**
- **Selected Car Display** - Shows car details when accessed from specific vehicle
- **Contact Information** - Business hours and contact details
- **Quick Response Guarantee** - Service level expectations
- **Form Validation** - Real-time client-side and server-side validation

### 4. Security Implementation

#### **CSRF Protection:**
```php
// Generate and verify CSRF tokens
$csrf_token = generateCSRFToken();

if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    $error_message = "Security token validation failed. Please try again.";
}
```

#### **Input Validation & Sanitization:**
```php
// Comprehensive input validation
$name = sanitizeInput($_POST['name'] ?? '');
$email = sanitizeInput($_POST['email'] ?? '');

// Validation rules
if (empty($name) || strlen($name) < 2) {
    $errors[] = "Name must be at least 2 characters long";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Valid email address is required";
}

// Phone number validation
if (!empty($phone) && !preg_match('/^[\+]?[1-9][\d]{0,15}$/', preg_replace('/[\s\-\(\)]/', '', $phone))) {
    $errors[] = "Phone number format is invalid";
}
```

#### **SQL Injection Prevention:**
```php
// Prepared statements for all database operations
$stmt = $connection->prepare($query);
$stmt->bind_param("iissssss", $user_id, $inquiry_car_id, $name, $email, $phone, $message, $inquiry_type, $preferred_contact);
```

### 5. Enhanced Navigation System

#### **Updated Navigation Structure:**
```
Home → About Us → Cars → Locations → Market Trends → Loan Calculator → Contact → Help → Privacy
```

#### **Navigation Updates Applied To:**
- ✅ All HTML pages (`index.html`, `about.html`, `contact.html`, `help.html`, `privacy.html`)
- ✅ All multimedia pages (`locations.html`, `market-trends.html`)
- ✅ PHP navigation system (`php/navigation.php`)
- ✅ All user authentication pages (login, register, profile, etc.)

### 6. Form Styling & User Experience

#### **Professional Form Design:**
- **Grid-based layout** with responsive column structure
- **Material design** inspired form elements
- **Color-coded sections** with clear visual hierarchy
- **Interactive elements** with hover and focus states

#### **Form Validation UI:**
```javascript
// Real-time validation feedback
requiredFields.forEach(field => {
    if (!field.value.trim()) {
        field.style.borderColor = '#e74c3c';
    } else {
        field.style.borderColor = '';
    }
});
```

#### **Enhanced Form Elements:**
- **Input prefixes/suffixes** for currency and percentages
- **Radio button styling** with visual selection feedback
- **Character counters** for text areas
- **Phone number formatting** with automatic formatting
- **Auto-resizing textareas** for better user experience

### 7. Integration Features

#### **Cross-Feature Integration:**
- **Calculator ↔ Car Listings** - Direct access from car details
- **Calculator ↔ Inquiry Form** - Seamless transition between tools
- **User Authentication** - Pre-filled forms for logged-in users
- **Admin Activity** - Logged actions for tracking and analytics

#### **Navigation Flow:**
```
Car Listing → Calculator → Inquiry Form → Contact Information
     ↓             ↓            ↓              ↓
Price Research → Payment Calc → Send Inquiry → Response
```

## File Structure

```
finalproject/
├── sql/
│   └── forms_setup.sql                  # NEW: Database setup for forms
├── public_html/
│   ├── calculator.php                   # NEW: Loan payment calculator
│   ├── inquiry.php                      # NEW: Car inquiry form
│   ├── index.html                       # UPDATED: Navigation
│   ├── about.html                       # UPDATED: Navigation
│   ├── contact.html                     # UPDATED: Navigation
│   ├── help.html                        # UPDATED: Navigation
│   ├── privacy.html                     # UPDATED: Navigation
│   ├── locations.html                   # UPDATED: Navigation
│   └── market-trends.html               # UPDATED: Navigation
├── php/
│   └── navigation.php                   # UPDATED: Added calculator link
└── FORMS_CALCULATOR_SETUP.md            # NEW: This documentation
```

## Usage Instructions

### 1. Database Setup

```bash
# Import the forms database setup
mysql -u root -p used_cars < sql/forms_setup.sql
```

### 2. Loan Calculator Usage

#### **Basic Calculator:**
1. **Visit Calculator Page:**
   ```
   http://localhost/finalproject/public_html/calculator.php
   ```

2. **Enter Loan Details:**
   - Vehicle Price: $25,000
   - Down Payment: $5,000 (auto-calculated as 20%)
   - Loan Term: 5 years
   - Interest Rate: 4.5%

3. **View Results:**
   - Monthly Payment: $372.86
   - Total Payment: $27,371.60
   - Total Interest: $2,371.60

#### **Calculator with Specific Car:**
```
http://localhost/finalproject/public_html/calculator.php?car_id=1
```

#### **Save Calculations:**
- Click "Save Calculation" button
- Requires user login for persistence
- Stored in `calculator_sessions` table

### 3. Car Inquiry Form Usage

#### **Basic Inquiry:**
1. **Visit Inquiry Page:**
   ```
   http://localhost/finalproject/public_html/inquiry.php
   ```

2. **Fill Out Form:**
   - Personal information (name, email, phone)
   - Select vehicle from dropdown
   - Choose inquiry type
   - Write detailed message
   - Select contact preference

3. **Submit Inquiry:**
   - Form validates all required fields
   - Stores in `inquiries` table
   - Shows success confirmation

#### **Inquiry from Car Details:**
```
http://localhost/finalproject/public_html/inquiry.php?car_id=1
```

### 4. Admin Management

#### **View Inquiries:**
```sql
-- Check recent inquiries
SELECT i.*, c.name as car_name, u.username 
FROM inquiries i 
JOIN cars c ON i.car_id = c.id 
LEFT JOIN users u ON i.user_id = u.id 
ORDER BY i.created_at DESC;
```

#### **View Calculator Sessions:**
```sql
-- Check calculator usage
SELECT cs.*, c.name as car_name, u.username 
FROM calculator_sessions cs 
LEFT JOIN cars c ON cs.car_id = c.id 
LEFT JOIN users u ON cs.user_id = u.id 
ORDER BY cs.created_at DESC;
```

## Technical Implementation

### 1. Loan Calculation Algorithm

#### **Monthly Payment Formula:**
```javascript
// Standard amortization formula
if (monthlyRate === 0) {
    // No interest case
    monthlyPayment = loanAmount / numPayments;
} else {
    // M = P * [r(1+r)^n] / [(1+r)^n - 1]
    monthlyPayment = loanAmount * (monthlyRate * Math.pow(1 + monthlyRate, numPayments)) / 
                    (Math.pow(1 + monthlyRate, numPayments) - 1);
}
```

#### **Validation Rules:**
- **Car Price:** $1,000 - $200,000
- **Down Payment:** $0 - Car Price
- **Loan Term:** 1 - 10 years
- **Interest Rate:** 0.1% - 20%

### 2. Form Validation System

#### **Client-Side Validation:**
```javascript
// Real-time validation
form.addEventListener('submit', function(e) {
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            field.style.borderColor = '#e74c3c';
        }
    });
    
    if (!isValid) {
        e.preventDefault();
        alert('Please fill in all required fields.');
    }
});
```

#### **Server-Side Validation:**
```php
// Comprehensive validation
$errors = [];

if (empty($name) || strlen($name) < 2) {
    $errors[] = "Name must be at least 2 characters long";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Valid email address is required";
}

if (!empty($errors)) {
    $error_message = implode('<br>', $errors);
}
```

### 3. AJAX Integration

#### **Calculator Session Saving:**
```javascript
fetch('calculator.php', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        showMessage('Calculation saved successfully!', 'success');
    } else {
        showMessage(data.message, 'error');
    }
});
```

#### **Form State Management:**
```javascript
// Preserve form state during validation
window.currentCalculation = {
    car_price: carPrice,
    down_payment: downPayment,
    loan_term: loanTerm,
    interest_rate: interestRate,
    monthly_payment: monthlyPayment,
    total_payment: totalPayment,
    total_interest: totalInterest
};
```

## Security Considerations

### 1. Input Validation

#### **Sanitization Functions:**
```php
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Additional validation
$phone = preg_replace('/[\s\-\(\)]/', '', $phone);
if (!preg_match('/^[\+]?[1-9][\d]{0,15}$/', $phone)) {
    $errors[] = "Invalid phone number format";
}
```

#### **SQL Injection Prevention:**
```php
// Always use prepared statements
$stmt = $connection->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->bind_param("i", $car_id);
$stmt->execute();
```

### 2. CSRF Protection

#### **Token Generation:**
```php
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
```

#### **Token Verification:**
```php
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
```

### 3. Data Protection

#### **User Privacy:**
- **Optional phone numbers** with validation
- **Contact preferences** respected in communications
- **Guest inquiries** supported without requiring registration
- **Data retention** policies for calculator sessions

## Customization Options

### 1. Calculator Parameters

#### **Interest Rate Ranges:**
```javascript
// Customize min/max interest rates
const minInterestRate = 0.1;  // 0.1%
const maxInterestRate = 20.0; // 20%

// Default rates by credit tier
const defaultRates = {
    excellent: 3.5,
    good: 4.5,
    fair: 6.5,
    poor: 9.5
};
```

#### **Loan Term Options:**
```php
// Customize available loan terms
$loan_terms = [
    1 => '1 year',
    2 => '2 years', 
    3 => '3 years',
    4 => '4 years',
    5 => '5 years',
    6 => '6 years',
    7 => '7 years'
];
```

### 2. Form Customization

#### **Inquiry Types:**
```php
$inquiry_types = [
    'general' => 'General Question',
    'test_drive' => 'Schedule Test Drive',
    'financing' => 'Financing Options',
    'trade_in' => 'Trade-in Value',
    'warranty' => 'Warranty Information',
    'inspection' => 'Vehicle Inspection'
];
```

#### **Contact Methods:**
```php
$contact_methods = [
    'email' => 'Email',
    'phone' => 'Phone Call',
    'text' => 'Text Message',
    'both' => 'Email or Phone'
];
```

### 3. Email Integration

#### **SMTP Configuration:**
```php
// Email notification setup
function sendInquiryNotification($inquiry_data) {
    $to = getSiteSetting('admin_email', 'admin@autodeals.com');
    $subject = "New Car Inquiry - {$inquiry_data['car_name']}";
    $message = generateInquiryEmail($inquiry_data);
    
    // Use PHPMailer or similar for production
    mail($to, $subject, $message, $headers);
}
```

## Analytics & Reporting

### 1. Calculator Analytics

#### **Usage Statistics:**
```sql
-- Calculator usage by month
SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    COUNT(*) as calculations,
    AVG(car_price) as avg_price,
    AVG(monthly_payment) as avg_payment
FROM calculator_sessions 
GROUP BY DATE_FORMAT(created_at, '%Y-%m')
ORDER BY month DESC;
```

#### **Popular Price Ranges:**
```sql
-- Most calculated price ranges
SELECT 
    CASE 
        WHEN car_price < 15000 THEN 'Under $15K'
        WHEN car_price < 25000 THEN '$15K - $25K'
        WHEN car_price < 35000 THEN '$25K - $35K'
        ELSE 'Over $35K'
    END as price_range,
    COUNT(*) as calculations
FROM calculator_sessions
GROUP BY price_range
ORDER BY calculations DESC;
```

### 2. Inquiry Analytics

#### **Inquiry Types Distribution:**
```sql
-- Inquiry type popularity
SELECT 
    inquiry_type,
    COUNT(*) as total_inquiries,
    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
    ROUND(COUNT(CASE WHEN status = 'completed' THEN 1 END) * 100.0 / COUNT(*), 2) as completion_rate
FROM inquiries 
GROUP BY inquiry_type
ORDER BY total_inquiries DESC;
```

#### **Response Time Tracking:**
```sql
-- Average response times
SELECT 
    DATE(created_at) as inquiry_date,
    COUNT(*) as daily_inquiries,
    COUNT(CASE WHEN status != 'new' THEN 1 END) as responded,
    AVG(CASE WHEN updated_at != created_at THEN 
        TIMESTAMPDIFF(HOUR, created_at, updated_at) 
    END) as avg_response_hours
FROM inquiries 
GROUP BY DATE(created_at)
ORDER BY inquiry_date DESC;
```

## Performance Optimization

### 1. Database Optimization

#### **Indexes for Performance:**
```sql
-- Optimize query performance
CREATE INDEX idx_inquiries_car_status ON inquiries(car_id, status);
CREATE INDEX idx_calculator_user_created ON calculator_sessions(user_id, created_at);
CREATE INDEX idx_inquiries_created_type ON inquiries(created_at, inquiry_type);
```

#### **Query Optimization:**
```sql
-- Efficient inquiry retrieval
SELECT i.*, c.name as car_name, c.price, u.username
FROM inquiries i
JOIN cars c ON i.car_id = c.id
LEFT JOIN users u ON i.user_id = u.id
WHERE i.status = 'new'
ORDER BY i.created_at DESC
LIMIT 20;
```

### 2. Frontend Optimization

#### **Form Performance:**
- **Debounced validation** for real-time feedback
- **Lazy loading** for large car dropdown lists
- **Progressive enhancement** for JavaScript features
- **Cached calculations** to avoid repeated computations

#### **Mobile Optimization:**
- **Touch-friendly** form elements
- **Optimized keyboard** types for numeric inputs
- **Compressed assets** for faster loading
- **Responsive layouts** for all screen sizes

## Troubleshooting

### Common Issues

#### **Calculator Not Working:**
1. Check JavaScript console for errors
2. Verify form field names match JavaScript selectors
3. Ensure number inputs have proper min/max values
4. Test calculation formula with known values

#### **Form Submissions Failing:**
1. Verify CSRF token generation and verification
2. Check database connection and table structure
3. Review PHP error logs for detailed error messages
4. Test form validation with various input combinations

#### **Navigation Issues:**
1. Ensure all navigation files are updated consistently
2. Check file paths are correct for all environments
3. Verify active link highlighting works on all pages
4. Test navigation on mobile devices

### Debug Information

#### **Enable Debug Mode:**
```php
// Add to form pages for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
```

#### **Database Query Testing:**
```php
// Test database queries
try {
    $connection = getDatabaseConnection();
    $query = "SELECT COUNT(*) as total FROM inquiries";
    $result = $connection->query($query);
    $count = $result->fetch_assoc();
    echo "Total inquiries: " . $count['total'];
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage();
}
```

## Future Enhancements

### Potential Additions

#### **Advanced Calculator Features:**
- **Loan comparison** tool with multiple offers
- **Trade-in value** integration with KBB API
- **Insurance cost** estimation
- **Total cost of ownership** calculator
- **Financing pre-approval** integration

#### **Enhanced Form Features:**
- **File upload** for trade-in photos
- **Appointment scheduling** calendar integration
- **Live chat** integration for instant support
- **SMS notifications** for inquiry status updates
- **CRM integration** for lead management

#### **Analytics Dashboard:**
- **Real-time inquiry** monitoring
- **Calculator usage** analytics
- **Conversion rate** tracking
- **Popular car models** analysis
- **Customer journey** mapping

The forms and calculator system provides comprehensive tools for user engagement while maintaining professional security standards and user experience excellence!