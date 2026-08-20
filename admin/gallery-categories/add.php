<?php
// admin/gallery-categories/add.php
require_once '../includes/auth.php';
require_role('admin'); // Hanya admin & superadmin
require_once '../config/db.php';

function alfatah_slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

$error = '';
$name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = alfatah_slugify($name);

    if ($name === '') {
        $error = "Nama kategori wajib diisi!";
    } else {
        try {
            // Cek keunikan slug
            $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM gallery_categories WHERE slug = ?");
            $stmtCheck->execute([$slug]);
            if ((int)$stmtCheck->fetchColumn() > 0) {
                $error = "Kategori dengan nama serupa sudah terdaftar!";
            }
        } catch (PDOException $e) {
            $error = "Gagal memvalidasi kategori: " . $e->getMessage();
        }

        if (empty($error)) {
            $cover_name = null;
            
            // Cek jika ada upload cover
            if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['cover']['tmp_name'];
                $file_name = $_FILES['cover']['name'];
                $file_size = $_FILES['cover']['size'];
                
                $check = getimagesize($file_tmp);
                if ($check === false) {
                    $error = "File cover yang diupload bukan gambar valid!";
                } else {
                    $mime = $check['mime'];
                    $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];
                    if (!in_array($mime, $allowed_mimes)) {
                        $error = "Format cover harus JPG, JPEG, PNG, atau WEBP.";
                    } elseif ($file_size > 2 * 1024 * 1024) {
                        $error = "Ukuran cover maksimal 2MB.";
                    } else {
                        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                        $cover_name = 'cat_cover_' . time() . '_' . uniqid() . '.' . $ext;
                        if (!is_dir('../uploads')) {
                            mkdir('../uploads', 0755, true);
                        }
                        if (!move_uploaded_file($file_tmp, '../uploads/' . $cover_name)) {
                            $error = "Gagal mengunggah gambar cover.";
                            $cover_name = null;
                        }
                    }
                }
            }

            if (empty($error)) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO gallery_categories (name, slug, cover_image) VALUES (?, ?, ?)");
                    $stmt->execute([$name, $slug, $cover_name]); logActivity($_SESSION['admin_id'], 'create', 'kategori galeri', $name, "Menambahkan kategori galeri baru '{$name}'");
                    header("Location: list.php?msg=add_success");
                    exit();
                } catch (PDOException $e) {
                    $error = "Gagal menyimpan kategori: " . $e->getMessage();
                }
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
    <title>Tambah Kategori Galeri - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Tambah Kategori Galeri</h4>
                    <p class="text-muted small mb-0">Buat folder/kategori baru untuk mengelompokkan foto sekolah</p>
                </div>
                <a href="list.php" class="btn btn-outline-secondary py-2 px-3"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="card border-0 rounded-4 shadow-sm p-4" style="max-width:600px;">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Nama Kategori / Folder</label>
                        <input type="text" name="name" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= htmlspecialchars($name) ?>" placeholder="Contoh: Study Tour 2026 / Pramuka" required autofocus>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Foto Cover Kategori <span class="text-muted fw-normal">(opsional)</span></label>
                        <input type="file" name="cover" class="form-control bg-light border-0 py-2.5 rounded-3" accept="image/*">
                        <small class="text-muted">Format: JPG, JPEG, PNG, WEBP. Maksimal 2MB.</small>
                    </div>
                    <button type="submit" class="btn btn-brand w-100 py-3 rounded-3 fw-bold"><i class="fa-solid fa-floppy-disk me-2"></i> Simpan Kategori</button>
                </form>
            </div>
        </main>
    </div>
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
