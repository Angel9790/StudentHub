
<?php
require "db.php";

$search = trim($_GET["search"] ?? "");
$course = trim($_GET["course"] ?? "");

$sql = "SELECT student_id, name, email, course
        FROM students
        WHERE (name LIKE ? OR email LIKE ?)";

if ($course != "") {
    $sql .= " AND course = ?";
}

$sql .= " ORDER BY student_id DESC";

$stmt = $conn->prepare($sql);
$searchValue = "%" . $search . "%";

if ($course != "") {
    $stmt->bind_param("sss", $searchValue, $searchValue, $course);
} else {
    $stmt->bind_param("ss", $searchValue, $searchValue);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">

    <h1>Student Management System</h1>
    <p>Manage student records using PHP and MySQLi.</p>

    <?php if (isset($_GET["message"])): ?>
        <p class="message">
            <?= htmlspecialchars($_GET["message"]) ?>
        </p>
    <?php endif; ?>

    <a href="add_student.php" class="btn">+ Add New Student</a>

    <h2>Search and Filter Students</h2>

    <form method="GET">
        <input type="text" name="search"
               placeholder="Search name or email"
               value="<?= htmlspecialchars($search) ?>">

        <select name="course">
            <option value="">All Courses</option>
            <option value="Computer Engineering"
                <?= $course == "Computer Engineering" ? "selected" : "" ?>>
                Computer Engineering
            </option>
            <option value="Information Technology"
                <?= $course == "Information Technology" ? "selected" : "" ?>>
                Information Technology
            </option>
        </select>

        <button type="submit">Search</button>
        <a href="index.php" class="btn">Reset</a>
    </form>

    <h2>Student Records</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Course</th>
            <th>Actions</th>
        </tr>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row["student_id"] ?></td>
                    <td><?= htmlspecialchars($row["name"]) ?></td>
                    <td><?= htmlspecialchars($row["email"]) ?></td>
                    <td><?= htmlspecialchars($row["course"]) ?></td>
                    <td>
                        <div class="actions">
                            <a class="btn edit"
                               href="edit_student.php?id=<?= $row["student_id"] ?>">
                                Edit
                            </a>

                            <form action="delete_student.php" method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this student?');">
                                <input type="hidden" name="student_id"
                                       value="<?= $row["student_id"] ?>">
                                <button type="submit" class="delete">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">No students found.</td>
            </tr>
        <?php endif; ?>
    </table>

</div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
