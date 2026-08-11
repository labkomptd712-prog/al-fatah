<?php
// admin/gallery-categories/list.php
require_once '../includes/auth.php';
require_role('admin'); // Hanya admin & superadmin
require_once '../config/db.php';

try {
    // Ambil kategori beserta jumlah foto didalamnya
    $stmt = $pdo->query("SELECT c.*, COUNT(g.id) as photo_count FROM gallery_categories c LEFT JOIN gallery g ON c.id = g.category_id GROUP BY c.id ORDER BY c.name ASC");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching gallery categories: " . $e->getMessage());
}

$message = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Galeri - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Kategori Galeri (Folder)</h4>
                    <p class="text-muted small mb-0">Kelola pengelompokan folder galeri foto sekolah</p>
                </div>
                <a href="add.php" class="btn btn-brand py-2 px-3"><i class="fa-solid fa-plus me-1"></i> Tambah Kategori</a>
            </div>

            <?php if ($message === 'add_success'): ?>
                <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-circle-check me-2"></i> Kategori berhasil ditambahkan!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php elseif ($message === 'edit_success'): ?>
                <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-circle-check me-2"></i> Kategori berhasil diperbarui!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php elseif ($message === 'delete_success'): ?>
                <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-circle-check me-2"></i> Kategori berhasil dihapus!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show"><i class="fa-solid fa-triangle-exclamation me-2"></i> <?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <div class="card border-0 rounded-4 shadow-sm overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width:50px;">No</th>
                                    <th style="width:100px;">Cover</th>
                                    <th>Nama Kategori</th>
                                    <th>Slug</th>
                                    <th>Jumlah Foto</th>
                                    <th class="pe-4 text-center" style="width:140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($categories)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fa-regular fa-folder-open fa-3x mb-3 text-secondary opacity-25"></i>
                                            <p class="mb-0">Belum ada kategori galeri.</p>
                                            <a href="add.php" class="btn btn-sm btn-brand mt-3">Tambah Sekarang</a>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($categories as $cat): ?>
                                        <tr>
                                            <td class="ps-4 text-secondary"><?= $no++ ?></td>
                                            <td>
                                                <?php if (!empty($cat['cover_image'])): ?>
                                                    <img src="../uploads/<?= htmlspecialchars($cat['cover_image']) ?>" alt="Cover" class="rounded" style="width:60px; height:45px; object-fit:cover;">
                                                <?php else: ?>
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width:60px; height:45px;">
                                                        <i class="fa-regular fa-image"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="fw-bold text-dark"><?= htmlspecialchars($cat['name']) ?></td>
                                            <td><code class="text-secondary"><?= htmlspecialchars($cat['slug']) ?></code></td>
                                            <td>
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2.5 py-1">
                                                    <?= (int) $cat['photo_count'] ?> Foto
                                                </span>
                                            </td>
                                            <td class="pe-4 text-center">
                                                <div class="d-inline-flex gap-2">
                                                    <a href="edit.php?id=<?= (int) $cat['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa-solid fa-pencil"></i></a>
                                                    <form action="delete.php" method="POST" onsubmit="return confirm('Hapus kategori ini? Foto di dalamnya akan dipindahkan ke kategori Umum.')">
                                                        <input type="hidden" name="id" value="<?= (int) $cat['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fa-solid fa-trash-can"></i></button>
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
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
