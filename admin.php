<?php
// Start session and check admin login
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Database connection
$servername = "localhost";
$username = "root"; // Replace with your username
$password = ""; // Replace with your password
$dbname = "dental_clinic"; // Based on your phpMyAdmin

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if ID is provided and valid
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];
        
        // Prepare and execute delete statement
        $stmt = $conn->prepare("DELETE FROM appointments WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Check if any row was affected
        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = "Appointment #$id deleted successfully!";
        } else {
            $_SESSION['error'] = "No appointment found with ID #$id";
        }
    } else {
        $_SESSION['error'] = "Invalid appointment ID.";
    }
    
    // Redirect back to the admin page
    header("Location: admin_dashboard.php");
    exit;
    
} catch(PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: admin.php");
    exit;
}
?>