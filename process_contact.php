<?php
// process_contact.php

// Database configuration (uncomment if you want to store messages in a database)
/*
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dental_clinic');
*/

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize
    $name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : '';
    $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL) : '';
    $message = isset($_POST['message']) ? htmlspecialchars(trim($_POST['message'])) : '';
    
    // Validate inputs
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required.';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    
    if (empty($message)) {
        $errors[] = 'Message is required.';
    }
    
    // If no errors, process the form
    if (empty($errors)) {
        // Option 1: Send email (recommended)
        $to = 'quitanegdentalclinic@example.com'; // Replace with your email
        $subject = 'New Contact Form Submission from ' . $name;
        $email_message = "You have received a new message from your website contact form.\n\n";
        $email_message .= "Name: $name\n";
        $email_message .= "Email: $email\n";
        $email_message .= "Message:\n$message\n";
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";
        
        if (mail($to, $subject, $email_message, $headers)) {
            $success = 'Thank you! Your message has been sent successfully. We will get back to you soon.';
        } else {
            $errors[] = 'There was a problem sending your message. Please try again later.';
        }
        
        // Option 2: Store in database (uncomment if you want to use database)
        /*
        try {
            $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message, created_at) 
                                  VALUES (:name, :email, :message, NOW())");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':message', $message);
            $stmt->execute();
            
            $success = 'Thank you! Your message has been submitted successfully. We will get back to you soon.';
        } catch(PDOException $e) {
            $errors[] = 'Database error: ' . $e->getMessage();
        }
        */
    }
}

// Redirect back to contact page with status
if (isset($success)) {
    // You could also store the success message in session and redirect
    header('Location: index.php#contact?status=success');
} else {
    // Store errors in session or pass via URL
    $errorString = implode(',', $errors);
    header('Location: index.php#contact?status=error&message=' . urlencode($errorString));
}
exit();

// If you prefer to show the message on the same page without redirect,
// you could include this at the top of your contact section in index.php:
/*
<?php
if (isset($_GET['status'])) {
    if ($_GET['status'] === 'success') {
        echo '<div class="alert alert-success">Thank you! Your message has been sent successfully.</div>';
    } elseif ($_GET['status'] === 'error' && isset($_GET['message'])) {
        echo '<div class="alert alert-error">Error: ' . htmlspecialchars($_GET['message']) . '</div>';
    }
}
?>
*/
?>