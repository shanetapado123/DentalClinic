<?php
session_start();
require_once 'db_config.php';
require_once 'includes/functions.php';

$error = '';
$success = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    try {
        $stmt = $pdo->prepare("SELECT id, reset_expires FROM users WHERE reset_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        
        if ($user) {
            if (strtotime($user['reset_expires']) > time()) {
                // Token is valid
                if (isset($_POST['new_password'])) {
                    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
                    
                    $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
                    $stmt->execute([$new_password, $user['id']]);
                    
                    $success = "Password has been reset successfully. You can now login with your new password.";
                }
            } else {
                $error = "Password reset link has expired.";
            }
        } else {
            $error = "Invalid password reset link.";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
} else {
    $error = "No reset token provided.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; background-color: #f5f5f5; }
        .container { max-width: 500px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; border: 2px solid black; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="password"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #3498db; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; }
        .error { color: red; margin-bottom: 15px; }
        .success { color: green; margin-bottom: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reset Password</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
            <p><a href="login.php">Return to login page</a></p>
        <?php elseif (isset($user) && strtotime($user['reset_expires']) > time()): ?>
            <form method="post">
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit">Reset Password</button>
            </form>
            
            <script>
                // Simple password confirmation
                document.querySelector('form').addEventListener('submit', function(e) {
                    const password = document.getElementById('new_password').value;
                    const confirm = document.getElementById('confirm_password').value;
                    
                    if (password !== confirm) {
                        e.preventDefault();
                        alert('Passwords do not match!');
                    }
                });
            </script>
        <?php endif; ?>
    </div>
</body>
</html>