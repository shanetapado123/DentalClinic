<?php
session_start();
require 'db_config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get export format
$format = $_GET['format'] ?? 'excel';

// Fetch all appointments
try {
    $stmt = $pdo->query("SELECT 
                        a.id, 
                        CONCAT(u.first_name, ' ', u.last_name) AS patient_name,
                        a.service_type,
                        a.reason,
                        a.appointment_date,
                        a.appointment_time,
                        CASE 
                            WHEN a.status = 1 THEN 'Completed'
                            WHEN a.status = 0 THEN 'Pending'
                            ELSE 'Cancelled'
                        END AS status,
                        a.created_at
                    FROM appointments a
                    LEFT JOIN users u ON a.user_id = u.id
                    ORDER BY a.appointment_date DESC, a.appointment_time DESC");
    $appointments = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Set headers based on format
if ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="appointments_' . date('Y-m-d') . '.csv"');
} else {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="appointments_' . date('Y-m-d') . '.xls"');
}

// Open output stream
$output = fopen('php://output', 'w');

// Write headers
fputcsv($output, [
    'ID',
    'Patient Name',
    'Service Type',
    'Reason',
    'Appointment Date',
    'Appointment Time',
    'Status',
    'Created At'
], "\t"); // Use tab as delimiter for Excel compatibility

// Write data
foreach ($appointments as $appointment) {
    fputcsv($output, [
        $appointment['id'],
        $appointment['patient_name'],
        $appointment['service_type'],
        $appointment['reason'],
        $appointment['appointment_date'],
        $appointment['appointment_time'],
        $appointment['status'],
        $appointment['created_at']
    ], "\t");
}

// Close stream
fclose($output);
exit();