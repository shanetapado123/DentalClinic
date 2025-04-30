<?php
session_start();
require 'db_config.php';

$stmt = $pdo->query("SELECT * FROM appointments ORDER BY appointment_date DESC, appointment_time DESC");
$appointments = $stmt->fetchAll();

$today = date('Y-m-d');


$today_stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_date = ?");
$today_stmt->execute([$today]);
$today_appointments = $today_stmt->fetchColumn();
$patients_stmt = $pdo->query("SELECT COUNT(*) FROM users");
$total_patients = $patients_stmt->fetchColumn();
$pending_stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE status = 0");
$pending_stmt->execute();
$pending_appointments = $pending_stmt->fetchColumn();


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Quitaneq's Dental Clinic</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
    :root {
        --primary-color: #6c5ce7;
        --secondary-color: #f8f9fe;
        --accent-color: #a29bfe;
        --text-dark: #2d3436;
        --success-color: #00b894;
        --warning-color: #fdcb6e;
        --danger-color: #d63031;
        --info-color: #0984e3;
    }
    
    body {
        background-color: var(--secondary-color);
        font-family: 'Poppins', sans-serif;
        color: var(--text-dark);
    }
    
    .sidebar {
        background: linear-gradient(135deg, var(--primary-color) 0%, #8c7ae6 100%);
        box-shadow: 4px 0 15px rgba(0, 0, 0, 0.1);
    }
    
    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 8px;
        border-radius: 8px;
        padding: 12px 15px;
        transition: all 0.3s ease;
    }
    
    .sidebar .nav-link:hover {
        color: white;
        background-color: rgba(255, 255, 255, 0.15);
        transform: translateX(5px);
    }
    
    .sidebar .nav-link.active {
        color: white;
        font-weight: 600;
        background-color: rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 20px -5px rgba(255, 255, 255, 0.2);
    }
    
    .sidebar .nav-link i {
        margin-right: 12px;
        font-size: 1.1rem;
    }
    
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.08);
        margin-bottom: 25px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.12);
    }
    
    .card-header {
        background-color: white;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        font-weight: 700;
        padding: 1.25rem 1.5rem;
        color: var(--primary-color);
    }
    
    .table-responsive {
        overflow-x: auto;
        border-radius: 12px;
    }
    
    .table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table th {
        border-top: none;
        color: var(--primary-color);
        font-weight: 600;
        padding: 1.25rem;
        background-color: rgba(108, 92, 231, 0.05);
        border-bottom: 2px solid rgba(108, 92, 231, 0.1);
    }
    
    .table td {
        padding: 1.25rem;
        vertical-align: middle;
        border-bottom: 1px solid rgba(0, 0, 0, 0.03);
        transition: background-color 0.2s ease;
    }
    
    .table tr:hover td {
        background-color: rgba(108, 92, 231, 0.03);
    }
    
    .badge-success {
        background-color: var(--success-color);
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .badge-warning {
        background-color: var(--warning-color);
        color: #2d3436;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .badge-danger {
        background-color: var(--danger-color);
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
    }
    
    .btn-primary {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background-color: var(--accent-color);
        border-color: var(--accent-color);
        transform: translateY(-2px);
        box-shadow: 0 7px 14px rgba(108, 92, 231, 0.2);
    }
    
    .navbar {
        box-shadow: 0 5px 20px -5px rgba(0, 0, 0, 0.08);
        background-color: white;
        border-radius: 12px;
        margin: 15px 15px 0;
        padding: 0.75rem 1.5rem;
    }
    
    .card.border-left-primary {
        border-left: 4px solid var(--primary-color) !important;
    }
    
    .card.border-left-success {
        border-left: 4px solid var(--success-color) !important;
    }
    
    .card.border-left-warning {
        border-left: 4px solid var(--warning-color) !important;
    }
    
    .card.border-left-danger {
        border-left: 4px solid var(--danger-color) !important;
    }
    
    
    .card.shadow {
        transition: all 0.3s ease;
    }
    
    .card.shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px -5px rgba(0, 0, 0, 0.15) !important;
    }
    
    
    .fa-calendar-day, .fa-users, .fa-clock {
        transition: transform 0.3s ease;
    }
    
    .card:hover .fa-calendar-day,
    .card:hover .fa-users,
    .card:hover .fa-clock {
        transform: scale(1.1);
    }
    

    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: rgba(0, 0, 0, 0.03);
    }
    
    ::-webkit-scrollbar-thumb {
        background: var(--primary-color);
        border-radius: 10px;
    }
    
    /* Gradient text for headings */
    .text-gradient {
        background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    /* Floating action button */
    .fab {
        position: fixed;
        bottom: 30px;
        right: 30px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        box-shadow: 0 10px 25px rgba(108, 92, 231, 0.3);
        transition: all 0.3s ease;
        z-index: 1000;
    }
    
    .fab:hover {
        transform: scale(1.1) rotate(90deg);
        box-shadow: 0 15px 35px rgba(108, 92, 231, 0.4);
    }
</style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse bg-primary">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white" style="font-family: monospace; font-size: 25px">Quitaneg's Dental Clinic</h4>
                        <hr class="bg-white">
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="patient_management.php" style="font-size: 20px;">
                                <i class="fas fa-fw fa-users"></i>
                                Patients
                            </a>
                        </li>
                        <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
                        <li class="nav-item mt-4">
                            <a class="nav-link text-danger" href="admin_logout.php" style="font-size: 20px;">
                                <i class="fas fa-fw fa-sign-out-alt"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <!-- Top Navigation -->
                        <br><br><br><br><br><br><br>
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>
                    
                    
                    </ul>
                </nav>

                
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Status Card</h1>
                </div>

                
                <div class="row">
                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Today's Appointments</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><br><?php echo $today_appointments; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                

                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-success shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                            Total Patients</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><br><?php echo $total_patients; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Pending Appointments</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo $pending_appointments; ?></div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                <!-- Recent Appointments -->
                

                    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                        <h6 class="m-0 font-weight-bold text-primary">Recent Appointments</h6>
    <div class="dropdown no-arrow">
        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="fas fa-download fa-sm"></i> Export
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
            <li><a class="dropdown-item" href="export_appointments.php?format=excel">Excel</a></li>
        </ul>
    </div>
</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>                                        
                                        <th>Patient</th>
                                        <th>Service</th>
                                        <th>Reason</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointments as $appointment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($appointment['user_id'] ? 'Patient ' . $appointment['user_id'] : ''); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['service_type']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['reason']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                                        <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                                        <td>
                                            <?php if ($appointment['status'] == 1): ?>
                                                <span class="badge badge-success">Completed</span>
                                            <?php elseif ($appointment['status'] == 0): ?>
                                                <span class="badge badge-warning">Pending</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger">Cancelled</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="view_appointment.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="delete_appointment.php?id=<?php echo $appointment['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this appointment?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom scripts -->
    <script>
        // Enable sidebar toggle
        document.getElementById('sidebarToggleTop').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('collapse');
        });
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>
</html>