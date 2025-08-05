<?php
/**
 * Simple Logout Page
 * Used Car Purchase Website
 */

// Start session
session_start();

// Destroy all session data
session_destroy();

// Redirect to home page
header('Location: index.html');
exit;
?>