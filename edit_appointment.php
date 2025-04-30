<?php
session_start();
require 'db_connection.php';

// Check authentication
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
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("
            UPDATE appointments 
            SET service_type = ?, reason = ?, appointment_date = ?, 
                appointment_time = ?, status = ?, updated_at = NOW()
            WHERE id = ?
        ");
        
        $stmt->execute([
            $_POST['service_type'] ?? '',
            $_POST['reason'] ?? '',
            $_POST['appointment_date'] ?? '',
            $_POST['appointment_time'] ?? '',
            $_POST['status'] ?? 0,
            $appointment_id
        ]);
        
        $success = "Appointment updated successfully";
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

// Fetch current appointment data
try {
    $stmt = $pdo->prepare("
        SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) as patient_name
        FROM appointments a
        LEFT JOIN users u ON a.user_id = u.id
        WHERE a.id = ?
    ");
    $stmt->execute([$appointment_id]);
    $appointment = $stmt->fetch();
    
    if (!$appointment) {
        $_SESSION['error'] = "Appointment not found";
        header("Location: admin_dashboard.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header("Location: admin_dashboard.php");
    exit();
}

// Set default values if keys don't exist
$appointment['patient_name'] = $appointment['patient_name'] ?? 'Guest';
$appointment['service_type'] = $appointment['service_type'] ?? '';
$appointment['reason'] = $appointment['reason'] ?? '';
$appointment['appointment_date'] = $appointment['appointment_date'] ?? '';
$appointment['appointment_time'] = $appointment['appointment_time'] ?? '';
$appointment['status'] = $appointment['status'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Appointment #<?php echo htmlspecialchars($appointment_id); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-title {
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h2 class="form-title">Edit Appointment #<?php echo htmlspecialchars($appointment_id); ?></h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Patient Name</label>
                    <input type="text" class="form-control" 
                           value="<?php echo htmlspecialchars($appointment['patient_name']); ?>" readonly>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Service Type</label>
                    <input type="text" name="service_type" class="form-control" 
                           value="<?php echo htmlspecialchars($appointment['service_type']); ?>" readonly>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Date</label>
                        <input type="date" name="appointment_date" class="form-control" 
                               value="<?php echo htmlspecialchars($appointment['appointment_date']); ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Time</label>
                        <input type="time" name="appointment_time" class="form-control" 
                               value="<?php echo htmlspecialchars($appointment['appointment_time']); ?>">
                    </div>
                </div>
                 <div class="mb-3">
                    <label class="form-label">Reason*</label>
                    <textarea name="reason" class="form-control" rows="3" required readonly><?php 
                        echo htmlspecialchars($appointment['reason']); 
                    ?></textarea>
                </div>
                
                
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="submit" class="btn btn-primary me-md-2">Update Appointment</button>
                    <a href="view_appointment.php" class="btn btn-secondary">Back</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>