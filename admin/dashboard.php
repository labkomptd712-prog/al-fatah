<?php
// admin/dashboard.php
require_once 'includes/auth.php';
require_once 'config/db.php';

$is_admin = is_admin_role();
$is_editor = is_editor_role();
$is_superadmin = is_superadmin_role();

$unread_messages = 0;
$total_news = 0;
$total_gallery = 0;
$pending_news = [];
$latest_messages = [];

$visits_today = 0;
$visits_month = 0;
$visits_total = 0;

try {
    // Count news
    $stmt = $pdo->query("SELECT COUNT(*) FROM news");
    $total_news = $stmt->fetchColumn();

    // Count gallery photos
    $stmt = $pdo->query("SELECT COUNT(*) FROM gallery");
    $total_gallery = $stmt->fetchColumn();

    // Query visitor statistics
    $stmtToday = $pdo->query("SELECT COUNT(*) FROM page_visits WHERE DATE(visited_at) = CURDATE()");
    $visits_today = (int)$stmtToday->fetchColumn();

    $stmtMonth = $pdo->query("SELECT COUNT(*) FROM page_visits WHERE YEAR(visited_at) = YEAR(CURDATE()) AND MONTH(visited_at) = MONTH(CURDATE())");
    $visits_month = (int)$stmtMonth->fetchColumn();

    $stmtTotal = $pdo->query("SELECT COUNT(*) FROM page_visits");
    $visits_total = (int)$stmtTotal->fetchColumn();

    if ($is_admin) {
        // Count unread messages
        $stmt = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE is_read = 0");
        $unread_messages = (int) $stmt->fetchColumn();

        // Fetch pending news for admin monitoring
        $stmtPending = $pdo->query("SELECT n.*, a.username as editor_name FROM news n LEFT JOIN admins a ON n.created_by = a.id WHERE n.status = 'pending' ORDER BY n.created_at DESC");
        $pending_news = $stmtPending->fetchAll();

        // Fetch pending revision requests
        $stmtRevisions = $pdo->query("SELECT r.*, a.username as requested_by_name FROM revision_requests r JOIN admins a ON r.requested_by = a.id WHERE r.status = 'pending' ORDER BY r.created_at DESC");
        $pending_revisions = $stmtRevisions->fetchAll();

        // Fetch latest messages for admin inbox preview
        $stmtMsg = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
        $latest_messages = $stmtMsg->fetchAll();
    } else {
        // Fetch pending news for editor's own submissions
        $stmtPending = $pdo->prepare("SELECT * FROM news WHERE status = 'pending' AND created_by = ? ORDER BY created_at DESC");
        $stmtPending->execute([$_SESSION['admin_id']]);
        $pending_news = $stmtPending->fetchAll();
    }
} catch (PDOException $e) {
    die("Error fetching dashboard statistics: " . $e->getMessage());
}

$message = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';
$is_admin = is_admin_role();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SDIT Al Fatah</title>
    <!-- Bootstrap 5 CSS -->
    <link href="../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom Admin CSS -->
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>
<body>

    <div class="admin-container">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="main-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Dashboard</h4>
                    <p class="text-muted small mb-0">Selamat datang kembali, <strong><?= htmlspecialchars($_SESSION['admin_username']) ?></strong>!</p>
                </div>
                <div class="text-end">
                    <?php if ($is_admin): ?>
                        <a href="messages/list.php" class="btn btn-brand py-2 px-3 position-relative text-white fw-semibold" style="background-color: #1acc8d; border-color: #1acc8d; border-radius: 10px;">
                            <i class="fa-solid fa-envelope me-1"></i> Pesan Masuk
                            <?php if ($unread_messages > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-2 border-white" style="font-size: 10px;">
                                    <?= $unread_messages ?>
                                </span>
                            <?php endif; ?>
                        </a>
                    <?php else: ?>
                        <a href="news/riwayat.php" class="btn btn-brand py-2 px-3 text-white fw-semibold" style="background-color: #1acc8d; border-color: #1acc8d; border-radius: 10px;">
                            <i class="fa-solid fa-clock-rotate-left me-1"></i> Riwayat Pengajuan
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            <?php if ($message === 'news_submit_success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> Berita berhasil diajukan. Menunggu persetujuan admin.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($message === 'gallery_add_success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> Foto galeri berhasil diunggah!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($message === 'team_add_success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> Anggota team berhasil ditambahkan!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Stats Widgets -->
            <div class="row g-4 mb-5">
                <!-- News Widget -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-stat bg-white h-100">
                        <div class="card-body d-flex align-items-center p-4">
                            <div class="card-stat-icon bg-success bg-opacity-10 text-success me-4">
                                <i class="fa-solid fa-newspaper"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1 text-uppercase fw-semibold" style="font-size: 12px; letter-spacing: 0.5px;">Berita Terbaru</h6>
                                <h3 class="fw-bold text-dark mb-0"><?= $total_news ?></h3>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                            <?php if ($is_admin): ?>
                            <a href="news/list.php" class="text-success text-decoration-none small fw-semibold">Kelola Berita <i class="fa-solid fa-arrow-right ms-1"></i></a>
                            <?php else: ?>
                            <a href="news/add.php" class="text-success text-decoration-none small fw-semibold">Tambah Berita <i class="fa-solid fa-arrow-right ms-1"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Gallery Widget -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-stat bg-white h-100">
                        <div class="card-body d-flex align-items-center p-4">
                            <div class="card-stat-icon bg-primary bg-opacity-10 text-primary me-4">
                                <i class="fa-solid fa-images"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1 text-uppercase fw-semibold" style="font-size: 12px; letter-spacing: 0.5px;">Galeri Foto</h6>
                                <h3 class="fw-bold text-dark mb-0"><?= $total_gallery ?></h3>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                            <?php if ($is_admin): ?>
                            <a href="gallery/list.php" class="text-primary text-decoration-none small fw-semibold">Kelola Galeri <i class="fa-solid fa-arrow-right ms-1"></i></a>
                            <?php else: ?>
                            <a href="gallery/add.php" class="text-primary text-decoration-none small fw-semibold">Tambah Galeri <i class="fa-solid fa-arrow-right ms-1"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <?php if ($is_admin): ?>
                <!-- Settings Widget -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-stat bg-white h-100">
                        <div class="card-body d-flex align-items-center p-4">
                            <div class="card-stat-icon bg-warning bg-opacity-10 text-warning me-4">
                                <i class="fa-solid fa-sliders"></i>
                            </div>
                            <div>
                                <h6 class="text-muted mb-1 text-uppercase fw-semibold" style="font-size: 12px; letter-spacing: 0.5px;">Pengaturan Profil</h6>
                                <h3 class="fw-bold text-dark mb-0">Aktif</h3>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                            <a href="settings.php" class="text-warning text-decoration-none small fw-semibold">Kelola Pengaturan <i class="fa-solid fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Visitor Stats Widget -->
                <div class="col-md-6 col-lg-4">
                    <div class="card card-stat bg-white h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center mb-3">
                                <div class="card-stat-icon bg-info bg-opacity-10 text-info me-3" style="font-size: 24px; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 10px;">
                                    <i class="fa-solid fa-chart-line"></i>
                                </div>
                                <div>
                                    <h6 class="text-muted mb-0 text-uppercase fw-semibold" style="font-size: 12px; letter-spacing: 0.5px;">Statistik Pengunjung</h6>
                                </div>
                            </div>
                            <div class="row text-center mt-2 g-2">
                                <div class="col-4 border-end">
                                    <h5 class="fw-bold text-dark mb-0"><?= $visits_today ?></h5>
                                    <small class="text-muted" style="font-size: 10px;">Hari Ini</small>
                                </div>
                                <div class="col-4 border-end">
                                    <h5 class="fw-bold text-dark mb-0"><?= $visits_month ?></h5>
                                    <small class="text-muted" style="font-size: 10px;">Bulan Ini</small>
                                </div>
                                <div class="col-4">
                                    <h5 class="fw-bold text-dark mb-0"><?= $visits_total ?></h5>
                                    <small class="text-muted" style="font-size: 10px;">Total</small>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-light border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                            <span class="text-muted small">Kunjungan Terlacak</span>
                            <i class="fa-solid fa-users text-info"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pending News and Messages Section -->
            <div class="row g-4 mb-4">
                <!-- Column 1: Pending News -->
                <div class="col-lg-<?= $is_admin ? '6' : '12' ?>">
                    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white h-100">
                        <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-clock text-warning me-2"></i> Pengajuan Berita Pending</h5>
                        <hr class="mt-0 mb-3 border-light">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Judul Berita</th>
                                        <?php if ($is_admin): ?>
                                            <th>Pengaju</th>
                                        <?php endif; ?>
                                        <th>Tanggal</th>
                                        <th class="text-center" style="width: 120px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($pending_news)): ?>
                                        <tr>
                                            <td colspan="<?= $is_admin ? 4 : 3 ?>" class="text-center py-4 text-muted small">
                                                <i class="fa-regular fa-folder-open fa-2x mb-2 text-secondary opacity-25"></i>
                                                <p class="mb-0">Tidak ada pengajuan berita pending.</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($pending_news as $news): ?>
                                            <tr>
                                                <td class="fw-bold text-dark small text-truncate" style="max-width: 180px;" title="<?= htmlspecialchars($news['title']) ?>">
                                                    <?= htmlspecialchars($news['title']) ?>
                                                </td>
                                                <?php if ($is_admin): ?>
                                                    <td>
                                                        <span class="badge bg-secondary small"><?= htmlspecialchars($news['editor_name'] ?? 'Superadmin') ?></span>
                                                    </td>
                                                <?php endif; ?>
                                                <td class="text-secondary small" style="font-size: 11px;">
                                                    <?= date('d/m/Y', strtotime($news['created_at'])) ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($is_admin): ?>
                                                        <form action="news/approve.php" method="POST" class="d-inline" onsubmit="return confirm('Setujui dan terbitkan berita ini?')">
                                                            <input type="hidden" name="id" value="<?= (int) $news['id'] ?>">
                                                            <button type="submit" class="btn btn-sm btn-success py-1 px-2.5 rounded-2 text-white" style="background-color: #1acc8d; border-color: #1acc8d; font-size: 11px;"><i class="fa-solid fa-check"></i> Setujui</button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2.5 py-1" style="font-size: 10px;"><i class="fa-solid fa-clock me-1"></i> Pending</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Column 2: Latest Messages (Only for Admin) -->
                <?php if ($is_admin): ?>
                <div class="col-lg-6">
                    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white h-100">
                        <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-envelope text-success me-2" style="color: #1acc8d !important;"></i> Pesan Masuk Terbaru</h5>
                        <hr class="mt-0 mb-3 border-light">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Pengirim</th>
                                        <th>Subjek</th>
                                        <th>Tanggal</th>
                                        <th class="text-center" style="width: 100px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($latest_messages)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted small">
                                                <i class="fa-solid fa-envelope-open fa-2x mb-2 text-secondary opacity-25"></i>
                                                <p class="mb-0">Tidak ada pesan masuk.</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($latest_messages as $msg): 
                                            $is_unread = ((int)$msg['is_read'] === 0);
                                        ?>
                                            <tr class="<?= $is_unread ? 'fw-bold text-dark' : 'text-secondary' ?>" style="<?= $is_unread ? 'background-color: rgba(26, 204, 141, 0.02);' : '' ?>">
                                                <td class="small"><?= htmlspecialchars($msg['name']) ?></td>
                                                <td class="small text-truncate" style="max-width: 120px;"><?= htmlspecialchars($msg['subject']) ?></td>
                                                <td class="small" style="font-size: 11px;"><?= date('d/m/y', strtotime($msg['created_at'])) ?></td>
                                                <td class="text-center">
                                                    <a href="messages/list.php?view_id=<?= (int) $msg['id'] ?>" class="btn btn-sm btn-outline-primary py-1 px-2.5 rounded-2" style="font-size: 11px;"><i class="fa-solid fa-envelope-open-text"></i> Baca</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($is_admin): ?>
            <!-- Revision Requests Queue -->
            <div class="row g-4 mb-4">
                <div class="col-12">
                    <div class="card border-0 rounded-4 shadow-sm p-4 bg-white">
                        <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-comment-dots text-danger me-2" style="color: #ef4444 !important;"></i> Revisi Diajukan Kepsek</h5>
                        <hr class="mt-0 mb-3 border-light">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Judul Item</th>
                                        <th>Modul</th>
                                        <th>Catatan</th>
                                        <th>Pengaju</th>
                                        <th>Tanggal</th>
                                        <th class="text-center" style="width: 200px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($pending_revisions)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted small">
                                                <i class="fa-regular fa-comment-dots fa-2x mb-2 text-secondary opacity-25"></i>
                                                <p class="mb-0">Tidak ada pengajuan revisi pending dari Kepala Sekolah.</p>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($pending_revisions as $rev): 
                                            // Map module name to edit page
                                            $edit_link = '';
                                            if ($rev['module_name'] === 'berita') $edit_link = 'news/edit.php?id=' . $rev['item_id'];
                                            elseif ($rev['module_name'] === 'fasilitas') $edit_link = 'facilities/edit.php?id=' . $rev['item_id'];
                                            elseif ($rev['module_name'] === 'ekskul') $edit_link = 'ekskul/edit.php?id=' . $rev['item_id'];
                                            elseif ($rev['module_name'] === 'prestasi') $edit_link = 'prestasi/edit.php?id=' . $rev['item_id'];
                                            elseif ($rev['module_name'] === 'testimonial') $edit_link = 'testimonials/edit.php?id=' . $rev['item_id'];
                                            elseif ($rev['module_name'] === 'team') $edit_link = 'team/edit.php?id=' . $rev['item_id'];
                                            elseif ($rev['module_name'] === 'kelas') $edit_link = 'classes/edit.php?id=' . $rev['item_id'];
                                            elseif ($rev['module_name'] === 'struktur') $edit_link = 'struktur/edit.php?id=' . $rev['item_id'];
                                            elseif ($rev['module_name'] === 'galeri') $edit_link = 'gallery/list.php';
                                        ?>
                                            <tr>
                                                <td class="fw-bold text-dark small"><?= htmlspecialchars($rev['item_title']) ?></td>
                                                <td><span class="badge bg-info bg-opacity-10 text-info px-2 py-1 small"><?= htmlspecialchars(ucfirst($rev['module_name'])) ?></span></td>
                                                <td class="small text-muted" style="max-width: 250px;"><?= htmlspecialchars($rev['catatan']) ?></td>
                                                <td><span class="badge bg-secondary small"><?= htmlspecialchars($rev['requested_by_name']) ?></span></td>
                                                <td class="text-secondary small" style="font-size: 11px;"><?= date('d/m/Y', strtotime($rev['created_at'])) ?></td>
                                                <td class="text-center">
                                                    <div class="d-inline-flex gap-2">
                                                        <?php if ($edit_link): ?>
                                                            <a href="<?= $edit_link ?>" class="btn btn-sm btn-outline-primary py-1 px-2.5 rounded-2" style="font-size: 11px;"><i class="fa-solid fa-pen-to-square"></i> Lihat/Edit</a>
                                                        <?php endif; ?>
                                                        <form action="revisions/resolve.php" method="POST" class="d-inline" onsubmit="return confirm('Tandai revisi ini sebagai selesai?')">
                                                            <input type="hidden" name="id" value="<?= (int) $rev['id'] ?>">
                                                            <input type="hidden" name="redirect" value="dashboard">
                                                            <button type="submit" class="btn btn-sm btn-success py-1 px-2.5 rounded-2 text-white" style="background-color: #1acc8d; border-color: #1acc8d; font-size: 11px;"><i class="fa-solid fa-check"></i> Tandai Selesai</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Welcome Board / Info Card -->
            <div class="card border-0 rounded-4 shadow-sm p-4 text-white" style="background: linear-gradient(135deg, #1acc8d, #15b37b) !important; border: none !important;">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="fw-bold mb-2">Panel Administrasi <span class="brand-font">SDIT Al Fatah</span></h4>
                        <p class="mb-0 text-white-50">Melalui panel ini, Anda dapat mengelola konten berita sekolah, mengunggah dokumentasi foto kegiatan (galeri), serta memperbarui data umum sekolah seperti visi, misi, kontak WhatsApp, dan tautan sosial media.</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <img src="../assets/img/logo afix.png" alt="Logo" class="img-fluid" style="max-height: 100px; filter: drop-shadow(0px 4px 10px rgba(0,0,0,0.15));">
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <?php if ($unread_messages > 0): ?>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() {
            if (typeof window.showSuccessToast === 'function') {
                window.showSuccessToast("Anda memiliki <?= $unread_messages ?> pesan masuk baru yang belum dibaca!");
            }
        }, 500); // Delay slightly for a smoother transition
    });
    </script>
    <?php endif; ?>
</body>
</html>
