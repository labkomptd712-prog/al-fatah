<?php
require_once '../includes/auth.php';
require_role('superadmin');
require_once '../config/db.php';

try {
    $stmt = $pdo->query("SELECT id, username, role, created_at FROM admins ORDER BY created_at ASC, id ASC");
    $admins_list = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching admins: " . $e->getMessage());
}

$current_admin_id = (int) ($_SESSION['admin_id'] ?? 0);
$message = $_GET['msg'] ?? '';
$error = $_GET['err'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Admin - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Kelola Admin</h4>
                    <p class="text-muted small mb-0">Daftar akun admin dan editor</p>
                </div>
                <a href="add.php" class="btn btn-brand py-2 px-3"><i class="fa-solid fa-plus me-1"></i> Tambah Akun</a>
            </div>

            <?php if ($message === 'add_success'): ?>
                <div class="alert alert-success alert-dismissible fade show"><i class="fa-solid fa-circle-check me-2"></i> Akun berhasil ditambahkan!<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php elseif ($message === 'delete_success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> Akun admin berhasil dihapus!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($message === 'edit_success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> Akun admin berhasil diperbarui!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
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
                                    <th>Username</th>
                                    <th style="width:120px;">Role</th>
                                    <th style="width:180px;">Tanggal Dibuat</th>
                                    <th class="pe-4 text-center" style="width:100px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($admins_list)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">Belum ada akun admin.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; foreach ($admins_list as $admin): ?>
                                        <tr>
                                            <td class="ps-4 text-secondary"><?= $no++ ?></td>
                                            <td class="fw-bold">
                                                <?= htmlspecialchars($admin['username']) ?>
                                                <?php if ((int) $admin['id'] === $current_admin_id): ?>
                                                    <span class="badge bg-secondary ms-1">Anda</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($admin['role'] === 'superadmin'): ?>
                                                    <span class="badge bg-danger">Superadmin</span>
                                                <?php elseif ($admin['role'] === 'editor'): ?>
                                                    <span class="badge bg-info text-dark">Editor</span>
                                                <?php elseif ($admin['role'] === 'kepsek'): ?>
                                                    <span class="badge bg-warning text-dark">Kepsek</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Admin</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?= date('d/m/Y H:i', strtotime($admin['created_at'])) ?></td>
                                            <td class="pe-4 text-center">
                                                <div class="d-inline-flex gap-2">
                                                    <a href="edit.php?id=<?= (int) $admin['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit Akun"><i class="fa-solid fa-pencil"></i></a>
                                                    <?php if ((int) $admin['id'] !== $current_admin_id): ?>
                                                        <form action="delete.php" method="POST" onsubmit="return confirm('Hapus akun <?= htmlspecialchars($admin['username']) ?>?')" class="d-inline">
                                                            <input type="hidden" name="id" value="<?= (int) $admin['id'] ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Akun"><i class="fa-solid fa-trash-can"></i></button>
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
