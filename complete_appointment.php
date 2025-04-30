<?php
session_start();
require 'db_config.php'; // Include your database configuration

// Check if appointment ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: appointments.php?error=invalid_id');
    exit();
}

$appointment_id = (int)$_GET['id'];

try {
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Update appointment status to 'Completed'
    $stmt = $pdo->prepare("UPDATE appointments SET status = 'Completed', last_updated = NOW() WHERE id = :id");
    $stmt->bindParam(':id', $appointment_id, PDO::PARAM_INT);
    $stmt->execute();

    // Check if any row was actually updated
    if ($stmt->rowCount() > 0) {
        // Log this action
        $log_stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details) VALUES (:user_id, 'appointment_completed', :details)");
        $log_stmt->bindParam(':user_id', $_SESSION['user_id']);
        $log_stmt->bindValue(':details', "Completed appointment #$appointment_id");
        $log_stmt->execute();

        header('Location: appointment_details.php?id='.$appointment_id.'&success=1');
    } else {
        header('Location: appointment_details.php?id='.$appointment_id.'&error=not_found');
    }
    exit();

} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    header('Location: appointment_details.php?id='.$appointment_id.'&error=db_error');
    exit();
}
?>