<?php
// admin/includes/auth.php
// Validasi session admin. Jika belum login, redirect ke halaman login.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
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

require_once __DIR__ . '/roles.php';

// Sinkronkan role dari database ke session (jika role di DB berubah)
if (isset($_SESSION['admin_id'])) {
    require_once __DIR__ . '/../config/db.php';
    try {
        require_once __DIR__ . '/../sql/migrate.php';
        $stmt = $pdo->prepare("SELECT role FROM admins WHERE id = ? LIMIT 1");
        $stmt->execute([(int) $_SESSION['admin_id']]);
        $row = $stmt->fetch();
        if ($row && !empty($row['role'])) {
            $_SESSION['admin_role'] = $row['role'];
        } elseif (!isset($_SESSION['admin_role'])) {
            $_SESSION['admin_role'] = 'admin';
        }
    } catch (PDOException $e) {
        if (!isset($_SESSION['admin_role'])) {
            $_SESSION['admin_role'] = 'admin';
        }
    }
} elseif (!isset($_SESSION['admin_role'])) {
    $_SESSION['admin_role'] = 'admin';
}
