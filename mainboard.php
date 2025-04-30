<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quitaneg Dental Clinic - Appointment</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1 class="container">Quitaneg's Dental Clinic - Appointment</h1><br>
    <p class="container">*Please fill out this form to make an appointment.*</p>
    <form action="process_appointment.php" method="POST">
        <label>Client Name:</label>
        <input type="text" name="client_name" required autocomplete="off"><br>

                <label for="appointment_reason">What is the exact reason to require a dental appointment? <span style="color: red;">*</span></label>
<select id="appointment_reason" name="appointment_reason" required>
    <option value="" disabled selected>Please select</option>
    <option value="Hygiene">Learning about dental hygiene</option>
    <option value="Cavities">Filling cavities</option>
    <option value="Decay_Removal">Removing buildup or decay from teeth</option>
    <option value="Repair_Teeth">Repairing or removing damaged teeth</option>
    <option value="Xray_Diagnostics">Reviewing X-rays and diagnostics</option>
    <option value="Fillings">Putting in fillings or sealants</option>
    <option value="Tooth Growth Check">Checking the growth of teeth and jawbones</option>
</select><br>
    <br><br>
        <label>Phone Number:</label>
        <input type="text" name="client_phone" required autocomplete="off"><br>

        <label>Appointment Date:</label>
        <input type="date" name="appointment_date" required autocomplete="off"><br>

        <label>Appointment Time:</label>
        <input type="time" name="appointment_time" required autocomplete="off"><br>

        <label>Notes:</label>
        <textarea name="notes"></textarea><br>



        <button type="submit" class="button" >Book Appointment</button>
        <button type="reset"  class="button">Reset</button>
        <button type="button" class="button" onclick="window.location.href='dashboard.php'">Back</button>
    </form>
    <footer>
        <p>&copy; 2025  Quitaneg's Dental Clinic. All rights reserved.</p>
    </footer>
</body>
</html>
