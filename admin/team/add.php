<?php
// admin/team/add.php
require_once '../includes/auth.php';
require_role('admin'); // Membatasi editor dari menambah data team
require_once '../config/db.php';

$error = '';
$name = '';
$position = '';
$display_order = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $display_order = intval($_POST['display_order'] ?? 0);
    $photo_position = trim($_POST['photo_position'] ?? 'center');

    if ($name === '' || $position === '') {
        $error = "Nama dan jabatan wajib diisi!";
    } else {
        $photo_name = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['photo'];
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $error = "Terjadi kesalahan saat mengupload foto.";
            } else {
                $check = getimagesize($file['tmp_name']);
                if ($check === false) {
                    $error = "File yang diupload bukan gambar valid!";
                } else {
                    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!in_array($check['mime'], $allowed)) {
                        $error = "Hanya JPG, PNG, GIF, WEBP yang diperbolehkan.";
                    } elseif ($file['size'] > 2 * 1024 * 1024) {
                        $error = "Ukuran foto maksimal 2MB.";
                    } else {
                        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $photo_name = 'team_' . time() . '_' . uniqid() . '.' . $ext;
                        if (!is_dir('../uploads')) {
                            mkdir('../uploads', 0755, true);
                        }
                        if (!move_uploaded_file($file['tmp_name'], '../uploads/' . $photo_name)) {
                            $error = "Gagal menyimpan file foto.";
                            $photo_name = null;
                        }
                    }
                }
            }
        }

        if ($error === '') {
            try {
                $stmt = $pdo->prepare("INSERT INTO team (name, position, photo, display_order, photo_position) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $position, $photo_name, $display_order, $photo_position]); logActivity($_SESSION['admin_id'], 'create', 'guru & staff', $name, "Menambahkan guru/staff baru '{$name}'");
                if (is_editor_role()) {
                    header("Location: ../dashboard.php?msg=team_add_success");
                } else {
                    header("Location: list.php?msg=add_success");
                }
                exit();
            } catch (PDOException $e) {
                $error = "Gagal menyimpan: " . $e->getMessage();
            }
        }
    }
}

$back_url = is_editor_role() ? '../dashboard.php' : 'list.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Team - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Tambah Anggota Team</h4>
                    <p class="text-muted small mb-0">Tambahkan anggota manajemen sekolah</p>
                </div>
                <a href="<?= htmlspecialchars($back_url) ?>" class="btn btn-outline-secondary py-2 px-3"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
            </div>
            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <div class="card border-0 rounded-4 shadow-sm p-4" style="max-width:640px;">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Nama</label>
                        <input type="text" name="name" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= htmlspecialchars($name) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Jabatan</label>
                        <input type="text" name="position" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= htmlspecialchars($position) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Urutan Tampil</label>
                        <input type="number" name="display_order" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= (int) $display_order ?>" min="0">
                        <small class="text-muted">Angka lebih kecil tampil lebih dulu.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Foto</label>
                        <input type="file" name="photo" class="form-control bg-light border-0" accept="image/*">
                        <small class="text-muted">Opsional. Maks. 2MB.</small>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Posisi Fokus Foto (Crop)</label>
                        <select name="photo_position" class="form-select bg-light border-0 py-2.5 rounded-3">
                            <option value="center" selected>Tengah (Default)</option>
                            <option value="top">Atas</option>
                            <option value="bottom">Bawah</option>
                            <option value="left">Kiri</option>
                            <option value="right">Kanan</option>
                        </select>
                        <small class="text-muted">Mengatur posisi fokus perataan gambar (object-position).</small>
                    </div>
                    <button type="submit" class="btn btn-brand w-100 py-2.5 rounded-3 fw-bold"><i class="fa-solid fa-floppy-disk me-2"></i> Simpan</button>
                </form>
            </div>
        </main>
    </div>
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
