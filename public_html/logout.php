<?php
/**
 * User Logout Page
 * Used Car Purchase Website - Authentication System
 */

// Include session management
require_once '../php/session.php';

// Log out the user
logoutUser();

// Redirect to home page
header("Location: index.html");
exit();
?>