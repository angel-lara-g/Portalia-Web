<?php
/**
 * config.example.php
 * ------------------
 * Safe template for database configuration.
 * Copy this file as config.php and fill in your own credentials.
 *
 * This file IS safe to commit to version control.
 * config.php is listed in .gitignore and should NEVER be committed.
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_username');
define('DB_PASS', 'your_db_password');
define('DB_NAME', 'dbPortalia');

function get_mysqli_connection(): mysqli {
    $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;
}

function get_pdo_connection(): PDO {
    try {
        return new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS
        );
    } catch (PDOException $e) {
        die("Connection error: " . $e->getMessage());
    }
}
?>
