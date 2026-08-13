<?php
session_start(); // Mulai session

// Hapus semua session yang aktif
session_unset();

// Hancurkan session
session_destroy();

// Hapus cookie session di browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect ke halaman login
header("Location: login.php");
exit;
?>
