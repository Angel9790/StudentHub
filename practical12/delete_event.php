
<?php
require __DIR__ . "/db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

if (
    !isset($_POST["event_id"]) ||
    !is_scalar($_POST["event_id"]) ||
    !ctype_digit((string)$_POST["event_id"]) ||
    (int)$_POST["event_id"] < 1
) {
    $conn->close();
    exit("Event ID was not received. Please return to the event list.");
}

$event_id = (int)$_POST["event_id"];

// Get the event and poster filename.
$stmt = $conn->prepare(
    "SELECT poster FROM events WHERE event_id = ?"
);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$event) {
    $conn->close();
    header("Location: index.php?message=" . urlencode("Event not found."));
    exit;
}

// Keep events that have registrations.
$stmt = $conn->prepare(
    "SELECT COUNT(*) AS total
     FROM registrations
     WHERE event_id = ?"
);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$registration = $stmt->get_result()->fetch_assoc();
$stmt->close();

if ((int)$registration["total"] > 0) {
    $conn->close();
    header(
        "Location: index.php?message=" .
        urlencode("Cannot delete this event because registrations are linked to it.")
    );
    exit;
}

// Delete the event.
$stmt = $conn->prepare(
    "DELETE FROM events WHERE event_id = ?"
);
$stmt->bind_param("i", $event_id);

if (!$stmt->execute()) {
    $stmt->close();
    $conn->close();
    header(
        "Location: index.php?message=" .
        urlencode("Database could not delete this event.")
    );
    exit;
}

$deleted = $stmt->affected_rows;
$stmt->close();
$conn->close();

if ($deleted > 0) {
    if (!empty($event["poster"])) {
        $posterPath = __DIR__ . "/uploads/" .
                      basename($event["poster"]);

        if (is_file($posterPath)) {
            unlink($posterPath);
        }
    }

    header(
        "Location: index.php?message=" .
        urlencode("Event deleted successfully.")
    );
    exit;
}

header(
    "Location: index.php?message=" .
    urlencode("Event could not be deleted.")
);
exit;
?>
