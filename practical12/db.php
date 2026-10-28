<?php
// Make mysqli return false on errors instead of throwing exceptions (PHP 8.1+ default)
mysqli_report(MYSQLI_REPORT_OFF);
 
$conn = new mysqli("localhost", "root", "", "studenthub_db");
 
if ($conn->connect_error) {
    exit("Database connection failed: " . htmlspecialchars($conn->connect_error, ENT_QUOTES, "UTF-8"));
}
 
$conn->set_charset("utf8mb4");