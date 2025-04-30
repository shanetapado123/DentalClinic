<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dental Clinic Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            padding: 20px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        h2 {
            text-align: center;
        }
        .stats {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }
        .card {
            background: #3498db;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            width: 30%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background: #3498db;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Dental Clinic Dashboard</h2>
        <div class="stats">
            <div class="card">
                <h3>Total Appointments</h3>
                <p id="totalAppointments">0</p>
            </div>
            <div class="card">
                <h3>Upcoming Appointments</h3>
                <p id="upcomingAppointments">0</p>
            </div>
        </div>
        <h3>Recent Appointments</h3>
        <table>
            <thead>
                <tr>
                    <th>Client Name</th>
                    <th>Reason</th>
                    <th>Phone</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Done</th>
                    <th>Delete</th>
                </tr>
            </thead>
            <tbody id="appointmentList">
                <!-- Data will be inserted here dynamically -->
            </tbody>
        </table>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            fetch("fetch_appointments.php")
                .then(response => response.json())
                .then(data => {
                    document.getElementById("totalAppointments").innerText = data.total;
                    document.getElementById("upcomingAppointments").innerText = data.upcoming;
                    
                    let tableBody = document.getElementById("appointmentList");
                    data.recent.forEach(appointment => {
                        let row = `<tr>
                            <td>${appointment.client_name}</td>
                            <td>${appointment.appointment_reason}</td>
                            <td>${appointment.client_phone}</td>
                            <td>${appointment.appointment_date}</td>
                            <td>${appointment.appointment_time}</td>
                        </tr>`;
                        tableBody.innerHTML += row;
                    });
                });
        });
    </script>
</body>
</html>
