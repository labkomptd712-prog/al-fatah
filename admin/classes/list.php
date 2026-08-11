<?php
// admin/classes/list.php
require_once '../includes/auth.php';
require_admin_role();
require_once '../config/db.php';

try {
    $stmt = $pdo->query("SELECT * FROM classes ORDER BY class_name ASC, id ASC");
    $classes_list = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching classes: " . $e->getMessage());
}

$message = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kelas - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Kelas &amp; Wali Kelas</h4>
                    <p class="text-muted small mb-0">Kelola daftar kelas dan wali kelas</p>
                </div>
                <a href="add.php" class="btn btn-brand py-2 px-3"><i class="fa-solid fa-plus me-1"></i> Tambah Kelas</a>
            </div>

            <?php if ($message === 'add_success'): ?>
                <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-circle-check me-2"></i> Kelas berhasil ditambahkan!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php elseif ($message === 'edit_success'): ?>
                <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-circle-check me-2"></i> Data berhasil diperbarui!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php elseif ($message === 'delete_success'): ?>
                <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-circle-check me-2"></i> Kelas berhasil dihapus!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
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
                                    <th>Nama Kelas</th>
                                    <th>Wali Kelas</th>
                                    <th style="width:120px;">Jumlah Siswa</th>
                                    <th class="pe-4 text-center" style="width:140px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($classes_list)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-chalkboard-user fa-3x mb-3 text-secondary opacity-25"></i>
                                            <p class="mb-0">Belum ada data kelas.</p>
                                            <a href="add.php" class="btn btn-sm btn-brand mt-3">Tambah Sekarang</a>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($classes_list as $class): ?>
                                        <tr>
                                            <td class="ps-4 text-secondary"><?= $no++ ?></td>
                                            <td class="fw-bold"><?= htmlspecialchars($class['class_name']) ?></td>
                                            <td><?= htmlspecialchars($class['wali_kelas']) ?></td>
                                            <td><?= $class['student_count'] !== null ? (int) $class['student_count'] : '—' ?></td>
                                            <td class="pe-4 text-center">
                                                <div class="d-inline-flex gap-2">
                                                    <a href="edit.php?id=<?= (int) $class['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pencil"></i></a>
                                                    <form action="delete.php" method="POST" onsubmit="return confirm('Hapus kelas ini?')">
                                                        <input type="hidden" name="id" value="<?= (int) $class['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash-can"></i></button>
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
