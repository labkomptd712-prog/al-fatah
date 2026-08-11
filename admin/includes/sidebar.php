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

$unread_messages = 0;
if ($is_admin) {
    try {
        $unread_messages = (int) $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0")->fetchColumn();
    } catch (PDOException $e) {
        $unread_messages = 0;
    }
}
?>
<!-- Inject favicon to document head dynamically -->
<script>
(function() {
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
    .sidebar {
        width: 260px !important;
        left: -260px !important;
        position: fixed !important;
        top: 0 !important;
        z-index: 1050 !important;
        transition: left 0.3s ease !important;
        display: block !important;
        background: #111 !important;
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
    
    .sidebar.show {
        left: 0 !important;
    }
    
    .main-content {
        margin-left: 0 !important;
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
    background-color: #1a1a1a;
    border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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

        <!-- Kategori 1: Kelola Konten -->
        <li class="nav-item mt-2">
            <a class="nav-link text-white d-flex align-items-center justify-content-between" data-bs-toggle="collapse" href="#kontenMenu" role="button" aria-expanded="<?= (strpos($script_name, '/news/') !== false || strpos($script_name, '/gallery') !== false || strpos($script_name, '/gallery-categories') !== false || strpos($script_name, '/facilities') !== false || strpos($script_name, '/facility-categories') !== false) ? 'true' : 'false' ?>">
                <i class="fa-solid fa-file-signature me-2"></i> <span>Kelola Konten</span>
                <i class="fa-solid fa-chevron-down ms-auto" style="font-size: 10px; transition: transform 0.2s;"></i>
            </a>
            <div class="collapse <?= (strpos($script_name, '/news/') !== false || strpos($script_name, '/gallery') !== false || strpos($script_name, '/gallery-categories') !== false || strpos($script_name, '/facilities') !== false || strpos($script_name, '/facility-categories') !== false) ? 'show' : '' ?>" id="kontenMenu">
                <ul class="nav flex-column ms-3 mt-1 small">
                    <?php if ($is_admin): ?>
                    <li>
                        <a href="<?= $base_admin ?>news/list.php" class="nav-link text-white-50 <?= (strpos($script_name, '/news/') !== false && strpos($script_name, 'riwayat') === false) ? 'active text-white bg-success' : '' ?>">
                            <i class="fa-solid fa-newspaper me-2"></i> <span>Berita Terbaru</span>
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

        <?php if ($is_admin): ?>
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
});
</script>
