<?php
require_once 'session_manager.php';
secure_session_start();

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");


define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dental_clinic');


error_reporting(E_ALL);
ini_set('display_errors', '1');


$error = '';
$username = '';


if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("CSRF token validation failed");
    }
    
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = $_POST['password']; 
    
    
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        try {
        
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
            
            
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :username LIMIT 1");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            
            if ($stmt->rowCount() == 1) {
                $admin = $stmt->fetch();
                
            
                if (password_verify($password, $admin['password_hash'])) {
                    session_regenerate_id(true);
                    
                
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_username'] = $admin['username'];
                    $_SESSION['admin_email'] = $admin['email'];
                    $_SESSION['last_activity'] = time();
                    
                    
                    $updateStmt = $pdo->prepare("UPDATE admins SET last_login = NOW() WHERE id = :id");
                    $updateStmt->bindParam(':id', $admin['id'], PDO::PARAM_INT);
                    $updateStmt->execute();
                    
        
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
        
                    $error = "Invalid username or password";
                
                }
            } else {
            
                $error = "Invalid username or password";
            }
        } catch (PDOException $e) {
        
            error_log("Database error: " . $e->getMessage());
            $error = "A system error occurred. Please try again later.";
        }
    }
    
    if (!empty($error)) {
        sleep(2);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Login - Secure</title>
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --error-color: #e74c3c;
            --success-color: #2ecc71;
            --text-color: #333;
            --light-gray: #f5f5f5;
            --border-radius: 4px;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-gray);
            color: var(--text-color);
            line-height: 1.6;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        h2 {
            text-align: center;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
        }
        
        .error-message {
            color: var(--error-color);
            background-color: rgba(231, 76, 60, 0.1);
            padding: 0.75rem;
            border-radius: var(--border-radius);
            margin-bottom: 1rem;
            text-align: center;
            display: <?php echo empty($error) ? 'none' : 'block'; ?>;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            font-size: 1rem;
            transition: border 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        .login-btn {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: var(--border-radius);
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .login-btn:hover {
            background-color: var(--secondary-color);
        }
        
        .user-login-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }
        
        .user-login-link a {
            color: black;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .user-login-link a:hover {
            color: blue;
            text-decoration: underline;
        }
        
        .password-toggle {
            position: relative;
        }
        
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 70%;
            transform: translateY(-50%);
            cursor: pointer;
            color: red;
            background-color: blue;

        }
         h1 { color: #2c3e50; text-align: center; }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Admin Login</h1>
        
        <?php if (!empty($error)): ?>
            <div class="error-message" id="error-message"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); ?>" required autofocus>
            </div>
            
            <div class="form-group password-toggle">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required><br>
                <span class="toggle-password" onclick="togglePasswordVisibility()">👁️</span>
            </div>
            
            <button type="submit" class="login-btn">Login</button><br><br>
            <button type="button" class="login-btn" onclick="window.location.href='index.php'">Back</button>

        </form>
        
        <div class="user-login-link">
            <a href="admin_register.php">Admin Register</a><br><br>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passwordField = document.getElementById('password');
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
        }
        
        
        setTimeout(() => {
            const errorMessage = document.getElementById('error-message');
            if (errorMessage) {
                errorMessage.style.transition = 'opacity 0.5s ease';
                errorMessage.style.opacity = '0';
                setTimeout(() => errorMessage.remove(), 500);
            }
        }, 5000);
    </script>
</body>
</html>