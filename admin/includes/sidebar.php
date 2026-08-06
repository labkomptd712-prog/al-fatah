<?php
// admin/includes/sidebar.php
// Sidebar navigasi untuk Panel Admin SDIT Al Fatah

$script_name = $_SERVER['SCRIPT_NAME'];
$admin_pos = strpos($script_name, '/admin/');
$base_admin = '';
if ($admin_pos !== false) {
    $base_admin = substr($script_name, 0, $admin_pos + 7);
} else {
    $base_admin = '/admin/';
}
?>
<div class="sidebar d-flex flex-column p-3 text-white bg-dark">
    <div class="sidebar-header text-center mb-3">
        <img src="<?= $base_admin ?>../assets/img/logo afix.png" alt="Logo SDIT Al Fatah" height="60" class="mb-2">
        <h6 class="fw-bold mb-0">SDIT Al Fatah</h6>
        <small class="text-success fw-semibold">Admin Panel</small>
    </div>
    <hr class="my-2 border-secondary">
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="<?= $base_admin ?>dashboard.php" class="nav-link text-white <?= (strpos($script_name, '/dashboard.php') !== false) ? 'active bg-success' : '' ?>">
                <i class="fa-solid fa-gauge-high me-2"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="<?= $base_admin ?>news/list.php" class="nav-link text-white <?= (strpos($script_name, '/news/') !== false) ? 'active bg-success' : '' ?>">
                <i class="fa-solid fa-newspaper me-2"></i> Berita Terbaru
            </a>
        </li>
        <li>
            <a href="<?= $base_admin ?>gallery/list.php" class="nav-link text-white <?= (strpos($script_name, '/gallery/') !== false) ? 'active bg-success' : '' ?>">
                <i class="fa-solid fa-images me-2"></i> Galeri Foto
            </a>
        </li>
        <li>
            <a href="<?= $base_admin ?>settings.php" class="nav-link text-white <?= (strpos($script_name, '/settings.php') !== false) ? 'active bg-success' : '' ?>">
                <i class="fa-solid fa-sliders me-2"></i> Pengaturan
            </a>
        </li>
    </ul>
    <hr class="my-2 border-secondary">
    <div class="dropdown">
        <a href="<?= $base_admin ?>logout.php" class="nav-link text-danger fw-semibold" onclick="return confirm('Apakah Anda yakin ingin logout?')">
            <i class="fa-solid fa-power-off me-2"></i> Logout
        </a>
    </div>
</div>
