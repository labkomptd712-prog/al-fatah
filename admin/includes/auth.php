<?php
// admin/includes/auth.php
// Validasi session admin. Jika belum login, redirect ke halaman login.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Cari letak URL absolut untuk login.php di folder admin/
    $script_name = $_SERVER['SCRIPT_NAME'];
    $admin_pos = strpos($script_name, '/admin/');
    if ($admin_pos !== false) {
        $base_admin_url = substr($script_name, 0, $admin_pos + 7);
        header("Location: " . $base_admin_url . "login.php");
    } else {
        header("Location: /admin/login.php");
    }
    exit();
}
?>
