
<?php
require_once "session_check.php";
require_once "db.php";

if (isset($_SESSION["user_id"])) {
    if ($_SESSION["role"] === "admin") {
        header("Location: admin_dashboard.php");
    } else {
        header("Location: student_dashboard.php");
    }
    exit;
}

$message = "";

if (isset($_GET["timeout"])) {
    $message = "Your session expired. Please log in again.";
} elseif (isset($_GET["loggedout"])) {
    $message = "You have logged out successfully.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $login = trim($_POST["login"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($login === "" || $password === "") {
        $message = "Please enter your username/email and password.";
    } else {
        $stmt = $conn->prepare(
            "SELECT user_id, name, username, email, password, role
             FROM users
             WHERE email = ? OR username = ?
             LIMIT 1"
        );

        $stmt->bind_param("ss", $login, $login);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result(
                $userId,
                $name,
                $dbUsername,
                $email,
                $passwordHash,
                $role
            );

            $stmt->fetch();

            if (password_verify($password, $passwordHash)) {
                if ($role !== "admin" && $role !== "student") {
                    $message = "This account has an invalid role.";
                } else {
                    session_regenerate_id(true);

                    $_SESSION["user_id"] = $userId;
                    $_SESSION["name"] = $name;
                    $_SESSION["username"] = $dbUsername;
                    $_SESSION["email"] = $email;
                    $_SESSION["role"] = $role;
                    $_SESSION["last_activity"] = time();

                    if ($role === "admin") {
                        header("Location: admin_dashboard.php");
                    } else {
                        header("Location: student_dashboard.php");
                    }
                    exit;
                }
            } else {
                $message = "Invalid username/email or password.";
            }
        } else {
            $message = "Invalid username/email or password.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-page">

    <main class="container login-container">
        <div class="brand-icon">SH</div>
        <h1>StudentHub</h1>
        <p class="subtitle">Sign in to your student account</p>

        <?php if ($message !== "") { ?>
            <p class="message" role="alert">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php } ?>

        <form method="POST" action="login.php">
            <label for="login">Username or Email</label>
            <input
                type="text"
                id="login"
                name="login"
                placeholder="Enter username or email"
                autocomplete="username"
                required
            >

            <label for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                autocomplete="current-password"
                required
            >

            <button type="submit">Sign In</button>
        </form>

        <p class="register-text">
            Don't have an account?
            <a href="../practical9/register.php">Register here</a>
        </p>
    </main>

</body>
</html>
