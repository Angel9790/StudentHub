
<?php
require_once "session_check.php";
requireRole("student");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard | StudentHub</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="topbar">
        <a class="site-name" href="student_dashboard.php">StudentHub</a>
        <span class="role-badge">Student</span>
    </header>

    <main class="container dashboard">
        <div class="welcome-icon">👋</div>
        <h1>Student Dashboard</h1>

        <p class="subtitle">
            Welcome back,
            <?php echo htmlspecialchars($_SESSION["name"]); ?>!
        </p>

        <section class="profile-card">
            <h2>Your Account</h2>

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

            <p>
                <strong>Role:</strong> Student
            </p>
        </section>

        <section class="feature-card">
            <h2>Student Menu</h2>
            <p>View your student profile and access StudentHub features.</p>
            <span class="feature-label">Student access enabled</span>
        </section>

        <a class="button-link logout-button" href="logout.php">
            Logout
        </a>
    </main>

</body>
</html>
