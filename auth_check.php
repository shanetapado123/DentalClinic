<?php
if (session_status() === PHP_SESSION_ACTIVE) {
    error_log("Session already active in " . __FILE__);
    // You can also see where it was started:
    error_log("Session started in: " . (headers_sent() ? 'headers sent' : 'headers not sent'));
}

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");

// Session timeout (30 minutes)
$inactive = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $inactive)) {
    session_unset();
    session_destroy();
    header("Location: admin_login.php?timeout=1");
    exit();
}
$_SESSION['last_activity'] = time();

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Optional: Check for IP changes
if (isset($_SESSION['ip_address']) && $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
    session_unset();
    session_destroy();
    header("Location: admin_login.php?security=1");
    exit();
}
$_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];