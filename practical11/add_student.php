
<?php
require "db.php";
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
            "INSERT INTO students (name, email, course) VALUES (?, ?, ?)"
        );
        $stmt->bind_param("sss", $name, $email, $course);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header("Location: index.php?message=" .
                   urlencode("Student added successfully."));
            exit;
        } else {
            $message = "Failed to add student. Check the email and try again.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">

    <h1>Add New Student</h1>

    <?php if ($message != ""): ?>
        <p class="message error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" class="page-form">
        <label>Student Name</label><br>
        <input type="text" name="name" required
               value="<?= htmlspecialchars($_POST["name"] ?? "") ?>">

        <label>Email Address</label><br>
        <input type="email" name="email" required
               value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">

        <label>Course</label><br>
        <select name="course" required>
            <option value="">Select Course</option>
            <option value="Computer Engineering">Computer Engineering</option>
            <option value="Information Technology">Information Technology</option>
        </select>

        <button type="submit">Save Student</button>
        <a href="index.php" class="btn">Back</a>
    </form>

</div>
</body>
</html>

<?php $conn->close(); ?>
