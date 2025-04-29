<?php
session_start(); // Start session to access session variables

// Destroy all session data
$_SESSION = array();
session_destroy();

// Optionally, destroy the session cookie too
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to login page (or home page)
header("Location: /klingo/views/login.php");
exit();
?>
