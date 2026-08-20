<?php
// admin/prestasi/add.php — halaman tambah prestasi (admin & editor)
require_once '../includes/auth.php';
require_once '../config/db.php';

$error = '';
$nama_siswa = '';
$jenis_lomba = '';
$keterangan = '';
$category_id = intval($_GET['category_id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_siswa = trim($_POST['nama_siswa'] ?? '');
    $jenis_lomba = trim($_POST['jenis_lomba'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    if ($category_id <= 0) {
        $category_id = null;
    }

    if (empty($nama_siswa)) {
        $error = "Nama siswa wajib diisi.";
    } elseif (empty($jenis_lomba)) {
        $error = "Jenis lomba wajib diisi.";
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        $error = "Silakan pilih foto prestasi.";
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
                    $image_name = 'prestasi_' . time() . '_' . uniqid() . '.' . $ext;
                    if (!is_dir('../uploads')) {
                        mkdir('../uploads', 0755, true);
                    }
                    if (move_uploaded_file($file_tmp, '../uploads/' . $image_name)) {
                        try {
                            $stmt = $pdo->prepare("INSERT INTO prestasi (nama_siswa, jenis_lomba, keterangan, foto, category_id) VALUES (?, ?, ?, ?, ?)");
                            $stmt->execute([$nama_siswa, $jenis_lomba, $keterangan, $image_name, $category_id]); logActivity($_SESSION['admin_id'], 'create', 'prestasi', $nama_siswa, "Menambahkan prestasi baru untuk siswa '{$nama_siswa}'");
                            if (is_editor_role()) {
                                header("Location: ../dashboard.php?msg=prestasi_add_success");
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

// Fetch all categories for the dropdown
try {
    $stmtCategories = $pdo->query("SELECT id, name FROM prestasi_categories ORDER BY name ASC");
    $categories = $stmtCategories->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}

$back_url = is_editor_role() ? '../dashboard.php' : 'list.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Prestasi Siswa - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Tambah Prestasi Siswa</h4>
                    <p class="text-muted small mb-0">Unggah foto dokumentasi penghargaan atau kejuaraan siswa</p>
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
                        <label class="form-label fw-semibold text-secondary">Pilih Foto Siswa / Piagam</label>
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

                    <div class="mb-3">
                        <label for="nama_siswa" class="form-label fw-semibold text-secondary">Nama Siswa</label>
                        <input type="text" class="form-control bg-light border-0 py-2.5 px-3 rounded-3" id="nama_siswa" name="nama_siswa" value="<?= htmlspecialchars($nama_siswa) ?>" placeholder="Contoh: Muhammad Ali" required>
                    </div>

                    <div class="mb-3">
                        <label for="jenis_lomba" class="form-label fw-semibold text-secondary">Jenis Lomba / Cabang</label>
                        <input type="text" class="form-control bg-light border-0 py-2.5 px-3 rounded-3" id="jenis_lomba" name="jenis_lomba" value="<?= htmlspecialchars($jenis_lomba) ?>" placeholder="Contoh: Pencak Silat Tingkat Kota" required>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-semibold text-secondary">Kategori Prestasi (Folder)</label>
                        <select name="category_id" id="category_id" class="form-select bg-light border-0 py-2.5 px-3 rounded-3" required>
                            <option value="" disabled <?= $category_id === 0 ? 'selected' : '' ?>>-- Pilih Kategori --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int) $cat['id'] ?>" <?= $category_id === (int)$cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label for="keterangan" class="form-label fw-semibold text-secondary">Keterangan / Juara</label>
                        <textarea class="form-control bg-light border-0 py-2 px-3 rounded-3" id="keterangan" name="keterangan" rows="3" placeholder="Contoh: Juara 1 Tingkat Provinsi Jawa Barat"><?= htmlspecialchars($keterangan) ?></textarea>
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
