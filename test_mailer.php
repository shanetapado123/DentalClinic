<?php
require __DIR__ . '/vendor/autoload.php';

try {
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    echo 'PHPMailer loaded successfully! Version: ' . $mail::VERSION;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
    echo '<br>PHP Version: ' . phpversion();
    echo '<br>Include path: ' . get_include_path();
}