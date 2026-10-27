
<?php
require_once "session_check.php";
requireRole("admin");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | StudentHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="topbar">
        <a class="site-name" href="admin_dashboard.php">StudentHub</a>
        <span class="role-badge admin-badge">Administrator</span>
    </header>

    <main class="container dashboard">
        <div class="welcome-icon">⚙️</div>
        <h1>Admin Dashboard</h1>

        <p class="subtitle">
            Welcome,
            <?php echo htmlspecialchars($_SESSION["name"]); ?>!
        </p>

        <section class="profile-card">
            <h2>Administrator Account</h2>

            <p>
                <strong>Name:</strong>
                <?php echo htmlspecialchars($_SESSION["name"]); ?>
            </p>

            <p>
                <strong>Username:</strong>
                <?php echo htmlspecialchars($_SESSION["username"]); ?>
            </p>

            <p>
                <strong>Email:</strong>
                <?php echo htmlspecialchars($_SESSION["email"]); ?>
            </p>

            <p><strong>Role:</strong> Administrator</p>
        </section>

        <section class="feature-card">
            <h2>Admin Menu</h2>
            <ul class="admin-menu">
                <li>Manage Students</li>
                <li>Manage Events</li>
                <li>View Registrations</li>
            </ul>
            <p class="small-note">
                These are demonstration menu items, not completed management features.
            </p>
        </section>

        <a class="button-link logout-button" href="logout.php">
            Logout
        </a>
    </main>

</body>
</html>
