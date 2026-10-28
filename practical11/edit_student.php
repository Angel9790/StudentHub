
<?php
require "db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    $conn->close();
    exit("Invalid student ID. <a href='index.php'>Go back</a>");
}

$stmt = $conn->prepare(
    "SELECT name, email, course FROM students WHERE student_id = ?"
);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    $conn->close();
    exit("Student not found. <a href='index.php'>Go back</a>");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $course = trim($_POST["course"] ?? "");

    if ($name == "" || !filter_var($email, FILTER_VALIDATE_EMAIL)
        || !in_array($course, ["Computer Engineering", "Information Technology"], true)) {
        $message = "Please enter valid student details.";
    } else {
        $stmt = $conn->prepare(
            "UPDATE students SET name = ?, email = ?, course = ?
             WHERE student_id = ?"
        );
        $stmt->bind_param("sssi", $name, $email, $course, $id);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: index.php?message=" .
                   urlencode("Student updated successfully."));
            exit;
        } else {
            $message = "Failed to update student.";
        }

        $stmt->close();
        $student["name"] = $name;
        $student["email"] = $email;
        $student["course"] = $course;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">

    <h1>Edit Student</h1>

    <?php if ($message != ""): ?>
        <p class="message error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" class="page-form">
        <label>Student Name</label><br>
        <input type="text" name="name" required
               value="<?= htmlspecialchars($student["name"]) ?>">

        <label>Email Address</label><br>
        <input type="email" name="email" required
               value="<?= htmlspecialchars($student["email"]) ?>">

        <label>Course</label><br>
        <select name="course" required>
            <option value="Computer Engineering"
                <?= $student["course"] == "Computer Engineering" ? "selected" : "" ?>>
                Computer Engineering
            </option>
            <option value="Information Technology"
                <?= $student["course"] == "Information Technology" ? "selected" : "" ?>>
                Information Technology
            </option>
        </select>

        <button type="submit">Update Student</button>
        <a href="index.php" class="btn">Cancel</a>
    </form>

</div>
</body>
</html>

<?php $conn->close(); ?>
