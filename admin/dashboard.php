<?php
// admin/dashboard.php
require_once 'includes/auth.php';
require_once 'config/db.php';

try {
    // Count news
    $stmt = $pdo->query("SELECT COUNT(*) FROM news");
    $total_news = $stmt->fetchColumn();

    // Count gallery photos
    $stmt = $pdo->query("SELECT COUNT(*) FROM gallery");
    $total_gallery = $stmt->fetchColumn();
} catch (PDOException $e) {
    die("Error fetching dashboard statistics: " . $e->getMessage());
}
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
                    <span class="badge bg-success py-2 px-3"><i class="fa-solid fa-circle-check me-1"></i> Sistem Aktif</span>
                </div>
            </div>

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
                            <a href="news/list.php" class="text-success text-decoration-none small fw-semibold">Kelola Berita <i class="fa-solid fa-arrow-right ms-1"></i></a>
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
                            <a href="gallery/list.php" class="text-primary text-decoration-none small fw-semibold">Kelola Galeri <i class="fa-solid fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>

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
            </div>

            <!-- Welcome Board / Info Card -->
            <div class="card border-0 rounded-4 shadow-sm p-4 text-white" style="background: linear-gradient(135deg, #1acc8d, #15b37b);">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="fw-bold mb-2">Panel Administrasi SDIT Al Fatah</h4>
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
</body>
</html>
