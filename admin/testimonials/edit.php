<?php
// admin/testimonials/edit.php
require_once '../includes/auth.php';
require_role('admin'); // Hanya admin & superadmin
require_once '../config/db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: list.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
    $stmt->execute([$id]);
    $t = $stmt->fetch();
} catch (PDOException $e) {
    die("Error fetching testimonial: " . $e->getMessage());
}

if (!$t) {
    header("Location: list.php?err=" . urlencode("Testimonial tidak ditemukan."));
    exit();
}

$error = '';
$name = $t['name'];
$position = $t['position'];
$quote = $t['quote'];
$display_order = (int) $t['display_order'];
$existing_photo = $t['photo'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $quote = trim($_POST['quote'] ?? '');
    $display_order = intval($_POST['display_order'] ?? 0);

    if ($name === '' || $position === '' || $quote === '') {
        $error = "Nama, jabatan/posisi, dan quote wajib diisi!";
    } else {
        $photo_name = $existing_photo;
        
        // Cek jika ada upload foto baru
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['photo']['tmp_name'];
            $file_name = $_FILES['photo']['name'];
            $file_size = $_FILES['photo']['size'];
            
            $check = getimagesize($file_tmp);
            if ($check === false) {
                $error = "File yang diupload bukan gambar valid!";
            } else {
                $mime = $check['mime'];
                $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];
                if (!in_array($mime, $allowed_mimes)) {
                    $error = "Format gambar harus JPG, JPEG, PNG, atau WEBP.";
                } elseif ($file_size > 2 * 1024 * 1024) {
                    $error = "Ukuran gambar maksimal 2MB.";
                } else {
                    $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                    $photo_name = 'testimonial_' . time() . '_' . uniqid() . '.' . $ext;
                    if (!is_dir('../uploads')) {
                        mkdir('../uploads', 0755, true);
                    }
                    if (move_uploaded_file($file_tmp, '../uploads/' . $photo_name)) {
                        // Hapus foto lama jika ada
                        if (!empty($existing_photo) && file_exists('../uploads/' . $existing_photo)) {
                            unlink('../uploads/' . $existing_photo);
                        }
                    } else {
                        $error = "Gagal mengunggah foto baru.";
                        $photo_name = $existing_photo;
                    }
                }
            }
        }

        if (empty($error)) {
            try {
                $stmtUpdate = $pdo->prepare("UPDATE testimonials SET name = ?, position = ?, quote = ?, photo = ?, display_order = ? WHERE id = ?");
                $stmtUpdate->execute([$name, $position, $quote, $photo_name, $display_order, $id]);
                header("Location: list.php?msg=edit_success");
                exit();
            } catch (PDOException $e) {
                $error = "Gagal memperbarui testimonial: " . $e->getMessage();
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
    <title>Edit Testimonial - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Edit Testimonial</h4>
                </div>
                <a href="list.php" class="btn btn-outline-secondary py-2 px-3"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="card border-0 rounded-4 shadow-sm p-4" style="max-width:720px;">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Nama Tokoh / Pengunjung</label>
                        <input type="text" name="name" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= htmlspecialchars($name) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Jabatan / Keterangan</label>
                        <input type="text" name="position" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= htmlspecialchars($position) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Foto Profil Baru <span class="text-muted fw-normal">(opsional)</span></label>
                        <?php if (!empty($existing_photo)): ?>
                            <div class="mb-2">
                                <img src="../uploads/<?= htmlspecialchars($existing_photo) ?>" alt="Foto" class="rounded-circle mb-2" style="width:60px; height:60px; object-fit:cover;">
                                <span class="text-muted small d-block">Foto saat ini (akan diganti jika mengunggah file baru)</span>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="photo" class="form-control bg-light border-0 py-2.5 rounded-3" accept="image/*">
                        <small class="text-muted">Format: JPG, JPEG, PNG, WEBP. Maks. 2MB.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Isi Testimonial / Quote</label>
                        <textarea name="quote" class="form-control bg-light border-0 p-3 rounded-3" rows="4" required><?= htmlspecialchars($quote) ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Urutan Tampil</label>
                        <input type="number" name="display_order" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= (int) $display_order ?>" min="0">
                    </div>
                    <button type="submit" class="btn btn-brand w-100 py-3 rounded-3 fw-bold"><i class="fa-solid fa-floppy-disk me-2"></i> Simpan Perubahan</button>
                </form>
            </div>
        </main>
    </div>
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
