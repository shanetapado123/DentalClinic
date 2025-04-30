<?php
require_once __DIR__ . '/config.php';
session_start();

// Authentication and authorization check
if (!isset($_SESSION['admin_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: admin_login.php');
    exit;
}

// Get patient ID from URL
$patient_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verify patient exists and get user_id
$stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
$stmt->execute([$patient_id]);
$patient = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$patient) {
    $_SESSION['error_message'] = "Patient not found";
    header('Location: patients.php');
    exit;
}

$user_id = $patient['user_id'];

// Handle deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        
        // Delete user account
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        
        $pdo->commit();
        
        $_SESSION['success_message'] = "Patient and user account deleted successfully";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error_message'] = "Error deleting patient: " . $e->getMessage();
    }
    
    header('Location: patients.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Patient</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h3>Confirm Deletion</h3>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <h4 class="alert-heading">Warning!</h4>
                    <p>You are about to permanently delete this patient and their user account.</p>
                    <hr>
                    <p class="mb-0">This action cannot be undone. All related data will be lost.</p>
                </div>
                
                <form method="POST">
                    <div class="d-flex justify-content-between">
                        <a href="patients.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-danger">Confirm Permanent Deletion</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>