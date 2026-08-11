<?php
// admin/footer-links/list.php
require_once '../includes/auth.php';
require_admin_role('admin'); // Only admin and superadmin
require_once '../config/db.php';

try {
    // Fetch all footer links sorted by category and display order
    $stmt = $pdo->query("SELECT * FROM footer_links ORDER BY category ASC, display_order ASC");
    $links = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching footer links: " . $e->getMessage());
}

$message = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Footer - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Kelola Tautan & Dokumen Footer</h4>
                    <p class="text-muted small mb-0">Kelola tautan menu "Layanan Kepegawaian" dan "Tautan" pada bagian bawah website</p>
                </div>
                <div>
                    <a href="add.php" class="btn btn-brand py-2 px-3"><i class="fa-solid fa-plus me-1"></i> Tambah Tautan</a>
                </div>
            </div>

            <!-- Notifications -->
            <?php if ($message === 'add_success'): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> Tautan baru berhasil ditambahkan!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($message === 'edit_success'): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> Perubahan tautan berhasil disimpan!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($message === 'delete_success'): ?>
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> Tautan berhasil dihapus dari database dan disk!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if ($error === 'db_error'): ?>
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> Terjadi kesalahan pada database.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Table Card -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                            <thead class="table-light text-uppercase text-secondary fw-semibold" style="font-size: 11px; letter-spacing: 0.5px;">
                                <tr>
                                    <th class="ps-4 py-3" style="width: 80px;">No. Urut</th>
                                    <th class="py-3">Kategori</th>
                                    <th class="py-3">Judul Menu</th>
                                    <th class="py-3">Tipe Link / Dokumen</th>
                                    <th class="py-3 text-end pe-4" style="width: 180px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($links)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="fa-regular fa-folder-open fs-2 mb-2 d-block"></i>
                                            Belum ada tautan footer yang terdaftar.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($links as $link): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold text-secondary"><?= htmlspecialchars($link['display_order']) ?></td>
                                            <td>
                                                <?php if ($link['category'] === 'layanan_kepegawaian'): ?>
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 rounded-3">Layanan Kepegawaian</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 rounded-3">Tautan</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="fw-semibold text-dark"><?= htmlspecialchars($link['title']) ?></td>
                                            <td>
                                                <?php if (!empty($link['file_path'])): ?>
                                                    <a href="../uploads/footer-docs/<?= htmlspecialchars($link['file_path']) ?>" target="_blank" class="text-danger fw-semibold text-decoration-none">
                                                        <i class="fa-solid fa-file-pdf me-1"></i> PDF Dokumen
                                                    </a>
                                                <?php elseif (!empty($link['external_url'])): ?>
                                                    <a href="<?= htmlspecialchars($link['external_url']) ?>" target="_blank" class="text-primary fw-semibold text-decoration-none">
                                                        <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> URL Eksternal
                                                    </a>
                                                <?php else: ?>
                                                    <span class="text-muted small">
                                                        <i class="fa-solid fa-link-slash me-1"></i> (Belum tersedia / Teks Non-aktif)
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end pe-4">
                                                <a href="edit.php?id=<?= $link['id'] ?>" class="btn btn-sm btn-outline-primary border-0 rounded-3 me-1" title="Edit Tautan">
                                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                                </a>
                                                <a href="delete.php?id=<?= $link['id'] ?>" class="btn btn-sm btn-outline-danger border-0 rounded-3" onclick="return confirm('Apakah Anda yakin ingin menghapus tautan footer ini?')" title="Hapus Tautan">
                                                    <i class="fa-solid fa-trash-can"></i> Hapus
                                                </a>
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

    <!-- Bootstrap 5 JS -->
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
