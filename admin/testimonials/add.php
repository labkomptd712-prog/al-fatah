<?php
// admin/testimonials/add.php
require_once '../includes/auth.php';
require_role('admin'); // Hanya admin & superadmin
require_once '../config/db.php';

$error = '';
$name = '';
$position = '';
$quote = '';
$display_order = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $quote = trim($_POST['quote'] ?? '');
    $display_order = intval($_POST['display_order'] ?? 0);

    if ($name === '' || $position === '' || $quote === '') {
        $error = "Nama, jabatan/posisi, dan quote wajib diisi!";
    } else {
        $photo_name = null;
        
        // Cek jika ada upload foto
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
                    if (!move_uploaded_file($file_tmp, '../uploads/' . $photo_name)) {
                        $error = "Gagal mengunggah foto.";
                        $photo_name = null;
                    }
                }
            }
        }

        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO testimonials (name, position, quote, photo, display_order) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$name, $position, $quote, $photo_name, $display_order]);
                header("Location: list.php?msg=add_success");
                exit();
            } catch (PDOException $e) {
                $error = "Gagal menyimpan testimonial: " . $e->getMessage();
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
    <title>Tambah Testimonial - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Tambah Testimonial</h4>
                    <p class="text-muted small mb-0">Tambahkan ulasan atau pesan dari tokoh/pengunjung baru</p>
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
                        <input type="text" name="name" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= htmlspecialchars($name) ?>" placeholder="Contoh: Dr. H. Ahmad Fauzi, M.Pd." required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Jabatan / Keterangan</label>
                        <input type="text" name="position" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= htmlspecialchars($position) ?>" placeholder="Contoh: Wali Murid Kelas 3 / Pengawas Sekolah" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Foto Profil <span class="text-muted fw-normal">(opsional)</span></label>
                        <input type="file" name="photo" class="form-control bg-light border-0 py-2.5 rounded-3" accept="image/*">
                        <small class="text-muted">Format: JPG, JPEG, PNG, WEBP. Maks. 2MB. Disarankan foto berasio 1:1.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Isi Testimonial / Quote</label>
                        <textarea name="quote" class="form-control bg-light border-0 p-3 rounded-3" rows="4" placeholder="Tuliskan ulasan atau kutipan pesan di sini..." required><?= htmlspecialchars($quote) ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Urutan Tampil</label>
                        <input type="number" name="display_order" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= (int) $display_order ?>" min="0">
                        <small class="text-muted">Urutan angka terkecil akan tampil paling awal di homepage.</small>
                    </div>
                    <button type="submit" class="btn btn-brand w-100 py-3 rounded-3 fw-bold"><i class="fa-solid fa-floppy-disk me-2"></i> Simpan Testimonial</button>
                </form>
            </div>
        </main>
    </div>
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
