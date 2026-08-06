<?php
// admin/news/list.php
require_once '../includes/auth.php';
require_admin_role();
require_once '../config/db.php';

try {
    // Fetch all news ordered by latest
    $stmt = $pdo->query("SELECT * FROM news ORDER BY created_at DESC");
    $news_list = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching news: " . $e->getMessage());
}

$message = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Berita - SDIT Al Fatah</title>
    <!-- Bootstrap 5 CSS -->
    <link href="../../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom Admin CSS -->
    <link href="../../assets/css/admin.css" rel="stylesheet">
</head>
<body>

    <div class="admin-container">
        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="main-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Berita Terbaru</h4>
                    <p class="text-muted small mb-0">Kelola artikel, berita, dan pengumuman sekolah</p>
                </div>
                <div>
                    <a href="add.php" class="btn btn-brand py-2 px-3"><i class="fa-solid fa-plus me-1"></i> Tambah Berita</a>
                </div>
            </div>

            <!-- Notifications -->
            <?php if ($message === 'add_success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> Berita berhasil ditambahkan!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($message === 'edit_success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> Berita berhasil diperbarui!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($message === 'delete_success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> Berita berhasil dihapus!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Table Card -->
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="min-width: 800px;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width: 60px;">No</th>
                                    <th style="width: 100px;">Gambar</th>
                                    <th>Judul Berita</th>
                                    <th>Slug</th>
                                    <th style="width: 150px;">Status</th>
                                    <th style="width: 180px;">Tanggal Dibuat</th>
                                    <th class="pe-4 text-center" style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($news_list)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fa-regular fa-newspaper fa-3x mb-3 text-secondary"></i>
                                                <p class="mb-0">Belum ada berita yang ditambahkan.</p>
                                                <a href="add.php" class="btn btn-sm btn-brand mt-3">Tambah Sekarang</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($news_list as $news): ?>
                                        <tr>
                                            <td class="ps-4 fw-semibold text-secondary"><?= $no++ ?></td>
                                            <td>
                                                <?php if (!empty($news['image']) && file_exists('../uploads/' . $news['image'])): ?>
                                                    <img src="../uploads/<?= htmlspecialchars($news['image']) ?>" alt="Preview" class="img-thumbnail" style="width: 70px; height: 50px; object-fit: cover; border-radius: 6px;">
                                                <?php else: ?>
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width: 70px; height: 50px; font-size: 11px;">No Img</div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark mb-1"><?= htmlspecialchars($news['title']) ?></div>
                                                <div class="text-muted small text-truncate" style="max-width: 250px;">
                                                    <?= strip_tags(htmlspecialchars_decode($news['content'] ?? '')) ?>
                                                </div>
                                            </td>
                                            <td><code class="small text-secondary"><?= htmlspecialchars($news['slug']) ?></code></td>
                                            <td>
                                                <?php if ($news['is_published'] == 1): ?>
                                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1.5"><i class="fa-solid fa-circle-check me-1 small"></i> Terbit</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-1.5"><i class="fa-solid fa-circle-minus me-1 small"></i> Draf</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-secondary small">
                                                <?= date('d M Y, H:i', strtotime($news['created_at'])) ?>
                                            </td>
                                            <td class="pe-4 text-center">
                                                <div class="d-inline-flex gap-2">
                                                    <a href="edit.php?id=<?= $news['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit Berita">
                                                        <i class="fa-solid fa-pencil"></i>
                                                    </a>
                                                    <form action="delete.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berita ini?')">
                                                        <input type="hidden" name="id" value="<?= $news['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Berita">
                                                            <i class="fa-solid fa-trash-can"></i>
                                                        </button>
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

        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
