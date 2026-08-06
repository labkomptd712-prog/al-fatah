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

function is_admin_role(): bool
{
    return ($_SESSION['admin_role'] ?? '') === 'admin';
}

function is_editor_role(): bool
{
    return ($_SESSION['admin_role'] ?? '') === 'editor';
}

/**
 * Hanya role admin. Editor diarahkan ke dashboard.
 */
function require_admin_role(): void
{
    if (!is_admin_role()) {
        $base = alfatah_admin_base_url();
        header('Location: ' . $base . 'dashboard.php?err=' . rawurlencode('Anda tidak memiliki akses ke halaman ini.'));
        exit();
    }
}
