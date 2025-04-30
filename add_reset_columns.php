<?php
require_once 'db_config.php';

try {
    $pdo->exec("
        ALTER TABLE users
        ADD COLUMN reset_token VARCHAR(64) NULL,
        ADD COLUMN reset_expires DATETIME NULL
    ");
    echo "Successfully added reset_token and reset_expires columns to users table.\n";
} catch (PDOException $e) {
    echo "Error adding columns: " . $e->getMessage() . "\n";
}