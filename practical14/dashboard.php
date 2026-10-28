
<?php
require "db.php";
requireLogin();

$isAdmin = $_SESSION["role"] === "admin";

$studentCount = 0;
$eventCount = 0;
$userCount = 0;
$registrationCount = 0;
$latest = null;

if ($isAdmin) {
    $studentCount = (int)$conn->query(
        "SELECT COUNT(*) AS total FROM students"
    )->fetch_assoc()["total"];

    $eventCount = (int)$conn->query(
        "SELECT COUNT(*) AS total FROM events"
    )->fetch_assoc()["total"];

    $userCount = (int)$conn->query(
        "SELECT COUNT(*) AS total FROM users"
    )->fetch_assoc()["total"];

    $registrationCount = (int)$conn->query(
        "SELECT COUNT(*) AS total FROM registrations"
    )->fetch_assoc()["total"];

    $latest = $conn->query(
        "SELECT r.student_name, e.title, r.registered_at
         FROM registrations r
         JOIN events e ON r.event_id = e.id
         ORDER BY r.registered_at DESC
         LIMIT 5"
    );

    logAction(
        $conn, $_SESSION["user_id"], $_SESSION["username"],
        "VIEW_DASHBOARD", "Admin opened dashboard"
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StudentHub Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="topbar">
        <div>
            <p class="eyebrow">STUDENTHUB • PRACTICAL 14</p>
            <h1>Admin Dashboard</h1>
        </div>
        <div class="user-info">
            <span>
                <?= htmlspecialchars($_SESSION["full_name"]) ?>
                (<?= htmlspecialchars($_SESSION["role"]) ?>)
            </span>
            <a class="button secondary" href="logout.php">Logout</a>
        </div>
    </header>

    <div class="layout">
        <aside class="sidebar">
            <h2>Navigation</h2>

            <a class="active" href="dashboard.php">Dashboard</a>

            <?php if ($isAdmin): ?>
                <a href="audit_logs.php">Audit Logs</a>
                <a href="#statistics">Statistics</a>
                <a href="#registrations">Latest Registrations</a>
            <?php else: ?>
                <p class="hint">
                    You have student-level access.
                </p>
            <?php endif; ?>
        </aside>

        <main class="content">
            <section class="welcome">
                <h2>
                    Welcome,
                    <?= htmlspecialchars($_SESSION["full_name"]) ?>!
                </h2>
                <p>
                    Your current role is
                    <strong>
                        <?= htmlspecialchars($_SESSION["role"]) ?>
                    </strong>.
                </p>
            </section>

            <?php if ($isAdmin): ?>
                <section id="statistics" class="stats">
                    <article class="stat-card">
                        <span>Total Users</span>
                        <strong><?= $userCount ?></strong>
                    </article>
                    <article class="stat-card">
                        <span>Total Students</span>
                        <strong><?= $studentCount ?></strong>
                    </article>
                    <article class="stat-card">
                        <span>Total Events</span>
                        <strong><?= $eventCount ?></strong>
                    </article>
                    <article class="stat-card">
                        <span>Registrations</span>
                        <strong><?= $registrationCount ?></strong>
                    </article>
                </section>

                <section id="registrations" class="panel">
                    <h2>Latest Registrations</h2>

                    <?php if ($latest && $latest->num_rows > 0): ?>
                        <div class="table-wrap">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Student</th>
                                        <th>Event</th>
                                        <th>Registered At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $latest->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($row["student_name"]) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($row["title"]) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($row["registered_at"]) ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No registrations found.</p>
                    <?php endif; ?>
                </section>

                <section class="panel">
                    <h2>Admin Access</h2>
                    <p>You can view statistics and audit logs.</p>
                    <a class="button" href="audit_logs.php">
                        View Audit Logs
                    </a>
                </section>
            <?php else: ?>
                <section class="panel">
                    <h2>Student Dashboard</h2>
                    <p>
                        You are logged in successfully, but admin
                        statistics and audit logs are restricted.
                    </p>
                </section>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
