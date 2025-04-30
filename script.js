document.getElementById('appointmentForm').addEventListener('submit', function(event) {
    // Basic client-side validation (you can add more robust checks)
    const clientName = document.getElementById('client_name').value;
    const appointmentDate = document.getElementById('appointment_date').value;
    const appointmentTime = document.getElementById('appointment_time').value;

    if (!clientName || !appointmentDate || !appointmentTime) {
        document.getElementById('message').textContent = 'Please fill in all required fields.';
        event.preventDefault(); // Prevent form submission
    } else {
        document.getElementById('message').textContent = ''; // Clear any previous message.
    }
});


document.getElementById('contactForm').addEventListener('submit', function(event) {
    // Basic client-side validation
    const name = document.getElementById('name').value;
    const email = document.getElementById('email').value;
    const message = document.getElementById('message').value;

    if (!name || !email || !message) {
        document.getElementById('messageDisplay').textContent = 'Please Fill in All Fields.';
        event.preventDefault(); // Prevent form submission
    } else {
        document.getElementById('messageDisplay').textContent = ''; // Clear any previous message.
    }
});

 document.addEventListener("DOMContentLoaded", function () {
    // Handle check action
    document.querySelectorAll(".check-appointment").forEach((checkbox) => {
        checkbox.addEventListener("change", function () {
            let row = this.closest("tr");
            if (this.checked) {
                row.style.backgroundColor = "#d4edda"; // Light green for done
            } else {
                row.style.backgroundColor = ""; // Reset color
            }
        });
    });

    // Handle delete action
    document.querySelectorAll(".delete-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            let row = this.closest("tr");
            row.remove();
        });
    });
});
