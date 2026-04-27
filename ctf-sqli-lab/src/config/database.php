<?php
// Database Configuration - CTF Lab
// WARNING: This file contains intentionally insecure configurations

$db_host = '127.0.0.1';
$db_user = 'ctfuser';
$db_pass = 'ctfpass123';
$db_name = 'ctf_company';
$db_socket = '/run/mysqld/mysqld.sock';

// Try socket first, then TCP
$conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    $conn = @new mysqli('localhost', $db_user, $db_pass, $db_name, 3306, $db_socket);
}

if ($conn->connect_error) {
    die("<div style='color:red;font-family:monospace;padding:20px;'>
        Database Connection Failed: " . $conn->connect_error . "<br>
        Please ensure MariaDB is running.
    </div>");
}

$conn->set_charset("utf8mb4");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
