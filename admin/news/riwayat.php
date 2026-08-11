<?php
// admin/news/riwayat.php
require_once '../includes/auth.php';
// Boleh diakses oleh superadmin, admin, maupun editor
require_once '../config/db.php';

$current_user_id = (int) ($_SESSION['admin_id'] ?? 0);
$role = $_SESSION['admin_role'] ?? 'editor';
$is_editor = ($role === 'editor');

try {
    if ($is_editor) {
        // Editor hanya melihat riwayat berita milik mereka sendiri
        $stmt = $pdo->prepare("SELECT * FROM news WHERE created_by = ? ORDER BY created_at DESC");
        $stmt->execute([$current_user_id]);
        $news_list = $stmt->fetchAll();
    } else {
        // Admin & Superadmin melihat daftar pengajuan seluruh editor untuk monitoring
        $stmt = $pdo->query("SELECT n.*, a.username as creator_name FROM news n LEFT JOIN admins a ON n.created_by = a.id ORDER BY n.created_at DESC");
        $news_list = $stmt->fetchAll();
    }
} catch (PDOException $e) {
    die("Error fetching news history: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengajuan Berita - SDIT Al Fatah</title>
    <link href="../../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="../../assets/css/admin.css" rel="stylesheet">
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/sidebar.php'; ?>
        <main class="main-content">
            <div class="main-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Riwayat Pengajuan Berita</h4>
                    <p class="text-muted small mb-0">
                        <?php if ($is_editor): ?>
                            Daftar status pengajuan berita Anda untuk dipublikasikan
                        <?php else: ?>
                            Monitoring pengajuan berita seluruh staf dan editor
                        <?php endif; ?>
                    </p>
                </div>
                <?php if ($is_editor): ?>
                    <a href="add.php" class="btn btn-brand py-2 px-3"><i class="fa-solid fa-plus me-1"></i> Ajukan Berita Baru</a>
                <?php endif; ?>
            </div>

            <div class="card border-0 rounded-4 shadow-sm overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width:50px;">No</th>
                                    <th>Judul Berita</th>
                                    <?php if (!$is_editor): ?>
                                        <th>Diajukan Oleh</th>
                                    <?php endif; ?>
                                    <th>Tanggal Diajukan</th>
                                    <th style="width:160px;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($news_list)): ?>
                                    <tr>
                                        <td colspan="<?= $is_editor ? 4 : 5 ?>" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-clock-rotate-left fa-3x mb-3 text-secondary opacity-25"></i>
                                            <p class="mb-0">Belum ada riwayat pengajuan berita.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($news_list as $news): ?>
                                        <tr>
                                            <td class="ps-4 text-secondary"><?= $no++ ?></td>
                                            <td class="fw-bold text-dark"><?= htmlspecialchars($news['title']) ?></td>
                                            <?php if (!$is_editor): ?>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        <?= htmlspecialchars($news['creator_name'] ?? 'Superadmin') ?>
                                                    </span>
                                                </td>
                                            <?php endif; ?>
                                            <td class="text-secondary small">
                                                <?= date('d M Y, H:i', strtotime($news['created_at'])) ?>
                                            </td>
                                            <td>
                                                <?php if ($news['status'] === 'published'): ?>
                                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1.5"><i class="fa-solid fa-circle-check me-1 small"></i> Terbit</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1.5"><i class="fa-solid fa-clock me-1 small"></i> Pending Persetujuan</span>
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
        </main>
    </div>
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
