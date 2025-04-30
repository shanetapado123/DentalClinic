<!-- appointment_form.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quitaneq's Dental Clinic - Appointment</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        select, input, textarea { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background-color: #45a049; }
    </style>
</head>
<body>
    <h1>Quitaneq's Dental Clinic - Appointment</h1>
    <p>Please fill out this form to make an appointment.</p>
    
    <form action="process_appointment.php" method="post">
        <div class="form-group">
            <label for="service_type">Service Type:</label>
            <select id="service_type" name="service_type" required>
                <option value="">-- Select a service --</option>
                <option value="Dental Checkup">Dental Checkup</option>
                <option value="Teeth Cleaning">Teeth Cleaning</option>
                <option value="Tooth Extraction">Tooth Extraction</option>
                <option value="Dental Filling">Dental Filling</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" required>
        </div>
        
        <div class="form-group">
            <label for="time">Time:</label>
            <input type="time" id="time" name="time" required>
        </div>
        
        <div class="form-group">
            <label for="appointment_reason">Reason (optional):</label>
            <textarea id="appointment_reason" name="appointment_reason" rows="3"></textarea>
        </div>
        
        <button type="submit">Book Appointment</button>
    </form>
    <br>
     <button type="button" class="login-btn" onclick="window.location.href='dashboard.php'">Back</button>
    
    <footer>
        <p>&copy; 2025 Quitaneq's Dental Clinic. All rights reserved.</p>
    </footer>
</body>
</html>