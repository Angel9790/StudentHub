
<?php
require "db.php";

$uploadDir = __DIR__ . "/uploads/";
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    $conn->close();
    exit("Invalid event ID. <a href='index.php'>Go back</a>");
}

$stmt = $conn->prepare("SELECT * FROM events WHERE event_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stmt->close();

if (!$event) {
    $conn->close();
    exit("Event not found. <a href='index.php'>Go back</a>");
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"] ?? "");
    $eventDate = trim($_POST["event_date"] ?? "");
    $venue = trim($_POST["venue"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $status = $_POST["status"] ?? "";
    $allowedStatuses = ["Open", "Closed"];
    $allowedTypes = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp"
    ];

    if ($title == "" || $eventDate == "" || $venue == ""
        || !in_array($status, $allowedStatuses, true)) {
        $message = "Please enter valid event details.";
    } else {
        $dateObject = DateTime::createFromFormat("!Y-m-d", $eventDate);

        if (!$dateObject || $dateObject->format("Y-m-d") !== $eventDate) {
            $message = "Please enter a valid event date.";
        } else {
            $oldPoster = $event["poster"];
            $newPoster = $oldPoster;
            $uploadedNewPoster = false;
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
                    } elseif (!is_dir($uploadDir) || !is_writable($uploadDir)) {
                        $message = "The uploads folder must exist and be writable.";
                        $valid = false;
                    } else {
                        $newPoster = bin2hex(random_bytes(16)) . "." . $allowedTypes[$mime];

                        if (move_uploaded_file($tmp, $uploadDir . $newPoster)) {
                            $uploadedNewPoster = true;
                        } else {
                            $message = "Could not save the new poster.";
                            $valid = false;
                        }
                    }
                }
            }

            if ($valid) {
                $stmt = $conn->prepare(
                    "UPDATE events
                     SET title = ?, event_date = ?, venue = ?,
                         description = ?, poster = ?, status = ?
                     WHERE event_id = ?"
                );

                $stmt->bind_param(
                    "ssssssi",
                    $title,
                    $eventDate,
                    $venue,
                    $description,
                    $newPoster,
                    $status,
                    $id
                );

                if ($stmt->execute()) {
                    if ($uploadedNewPoster && $oldPoster) {
                        $oldPath = $uploadDir . basename($oldPoster);
                        if (is_file($oldPath)) {
                            unlink($oldPath);
                        }
                    }

                    $stmt->close();
                    $conn->close();
                    header("Location: index.php?message=" .
                           urlencode("Event updated successfully."));
                    exit;
                } else {
                    if ($uploadedNewPoster && is_file($uploadDir . $newPoster)) {
                        unlink($uploadDir . $newPoster);
                    }
                    $message = "Could not update the event.";
                }

                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <h1>Edit Event</h1>

    <?php if ($message != ""): ?>
        <p class="message error"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Event Title</label>
        <input type="text" name="title" required
               value="<?= htmlspecialchars($_POST["title"] ?? $event["title"]) ?>">

        <label>Event Date</label>
        <input type="date" name="event_date" required
               value="<?= htmlspecialchars($_POST["event_date"] ?? $event["event_date"]) ?>">

        <label>Venue</label>
        <input type="text" name="venue" required
               value="<?= htmlspecialchars($_POST["venue"] ?? $event["venue"]) ?>">

        <label>Description</label>
        <textarea name="description"><?= htmlspecialchars($_POST["description"] ?? $event["description"] ?? "") ?></textarea>

        <label>Event Status</label>
        <select name="status">
            <option value="Open"
                <?= ($_POST["status"] ?? $event["status"]) == "Open" ? "selected" : "" ?>>
                Open
            </option>
            <option value="Closed"
                <?= ($_POST["status"] ?? $event["status"]) == "Closed" ? "selected" : "" ?>>
                Closed
            </option>
        </select>

        <?php if (!empty($event["poster"])): ?>
            <p>Current poster:</p>
            <img class="poster"
                 src="uploads/<?= rawurlencode(basename($event["poster"])) ?>"
                 alt="Current event poster">
        <?php endif; ?>

        <label>Choose a new poster (optional, max 2 MB)</label>
        <input type="file" name="poster" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">

        <button type="submit">Update Event</button>
        <a class="btn" href="index.php">Cancel</a>
    </form>
</div>
</body>
</html>

<?php $conn->close(); ?>
