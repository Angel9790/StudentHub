
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        "httponly" => true,
        "secure" => !empty($_SERVER["HTTPS"])
                    && $_SERVER["HTTPS"] !== "off",
        "samesite" => "Lax"
    ]);

    session_start();
}

$timeout = 900; // 15 minutes

if (isset($_SESSION["user_id"])) {
    if (
        isset($_SESSION["last_activity"]) &&
        time() - $_SESSION["last_activity"] > $timeout
    ) {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                "",
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();

        header("Location: login.php?timeout=1");
        exit;
    }

    $_SESSION["last_activity"] = time();
}

function requireLogin()
{
    if (!isset($_SESSION["user_id"])) {
        header("Location: login.php");
        exit;
    }
}

function requireRole($requiredRole)
{
    requireLogin();

    if ($_SESSION["role"] !== $requiredRole) {
        if ($_SESSION["role"] === "admin") {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: student_dashboard.php");
        }

        exit;
    }
}
?>
