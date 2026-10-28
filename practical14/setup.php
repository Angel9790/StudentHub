
<?php
$conn = new mysqli("localhost", "root", "");

if ($conn->connect_error) {
    die("MySQL connection failed: " . $conn->connect_error);
}

$conn->query("CREATE DATABASE IF NOT EXISTS practical14_db");
$conn->select_db("practical14_db");
$conn->set_charset("utf8mb4");

$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    status ENUM('active', 'inactive') NOT NULL DEFAULT 'active'
)";
if (!$conn->query($sql)) {
    die("Users table error: " . $conn->error);
}

$sql = "CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL
)";
if (!$conn->query($sql)) {
    die("Students table error: " . $conn->error);
}

$sql = "CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    event_date DATE NOT NULL
)";
if (!$conn->query($sql)) {
    die("Events table error: " . $conn->error);
}

$sql = "CREATE TABLE IF NOT EXISTS registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_name VARCHAR(100) NOT NULL,
    event_id INT NOT NULL,
    registered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id)
)";
if (!$conn->query($sql)) {
    die("Registrations table error: " . $conn->error);
}

$sql = "CREATE TABLE IF NOT EXISTS audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    username VARCHAR(50) NOT NULL,
    action VARCHAR(100) NOT NULL,
    details VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (!$conn->query($sql)) {
    die("Audit logs table error: " . $conn->error);
}

$users = [
    ["Administrator", "admin", "admin123", "admin"],
    ["Demo Student", "student", "student123", "student"]
];

foreach ($users as $u) {
    $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $check->bind_param("s", $u[1]);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows == 0) {
        $hash = password_hash($u[2], PASSWORD_DEFAULT);
        $insert = $conn->prepare(
            "INSERT INTO users (full_name, username, password, role)
             VALUES (?, ?, ?, ?)"
        );
        $insert->bind_param("ssss", $u[0], $u[1], $hash, $u[3]);
        $insert->execute();
        $insert->close();
    }

    $check->close();
}

$conn->query("INSERT INTO students (name, email)
    SELECT 'Angel Bavarva', 'angel@example.com'
    WHERE NOT EXISTS (
        SELECT 1 FROM students WHERE email = 'angel@example.com'
    )");

$conn->query("INSERT INTO students (name, email)
    SELECT 'Riya Mehta', 'riya@example.com'
    WHERE NOT EXISTS (
        SELECT 1 FROM students WHERE email = 'riya@example.com'
    )");

$conn->query("INSERT INTO events (title, event_date)
    SELECT 'Web Development Workshop', '2026-11-15'
    WHERE NOT EXISTS (
        SELECT 1 FROM events WHERE title = 'Web Development Workshop'
    )");

$conn->query("INSERT INTO registrations (student_name, event_id)
    SELECT 'Angel Bavarva', id FROM events
    WHERE title = 'Web Development Workshop'
    AND NOT EXISTS (
        SELECT 1 FROM registrations
        WHERE student_name = 'Angel Bavarva'
    )");

echo "<h2>Practical 14 database setup successful!</h2>";
echo "<p>Admin username: admin</p>";
echo "<p>Admin password: admin123</p>";
echo "<p>Student username: student</p>";
echo "<p>Student password: student123</p>";
echo "<p>Now open login.php and test the application.</p>";

$conn->close();
?>
