
<?php
require __DIR__ . "/db.php";

$message = isset($_GET["message"]) ? $_GET["message"] : "";

$sql = "SELECT event_id, title, event_date, venue,
               description, poster, status
        FROM events
        ORDER BY event_id DESC";

$result = $conn->query($sql);

if (!$result) {
    exit("Unable to load events. Check the events table columns.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub - Event Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">

    <h1>StudentHub Event Management</h1>

    <?php if ($message !== ""): ?>
        <p class="message">
            <?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?>
        </p>
    <?php endif; ?>

    <p><a href="add_event.php">Add New Event</a></p>

    <?php if ($result->num_rows > 0): ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Poster</th>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Venue</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($event = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo (int)$event["event_id"]; ?></td>

                    <td>
                    <?php if (!empty($event["poster"])): ?>
                        <img
                            src="uploads/<?php echo rawurlencode(basename($event["poster"])); ?>"
                            alt="Event poster"
                            width="100">
                    <?php else: ?>
                        No poster
                    <?php endif; ?>
                    </td>

                    <td><?php echo htmlspecialchars($event["title"] ?? "", ENT_QUOTES, "UTF-8"); ?></td>
                    <td><?php echo htmlspecialchars($event["event_date"] ?? "", ENT_QUOTES, "UTF-8"); ?></td>
                    <td><?php echo htmlspecialchars($event["venue"] ?? "", ENT_QUOTES, "UTF-8"); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($event["description"] ?? "", ENT_QUOTES, "UTF-8")); ?></td>
                    <td><?php echo htmlspecialchars($event["status"] ?? "Open", ENT_QUOTES, "UTF-8"); ?></td>

                    <td>
                        <p>
                            <a href="edit_event.php?id=<?php echo (int)$event["event_id"]; ?>">
                                Edit
                            </a>
                        </p>

                        <form
                            action="delete_event.php"
                            method="POST"
                            onsubmit="return confirm('Are you sure you want to delete this event?');">
                            <input
                                type="hidden"
                                name="event_id"
                                value="<?php echo (int)$event["event_id"]; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php else: ?>
        <p>No events found. Click Add New Event to create one.</p>
    <?php endif; ?>

</div>
</body>
</html>
<?php
$result->free();
$conn->close();
?>
