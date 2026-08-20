<?php
// admin/team/edit.php
require_once '../includes/auth.php';
require_admin_role();
require_once '../config/db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: list.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM team WHERE id = ?");
$stmt->execute([$id]);
$member = $stmt->fetch();
if (!$member) {
    header("Location: list.php?err=" . urlencode("Data tidak ditemukan."));
    exit();
}

$error = '';
$name = $member['name'];
$position = $member['position'];
$display_order = (int) $member['display_order'];
$existing_photo = $member['photo'];
$photo_position = $member['photo_position'] ?? 'center';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $display_order = intval($_POST['display_order'] ?? 0);
    $photo_position = trim($_POST['photo_position'] ?? 'center');

    if ($name === '' || $position === '') {
        $error = "Nama dan jabatan wajib diisi!";
    } else {
        $photo_name = $existing_photo;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['photo'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                $check = getimagesize($file['tmp_name']);
                if ($check === false) {
                    $error = "File bukan gambar valid!";
                } else {
                    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!in_array($check['mime'], $allowed)) {
                        $error = "Format gambar tidak didukung.";
                    } elseif ($file['size'] > 2 * 1024 * 1024) {
                        $error = "Ukuran foto maksimal 2MB.";
                    } else {
                        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $photo_name = 'team_' . time() . '_' . uniqid() . '.' . $ext;
                        if (move_uploaded_file($file['tmp_name'], '../uploads/' . $photo_name)) {
                            if (!empty($existing_photo) && file_exists('../uploads/' . $existing_photo)) {
                                @unlink('../uploads/' . $existing_photo);
                            }
                        } else {
                            $error = "Gagal mengupload foto.";
                            $photo_name = $existing_photo;
                        }
                    }
                }
            }
        }

        if ($error === '') {
            try {
                $stmt = $pdo->prepare("UPDATE team SET name = ?, position = ?, photo = ?, display_order = ?, photo_position = ? WHERE id = ?");
                $stmt->execute([$name, $position, $photo_name, $display_order, $photo_position, $id]); logActivity($_SESSION['admin_id'], 'update', 'guru & staff', $name, "Mengubah data guru/staff '{$name}'");
                header("Location: list.php?msg=edit_success");
                exit();
            } catch (PDOException $e) {
                $error = "Gagal memperbarui: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Team - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Edit Anggota Team</h4>
                </div>
                <a href="list.php" class="btn btn-outline-secondary py-2 px-3"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
            </div>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
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
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Foto</label>
                        <?php if (!empty($existing_photo) && file_exists('../uploads/' . $existing_photo)): ?>
                            <div class="mb-2"><img src="../uploads/<?= htmlspecialchars($existing_photo) ?>" alt="" style="height:80px;border-radius:8px;object-fit:cover;"></div>
                        <?php endif; ?>
                        <input type="file" name="photo" class="form-control bg-light border-0" accept="image/*">
                        <small class="text-muted">Kosongkan jika tidak ingin mengganti foto.</small>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Posisi Fokus Foto (Crop)</label>
                        <select name="photo_position" class="form-select bg-light border-0 py-2.5 rounded-3">
                            <option value="center" <?= $photo_position === 'center' ? 'selected' : '' ?>>Tengah (Default)</option>
                            <option value="top" <?= $photo_position === 'top' ? 'selected' : '' ?>>Atas</option>
                            <option value="bottom" <?= $photo_position === 'bottom' ? 'selected' : '' ?>>Bawah</option>
                            <option value="left" <?= $photo_position === 'left' ? 'selected' : '' ?>>Kiri</option>
                            <option value="right" <?= $photo_position === 'right' ? 'selected' : '' ?>>Kanan</option>
                        </select>
                        <small class="text-muted">Mengatur posisi fokus perataan gambar (object-position).</small>
                    </div>
                    <button type="submit" class="btn btn-brand w-100 py-2.5 rounded-3 fw-bold"><i class="fa-solid fa-floppy-disk me-2"></i> Simpan Perubahan</button>
                </form>
            </div>
        </main>
    </div>
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
