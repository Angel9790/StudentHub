
<?php
require "db.php";

if (isset($_SESSION["user_id"])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT id, full_name, username, password, role, status
         FROM users WHERE username = ?"
    );
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user["password"])
        && $user["status"] === "active") {

        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["full_name"] = $user["full_name"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["role"] = $user["role"];

        logAction(
            $conn, $user["id"], $user["username"],
            "LOGIN", "User logged in"
        );

        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid login details or inactive account.";

        logAction(
            $conn, null, $username,
            "LOGIN_FAILED", "Failed login attempt"
        );
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StudentHub Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
    <main class="login-card">
        <p class="eyebrow">STUDENTHUB • PRACTICAL 14</p>
        <h1>Sign in</h1>
        <p>Log in to access your dashboard.</p>

        <?php if ($error !== ""): ?>
            <p class="error">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <label for="username">Username</label>
            <input id="username" name="username" required>

            <label for="password">Password</label>
            <input id="password" type="password"
                   name="password" required>

            <button type="submit">Login</button>
        </form>

        <p class="hint">Local demo: admin / admin123</p>
    </main>
</body>
</html>
