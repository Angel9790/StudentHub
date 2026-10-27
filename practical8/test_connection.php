
<?php
require_once "db.php";

echo "<h1>StudentHub Database</h1>";
echo "<p>Database connection successful!</p>";

$stmt = $pdo->query("SELECT COUNT(*) AS total FROM students");
$result = $stmt->fetch();

echo "<p>Total students: " . $result["total"] . "</p>";
?>
