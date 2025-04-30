<?php
require_once 'session_manager.php';
require_once 'db_config.php';

$errors = [];
$success = false;


$fields = [
    'username' => '',
    'full_name' => '',
    'email' => '',
    'password' => '',
    'confirm_password' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    foreach ($fields as $key => $value) {
        $fields[$key] = trim($_POST[$key] ?? '');
    }

    
    if (empty($fields['username'])) {
        $errors['username'] = 'Username is required';
    } elseif (!preg_match('/^[a-zA-Z0-9_]{4,20}$/', $fields['username'])) {
        $errors['username'] = 'Username must be 4-20 chars (letters, numbers, _)';
    }

    if (empty($fields['full_name'])) {
        $errors['full_name'] = 'Full name is required';
    }

    if (empty($fields['email'])) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($fields['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }

    if (empty($fields['password'])) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($fields['password']) < 8) {
        $errors['password'] = 'Password must be at least 8 characters';
    }

    if ($fields['password'] !== $fields['confirm_password']) {
        $errors['confirm_password'] = 'Passwords do not match';
    }

   if (empty($errors)) {
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

        
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
        $stmt->execute([$fields['username']]);
        if ($stmt->rowCount() > 0) {
            $errors['username'] = 'Username already taken';
        }

        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ?");
        $stmt->execute([$fields['email']]);
        if ($stmt->rowCount() > 0) {
            $errors['email'] = 'Email already registered';
        }

    
        if (empty($errors)) {
            $passwordHash = password_hash($fields['password'], PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("
                INSERT INTO admins 
                (username, email, password_hash) 
                VALUES (?, ?, ?)
            ");
            
            $stmt->execute([
                $fields['username'],
                $fields['email'],
                $passwordHash
            ]);

            $success = true;
            $fields = array_fill_keys(array_keys($fields), ''); // Reset form
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        $errors['database'] = 'Registration failed. Error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Registration - Dental Clinic</title>
    <style>
        :root {
            --primary: #3498db;
            --primary-dark: #2980b9;
            --danger: #e74c3c;
            --success: #2ecc71;
            --gray: #95a5a6;
            --light-gray: #ecf0f1;
            --dark: #2c3e50;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        .registration-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            padding: 2rem;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        h1 {
            color: var(--dark);
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
        }
        
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        
        .alert-success {
            background-color: rgba(46, 204, 113, 0.2);
            color: var(--success);
            border: 1px solid var(--success);
        }
        
        .alert-danger {
            background-color: rgba(231, 76, 60, 0.2);
            color: var(--danger);
            border: 1px solid var(--danger);
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
            transition: border 0.3s;
        }
        
        input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        .error-message {
            color: var(--danger);
            font-size: 0.8rem;
            margin-top: 0.25rem;
        }
        
        .btn {
            width: 18%;
            padding: 0.75rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
        }
        
        .text-center {
            text-align: center;
        }
        
        .mt-3 {
            margin-top: 1rem;
        }
        
        .login-link {
            color: var(--primary);
            text-decoration: none;
        }
        
        .login-link:hover {
            text-decoration: underline;
        }
        
        .password-container {
            position: relative;
        }
        
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--gray);
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <h1>Admin Registration</h1>
        
        <?php if ($success): ?>
            <div class="alert alert-success">
                Registration successful! <button><a href="admin_login.php" class="login-link">Login here</a></button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors['database'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($errors['database']) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    value="<?= htmlspecialchars($fields['username']) ?>" 
                    required
                    <?= isset($errors['username']) ? 'aria-invalid="true"' : '' ?>
                >
                <?php if (isset($errors['username'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['username']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input 
                    type="text" 
                    id="full_name" 
                    name="full_name" 
                    value="<?= htmlspecialchars($fields['full_name']) ?>" 
                    required
                    <?= isset($errors['full_name']) ? 'aria-invalid="true"' : '' ?>
                >
                <?php if (isset($errors['full_name'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['full_name']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="<?= htmlspecialchars($fields['email']) ?>" 
                    required
                    <?= isset($errors['email']) ? 'aria-invalid="true"' : '' ?>
                >
                <?php if (isset($errors['email'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['email']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="password-container">
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required
                        minlength="8"
                        <?= isset($errors['password']) ? 'aria-invalid="true"' : '' ?>
                    >
                    <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
                </div>
                <?php if (isset($errors['password'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['password']) ?></span>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <div class="password-container">
                    <input 
                        type="password" 
                        id="confirm_password" 
                        name="confirm_password" 
                        required
                        minlength="8"
                        <?= isset($errors['confirm_password']) ? 'aria-invalid="true"' : '' ?>
                    >
                    <span class="toggle-password" onclick="togglePassword('confirm_password')">👁️</span>
                </div>
                <?php if (isset($errors['confirm_password'])): ?>
                    <span class="error-message"><?= htmlspecialchars($errors['confirm_password']) ?></span>
                <?php endif; ?>
            </div>
            
            <button type="submit" class="btn btn-primary">Register</button>
            <br><br>
            <button type="button" class="btn btn-primary" onclick="window.location.href='admin_login.php'">Back</button>
        </form>
        
        <div class="text-center mt-3">
            <p>Already have an account? <a href="admin_login.php" class="login-link">Login here</a></p>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            field.type = field.type === 'password' ? 'text' : 'password';
        }
    </script>
</body>
</html>