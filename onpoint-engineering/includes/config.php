<?php
session_start();

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'civil_engineering');
define('SITE_URL', 'http://localhost/onpoint-engineering');

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Get company info
$stmt = $pdo->query("SELECT * FROM company_info WHERE id = 1");
$company_info = $stmt->fetch();

// Include functions
require_once 'functions.php';

// Function to get base URL
function getBaseUrl() {
    return SITE_URL;
}
?>