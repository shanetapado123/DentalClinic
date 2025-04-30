<?php
// config.php - Database configuration
$host = 'localhost';
$dbname = 'dental_clinic';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
// Prevent direct access
if (!defined('DB_HOST')) {
    die('Direct access not allowed');
}
?>