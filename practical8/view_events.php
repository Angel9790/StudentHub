
<?php
require_once "db.php";

$sql = "SELECT * FROM events";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$events = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>StudentHub Events</title>
</head>
<body>
    <h1>StudentHub Events</h1>

    <table border="1" cellpadding="8">
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Date</th>
            <th>Venue</th>
        </tr>

        <?php foreach ($events as $event) { ?>
        <tr>
            <td><?php echo htmlspecialchars($event["event_id"]); ?></td>
            <td><?php echo htmlspecialchars($event["title"]); ?></td>
            <td><?php echo htmlspecialchars($event["event_date"]); ?></td>
            <td><?php echo htmlspecialchars($event["venue"]); ?></td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
