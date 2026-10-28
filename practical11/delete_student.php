
<?php
require "db.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $conn->close();
    header("Location: index.php");
    exit;
}

$id = filter_input(INPUT_POST, "student_id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    $message = "Invalid student ID.";
} else {
    $stmt = $conn->prepare(
        "DELETE FROM students WHERE student_id = ?"
    );
    $stmt->bind_param("i", $id);

    if ($stmt->execute() && $stmt->affected_rows > 0) {
        $message = "Student deleted successfully.";
    } else {
        $message = "Student not found or deletion failed.";
    }

    $stmt->close();
}

$conn->close();

header("Location: index.php?message=" . urlencode($message));
exit;
?>
