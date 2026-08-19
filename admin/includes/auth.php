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

// Global Read-Only Access Control for Kepsek (Kepala Sekolah)
if (is_kepsek_role()) {
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    // Normalize slashes
    $script = str_replace('\\', '/', $script);
    
    $is_forbidden = false;
    if (strpos($script, '/admin/admins/') !== false) {
        $is_forbidden = true;
    } elseif (strpos($script, '/admin/settings.php') !== false) {
        $is_forbidden = true;
    } else {
        // Block modification files
        $basename = basename($script);
        if (in_array($basename, ['add.php', 'edit.php', 'delete.php', 'approve.php', 'photos.php'], true)) {
            $is_forbidden = true;
        }
    }
    
    if ($is_forbidden) {
        $admin_pos = strpos($script, '/admin/');
        $base_admin_url = ($admin_pos !== false) ? substr($script, 0, $admin_pos + 7) : '/admin/';
        header("Location: " . $base_admin_url . "dashboard.php?err=" . urlencode("Akses ditolak: Kepala Sekolah tidak diizinkan mengubah data."));
        exit();
    }
}
