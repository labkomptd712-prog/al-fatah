<?php
// admin/gallery/add.php — halaman tambah foto (admin & editor)
require_once '../includes/auth.php';
require_once '../config/db.php';

$error = '';
$caption = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caption = trim($_POST['caption'] ?? '');

    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        $error = "Silakan pilih foto untuk diupload.";
    } else {
        $file = $_FILES['image'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_error = $file['error'];
        $file_name = $file['name'];

        if ($file_error !== UPLOAD_ERR_OK) {
            $error = "Terjadi kesalahan saat mengunggah foto.";
        } else {
            $check = getimagesize($file_tmp);
            if ($check === false) {
                $error = "File yang diupload bukan gambar valid!";
            } else {
                $mime = $check['mime'];
                $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($mime, $allowed_mimes)) {
                    $error = "Hanya file gambar JPG, JPEG, PNG, GIF, dan WEBP yang diperbolehkan.";
                } elseif ($file_size > 2 * 1024 * 1024) {
                    $error = "Ukuran gambar terlalu besar! Maksimal 2MB.";
                } else {
                    $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                    $image_name = 'gallery_' . time() . '_' . uniqid() . '.' . $ext;
                    if (!is_dir('../uploads')) {
                        mkdir('../uploads', 0755, true);
                    }
                    if (move_uploaded_file($file_tmp, '../uploads/' . $image_name)) {
                        try {
                            $stmt = $pdo->prepare("INSERT INTO gallery (caption, image) VALUES (?, ?)");
                            $stmt->execute([$caption, $image_name]); logActivity($_SESSION['admin_id'], 'create', 'galeri', $caption ?: 'Foto Galeri', "Mengunggah foto galeri baru" . ($caption ? " dengan caption '{$caption}'" : ""));
                            if (is_editor_role()) {
                                header("Location: ../dashboard.php?msg=gallery_add_success");
                            } else {
                                header("Location: list.php?msg=upload_success");
                            }
                            exit();
                        } catch (PDOException $e) {
                            $error = "Gagal menyimpan data ke database: " . $e->getMessage();
                        }
                    } else {
                        $error = "Gagal memindahkan file foto ke direktori upload.";
                    }
                }
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
    <title>Tambah Galeri - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Tambah Foto Galeri</h4>
                    <p class="text-muted small mb-0">Unggah dokumentasi kegiatan sekolah</p>
                </div>
                <a href="<?= htmlspecialchars($back_url) ?>" class="btn btn-outline-secondary py-2 px-3"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card border-0 rounded-4 shadow-sm p-4" style="max-width: 560px;">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Pilih File Foto</label>
                        <div class="border border-2 border-dashed border-secondary border-opacity-25 rounded-3 p-3 bg-light text-center mb-2" style="min-height: 150px; cursor: pointer;" onclick="document.getElementById('imageInput').click();">
                            <img id="imagePreview" src="#" alt="Preview" class="img-fluid rounded mb-2 d-none" style="max-height: 120px; object-fit: cover;">
                            <div id="uploadPlaceholder" class="text-muted py-3">
                                <i class="fa-regular fa-image fa-2x mb-2 text-secondary text-opacity-50"></i>
                                <p class="small mb-0">Klik untuk memilih foto</p>
                            </div>
                        </div>
                        <input type="file" id="imageInput" name="image" class="d-none" accept="image/*" required>
                        <small class="text-muted">Maksimal 2MB (JPG, JPEG, PNG, WEBP)</small>
                    </div>
                    <div class="mb-4">
                        <label for="caption" class="form-label fw-semibold text-secondary">Keterangan / Caption</label>
                        <textarea class="form-control bg-light border-0 py-2 px-3 rounded-3" id="caption" name="caption" rows="3" placeholder="Deskripsi singkat..."><?= htmlspecialchars($caption) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-brand w-100 py-2.5 rounded-3 fw-bold"><i class="fa-solid fa-upload me-2"></i> Unggah Sekarang</button>
                </form>
            </div>
        </main>
    </div>
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('imageInput').addEventListener('change', function() {
            const preview = document.getElementById('imagePreview');
            const placeholder = document.getElementById('uploadPlaceholder');
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                    placeholder.classList.add('d-none');
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>
</body>
</html>
