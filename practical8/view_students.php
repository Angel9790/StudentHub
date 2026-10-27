
<?php
require_once "db.php";

$sql = "SELECT * FROM students";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$students = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>StudentHub Students</title>
</head>
<body>
    <h1>StudentHub Students</h1>

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Course</th>
        </tr>

        <?php foreach ($students as $student) { ?>
        <tr>
            <td><?php echo htmlspecialchars($student["student_id"]); ?></td>
            <td><?php echo htmlspecialchars($student["name"]); ?></td>
            <td><?php echo htmlspecialchars($student["email"]); ?></td>
            <td><?php echo htmlspecialchars($student["course"]); ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
