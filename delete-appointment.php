<?php
require_once 'db_connection.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Get appointment ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid appointment ID");
}
$appointment_id = intval($_GET['id']);

// Delete appointment
$stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
$stmt->bind_param("i", $appointment_id);

if ($stmt->execute()) {
    header('Location: admin_dashboard.php?success=Appointment deleted');
} else {
    header('Location: admin_dashboard.php?error=Error deleting appointment');
}
exit;
?>