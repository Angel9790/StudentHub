
<?php
require "db.php";

if (isset($_SESSION["user_id"])) {
    logAction(
        $conn, $_SESSION["user_id"], $_SESSION["username"],
        "LOGOUT", "User logged out"
    );
}

$_SESSION = [];
session_destroy();

header("Location: login.php");
exit;
?>
