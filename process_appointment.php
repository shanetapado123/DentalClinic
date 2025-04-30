<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Database configuration
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dental_clinic');

// Connect to database
try {
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    $_SESSION['error'] = "Database connection failed";
    header("Location: appointment_form.php");
    exit;
}

// Validate and sanitize input
$required = ['service_type', 'appointment_date', 'appointment_time'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['error'] = "Please fill all required fields";
        header("Location: appointment_form.php");
        exit;
    }
}

$service_type = htmlspecialchars($_POST['service_type']);
$appointment_date = htmlspecialchars($_POST['appointment_date']);
$appointment_time = htmlspecialchars($_POST['appointment_time']);
$appointment_reason = isset($_POST['appointment_reason']) ? htmlspecialchars($_POST['appointment_reason']) : null;

// Validate date and time
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $appointment_date)) {
    $_SESSION['error'] = "Invalid date format";
    header("Location: appointment_form.php");
    exit;
}

if (!preg_match('/^\d{2}:\d{2}$/', $appointment_time)) {
    $_SESSION['error'] = "Invalid time format";
    header("Location: appointment_form.php");
    exit;
}

// Check if the selected time is in the future
$appointment_datetime = new DateTime("$appointment_date $appointment_time");
$now = new DateTime();
if ($appointment_datetime <= $now) {
    $_SESSION['error'] = "Appointment must be in the future";
    header("Location: appointment_form.php");
    exit;
}

// Check for existing appointments at the same time
try {
    $stmt = $conn->prepare("SELECT id FROM appointments 
                          WHERE appointment_date = ? 
                          AND appointment_time = ? 
                          AND user_id = ?");
    $stmt->execute([$appointment_date, $appointment_time, $_SESSION['user_id']]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "You already have an appointment at this time";
        header("Location: appointment_form.php");
        exit;
    }
} catch(PDOException $e) {
    $_SESSION['error'] = "Error checking availability";
    header("Location: appointment_form.php");
    exit;
}

// Insert the appointment
try {
    $stmt = $conn->prepare("INSERT INTO appointments 
                          (user_id, service_type, appointment_reason, 
                          appointment_date, appointment_time, created_at) 
                          VALUES (?, ?, ?, ?, ?, NOW())");
    
    $stmt->execute([
        $_SESSION['user_id'],
        $service_type,
        $appointment_reason,
        $appointment_date,
        $appointment_time
    ]);
    
    $_SESSION['success'] = "Appointment booked successfully!";
    header("Location: dashboard.php"); // Redirect to dashboard after successful booking
    exit;
    
} catch(PDOException $e) {
    error_log("Appointment Error: " . $e->getMessage());
    $_SESSION['error'] = "Error saving appointment. Please try again.";
    header("Location: appointment_form.php");
    exit;
}
?>