<?php

session_start();
require_once 'db_config.php';
require_once 'includes/functions.php';
require 'vendor/autoload.php'; // Add this line to load PHPMailer

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
$success = '';
$email = '';
$attempts_limit = 5;
$lockout_time = 30; // seconds

// Initialize login attempts if not set
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}

// Check if user is currently locked out
if (isset($_SESSION['lockout_time'])) {
    $elapsed_time = time() - $_SESSION['lockout_time'];
    if ($elapsed_time < $lockout_time) {
        $remaining_time = $lockout_time - $elapsed_time;
        $disabled = true;
        $error = "Too many failed attempts. Please try again in $remaining_time seconds.";
    } else {
        // Lockout period has expired
        unset($_SESSION['login_attempts']);
        unset($_SESSION['lockout_time']);
        $disabled = false;
    }
}

// Handle forgot password request
if (isset($_POST['forgot_password'])) {
    $email = trim($_POST['email']);
    
    try {
        $stmt = $pdo->prepare("SELECT id, first_name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
            $stmt->execute([$token, $expires, $user['id']]);
            
            // Send email with reset link
            $reset_link = "http://localhost/Dental/reset_password.php?token=$token";
            
            // Create PHPMailer instance
            $mail = new PHPMailer\PHPMailer\PHPMailer();
            
            // SMTP configuration for Gmail
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'alexdumpacc22@gmail.com'; // Your Gmail address
            $mail->Password = 'pqok cdds tkvs sfss'; // Your Gmail password or app password
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;
            
            // Sender and recipient
            $mail->setFrom('no-reply@gmail.com', 'Quitaneg Dental Clinic');
            $mail->addAddress($email, $user['first_name']);
            
            // Email content
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "
                <h2>Password Reset</h2>
                <p>Hello {$user['first_name']},</p>
                <p>You have requested to reset your password. Click the link below to proceed:</p>
                <p><a href='$reset_link'>$reset_link</a></p>
                <p>This link will expire in 1 hour.</p>
                <p>If you didn't request this, please ignore this email.</p>
            ";
            
            $mail->AltBody = "Password Reset\n\nHello {$user['first_name']},\n\nYou have requested to reset your password. Use this link to proceed:\n$reset_link\n\nThis link will expire in 1 hour.\n\nIf you didn't request this, please ignore this email.";
            
            if ($mail->send()) {
                $success = "Password reset link has been sent to your email.";
            } else {
                $error = "Failed to send reset email. Error: " . $mail->ErrorInfo;
            }
        } else {
            $error = "Email not found in our system.";
        }
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    } catch (Exception $e) {
        $error = "Mailer Error: " . $e->getMessage();
    }
}

// Add this code right before the HTML starts (after the forgot password handling)

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['forgot_password'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (!isset($_POST['terms'])) {
        $error = "You must accept the Terms and Conditions";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, first_name, last_name, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_email'] = $email;
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid email or password";
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Handle login attempt
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['forgot_password']) && !isset($disabled)) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (!isset($_POST['terms'])) {
        $error = "You must accept the Terms and Conditions";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, first_name, last_name, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Successful login - reset attempts
                unset($_SESSION['login_attempts']);
                unset($_SESSION['lockout_time']);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
                $_SESSION['user_email'] = $email;
                
                header("Location: dashboard.php");
                exit();
            } else {
                // Failed login attempt
                $_SESSION['login_attempts']++;
                
                if ($_SESSION['login_attempts'] >= $attempts_limit) {
                    $_SESSION['lockout_time'] = time();
                    $remaining_time = $lockout_time;
                    $disabled = true;
                    $error = "Too many failed attempts. Please try again in $remaining_time seconds.";
                } else {
                    $remaining_attempts = $attempts_limit - $_SESSION['login_attempts'];
                    $error = "Invalid email or password. $remaining_attempts attempts remaining.";
                }
            }
        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Quitaneg's Dental Clinic</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 20px; background-color: #f5f5f5; }
        .container { max-width: 500px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; border: 2px solid black; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1 { color: #2c3e50; text-align: center; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="email"], input[type="password"] { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #3498db; color: white; border: none; padding: 10px 15px; border-radius: 4px; cursor: pointer; }
        button:hover { background: #2980b9; }
        button:disabled { background: #95a5a6; cursor: not-allowed; }
        .error { color: red; margin-bottom: 15px; }
        .success { color: green; margin-bottom: 15px; }
        .terms { margin: 15px 0; font-size: 0.9em; }
        .terms a { color: #3498db; text-decoration: none; }
        .terms a:hover { text-decoration: underline; }
        .form-disabled { opacity: 0.6; pointer-events: none; }
        .user-login-link { text-align: center; margin-top: 1.5rem; font-size: 0.9rem; }
        .user-login-link a { color: #3498db; text-decoration: none; transition: color 0.3s; }
        .user-login-link a:hover { color: blue; text-decoration: underline; }
        .forgot-password { text-align: center; margin-top: 10px; }
        .modal { display: none; position: fixed; z-index: 1; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4); }
        .modal-content { background-color: #fefefe; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 400px; border-radius: 5px; }
        .close { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close:hover { color: black; }
    </style>
</head>
<body>
    <div class="container <?php echo isset($disabled) && $disabled ? 'form-disabled' : ''; ?>">
        <h1>Login to Your Account</h1>
        
        <?php if (isset($_GET['registered'])): ?>
            <div class="success">
                <p>Registration successful! Please login.</p>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="error">
                <p><?php echo htmlspecialchars($error); ?></p>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success">
                <p><?php echo htmlspecialchars($success); ?></p>
            </div>
        <?php endif; ?>
        
        <form action="login.php" method="post" id="loginForm">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>" <?php echo isset($disabled) && $disabled ? 'disabled' : ''; ?>>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required <?php echo isset($disabled) && $disabled ? 'disabled' : ''; ?>>
            </div>
            
            <div class="terms">
                <input type="checkbox" id="terms" name="terms" required <?php echo isset($disabled) && $disabled ? 'disabled' : ''; ?>>
                <label for="terms">I agree to the <a href="terms.php" target="_blank">Terms and Conditions</a> and <a href="privacy.php" target="_blank">Privacy Policy</a></label>
            </div>
            
            <button type="submit" id="loginButton" disabled <?php echo isset($disabled) && $disabled ? 'disabled' : ''; ?>>Login</button>
            
            <div class="forgot-password user-login-link">
                <a href="#" id="forgotPasswordLink">Forgot Password?</a>
            </div>
        </form>
        
        <div class="user-login-link">
            <p>Don't have an account? <a href="register.php">Register here</a></p>
        </div>
        <center><button type="button" class="button" onclick="window.location.href='index.php'">Back</button></center>
    </div>
    
    <!-- Forgot Password Modal -->
    <div id="forgotPasswordModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Forgot Password</h2>
            <form action="login.php" method="post">
                <div class="form-group">
                    <label for="forgotEmail">Email Address</label>
                    <input type="email" id="forgotEmail" name="email" required>
                </div>
                <button type="submit" name="forgot_password">Send Reset Link</button>
            </form>
        </div>
    </div>
     
    <script>
        
        document.getElementById('terms')?.addEventListener('change', function() {
            document.getElementById('loginButton').disabled = !this.checked;
        });

        
        document.getElementById('loginForm')?.addEventListener('submit', function(e) {
            if (!document.getElementById('terms').checked) {
                e.preventDefault();
                alert('You must accept the Terms and Conditions to proceed.');
            }
        });

        
        const modal = document.getElementById("forgotPasswordModal");
        const btn = document.getElementById("forgotPasswordLink");
        const span = document.getElementsByClassName("close")[0];

        btn.onclick = function() {
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        <?php if (isset($disabled) && $disabled): ?>
        
        let remainingTime = <?php echo $remaining_time ?? $lockout_time; ?>;
        const errorElement = document.querySelector('.error');
        
        function updateCountdown() {
            remainingTime--;
            if (remainingTime <= 0) {
                errorElement.textContent = 'You may now try to login again.';
                location.reload();
            } else {
                errorElement.textContent = `Too many failed attempts. Please try again in ${remainingTime} seconds.`;
                setTimeout(updateCountdown, 1000);
            }
        }
        
        setTimeout(updateCountdown, 1000);
        <?php endif; ?>
    </script>
</body>
</html>