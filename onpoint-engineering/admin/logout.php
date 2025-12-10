<?php
include '../includes/config.php';
include '../includes/auth.php';

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: " . SITE_URL . "/login.php");
exit();
?>