<?php
require_once __DIR__ . '/config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

try {
    $patient_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    $stmt = $pdo->prepare("
        SELECT p.*, u.email AS user_email 
        FROM dental_clinic.patients p
        JOIN dental_clinic.users u ON p.user_id = u.id
        WHERE p.id = ?
    ");
    $stmt->execute([$patient_id]);
    $patient = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$patient) {
        die('Patient not found');
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
    $last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

    // Update patient in database
    try {
        $pdo->beginTransaction();
        
        // Update users table
        $user_update = $pdo->prepare("
            UPDATE users 
            SET email = ?, first_name = ?, last_name = ?, phone = ?, updated_at = NOW()
            WHERE id = ?
        ");
        $user_update->execute([$email, $first_name, $last_name, $phone, $patient['user_id']]);
        
        // Update patients table
        $patient_update = $pdo->prepare("
            UPDATE patients 
            SET first_name = ?, last_name = ?, phone = ?, email = ?
            WHERE id = ?
        ");
        $patient_update->execute([$first_name, $last_name, $phone, $email, $patient_id]);
        
        $pdo->commit();
        
        $_SESSION['success_message'] = "Patient updated successfully";
        header("Location: view_patient.php?id=$patient_id");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error_message = "Error updating patient: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Patient - <?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .edit-form {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="my-4">Edit Patient: <?= htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']) ?></h2>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>
        
        <form method="POST" class="edit-form">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" 
                           value="<?= htmlspecialchars($patient['first_name']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" 
                           value="<?= htmlspecialchars($patient['last_name']) ?>" required>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" 
                           value="<?= htmlspecialchars($patient['email']) ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" 
                           value="<?= htmlspecialchars($patient['phone']) ?>" required>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Account Created</label>
                <input type="text" class="form-control" 
                       value="<?= date('M d, Y h:i A', strtotime($patient['created_at'])) ?>" readonly>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="patients.php" class="btn btn-secondary">Back to List</a>
                <button type="submit" class="btn btn-primary">Update Patient</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>