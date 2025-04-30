<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


$conn = new PDO("mysql:host=127.0.0.1;dbname=dental_clinic", 'root', '');
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


$stmt = $conn->prepare("SELECT * FROM appointments 
                       WHERE user_id = ? 
                       ORDER BY appointment_date DESC, appointment_time DESC");
$stmt->execute([$_SESSION['user_id']]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Quitaneq's Dental Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .welcome-message {
            margin-bottom: 30px;
        }
        .appointment-card {
            margin-bottom: 20px;
            border-left: 4px solid #0d6efd;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
        }
        .delete-btn:hover {
            background-color: #bb2d3b;
            color: white;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="d-flex justify-content-between align-items-center welcome-message">
            <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'user'); ?>!</h1>
            <a href="logout.php" class="btn btn-outline-danger">Logout</a>
        </div>
        
        <p class="lead">Manage your dental appointments with us.</p>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <div class="d-grid gap-2 d-md-block mb-4">
            <a href="appointment_form.php" class="btn btn-primary">Set New Appointment</a>
        </div>
        
        <h2>Your Appointments</h2>
        
        <?php if (empty($appointments)): ?>
            <div class="alert alert-info">You have no upcoming appointments.</div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($appointments as $appointment): ?>
                    <div class="col-md-6">
                        <div class="card appointment-card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($appointment['service_type']); ?></h5>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <?php 
                                        $date = new DateTime($appointment['appointment_date']);
                                        echo $date->format('F j, Y') . ' at ' . $appointment['appointment_time'];
                                    ?>
                                </h6>
                                <?php if (!empty($appointment['appointment_reason'])): ?>
                                    <p class="card-text"><?php echo htmlspecialchars($appointment['appointment_reason']); ?></p>
                                <?php endif; ?>
                                <a href="delete_appointment.php?id=<?php echo $appointment['id']; ?>" 
                                   class="btn btn-sm delete-btn"
                                   onclick="return confirm('Are you sure you want to delete this appointment?')">
                                    Delete
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>