
<?php
require_once "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($name == "" || $username == "" ||
        $email == "" || $password == "") {
        $message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $message = "Password must contain at least 8 characters.";
    } else {
        $check = $conn->prepare(
            "SELECT user_id FROM users
             WHERE email = ? OR username = ?"
        );

        $check->bind_param("ss", $email, $username);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Email or username already exists.";
        } else {
            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO users
                 (name, username, email, password)
                 VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssss",
                $name,
                $username,
                $email,
                $hashedPassword
            );

            if ($stmt->execute()) {
                $message = "Registration successful!";
            } else {
                $message = "Registration failed. Please try again.";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudentHub Registration</title>
</head>
<body>
    <h1>StudentHub User Registration</h1>

    <?php if ($message != "") { ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php } ?>

    <form method="POST" action="">
        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name"
               required minlength="2">
        <br><br>

        <label for="username">Username:</label>
        <input type="text" id="username" name="username"
               required minlength="3">
        <br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password"
               required minlength="8">
        <br><br>

        <button type="submit">Register</button>
    </form>
</body>
</html>
