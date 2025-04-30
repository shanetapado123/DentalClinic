<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db_config.php';

// Check if appointment ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "Invalid appointment ID";
    header("Location: dashboard.php");
    exit();
}

$appointment_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

try {
    // Verify the appointment belongs to the current user
    $stmt = $pdo->prepare("SELECT id, status FROM appointments WHERE id = ? AND user_id = ?");
    $stmt->execute([$appointment_id, $user_id]);
    $appointment = $stmt->fetch();

    if (!$appointment) {
        $_SESSION['error'] = "Appointment not found or you don't have permission to cancel it";
        header("Location: dashboard.php");
        exit();
    }

    // Check if appointment can be cancelled (only pending or confirmed)
    if (!in_array($appointment['status'], ['pending', 'confirmed'])) {
        $_SESSION['error'] = "Only pending or confirmed appointments can be cancelled";
        header("Location: dashboard.php");
        exit();
    }

    // Update the appointment status to cancelled
    $update_stmt = $pdo->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ?");
    $update_stmt->execute([$appointment_id]);

    $_SESSION['success'] = "Appointment successfully cancelled";
    header("Location: dashboard.php");
    exit();

} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: dashboard.php");
    exit();
}
?>