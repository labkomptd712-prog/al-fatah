<?php
// admin/admins/add.php
require_once '../includes/auth.php';
require_role('superadmin');
require_once '../config/db.php';

$error = '';
$username = '';
$role = 'editor';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $role = trim($_POST['role'] ?? 'editor');

    if ($username === '' || $password === '') {
        $error = "Username dan password wajib diisi!";
    } elseif (!in_array($role, ['superadmin', 'admin', 'editor'], true)) {
        $error = "Role tidak valid!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Username sudah digunakan!";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO admins (username, password, role) VALUES (?, ?, ?)");
                $stmt->execute([$username, $hash, $role]);
                header("Location: list.php?msg=add_success");
                exit();
            }
        } catch (PDOException $e) {
            $error = "Gagal menyimpan: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Admin - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Tambah Akun Admin</h4>
                    <p class="text-muted small mb-0">Buat akun baru dengan role admin atau editor</p>
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
                        <input type="text" name="username" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= htmlspecialchars($username) ?>" required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Password</label>
                        <input type="password" name="password" class="form-control bg-light border-0 py-2.5 rounded-3" required minlength="6">
                        <small class="text-muted">Minimal 6 karakter.</small>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Role</label>
                        <select name="role" class="form-select bg-light border-0 py-2.5 rounded-3" required>
                            <option value="superadmin" <?= $role === 'superadmin' ? 'selected' : '' ?>>Superadmin (Akses penuh & kelola akun)</option>
                            <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin (Kelola konten website)</option>
                            <option value="editor" <?= $role === 'editor' ? 'selected' : '' ?>>Editor (Hanya ajukan berita/galeri/team)</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-brand w-100 py-2.5 rounded-3 fw-bold"><i class="fa-solid fa-floppy-disk me-2"></i> Simpan Akun</button>
                </form>
            </div>
        </main>
    </div>
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
