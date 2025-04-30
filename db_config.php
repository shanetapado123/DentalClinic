<?php
$host = 'localhost';
$dbname = 'dental_clinic';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Change if different
define('DB_PASS', ''); // Change if you have a password
define('DB_NAME', 'dental_clinic');

// Disable error display in production
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Database configuration for dental_clinic
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Or your phpMyAdmin username
define('DB_PASS', ''); // Or your phpMyAdmin password
define('DB_NAME', 'dental_clinic');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', '1');
// Hidden admin trigger (change to something more secure)
define('HIDDEN_ADMIN_KEY', 'dental_admin_2024');
?>
