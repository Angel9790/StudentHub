
<?php
$conn = new mysqli("localhost", "root", "", "practical13_db");

if ($conn->connect_error) {
    http_response_code(500);
    die("Database connection failed.");
}

$conn->set_charset("utf8mb4");
?>
