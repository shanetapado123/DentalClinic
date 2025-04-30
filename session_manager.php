<?php
function secure_session_start() {
    // Only start if not already started
    if (session_status() === PHP_SESSION_NONE) {
        // Set secure session parameters
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1); // Enable if using HTTPS
        ini_set('session.use_strict_mode', 1);
        
        session_start();
        
        // Regenerate ID if session is new
        if (empty($_SESSION['initiated'])) {
            session_regenerate_id(true);
            $_SESSION['initiated'] = true;
        }
    }
}
?>