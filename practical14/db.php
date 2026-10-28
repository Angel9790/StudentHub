
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = new mysqli("localhost", "root", "", "practical14_db");

if ($conn->connect_error) {
    die("Database connection failed.");
}

$conn->set_charset("utf8mb4");

function logAction($conn, $userId, $username, $action, $details) {
    $stmt = $conn->prepare(
        "INSERT INTO audit_logs (user_id, username, action, details)
         VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "isss", $userId, $username, $action, $details
    );
    $stmt->execute();
    $stmt->close();
}

function requireLogin() {
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php");
        exit;
    }
}

function requireAdmin() {
    requireLogin();

    if ($_SESSION["role"] !== "admin") {
        http_response_code(403);
        exit("Access denied. Admin permission is required.");
    }
}
?>
