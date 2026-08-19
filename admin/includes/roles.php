<?php
/**
 * Helper role admin. Dipakai setelah auth.php.
 * role: 'admin' (penuh) | 'editor' (hanya add berita/galeri/team)
 */

if (!isset($_SESSION['admin_role'])) {
    $_SESSION['admin_role'] = 'admin';
}

function alfatah_admin_base_url(): string
{
    $script_name = $_SERVER['SCRIPT_NAME'] ?? '';
    $admin_pos = strpos($script_name, '/admin/');
    if ($admin_pos !== false) {
        return substr($script_name, 0, $admin_pos + 7);
    }
    return '/admin/';
}

function is_superadmin_role(): bool
{
    return ($_SESSION['admin_role'] ?? '') === 'superadmin';
}

function is_admin_role(): bool
{
    $role = $_SESSION['admin_role'] ?? '';
    return $role === 'admin' || $role === 'superadmin';
}

function is_editor_role(): bool
{
    return ($_SESSION['admin_role'] ?? '') === 'editor';
}

function is_kepsek_role(): bool
{
    return ($_SESSION['admin_role'] ?? '') === 'kepsek';
}

/**
 * Memastikan hak akses pengguna sesuai role minimal.
 */
function require_admin_role(string $min_role = 'admin'): void
{
    $current_role = $_SESSION['admin_role'] ?? '';
    
    $allowed = false;
    if ($min_role === 'superadmin') {
        $allowed = ($current_role === 'superadmin');
    } elseif ($min_role === 'admin') {
        $allowed = ($current_role === 'admin' || $current_role === 'superadmin' || $current_role === 'kepsek');
    } elseif ($min_role === 'editor') {
        $allowed = in_array($current_role, ['editor', 'admin', 'superadmin', 'kepsek'], true);
    }
    
    if (!$allowed) {
        $base = alfatah_admin_base_url();
        $msg = 'Anda tidak memiliki akses ke halaman ini.';
        if ($min_role === 'superadmin') {
            $msg = 'Hanya superadmin yang bisa mengakses halaman ini.';
        }
        header('Location: ' . $base . 'dashboard.php?err=' . rawurlencode($msg));
        exit();
    }
}

function require_role(string $min_role): void
{
    require_admin_role($min_role);
}
