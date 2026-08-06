<?php
// admin/logout.php
// Menghapus session admin dan redirect ke halaman login

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Unset semua session variables
$_SESSION = array();

// Hancurkan session cookie jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan session secara total
session_destroy();

// Redirect ke login.php
header("Location: login.php");
exit();
?>
