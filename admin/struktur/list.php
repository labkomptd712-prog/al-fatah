<?php
// admin/struktur/list.php
require_once '../includes/auth.php';
require_role('admin'); // Memastikan editor tidak memiliki akses
require_once '../config/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM org_structure ORDER BY display_order ASC, id ASC");
    $structure_list = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching organizational structure: " . $e->getMessage());
}

$message = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struktur Organisasi - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Struktur Organisasi</h4>
                    <p class="text-muted small mb-0">Kelola susunan jabatan dan nama pejabat di website utama</p>
                </div>
                <a href="add.php" class="btn btn-brand py-2 px-3"><i class="fa-solid fa-plus me-1"></i> Tambah Jabatan</a>
            </div>

            <?php if ($message === 'add_success'): ?>
                <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-circle-check me-2"></i> Jabatan berhasil ditambahkan!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php elseif ($message === 'edit_success'): ?>
                <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-circle-check me-2"></i> Data berhasil diperbarui!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php elseif ($message === 'delete_success'): ?>
                <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-circle-check me-2"></i> Jabatan berhasil dihapus!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
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
                                    <th>Nama Jabatan</th>
                                    <th>Nama Pejabat / Staf</th>
                                    <th style="width:120px;">Urutan Tampil</th>
                                    <th class="pe-4 text-center" style="width:140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($structure_list)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-sitemap fa-3x mb-3 text-secondary opacity-25"></i>
                                            <p class="mb-0">Belum ada data struktur organisasi.</p>
                                            <a href="add.php" class="btn btn-sm btn-brand mt-3">Tambah Sekarang</a>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($structure_list as $slot): ?>
                                        <tr>
                                            <td class="ps-4 text-secondary"><?= $no++ ?></td>
                                            <td class="fw-bold text-dark"><?= htmlspecialchars($slot['position_title']) ?></td>
                                            <td>
                                                <?php if (!empty($slot['person_name'])): ?>
                                                    <?= htmlspecialchars($slot['person_name']) ?>
                                                <?php else: ?>
                                                    <span class="text-danger fw-semibold"><i>[Belum diisi]</i></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= (int) $slot['display_order'] ?></td>
                                            <td class="pe-4 text-center">
                                                <div class="d-inline-flex gap-2">
                                                    <a href="edit.php?id=<?= (int) $slot['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa-solid fa-pencil"></i></a>
                                                    <form action="delete.php" method="POST" onsubmit="return confirm('Hapus jabatan ini dari struktur organisasi?')">
                                                        <input type="hidden" name="id" value="<?= (int) $slot['id'] ?>">
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
