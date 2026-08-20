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

$role = $_SESSION['admin_role'] ?? 'admin';
$is_admin = ($role === 'admin' || $role === 'superadmin');
$is_editor = ($role === 'editor');
$is_superadmin = ($role === 'superadmin');
$is_kepsek = ($role === 'kepsek');

$unread_messages = 0;
$pending_news_count = 0;
$pending_revisinya_count = 0;

if ($is_admin) {
    try {
        $unread_messages = (int) $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
        $pending_news_count = (int) $pdo->query("SELECT COUNT(*) FROM news WHERE status = 'pending'")->fetchColumn();
        $pending_revisinya_count = (int) $pdo->query("SELECT COUNT(*) FROM revision_requests WHERE status = 'pending'")->fetchColumn();
    } catch (PDOException $e) {
        // Fail silently
    }
}
?>
<!-- Theme initialization and assets dynamic injector -->
<script>
(function() {
    // 1. Initialize dark mode theme immediately to avoid white flashes
    var savedTheme = localStorage.getItem('admin_theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);

    // 2. Force cache bust of admin.css dynamically to load new styling & variables
    var links = document.getElementsByTagName('link');
    for (var i = 0; i < links.length; i++) {
        var href = links[i].getAttribute('href');
        if (href && href.indexOf('admin.css') !== -1) {
            var cleanHref = href.split('?')[0];
            links[i].setAttribute('href', cleanHref + '?v=2.9');
        }
    }

    // 3. Inject favicon
    var link = document.querySelector("link[rel*='icon']") || document.createElement('link');
    link.type = 'image/png';
    link.rel = 'icon';
    link.href = '<?= $base_admin ?>../assets/img/logo afix.png';
    document.head.appendChild(link);
    
    var appleLink = document.querySelector("link[rel='apple-touch-icon']") || document.createElement('link');
    appleLink.rel = 'apple-touch-icon';
    appleLink.href = '<?= $base_admin ?>../assets/img/logo afix.png';
    document.head.appendChild(appleLink);
})();

// 3. Inject Theme Toggle Button on DOMContentLoaded
document.addEventListener('DOMContentLoaded', function() {
    var toggleBtn = document.createElement('button');
    toggleBtn.className = 'dark-mode-toggle btn';
    toggleBtn.style.width = '40px';
    toggleBtn.style.height = '40px';
    toggleBtn.style.borderRadius = '50%';
    toggleBtn.style.border = '1px solid var(--border-color)';
    toggleBtn.style.backgroundColor = 'var(--card-bg)';
    toggleBtn.style.color = 'var(--text-color)';
    toggleBtn.style.display = 'inline-flex';
    toggleBtn.style.alignItems = 'center';
    toggleBtn.style.justifyContent = 'center';
    toggleBtn.style.cursor = 'pointer';
    toggleBtn.style.marginLeft = '12px';
    toggleBtn.style.transition = 'all 0.2s ease-in-out';
    toggleBtn.setAttribute('title', 'Switch Theme');

    // Set initial icon
    var currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
    toggleBtn.innerHTML = currentTheme === 'dark' ? '<i class="fa-solid fa-sun"></i>' : '<i class="fa-solid fa-moon"></i>';

    // Find header topbar area to append
    var mobileHeader = document.querySelector('.mobile-admin-header');
    var headerRight = document.querySelector('.main-header .text-end') || document.querySelector('.main-header');
    if (window.innerWidth < 768 && mobileHeader) {
        toggleBtn.style.marginLeft = 'auto';
        mobileHeader.appendChild(toggleBtn);
    } else if (headerRight) {
        headerRight.appendChild(toggleBtn);
    } else {
        // Fallback floating toggle if header is not found
        toggleBtn.style.position = 'fixed';
        toggleBtn.style.top = '15px';
        toggleBtn.style.right = '15px';
        toggleBtn.style.zIndex = '1050';
        document.body.appendChild(toggleBtn);
    }

    // Toggle theme on click
    toggleBtn.addEventListener('click', function() {
        var activeTheme = document.documentElement.getAttribute('data-theme') || 'light';
        var newTheme = activeTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('admin_theme', newTheme);
        
        // Update icon
        toggleBtn.innerHTML = newTheme === 'dark' ? '<i class="fa-solid fa-sun"></i>' : '<i class="fa-solid fa-moon"></i>';
    });
});
</script>
<style>
/* Sidebar and main content spacing for desktop layout */
@media (min-width: 992px) {
    .sidebar {
        width: 260px;
        height: 100vh !important;
        position: fixed !important;
        top: 0;
        left: 0;
        z-index: 1000;
        box-shadow: 4px 0 10px rgba(0, 0, 0, 0.05);
        overflow-y: auto !important;
        overflow-x: hidden !important;
        transition: width 0.2s ease !important;
    }

    .main-content {
        margin-left: 260px !important;
        transition: margin-left 0.2s ease !important;
    }
}

/* Force Collapsed Sidebar Layout on Tablet Screen Sizes */
@media (min-width: 768px) and (max-width: 991.98px) {
    .sidebar {
        width: 75px !important;
        height: 100vh !important;
        position: fixed !important;
        top: 0;
        left: 0;
        z-index: 1000;
        box-shadow: 4px 0 10px rgba(0, 0, 0, 0.05);
        overflow-y: auto !important;
        overflow-x: hidden !important;
        padding: 10px 5px !important;
    }

    .main-content {
        margin-left: 75px !important;
        padding: 15px !important;
    }

    /* Hide text nodes, labels, and chevron toggles */
    .sidebar .sidebar-header h6,
    .sidebar .sidebar-header small,
    .sidebar .sidebar-header div,
    .sidebar .sidebar-header .user-info,
    .sidebar hr,
    .sidebar .nav-link span,
    .sidebar .nav-link .fa-chevron-down {
        display: none !important;
    }

    .sidebar-header {
        margin-bottom: 10px !important;
        padding-top: 10px !important;
    }

    .sidebar-header img {
        height: 40px !important;
        margin-bottom: 0 !important;
    }

    .sidebar .nav-link {
        text-align: center;
        padding: 12px 10px !important;
        justify-content: center !important;
    }

    .sidebar .nav-link i {
        margin-right: 0 !important;
        font-size: 18px;
    }

    .sidebar .collapse {
        display: none !important;
    }
    
    #sidebarToggle {
        position: relative !important;
        top: auto !important;
        right: auto !important;
        margin: 5px auto 10px auto !important;
        display: block !important;
        width: fit-content;
    }
}

/* Custom scrollbar for sidebar */
.sidebar::-webkit-scrollbar {
    width: 6px;
}
.sidebar::-webkit-scrollbar-track {
    background: rgba(0, 0, 0, 0.1);
}
.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 3px;
}
.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.4);
}

/* Sidebar sub-menu collapsible styling */
.sidebar .collapse ul li a {
    padding: 8px 12px;
    margin-bottom: 2px;
    font-size: 13px;
    border-radius: 6px;
    text-decoration: none;
    display: block;
    transition: all 0.2s ease;
}
.sidebar .collapse ul li a:hover {
    color: #fff !important;
    background-color: rgba(255, 255, 255, 0.05);
}
.sidebar .nav-link[aria-expanded="true"] .fa-chevron-down {
    transform: rotate(180deg);
}

/* Collapsed Sidebar State (Desktop Manual Toggled) */
body.sidebar-collapsed .sidebar {
    width: 75px !important;
    padding: 10px 5px !important;
}

body.sidebar-collapsed .main-content {
    margin-left: 75px !important;
}

body.sidebar-collapsed .sidebar .sidebar-header h6,
body.sidebar-collapsed .sidebar .sidebar-header small,
body.sidebar-collapsed .sidebar .sidebar-header div,
body.sidebar-collapsed .sidebar .sidebar-header .user-info,
body.sidebar-collapsed .sidebar hr,
body.sidebar-collapsed .sidebar .nav-link span,
body.sidebar-collapsed .sidebar .nav-link .fa-chevron-down {
    display: none !important;
}

body.sidebar-collapsed .sidebar-header {
    margin-bottom: 10px !important;
    padding-top: 10px !important;
}

body.sidebar-collapsed .sidebar-header img {
    height: 40px !important;
    margin-bottom: 0 !important;
}

body.sidebar-collapsed .sidebar .nav-link {
    text-align: center;
    padding: 12px 10px !important;
    justify-content: center !important;
}

body.sidebar-collapsed .sidebar .nav-link i {
    margin-right: 0 !important;
    font-size: 18px;
}

body.sidebar-collapsed #sidebarToggle {
    position: relative !important;
    top: auto !important;
    right: auto !important;
    margin: 5px auto 10px auto !important;
    display: block !important;
    width: fit-content;
}

body.sidebar-collapsed .sidebar .collapse {
    display: none !important;
}

/* Styles for slide-in sidebar on Mobile */
@media (max-width: 767.98px) {
    .admin-container {
        flex-direction: column !important;
    }
    .sidebar {
        width: 260px !important;
        left: -260px !important;
        position: fixed !important;
        top: 0 !important;
        z-index: 1050 !important;
        transition: left 0.3s ease !important;
        display: block !important;
        background-color: var(--sidebar-bg) !important;
        padding: 15px !important;
    }
    
    .sidebar .sidebar-header h6,
    .sidebar .sidebar-header small,
    .sidebar .sidebar-header div,
    .sidebar .sidebar-header .user-info,
    .sidebar hr,
    .sidebar .nav-link span,
    .sidebar .nav-link .fa-chevron-down {
        display: inline-block !important;
    }
    .sidebar hr {
        display: block !important;
        border-top: 1px solid rgba(255,255,255,0.1) !important;
    }
    .sidebar .sidebar-header {
        display: block !important;
        margin-bottom: 20px !important;
        padding-top: 20px !important;
    }
    .sidebar .sidebar-header img {
        height: 50px !important;
        display: inline-block !important;
    }
    .sidebar .nav-link {
        text-align: left !important;
        padding: 10px 15px !important;
        justify-content: flex-start !important;
    }
    .sidebar .nav-link i {
        margin-right: 10px !important;
        font-size: 16px !important;
    }
    .sidebar .collapse.show {
        display: block !important;
    }
    .sidebar .collapse:not(.show) {
        display: none !important;
    }
    
    .sidebar.show,
    .sidebar.open,
    .sidebar.active {
        left: 0 !important;
    }
    
    .main-content {
        margin-left: 0 !important;
        width: 100vw !important;
        max-width: 100vw !important;
        padding: 15px !important;
    }

    #sidebarToggle {
        display: none !important;
    }
}

/* Overlay for mobile sidebar */
.sidebar-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.55);
    z-index: 1040;
    backdrop-filter: blur(2px);
}
.sidebar-overlay.show {
    display: block;
}

/* Mobile top nav bar styles */
.mobile-admin-header {
    background-color: var(--sidebar-bg) !important;
    border-bottom: 1px solid var(--border-color) !important;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    color: #ffffff !important;
}
.badge-notif {
    background: #ef4444;
    color: white;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 10px;
    margin-left: 8px;
    display: inline-block;
    vertical-align: middle;
    line-height: 1;
}
</style>

<!-- Mobile Header Bar (Only visible on screens < 768px) -->
<div class="mobile-admin-header d-flex align-items-center d-md-none bg-dark text-white px-3 py-2 position-sticky top-0" style="z-index: 1030; height: 56px; margin: 0; width: 100%;">
    <button id="mobileSidebarToggle" class="btn text-white p-0 border-0 me-3" style="font-size: 24px; line-height: 1;">
        <i class="fa-solid fa-bars"></i>
    </button>
    <img src="<?= $base_admin ?>../assets/img/logo afix.png" alt="Logo" height="36" class="me-2">
    <span class="fw-bold" style="font-size: 16px;">Admin SDIT</span>
</div>

<!-- Sidebar Overlay (Only visible on mobile when sidebar is open) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="sidebar d-flex flex-column p-3 text-white bg-dark">
    <div class="sidebar-header text-center mb-3 position-relative">
        <button id="sidebarToggle" class="btn btn-sm btn-dark position-absolute top-0 end-0 border-0 text-white-50 p-1" style="font-size: 12px; z-index: 1010; background-color: rgba(0,0,0,0.2);" title="Minimize Sidebar">
            <i class="fa-solid fa-chevron-left"></i>
        </button>
        <img src="<?= $base_admin ?>../assets/img/logo afix.png" alt="Logo SDIT Al Fatah" height="60" class="mb-2">
        <h6 class="fw-bold mb-0"><span class="brand-font">SDIT Al Fatah</span></h6>
        <small class="text-success fw-semibold">Admin Panel</small>
        <?php if ($is_superadmin): ?>
            <div class="mt-1"><span class="badge bg-danger">Superadmin</span></div>
        <?php elseif ($is_editor): ?>
            <div class="mt-1"><span class="badge bg-secondary">Editor</span></div>
        <?php else: ?>
            <div class="mt-1"><span class="badge bg-success">Admin</span></div>
        <?php endif; ?>
        <div class="mt-2 text-white-50 small user-info">
            <i class="fa-solid fa-user me-1"></i> User: <strong><?= htmlspecialchars($_SESSION['admin_username'] ?? '') ?></strong>
        </div>
    </div>
    <hr class="my-2 border-secondary">
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="<?= $base_admin ?>dashboard.php" class="nav-link text-white <?= (strpos($script_name, '/dashboard.php') !== false) ? 'active bg-success' : '' ?>">
                <i class="fa-solid fa-gauge-high me-2"></i> <span>Dashboard</span>
            </a>
        </li>
        
        <?php if ($is_admin): ?>
        <li class="nav-item">
            <a href="<?= $base_admin ?>messages/list.php" class="nav-link text-white <?= (strpos($script_name, '/messages/') !== false) ? 'active bg-success' : '' ?>">
                <i class="fa-solid fa-envelope me-2"></i> <span>Pesan Masuk</span>
                <span class="badge-notif ms-auto <?= ($unread_messages > 0) ? '' : 'd-none' ?>" id="badge-messages"><?= $unread_messages ?></span>
            </a>
        </li>
        <?php endif; ?>

        <!-- Kategori 1: Kelola Konten -->
        <li class="nav-item mt-2">
            <a class="nav-link text-white d-flex align-items-center justify-content-between" data-bs-toggle="collapse" href="#kontenMenu" role="button" aria-expanded="<?= (strpos($script_name, '/news/') !== false || strpos($script_name, '/gallery') !== false || strpos($script_name, '/gallery-categories') !== false || strpos($script_name, '/ekskul') !== false || strpos($script_name, '/ekskul-categories') !== false || strpos($script_name, '/prestasi') !== false || strpos($script_name, '/prestasi-categories') !== false || strpos($script_name, '/facilities') !== false || strpos($script_name, '/facility-categories') !== false) ? 'true' : 'false' ?>">
                <i class="fa-solid fa-file-signature me-2"></i> <span>Kelola Konten</span>
                <i class="fa-solid fa-chevron-down ms-auto" style="font-size: 10px; transition: transform 0.2s;"></i>
            </a>
            <div class="collapse <?= (strpos($script_name, '/news/') !== false || strpos($script_name, '/gallery') !== false || strpos($script_name, '/gallery-categories') !== false || strpos($script_name, '/ekskul') !== false || strpos($script_name, '/ekskul-categories') !== false || strpos($script_name, '/prestasi') !== false || strpos($script_name, '/prestasi-categories') !== false || strpos($script_name, '/facilities') !== false || strpos($script_name, '/facility-categories') !== false) ? 'show' : '' ?>" id="kontenMenu">
                <ul class="nav flex-column ms-3 mt-1 small">
                    <?php if ($is_admin || $is_kepsek): ?>
                    <li>
                        <a href="<?= $base_admin ?>news/list.php" class="nav-link text-white-50 <?= (strpos($script_name, '/news/') !== false && strpos($script_name, 'riwayat') === false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-newspaper me-2"></i> <span>Berita Terbaru</span>
                            <span class="badge-notif ms-auto <?= ($pending_news_count > 0) ? '' : 'd-none' ?>" id="badge-news"><?= $pending_news_count ?></span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_admin ?>gallery/list.php" class="nav-link text-white-50 <?= (strpos($script_name, '/gallery/') !== false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-images me-2"></i> <span>Galeri Foto</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_admin ?>gallery-categories/list.php" class="nav-link text-white-50 <?= (strpos($script_name, '/gallery-categories/') !== false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-folder-tree me-2"></i> <span>Kategori Galeri</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_admin ?>ekskul/list.php" class="nav-link text-white-50 <?= (strpos($script_name, '/ekskul/') !== false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-award me-2"></i> <span>Kelola Ekskul</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_admin ?>ekskul-categories/list.php" class="nav-link text-white-50 <?= (strpos($script_name, '/ekskul-categories/') !== false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-layer-group me-2"></i> <span>Kategori Ekskul</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_admin ?>prestasi/list.php" class="nav-link text-white-50 <?= (strpos($script_name, '/prestasi/') !== false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-trophy me-2"></i> <span>Kelola Prestasi</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_admin ?>prestasi-categories/list.php" class="nav-link text-white-50 <?= (strpos($script_name, '/prestasi-categories/') !== false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-layer-group me-2"></i> <span>Kategori Prestasi</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_admin ?>facilities/list.php" class="nav-link text-white-50 <?= (strpos($script_name, '/facilities/') !== false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-building me-2"></i> <span>Kelola Fasilitas</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_admin ?>facility-categories/list.php" class="nav-link text-white-50 <?= (strpos($script_name, '/facility-categories/') !== false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-folder-open me-2"></i> <span>Kategori Fasilitas</span>
                        </a>
                    </li>
                    <?php else: ?>
                    <li>
                        <a href="<?= $base_admin ?>news/add.php" class="nav-link text-white-50 <?= (strpos($script_name, '/news/add.php') !== false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-plus me-2"></i> <span>Tambah Berita</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_admin ?>gallery/add.php" class="nav-link text-white-50 <?= (strpos($script_name, '/gallery/add.php') !== false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-plus me-2"></i> <span>Tambah Galeri</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_admin ?>ekskul/add.php" class="nav-link text-white-50 <?= (strpos($script_name, '/ekskul/add.php') !== false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-plus me-2"></i> <span>Tambah Ekskul</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_admin ?>prestasi/add.php" class="nav-link text-white-50 <?= (strpos($script_name, '/prestasi/add.php') !== false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-plus me-2"></i> <span>Tambah Prestasi</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_admin ?>facilities/add.php" class="nav-link text-white-50 <?= (strpos($script_name, '/facilities/add.php') !== false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-plus me-2"></i> <span>Tambah Fasilitas</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_admin ?>news/riwayat.php" class="nav-link text-white-50 <?= (strpos($script_name, '/news/riwayat.php') !== false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-clock-rotate-left me-2"></i> <span>Riwayat Pengajuan</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </li>

        <?php if ($is_admin || $is_kepsek): ?>
        <!-- Kategori 2: Akademik -->
        <li class="nav-item mt-2">
            <a class="nav-link text-white d-flex align-items-center justify-content-between" data-bs-toggle="collapse" href="#akademikMenu" role="button" aria-expanded="<?= (strpos($script_name, '/testimonials/') !== false || strpos($script_name, '/team/') !== false || strpos($script_name, '/classes/') !== false || strpos($script_name, '/struktur/') !== false) ? 'true' : 'false' ?>">
                <i class="fa-solid fa-graduation-cap me-2"></i> <span>Akademik</span>
                <i class="fa-solid fa-chevron-down ms-auto" style="font-size: 10px; transition: transform 0.2s;"></i>
            </a>
            <div class="collapse <?= (strpos($script_name, '/testimonials/') !== false || strpos($script_name, '/team/') !== false || strpos($script_name, '/classes/') !== false || strpos($script_name, '/struktur/') !== false) ? 'show' : '' ?>" id="akademikMenu">
                <ul class="nav flex-column ms-3 mt-1 small">
                    <li>
                        <a href="<?= $base_admin ?>testimonials/list.php" class="nav-link text-white-50 <?= (strpos($script_name, '/testimonials/') !== false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-comment-dots me-2"></i> <span>Alumni & Testimoni</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_admin ?>team/list.php" class="nav-link text-white-50 <?= (strpos($script_name, '/team/') !== false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-users me-2"></i> <span>Team / Guru</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_admin ?>classes/list.php" class="nav-link text-white-50 <?= (strpos($script_name, '/classes/') !== false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-chalkboard-user me-2"></i> <span>Kelas & Wali</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= $base_admin ?>struktur/list.php" class="nav-link text-white-50 <?= (strpos($script_name, '/struktur/') !== false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-sitemap me-2"></i> <span>Struktur Org</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        <?php endif; ?>

        <?php if ($is_admin): ?>
        <!-- Revisi Kepsek Nav Link -->
        <li class="nav-item mt-2">
            <a href="<?= $base_admin ?>revisions/list.php" class="nav-link text-white <?= (strpos($script_name, '/revisions/') !== false) ? 'active bg-success' : '' ?>">
                <i class="fa-solid fa-comments me-2"></i> <span>Revisi Kepsek</span>
                <span class="badge-notif ms-auto <?= ($pending_revisinya_count > 0) ? '' : 'd-none' ?>" id="badge-revisi"><?= $pending_revisinya_count ?></span>
            </a>
        </li>
        
        <!-- Riwayat Aktivitas Nav Link -->
        <li class="nav-item mt-2">
            <a href="<?= $base_admin ?>activity-log/list.php" class="nav-link text-white <?= (strpos($script_name, '/activity-log/') !== false) ? 'active bg-success' : '' ?>">
                <i class="fa-solid fa-clock-rotate-left me-2"></i> <span>Riwayat Aktivitas</span>
            </a>
        </li>

        <!-- Kategori 3: Pengaturan -->
        <li class="nav-item mt-2">
            <a class="nav-link text-white d-flex align-items-center justify-content-between" data-bs-toggle="collapse" href="#pengaturanMenu" role="button" aria-expanded="<?= (strpos($script_name, '/settings.php') !== false || strpos($script_name, '/admins/') !== false || strpos($script_name, '/footer-links/') !== false) ? 'true' : 'false' ?>">
                <i class="fa-solid fa-gears me-2"></i> <span>Pengaturan</span>
                <i class="fa-solid fa-chevron-down ms-auto" style="font-size: 10px; transition: transform 0.2s;"></i>
            </a>
            <div class="collapse <?= (strpos($script_name, '/settings.php') !== false || strpos($script_name, '/admins/') !== false || strpos($script_name, '/footer-links/') !== false) ? 'show' : '' ?>" id="pengaturanMenu">
                <ul class="nav flex-column ms-3 mt-1 small">
                    <li>
                        <a href="<?= $base_admin ?>settings.php" class="nav-link text-white-50 <?= (strpos($script_name, '/settings.php') !== false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-sliders me-2"></i> <span>Pengaturan Umum</span>
                        </a>
                    </li>
                    <?php if ($is_admin): ?>
                    <li>
                        <a href="<?= $base_admin ?>footer-links/list.php" class="nav-link text-white-50 <?= (strpos($script_name, '/footer-links/') !== false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-list me-2"></i> <span>Kelola Footer</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if ($is_superadmin): ?>
                    <li>
                        <a href="<?= $base_admin ?>admins/list.php" class="nav-link text-white-50 <?= (strpos($script_name, '/admins/') !== false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-user-shield me-2"></i> <span>Kelola Akun Admin</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </li>
        <?php endif; ?>
    </ul>
    <hr class="my-2 border-secondary">
    <div class="dropdown">
        <a href="<?= $base_admin ?>logout.php" class="nav-link text-danger fw-semibold" onclick="return confirm('Apakah Anda yakin ingin logout?')">
            <i class="fa-solid fa-power-off me-2"></i> <span>Logout</span>
        </a>
    </div>
</div>
<!-- Custom Confirm Modal & Toast Notifications -->
<script src="<?= $base_admin ?>assets/js/confirm-modal.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const toggleBtn = document.getElementById('sidebarToggle');
    const body = document.body;
    
    // Restore sidebar state from localStorage
    if (localStorage.getItem('sidebar-collapsed') === 'true') {
        body.classList.add('sidebar-collapsed');
        if (toggleBtn) {
            toggleBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
            toggleBtn.title = "Expand Sidebar";
        }
    }
    
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function() {
            body.classList.toggle('sidebar-collapsed');
            const isCollapsed = body.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebar-collapsed', isCollapsed);
            
            if (isCollapsed) {
                toggleBtn.innerHTML = '<i class="fa-solid fa-chevron-right"></i>';
                toggleBtn.title = "Expand Sidebar";
            } else {
                toggleBtn.innerHTML = '<i class="fa-solid fa-chevron-left"></i>';
                toggleBtn.title = "Minimize Sidebar";
            }
        });
    }
    
    // Mobile Sidebar Toggle
    const toggleMobileBtn = document.getElementById('mobileSidebarToggle');
    const sidebarEl = document.querySelector('.sidebar');
    const overlayEl = document.getElementById('sidebarOverlay');

    if (toggleMobileBtn && sidebarEl && overlayEl) {
        toggleMobileBtn.addEventListener('click', function() {
            sidebarEl.classList.toggle('show');
            overlayEl.classList.toggle('show');
        });

        overlayEl.addEventListener('click', function() {
            sidebarEl.classList.remove('show');
            overlayEl.classList.remove('show');
        });
    }

    // Dropdown state persistence across page loads
    const menuIds = ['kontenMenu', 'akademikMenu', 'pengaturanMenu'];
    menuIds.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        
        const savedState = localStorage.getItem('collapse-' + id);
        const trigger = document.querySelector(`a[href="#${id}"]`);
        
        // Restore collapse states
        if (savedState === 'show') {
            el.classList.add('show');
            if (trigger) {
                trigger.setAttribute('aria-expanded', 'true');
                trigger.classList.remove('collapsed');
            }
        } else if (savedState === 'hide') {
            el.classList.remove('show');
            if (trigger) {
                trigger.setAttribute('aria-expanded', 'false');
                trigger.classList.add('collapsed');
            }
        }
        
        // Listen to Bootstrap collapse events to save state
        el.addEventListener('shown.bs.collapse', function() {
            localStorage.setItem('collapse-' + id, 'show');
        });
        el.addEventListener('hidden.bs.collapse', function() {
            localStorage.setItem('collapse-' + id, 'hide');
        });
    });

    // Automatically wrap text in header "Tambah" buttons and add .btn-add-new class
    document.querySelectorAll('.main-header a.btn, .main-header button.btn').forEach(function(btn) {
        if (btn.querySelector('.fa-plus')) {
            btn.classList.add('btn-add-new');
            btn.childNodes.forEach(function(node) {
                if (node.nodeType === Node.TEXT_NODE && node.textContent.trim().length > 0) {
                    var span = document.createElement('span');
                    span.className = 'btn-text';
                    span.textContent = node.textContent;
                    node.parentNode.replaceChild(span, node);
                }
            });
        }
    });

    // Run only on mobile screen sizes (< 768px)
    if (window.innerWidth < 768) {
        document.querySelectorAll('table').forEach(function(table) {
            // Collect headers
            var headers = [];
            table.querySelectorAll('thead th').forEach(function(th) {
                headers.push(th.textContent.trim());
            });
            
            var tbody = table.querySelector('tbody');
            if (!tbody) return;

            // Check if there are no items
            if (tbody.querySelector('td[colspan]')) {
                // Empty table state, skip
                return;
            }

            // Create container for details cards
            var container = document.createElement('div');
            container.className = 'mobile-accordion-list';

            var rows = Array.from(tbody.querySelectorAll('tr'));
            rows.forEach(function(tr) {
                var tds = tr.querySelectorAll('td');
                if (tds.length === 0) return;

                // Find No. Urut (or first column)
                var urutText = tds[0] ? tds[0].textContent.trim() : '';
                
                var judulText = '';
                var categoryBadgeHTML = '';
                var detailsHTML = '';
                var actionsHTML = '';

                tds.forEach(function(td, index) {
                    var label = headers[index] || '';
                    
                    if (label.toLowerCase().indexOf('no') !== -1 || label === '#') {
                        urutText = td.innerHTML;
                    } else if (label.toLowerCase().indexOf('judul') !== -1 || label.toLowerCase().indexOf('nama') !== -1 || label.toLowerCase().indexOf('title') !== -1 || label.toLowerCase().indexOf('username') !== -1 || label.toLowerCase().indexOf('tokoh') !== -1) {
                        judulText = td.innerHTML;
                    } else if (label.toLowerCase().indexOf('kategori') !== -1 || label.toLowerCase().indexOf('role') !== -1 || label.toLowerCase().indexOf('jabatan') !== -1 || label.toLowerCase().indexOf('keterangan') !== -1) {
                        categoryBadgeHTML = td.innerHTML;
                    } else if (label.toLowerCase().indexOf('aksi') !== -1 || label.toLowerCase().indexOf('pilihan') !== -1 || label.toLowerCase().indexOf('action') !== -1) {
                        actionsHTML = td.innerHTML;
                    } else {
                        // Regular detail column
                        detailsHTML += '<p class="mb-2"><strong>' + label + ':</strong> ' + td.innerHTML + '</p>';
                    }
                });

                // Fallbacks if not found
                if (!judulText) {
                    if (tds[1]) judulText = tds[1].innerHTML;
                    else if (tds[0]) judulText = tds[0].innerHTML;
                }
                
                // Only use tds[2] for badge if it's not already used as judulText
                if (!categoryBadgeHTML && tds[2] && tds[2].innerHTML !== judulText && tds[2] !== tds[tds.length-1]) {
                    categoryBadgeHTML = tds[2].innerHTML;
                }

                // Create details element
                var detailsEl = document.createElement('details');
                detailsEl.className = 'mobile-accordion-item border-0 mb-3 shadow-sm';
                detailsEl.style.display = 'block';

                var summaryEl = document.createElement('summary');
                summaryEl.className = 'd-flex align-items-center justify-content-between p-3';
                summaryEl.style.outline = 'none';

                var summaryLeft = document.createElement('div');
                summaryLeft.className = 'd-flex align-items-center gap-2 flex-wrap';
                
                if (urutText) {
                    var urutSpan = document.createElement('span');
                    urutSpan.className = 'badge bg-secondary-subtle text-secondary me-2';
                    urutSpan.innerHTML = urutText;
                    summaryLeft.appendChild(urutSpan);
                }

                var judulSpan = document.createElement('span');
                judulSpan.className = 'fw-bold text-dark';
                judulSpan.innerHTML = judulText;
                summaryLeft.appendChild(judulSpan);

                if (categoryBadgeHTML) {
                    var badgeSpan = document.createElement('span');
                    badgeSpan.className = 'ms-2';
                    badgeSpan.innerHTML = categoryBadgeHTML;
                    summaryLeft.appendChild(badgeSpan);
                }

                summaryEl.appendChild(summaryLeft);

                var chevron = document.createElement('i');
                chevron.className = 'fa-solid fa-chevron-down text-muted ms-auto transition-transform';
                chevron.style.transition = 'transform 0.2s ease';
                summaryEl.appendChild(chevron);

                detailsEl.appendChild(summaryEl);

                var contentDiv = document.createElement('div');
                contentDiv.className = 'accordion-detail p-3 pt-0 border-top mt-2';
                contentDiv.style.borderTop = '1px solid var(--border-color)';
                contentDiv.style.paddingTop = '15px';
                contentDiv.innerHTML = detailsHTML;

                if (actionsHTML) {
                    var actionsDiv = document.createElement('div');
                    actionsDiv.className = 'actions d-flex gap-2 mt-3 justify-content-center';
                    actionsDiv.innerHTML = actionsHTML;
                    // Force full-width edit/delete buttons
                    actionsDiv.querySelectorAll('.btn, a').forEach(function(btn) {
                        btn.style.flexGrow = '1';
                        btn.style.textAlign = 'center';
                        btn.style.display = 'inline-flex';
                        btn.style.alignItems = 'center';
                        btn.style.justifyContent = 'center';
                    });
                    contentDiv.appendChild(actionsDiv);
                }

                detailsEl.appendChild(contentDiv);

                // Listen to toggle events to rotate chevron
                detailsEl.addEventListener('toggle', function() {
                    if (detailsEl.open) {
                        chevron.style.transform = 'rotate(180deg)';
                    } else {
                        chevron.style.transform = 'rotate(0deg)';
                    }
                });

                container.appendChild(detailsEl);
            });

            // Replace table (or table-responsive wrapper) with the accordion container
            var targetToReplace = table;
            if (table.parentNode && table.parentNode.classList.contains('table-responsive')) {
                targetToReplace = table.parentNode;
            }
            if (targetToReplace.parentNode) {
                targetToReplace.parentNode.replaceChild(container, targetToReplace);
            }
        });
    }
});
</script>

<?php if (is_kepsek_role()): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Hide all "+ Tambah" or similar create links/buttons
    document.querySelectorAll('a[href*="add.php"], a[href*="upload.php"], button.btn-add-new, a.btn-add-new, .btn-brand').forEach(function(el) {
        var txt = el.textContent.toLowerCase();
        if (txt.indexOf('tambah') !== -1 || txt.indexOf('add') !== -1 || el.classList.contains('btn-add-new') || el.getAttribute('href')?.indexOf('add.php') !== -1) {
            el.style.display = 'none';
        }
    });

    // 2. Determine module name based on current URL path
    var path = window.location.pathname.replace(/\\/g, '/');
    var module = '';
    if (path.indexOf('/news/') !== -1) module = 'berita';
    else if (path.indexOf('/facilities/') !== -1) module = 'fasilitas';
    else if (path.indexOf('/facility-categories/') !== -1) module = 'kategori fasilitas';
    else if (path.indexOf('/ekskul/') !== -1) module = 'ekskul';
    else if (path.indexOf('/ekskul-categories/') !== -1) module = 'kategori ekskul';
    else if (path.indexOf('/prestasi/') !== -1) module = 'prestasi';
    else if (path.indexOf('/prestasi-categories/') !== -1) module = 'kategori prestasi';
    else if (path.indexOf('/gallery/') !== -1) module = 'galeri';
    else if (path.indexOf('/gallery-categories/') !== -1) module = 'kategori galeri';
    else if (path.indexOf('/testimonials/') !== -1) module = 'testimonial';
    else if (path.indexOf('/team/') !== -1) module = 'team';
    else if (path.indexOf('/classes/') !== -1) module = 'kelas';
    else if (path.indexOf('/struktur/') !== -1) module = 'struktur';

    // Helper to extract item ID from edit/delete urls or hidden inputs
    function extractIdFromCell(cell) {
        var input = cell.querySelector('input[name="id"]');
        if (input && input.value) return input.value;
        var links = cell.querySelectorAll('a, form, button');
        for (var i = 0; i < links.length; i++) {
            var href = links[i].getAttribute('href') || links[i].getAttribute('action') || '';
            var match = href.match(/[?&]id=(\d+)/);
            if (match) return match[1];
        }
        return '';
    }

    // Helper to extract item Title from the table row
    function extractTitleFromRow(row) {
        var bold = row.querySelector('.fw-bold, td:nth-child(3)');
        if (bold) return bold.textContent.trim();
        var tds = row.querySelectorAll('td');
        if (tds.length > 2) return tds[2].textContent.trim();
        if (tds.length > 1) return tds[1].textContent.trim();
        return '';
    }

    // 3. For Desktop: Replace edit/delete buttons in tables
    document.querySelectorAll('table').forEach(function(table) {
        // Find Action/Aksi column header index
        var actionIndex = -1;
        table.querySelectorAll('thead th').forEach(function(th, idx) {
            var txt = th.textContent.toLowerCase();
            if (txt.indexOf('aksi') !== -1 || txt.indexOf('pilihan') !== -1 || txt.indexOf('action') !== -1) {
                actionIndex = idx;
            }
        });

        if (actionIndex !== -1) {
            table.querySelectorAll('tbody tr').forEach(function(tr) {
                var tds = tr.querySelectorAll('td');
                if (tds[actionIndex]) {
                    var id = extractIdFromCell(tds[actionIndex]);
                    var title = extractTitleFromRow(tr);
                    if (id) {
                        tds[actionIndex].innerHTML = `
                            <button class="btn btn-sm btn-warning text-white btn-request-revision px-2.5 py-1.5" 
                                    data-module="${module}" 
                                    data-id="${id}" 
                                    data-title="${title.replace(/"/g, '&quot;')}" 
                                    title="Ajukan Revisi">
                                <i class="fa-solid fa-comment-dots"></i> <span class="d-md-inline d-none">Ajukan Revisi</span>
                            </button>
                        `;
                    } else {
                        tds[actionIndex].innerHTML = '-';
                    }
                }
            });
        }
    });

    // 4. For Mobile: Wait for the accordion conversion, then replace action buttons
    setTimeout(function() {
        document.querySelectorAll('.mobile-accordion-item').forEach(function(card) {
            var summarySpan = card.querySelector('summary span.fw-bold');
            var title = summarySpan ? summarySpan.textContent.trim() : '';
            var actionsDiv = card.querySelector('.actions');
            if (actionsDiv) {
                var id = extractIdFromCell(actionsDiv);
                if (id) {
                    actionsDiv.innerHTML = `
                        <button class="btn btn-sm btn-warning text-white btn-request-revision w-100 py-2" 
                                data-module="${module}" 
                                data-id="${id}" 
                                data-title="${title.replace(/"/g, '&quot;')}" 
                                title="Ajukan Revisi">
                            <i class="fa-solid fa-comment-dots me-1"></i> Ajukan Catatan Revisi
                        </button>
                    `;
                } else {
                    actionsDiv.innerHTML = '';
                }
            }
        });

        // Re-bind click event to any newly added dynamic buttons
        bindClickEvents();
    }, 150);

    // 5. Inject Revision Modal dynamically
    var modalDiv = document.createElement('div');
    modalDiv.innerHTML = `
    <div class="modal fade" id="revisionModal" tabindex="-1" aria-labelledby="revisionModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: var(--card-bg); color: var(--text-color); border: 1px solid var(--border-color);">
          <div class="modal-header" style="border-bottom: 1px solid var(--border-color);">
            <h5 class="modal-title fw-bold" id="revisionModalLabel"><i class="fa-solid fa-comment-dots text-warning me-2"></i> Ajukan Catatan Revisi</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="modal" aria-label="Close" style="filter: var(--close-btn-filter);"></button>
          </div>
          <form id="revisionForm" action="<?= $base_admin ?>revisions/submit.php" method="POST">
            <div class="modal-body">
              <input type="hidden" name="module_name" id="revModule">
              <input type="hidden" name="item_id" id="revItemId">
              <input type="hidden" name="item_title" id="revItemTitle">
              <div class="mb-3">
                <label class="form-label fw-semibold small text-muted">Item / Baris Data:</label>
                <div id="revItemNameDisplay" class="fw-bold text-dark p-2.5 rounded-3 bg-light bg-opacity-10" style="color: var(--text-color) !important;"></div>
              </div>
              <div class="mb-3">
                <label for="revCatatan" class="form-label fw-semibold small text-muted">Catatan Perbaikan/Revisi:</label>
                <textarea class="form-control" name="catatan" id="revCatatan" rows="4" placeholder="Tulis instruksi revisi secara spesifik..." required style="background: var(--hover-bg); color: var(--text-color); border-color: var(--border-color);"></textarea>
              </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid var(--border-color);">
              <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
              <button type="submit" class="btn btn-brand btn-sm text-white px-3" style="background: #1acc8d; border-color: #1acc8d;">Kirim Catatan</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    `;
    document.body.appendChild(modalDiv);

    // Initial click event binding
    bindClickEvents();

    function bindClickEvents() {
        var myModal = null;
        document.querySelectorAll('.btn-request-revision').forEach(function(btn) {
            btn.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var mod = btn.getAttribute('data-module');
                var id = btn.getAttribute('data-id');
                var title = btn.getAttribute('data-title');
                
                document.getElementById('revModule').value = mod;
                document.getElementById('revItemId').value = id;
                document.getElementById('revItemTitle').value = title;
                document.getElementById('revItemNameDisplay').textContent = title;
                document.getElementById('revCatatan').value = '';
                
                if (!myModal) {
                    myModal = new bootstrap.Modal(document.getElementById('revisionModal'));
                }
                myModal.show();
            };
        });
    }

    <?php if ($is_admin): ?>
    // 5. Polling for real-time notification counts every 30 seconds
    setInterval(async () => {
        try {
            const res = await fetch('<?= $base_admin ?>api/notification-count.php');
            if (res.ok) {
                const data = await res.json();
                
                const updateBadge = (id, count) => {
                    const badge = document.getElementById(id);
                    if (badge) {
                        badge.textContent = count;
                        if (count > 0) {
                            badge.classList.remove('d-none');
                        } else {
                            badge.classList.add('d-none');
                        }
                    }
                };
                
                updateBadge('badge-messages', data.unread_messages);
                updateBadge('badge-news', data.pending_news);
                updateBadge('badge-revisi', data.pending_revisi);
            }
        } catch (e) {
            // fail silently
        }
    }, 30000);
    <?php endif; ?>
});
</script>
<?php endif; ?>
