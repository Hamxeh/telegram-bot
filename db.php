<?php
require_once 'config.php';

function getDB() {
    try {
        return new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
    } catch (PDOException $e) {
        file_put_contents("db_error.log", $e->getMessage());
        die("Database error");
    }
}
?>
