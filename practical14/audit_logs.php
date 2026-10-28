
<?php
require "db.php";
requireAdmin();

$result = $conn->query(
    "SELECT username, action, details, created_at
     FROM audit_logs
     ORDER BY id DESC
     LIMIT 100"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Audit Logs</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="topbar">
        <div>
            <p class="eyebrow">STUDENTHUB • PRACTICAL 14</p>
            <h1>Audit Logs</h1>
        </div>
        <a class="button secondary" href="dashboard.php">
            Back to Dashboard
        </a>
    </header>

    <main class="content">
        <section class="panel">
            <h2>Recorded Activities</h2>
            <p>Latest 100 audit log entries.</p>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Action</th>
                            <th>Details</th>
                            <th>Date and Time</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($row["username"]) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($row["action"]) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($row["details"]) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($row["created_at"]) ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
