<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}


$db_host = 'localhost';
$db_name = 'dental_clinic';
$db_user = 'root';
$db_pass = '';


$error_message = '';
$success_message = '';
$available_times = [];

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $service_type = $_POST['service_type'] ?? '';
        $appointment_date = $_POST['appointment_date'] ?? '';
        $appointment_time = $_POST['appointment_time'] ?? '';
        $reason = $_POST['reason'] ?? '';
        $user_id = $_SESSION['user_id']; // Get from session

        
        if (empty($service_type) || empty($appointment_date) || empty($appointment_time) || empty($reason)) {
            $error_message = 'All fields are required!';
        } else {
            
            $stmt = $pdo->prepare("SELECT * FROM appointments WHERE appointment_date = :appointment_date AND appointment_time = :appointment_time");
            $stmt->bindParam(':appointment_date', $appointment_date);
            $stmt->bindParam(':appointment_time', $appointment_time);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $error_message = 'This time slot is already booked. Please choose another time.';
            } else {
                
                $stmt = $pdo->prepare("INSERT INTO appointments (user_id, service_type, appointment_date, appointment_time, reason) 
                                     VALUES (:user_id, :service_type, :appointment_date, :appointment_time, :reason)");
                $stmt->bindParam(':user_id', $user_id);
                $stmt->bindParam(':service_type', $service_type);
                $stmt->bindParam(':appointment_date', $appointment_date);
                $stmt->bindParam(':appointment_time', $appointment_time);
                $stmt->bindParam(':reason', $reason);

                if ($stmt->execute()) {
                    $success_message = 'Appointment booked successfully!';
                    $_POST = [];
                } else {
                    $error_message = 'Failed to book appointment. Please try again.';
                }
            }
        }
    }

    
    if (!empty($_POST['appointment_date'])) {
        $stmt = $pdo->prepare("SELECT appointment_time FROM appointments WHERE appointment_date = :appointment_date");
        $stmt->bindParam(':appointment_date', $_POST['appointment_date']);
        $stmt->execute();
        $booked_times = $stmt->fetchAll(PDO::FETCH_COLUMN);

        
        $all_times = [
            '09:00 AM', '10:00 AM', '11:00 AM', 
            '12:00 PM', '01:00 PM', '02:00 PM', 
            '03:00 PM', '04:00 PM'
        ];

        
        $available_times = array_diff($all_times, $booked_times);
    }
} catch (PDOException $e) {
    $error_message = 'Database error: ' . $e->getMessage();
    error_log("Appointment Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quitaneq's Dental Clinic - Appointment</title>
    <style>
             :root {
        --primary: #00b4d8;      
        --primary-dark: #0077b6;  
        --secondary: #f8f9fa;     
        --accent: #ff9e00;       
        --text: #2d3436;         
        --light-gray: #dfe6e9;   
        --success: #2ecc71;      
        --error: #e74c3c;       
        --white: #ffffff;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: 'Poppins', 'Segoe UI', system-ui, sans-serif;
    }

    body {
        background-color: #f5f7fa;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
        line-height: 1.6;
    }

    .form-container {
        background: var(--white);
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 600px;
        padding: 40px;
        position: relative;
        overflow: hidden;
    }

    .form-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 8px;
        background: linear-gradient(90deg, var(--primary), var(--accent));
    }

    h1 {
        color: var(--primary-dark);
        text-align: center;
        margin-bottom: 30px;
        font-weight: 600;
        font-size: 28px;
    }

    .error {
        color: var(--error);
        background-color: rgba(231, 76, 60, 0.1);
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-left: 4px solid var(--error);
        animation: fadeIn 0.3s ease-out;
    }

    .error::before {
        content: '!';
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        background-color: var(--error);
        color: white;
        border-radius: 50%;
        font-weight: bold;
        font-size: 14px;
    }

    .success {
        color: var(--success);
        background-color: rgba(46, 204, 113, 0.1);
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-left: 4px solid var(--success);
        animation: fadeIn 0.3s ease-out;
    }

    .success::before {
        content: '✓';
        font-weight: bold;
        font-size: 18px;
    }

    .form-group {
        margin-bottom: 25px;
        position: relative;
    }

    label {
        display: block;
        margin-bottom: 10px;
        color: var(--text);
        font-weight: 500;
        font-size: 15px;
    }

    .required:after {
        content: " *";
        color: var(--error);
    }

    select, input, textarea {
        width: 100%;
        padding: 14px 16px;
        border: 2px solid var(--light-gray);
        border-radius: 8px;
        font-size: 15px;
        transition: all 0.3s;
        background-color: var(--white);
    }

    select:focus, input:focus, textarea:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(0, 180, 216, 0.2);
    }

    textarea {
        min-height: 120px;
        resize: vertical;
    }

    .time-slots {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-top: 10px;
    }

    .time-option {
        padding: 12px 8px;
        border: 2px solid var(--light-gray);
        border-radius: 8px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
        font-size: 14px;
    }

    .time-option:hover {
        border-color: var(--primary);
        transform: translateY(-2px);
    }

    .time-option.selected {
        background-color: var(--primary);
        color: var(--white);
        border-color: var(--primary);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 180, 216, 0.2);
    }

    .time-option.booked {
        background-color: #ffeeee;
        color: var(--error);
        border-color: #ffd3d3;
        cursor: not-allowed;
        text-decoration: line-through;
        opacity: 0.7;
    }

    button {
        background: linear-gradient(90deg, var(--primary), var(--primary-dark));
        color: var(--white);
        border: none;
        padding: 16px;
        font-size: 16px;
        font-weight: 600;
        border-radius: 8px;
        cursor: pointer;
        width: 100%;
        transition: all 0.3s;
        margin-top: 15px;
        letter-spacing: 0.5px;
    }

    button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 163, 225, 0.3);
    }

    button:disabled {
        background: #cccccc;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
        .form-container {
            padding: 30px 20px;
        }
        
        .time-slots {
            grid-template-columns: repeat(2, 1fr);
        }
        
        h1 {
            font-size: 24px;
        }
    }

    @media (max-width: 480px) {
        .time-slots {
            grid-template-columns: 1fr;
        }
    }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Quitaneq's Dental Clinic - Appointment</h1>
        
        <?php if (!empty($error_message)): ?>
            <div class="error"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success_message)): ?>
            <div class="success"><?= htmlspecialchars($success_message) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="appointment_form.php">
            <div class="form-group">
                <label for="service_type" class="required">Service Type</label>
                <select id="service_type" name="service_type" required>
                    <option value="">- Select a service -</option>
                    <option value="Cleaning" <?= isset($_POST['service_type']) && $_POST['service_type'] === 'Cleaning' ? 'selected' : '' ?>>Cleaning</option>
                    <option value="Filling" <?= isset($_POST['service_type']) && $_POST['service_type'] === 'Filling' ? 'selected' : '' ?>>Filling</option>
                    <option value="Extraction" <?= isset($_POST['service_type']) && $_POST['service_type'] === 'Extraction' ? 'selected' : '' ?>>Extraction</option>
                    <option value="Braces" <?= isset($_POST['service_type']) && $_POST['service_type'] === 'Braces' ? 'selected' : '' ?>>Braces</option>
                    <option value="Checkup" <?= isset($_POST['service_type']) && $_POST['service_type'] === 'Checkup' ? 'selected' : '' ?>>Checkup</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="appointment_date" class="required">Date</label>
                <input type="date" id="appointment_date" name="appointment_date" required 
                       min="<?= date('Y-m-d') ?>" 
                       value="<?= isset($_POST['appointment_date']) ? htmlspecialchars($_POST['appointment_date']) : '' ?>">
            </div>
            
            <div class="form-group">
                <label class="required">Time</label>
                <?php if (!empty($_POST['appointment_date'])): ?>
                    <div class="time-slots">
                        <?php 
                        $all_times = [
                            '09:00 AM', '10:00 AM', '11:00 AM', 
                            '12:00 PM', '01:00 PM', '02:00 PM', 
                            '03:00 PM', '04:00 PM', '05:00 PM',
                            '06:00 PM', '07:00 PM', '08:00 PM'
                        ];
                        
                        foreach ($all_times as $time): 
                            $isBooked = in_array($time, $booked_times ?? []);
                            $isSelected = isset($_POST['appointment_time']) && $_POST['appointment_time'] === $time;
                        ?>
                            <div class="time-option <?= $isBooked ? 'booked' : '' ?> <?= $isSelected ? 'selected' : '' ?>"
                                 onclick="<?= !$isBooked ? "document.querySelector('input[name=appointment_time]').value='" . htmlspecialchars($time) . "'" : '' ?>">
                                <?= htmlspecialchars($time) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="appointment_time" value="<?= isset($_POST['appointment_time']) ? htmlspecialchars($_POST['appointment_time']) : '' ?>">
                <?php else: ?>
                    <p>Please select a date first to see available times</p>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="reason" class="required">Reason </label>
                <textarea id="reason" name="reason" required><?= isset($_POST['reason']) ? htmlspecialchars($_POST['reason']) : '' ?></textarea>
            </div>
            
            <button type="submit">Book Appointment</button>
            <button type="button" class="button" onclick="window.location.href='dashboard.php'">Back</button>
        </form>
    </div>

    <script>
        
        document.getElementById('appointment_date').addEventListener('change', function() {
            this.form.submit();
        });
        
       
        document.querySelectorAll('.time-option:not(.booked)').forEach(option => {
            option.addEventListener('click', function() {
                document.querySelectorAll('.time-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                this.classList.add('selected');
            });
        });
    </script>
</body>
</html>