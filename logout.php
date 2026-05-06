<?php
// Start de sessie
session_start();

// Unset alle sessievariabelen
$_SESSION = array();

// Vernieuw de sessiecookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Vernietig de sessie
session_destroy();

// Redirect naar de loginpagina
header("location: login.php");
exit;
?>
