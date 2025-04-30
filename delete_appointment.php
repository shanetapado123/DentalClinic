<?php
session_start();
require 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Validate appointment ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid appointment ID";
    header("Location: admin_dashboard.php");
    exit();
}

$appointment_id = $_GET['id'];

try {
    // First check the appointment status
    $check_stmt = $pdo->prepare("SELECT status FROM appointments WHERE id = ?");
    $check_stmt->execute([$appointment_id]);
    $appointment = $check_stmt->fetch();

    if (!$appointment) {
        $_SESSION['error'] = "Appointment not found";
        header("Location: admin_dashboard.php");
        exit();
    }

    // Only allow deletion of completed (1) or cancelled (2) appointments
    if ($appointment['status'] == 0) {
        $_SESSION['error'] = "Cannot delete pending appointments. Please cancel them first.";
        header("Location: admin_dashboard.php");
        exit();
    }

    // Proceed with deletion
    $delete_stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ?");
    $delete_stmt->execute([$appointment_id]);

    if ($delete_stmt->rowCount() > 0) {
        $_SESSION['success'] = "Appointment deleted successfully";
    } else {
        $_SESSION['error'] = "No appointment was deleted";
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
}

header("Location: admin_dashboard.php");
exit();
?>