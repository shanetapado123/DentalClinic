<?php
session_start();
require 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Handle delete action
if (isset($_GET['delete'])) {
    $patientId = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$patientId]);
        $_SESSION['success_message'] = "Patient deleted successfully";
        header("Location: patient_management.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['error_message'] = "Error deleting patient: " . $e->getMessage();
        header("Location: patient_management.php");
        exit();
    }
}

// Fetch all patients
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY last_name, first_name");
    $patients = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Management | Quitaneq's Dental Clinic</title>
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
        
        .table th {
            border-top: none;
            color: #5a5c69;
            font-weight: 700;
        }
        
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .action-btns {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Patient List</h1>
            <div>
                <a href="admin_dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
                <a href="add_patient.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add Patient
                </a>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Registered Patients</h6>
                <div class="dropdown no-arrow">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-download fa-sm"></i> Export
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <li><a class="dropdown-item" href="export_patients.php?format=csv">CSV</a></li>
                        <li><a class="dropdown-item" href="export_patients.php?format=excel">Excel</a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="patientsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Registered</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($patients as $patient): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($patient['id']); ?></td>
                                <td><?php echo htmlspecialchars($patient['first_name']); ?></td>
                                <td><?php echo htmlspecialchars($patient['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($patient['email']); ?></td>
                                <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                                <td><?php echo date('M j, Y', strtotime($patient['created_at'])); ?></td>
                                <td class="action-btns">
                                    <a href="edit_patient.php?id=<?php echo $patient['id']; ?>" class="btn btn-sm btn-warning btn-action" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger btn-action delete-btn" data-id="<?php echo $patient['id']; ?>" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this patient? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a href="#" id="confirmDelete" class="btn btn-danger">Delete</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Delete button click handler
            $('.delete-btn').click(function() {
                var patientId = $(this).data('id');
                var deleteUrl = 'patient_management.php?delete=' + patientId;
                $('#confirmDelete').attr('href', deleteUrl);
                $('#deleteModal').modal('show');
            });

            // Initialize DataTable
            $('#patientsTable').DataTable({
                responsive: true,
                columnDefs: [
                    { orderable: false, targets: [6] } // Disable sorting for actions column
                ]
            });
        });
    </script>
</body>
</html>