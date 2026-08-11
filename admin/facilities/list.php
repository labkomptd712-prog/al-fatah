<?php
// admin/facilities/list.php
require_once '../includes/auth.php';
require_role('editor'); // Boleh diakses editor, admin, dan superadmin
require_once '../config/db.php';

$message = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';

$category_filter = intval($_GET['category_id'] ?? 0);

// Fetch all categories for filter dropdown
try {
    $stmtCat = $pdo->query("SELECT id, name FROM facility_categories ORDER BY name ASC");
    $categories = $stmtCat->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}

// Fetch facilities (filtered or all)
try {
    $sql = "SELECT f.*, c.name as category_name FROM facilities f LEFT JOIN facility_categories c ON f.category_id = c.id";
    $params = [];
    if ($category_filter > 0) {
        $sql .= " WHERE f.category_id = ?";
        $params[] = $category_filter;
    }
    $sql .= " ORDER BY f.display_order ASC, f.name ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $facilities = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching facilities: " . $e->getMessage());
}

$is_admin = is_admin_role();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Fasilitas - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Daftar Fasilitas</h4>
                    <p class="text-muted small mb-0">Kelola item fasilitas sekolah dan sarana prasarana</p>
                </div>
                <a href="add.php" class="btn btn-brand py-2 px-3"><i class="fa-solid fa-plus me-1"></i> Tambah Fasilitas</a>
            </div>

            <?php if ($message === 'add_success'): ?>
                <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-circle-check me-2"></i> Fasilitas berhasil ditambahkan!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php elseif ($message === 'edit_success'): ?>
                <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-circle-check me-2"></i> Fasilitas berhasil diperbarui!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php elseif ($message === 'delete_success'): ?>
                <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-circle-check me-2"></i> Fasilitas berhasil dihapus!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show"><i class="fa-solid fa-triangle-exclamation me-2"></i> <?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <!-- Filter Card -->
            <div class="card border-0 rounded-4 shadow-sm mb-4">
                <div class="card-body p-3">
                    <form action="" method="GET" class="row g-3 align-items-center">
                        <div class="col-md-4">
                            <select name="category_id" class="form-select bg-light border-0 py-2" onchange="this.form.submit()">
                                <option value="0">-- Semua Kategori --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $category_filter === (int)$cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary py-2 w-100"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card border-0 rounded-4 shadow-sm overflow-hidden">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4" style="width:50px;">No</th>
                                    <th style="width:100px;">Foto</th>
                                    <th>Nama Fasilitas</th>
                                    <th>Kategori</th>
                                    <th>Urutan</th>
                                    <th>Tanggal Dibuat</th>
                                    <th class="pe-4 text-center" style="width:140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($facilities)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-building-circle-exclamation fa-3x mb-3 text-secondary opacity-25"></i>
                                            <p class="mb-0">Belum ada fasilitas terdaftar.</p>
                                            <a href="add.php" class="btn btn-sm btn-brand mt-3">Tambah Sekarang</a>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($facilities as $fac): ?>
                                        <tr>
                                            <td class="ps-4 text-secondary"><?= $no++ ?></td>
                                            <td>
                                                <?php if (!empty($fac['image'])): ?>
                                                    <img src="../uploads/<?= htmlspecialchars($fac['image']) ?>" alt="Foto" class="rounded" style="width:60px; height:45px; object-fit:cover;">
                                                <?php else: ?>
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted" style="width:60px; height:45px;">
                                                        <i class="fa-regular fa-image"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="fw-bold text-dark">
                                                <?= htmlspecialchars($fac['name']) ?>
                                                <?php if (!empty($fac['description'])): ?>
                                                    <div class="text-muted fw-normal small text-truncate" style="max-width: 250px;"><?= htmlspecialchars($fac['description']) ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2.5 py-1">
                                                    <?= htmlspecialchars($fac['category_name'] ?? 'Umum') ?>
                                                </span>
                                            </td>
                                            <td><?= (int)$fac['display_order'] ?></td>
                                            <td><?= date('d/m/Y', strtotime($fac['created_at'])) ?></td>
                                            <td class="pe-4 text-center">
                                                <div class="d-inline-flex gap-2">
                                                    <a href="photos.php?facility_id=<?= (int)$fac['id'] ?>" class="btn btn-sm btn-outline-success" title="Kelola Foto"><i class="fa-solid fa-images"></i></a>
                                                    <a href="edit.php?id=<?= (int)$fac['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fa-solid fa-pencil"></i></a>
                                                    <?php if ($is_admin): ?>
                                                    <form action="delete.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus fasilitas ini?')">
                                                        <input type="hidden" name="id" value="<?= (int)$fac['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"><i class="fa-solid fa-trash-can"></i></button>
                                                    </form>
                                                    <?php endif; ?>
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
