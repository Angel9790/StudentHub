
<?php
require "db.php";

$message = "";
$uploadDir = __DIR__ . "/uploads/";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"] ?? "");
    $eventDate = trim($_POST["event_date"] ?? "");
    $venue = trim($_POST["venue"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $status = $_POST["status"] ?? "Open";

    $posterName = null;
    $allowedStatuses = ["Open", "Closed"];
    $allowedTypes = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp"
    ];

    if ($title == "" || $eventDate == "" || $venue == ""
        || !in_array($status, $allowedStatuses, true)) {
        $message = "Please enter valid event details.";
    } elseif (!DateTime::createFromFormat("!Y-m-d", $eventDate)
        || DateTime::createFromFormat("!Y-m-d", $eventDate)->format("Y-m-d") !== $eventDate) {
        $message = "Please enter a valid event date.";
    } else {
        $valid = true;

        if (isset($_FILES["poster"]) &&
            $_FILES["poster"]["error"] != UPLOAD_ERR_NO_FILE) {

            if ($_FILES["poster"]["error"] != UPLOAD_ERR_OK) {
                $message = "Poster upload failed.";
                $valid = false;
            } elseif ($_FILES["poster"]["size"] > 2 * 1024 * 1024) {
                $message = "Poster must be 2 MB or smaller.";
                $valid = false;
            } else {
                $tmp = $_FILES["poster"]["tmp_name"];
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($tmp);

                if (!isset($allowedTypes[$mime]) || getimagesize($tmp) === false) {
                    $message = "Only valid JPG, PNG, or WebP images are allowed.";
                    $valid = false;
                } else {
                    $posterName = bin2hex(random_bytes(16)) . "." . $allowedTypes[$mime];

                    if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
                        $message = "The uploads folder must exist and be writable.";
                        $valid = false;
                    } elseif (!move_uploaded_file($tmp, $uploadDir . $posterName)) {
                        $message = "Could not save the poster.";
                        $valid = false;
                    }
                }
            }
        }

        if ($valid) {
            $stmt = $conn->prepare(
                "INSERT INTO events
                (title, event_date, venue, description, poster, status)
                VALUES (?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssssss",
                $title,
                $eventDate,
                $venue,
                $description,
                $posterName,
                $status
            );

            if ($stmt->execute()) {
                $stmt->close();
                $conn->close();
                header("Location: index.php?message=" .
                       urlencode("Event added successfully."));
                exit;
            } else {
                if ($posterName !== null && is_file($uploadDir . $posterName)) {
                    unlink($uploadDir . $posterName);
                }
                $message = "Could not add the event.";
            }

            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Event</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Add New Event</h1>

    <?php if ($message != ""): ?>
        <p class="message error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Event Title</label>
        <input type="text" name="title" required
               value="<?= htmlspecialchars($_POST["title"] ?? "") ?>">

        <label>Event Date</label>
        <input type="date" name="event_date" required
               value="<?= htmlspecialchars($_POST["event_date"] ?? "") ?>">

        <label>Venue</label>
        <input type="text" name="venue" required
               value="<?= htmlspecialchars($_POST["venue"] ?? "") ?>">

        <label>Description</label>
        <textarea name="description"><?= htmlspecialchars($_POST["description"] ?? "") ?></textarea>

        <label>Event Status</label>
        <select name="status">
            <option value="Open">Open</option>
            <option value="Closed">Closed</option>
        </select>

        <label>Poster (JPG, PNG or WebP, max 2 MB)</label>
        <input type="file" name="poster" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

        <button type="submit">Save Event</button>
        <a class="btn" href="index.php">Back</a>
    </form>
</div>
</body>
</html>

<?php $conn->close(); ?>
