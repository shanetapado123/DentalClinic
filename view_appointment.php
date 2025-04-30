<?php
session_start();
require 'db_config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get appointment ID from URL
$appointmentId = $_GET['id'] ?? null;
if (!$appointmentId) {
    $_SESSION['error_message'] = "Appointment ID not specified";
    header("Location: admin_dashboard.php");
    exit();
}

// Fetch appointment details
try {
    $stmt = $pdo->prepare("SELECT a.*, u.first_name, u.last_name, u.email, u.phone 
                          FROM appointments a 
                          LEFT JOIN users u ON a.user_id = u.id 
                          WHERE a.id = ?");
    $stmt->execute([$appointmentId]);
    $appointment = $stmt->fetch();
    
    if (!$appointment) {
        $_SESSION['error_message'] = "Appointment not found";
        header("Location: admin_dashboard.php");
        exit();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $newStatus = $_POST['status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE appointments SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $appointmentId]);
        
        $_SESSION['success_message'] = "Appointment status updated successfully";
        header("Location: view_appointment.php?id=" . $appointmentId);
        exit();
    } catch (PDOException $e) {
        $error = "Error updating appointment: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Appointment | Quitaneq's Dental Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
        }
        
        body {
            background-color: var(--secondary-color);
            font-family: 'Nunito', sans-serif;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid #e3e6f0;
            font-weight: 700;
        }
        
        .status-badge {
            font-size: 1rem;
            padding: 0.5rem 1rem;
        }
        
        .info-label {
            font-weight: 600;
            color: #5a5c69;
        }
        
        .info-value {
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h5 class="m-0 font-weight-bold text-primary">Appointment Details</h5>
                        <a href="admin_dashboard.php" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($_SESSION['success_message'])): ?>
                            <div class="alert alert-success">
                                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p class="info-label">Appointment ID</p>
                                <p class="info-value"><?php echo htmlspecialchars($appointment['id']); ?></p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <p class="info-label">Status</p>
                                <?php if ($appointment['status'] == 1): ?>
                                    <span class="badge bg-success status-badge">Completed</span>
                                <?php elseif ($appointment['status'] == 0): ?>
                                    <span class="badge bg-warning status-badge">Pending</span>
                                <?php else: ?>
                                    <span class="badge bg-danger status-badge">Cancelled</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <h6 class="font-weight-bold text-primary mb-3">Patient Information</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p class="info-label">Patient Name</p>
                                <p class="info-value">
                                    <div class="info-value p-3 bg-light rounded">
    <?php echo $appointment['first_name'] ? nl2br(htmlspecialchars($appointment['first_name'])) : 'No additional notes'; ?>
</div>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="info-label">Contact Email</p>
                                <p class="info-value"><?php echo htmlspecialchars($appointment['email']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="info-label">Phone Number</p>
                                <p class="info-value"><?php echo htmlspecialchars($appointment['phone']); ?></p>
                            </div>
                        </div>
                        
                        <h6 class="font-weight-bold text-primary mb-3">Appointment Details</h6>
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <p class="info-label">Service Type</p>
                                <p class="info-value"><?php echo htmlspecialchars($appointment['service_type']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="info-label">Appointment Date</p>
                                <p class="info-value">
                                    <?php echo date('F j, Y', strtotime($appointment['appointment_date'])); ?>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="info-label">Appointment Time</p>
                                <p class="info-value">
                                    <?php echo date('g:i A', strtotime($appointment['appointment_time'])); ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <p class="info-label">Reason for Visit</p>
                            <div class="info-value p-3 bg-light rounded">
                                <?php echo nl2br(htmlspecialchars($appointment['reason'])); ?>
                            </div>
                        </div>
                        
                
                        
                        <hr>
                        
                        <h6 class="font-weight-bold text-primary mb-3">Update Status</h6>
                        <form method="POST" class="mb-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <select class="form-select" name="status">
                                        <option value="0" <?php echo $appointment['status'] == 0 ? 'selected' : ''; ?>>Pending</option>
                                        <option value="1" <?php echo $appointment['status'] == 1 ? 'selected' : ''; ?>>Completed</option>
                                        <option value="2" <?php echo $appointment['status'] == 2 ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Status
                                    </button>
                                </div>
                            </div>
                        </form>
                        
                        <div class="d-flex justify-content-between">
                            <a href="edit_appointment.php?id=<?php echo $appointment['id']; ?>" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit Appointment
                            </a>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>