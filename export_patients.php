<?php
session_start();
require 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get export format
$format = $_GET['format'] ?? 'csv';

// Fetch all patients
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY last_name, first_name");
    $patients = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Set headers based on format
if ($format === 'excel') {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="patients_' . date('Y-m-d') . '.xls"');
} else {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="patients_' . date('Y-m-d') . '.csv"');
}

// Open output stream
$output = fopen('php://output', 'w');

// Write headers
fputcsv($output, [
    'ID',
    'First Name',
    'Last Name',
    'Email',
    'Phone',
    'Date Registered'
], "\t"); // Use tab as delimiter for Excel compatibility

// Write data
foreach ($patients as $patient) {
    fputcsv($output, [
        $patient['id'],
        $patient['first_name'],
        $patient['last_name'],
        $patient['email'],
        $patient['phone'],
        $patient['created_at']
    ], "\t");
}

// Close stream
fclose($output);
exit();