<?php
// admin/admins/edit.php
require_once '../includes/auth.php';
require_role('superadmin'); // Membatasi akses: hanya superadmin yang boleh mengubah akun
require_once '../config/db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: list.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([$id]);
    $admin = $stmt->fetch();
} catch (PDOException $e) {
    die("Error fetching admin: " . $e->getMessage());
}

if (!$admin) {
    header("Location: list.php?err=" . urlencode("Akun tidak ditemukan."));
    exit();
}

$error = '';
$username = $admin['username'];
$role = $admin['role'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = trim($_POST['role'] ?? 'editor');
    $password = trim($_POST['password'] ?? '');

    if (!in_array($role, ['superadmin', 'admin', 'editor', 'kepsek'], true)) {
        $error = "Role tidak valid!";
    } elseif ($password !== '' && strlen($password) < 6) {
        $error = "Password baru minimal 6 karakter!";
    } else {
        try {
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmtUpdate = $pdo->prepare("UPDATE admins SET role = ?, password = ? WHERE id = ?");
                $stmtUpdate->execute([$role, $hash, $id]); logActivity($_SESSION['admin_id'], 'update', 'admin', $username, "Mengubah data admin '{$username}'");
            } else {
                $stmtUpdate = $pdo->prepare("UPDATE admins SET role = ? WHERE id = ?");
                $stmtUpdate->execute([$role, $id]); logActivity($_SESSION['admin_id'], 'update', 'admin', $username, "Mengubah data admin '{$username}'");
            }

            // Jika admin mengedit akunnya sendiri, update session
            if ((int) $id === (int) ($_SESSION['admin_id'] ?? 0)) {
                $_SESSION['admin_role'] = $role;
            }

            header("Location: list.php?msg=edit_success");
            exit();
        } catch (PDOException $e) {
            $error = "Gagal memperbarui akun: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admin - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Edit Akun Admin</h4>
                    <p class="text-muted small mb-0">Ubah detail role atau perbarui password akun admin/editor</p>
                </div>
                <a href="list.php" class="btn btn-outline-secondary py-2 px-3"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
            </div>
            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <div class="card border-0 rounded-4 shadow-sm p-4" style="max-width:640px;">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Username</label>
                        <input type="text" class="form-control bg-light border-0 py-2.5 rounded-3 text-muted" value="<?= htmlspecialchars($username) ?>" readonly disabled>
                        <small class="text-muted">Username bersifat unik dan tidak dapat diubah.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Password Baru <span class="text-muted fw-normal">(opsional)</span></label>
                        <input type="password" name="password" class="form-control bg-light border-0 py-2.5 rounded-3" minlength="6">
                        <small class="text-muted">Kosongkan jika tidak ingin mengganti password lama.</small>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Role</label>
                        <select name="role" class="form-select bg-light border-0 py-2.5 rounded-3" required>
                            <option value="superadmin" <?= $role === 'superadmin' ? 'selected' : '' ?>>Superadmin (Akses penuh & kelola akun)</option>
                            <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin (Kelola konten website)</option>
                            <option value="editor" <?= $role === 'editor' ? 'selected' : '' ?>>Editor (Hanya ajukan berita/galeri/team)</option>
                            <option value="kepsek" <?= $role === 'kepsek' ? 'selected' : '' ?>>Kepala Sekolah (Lihat saja & ajukan revisi)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-brand w-100 py-2.5 rounded-3 fw-bold"><i class="fa-solid fa-floppy-disk me-2"></i> Simpan Perubahan</button>
                </form>
            </div>
        </main>
    </div>
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
