<?php
// admin/facility-categories/add.php
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
            $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM facility_categories WHERE slug = ?");
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
                    $stmt = $pdo->prepare("INSERT INTO facility_categories (name, slug, cover_image) VALUES (?, ?, ?)");
                    $stmt->execute([$name, $slug, $cover_name]); logActivity($_SESSION['admin_id'], 'create', 'kategori fasilitas', $name, "Menambahkan kategori fasilitas baru '{$name}'");
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
    <title>Tambah Kategori Fasilitas - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Tambah Kategori Fasilitas</h4>
                    <p class="text-muted small mb-0">Buat kelompok kategori fasilitas sekolah baru</p>
                </div>
                <a href="list.php" class="btn btn-outline-secondary py-2 px-3"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
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
                        <label for="name" class="form-label fw-semibold text-secondary">Nama Kategori</label>
                        <input type="text" class="form-control bg-light border-0 py-2.5 px-3 rounded-3" id="name" name="name" value="<?= htmlspecialchars($name) ?>" placeholder="Contoh: Gedung, Laboratorium" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Pilih Gambar Cover (Opsional)</label>
                        <div class="border border-2 border-dashed border-secondary border-opacity-25 rounded-3 p-3 bg-light text-center mb-2" style="min-height: 150px; cursor: pointer;" onclick="document.getElementById('coverInput').click();">
                            <img id="coverPreview" src="#" alt="Preview" class="img-fluid rounded mb-2 d-none" style="max-height: 120px; object-fit: cover;">
                            <div id="uploadPlaceholder" class="text-muted py-3">
                                <i class="fa-regular fa-image fa-2x mb-2 text-secondary text-opacity-50"></i>
                                <p class="small mb-0">Klik untuk memilih cover gambar</p>
                            </div>
                        </div>
                        <input type="file" id="coverInput" name="cover" class="d-none" accept="image/*">
                        <small class="text-muted">Maksimal 2MB (JPG, JPEG, PNG, WEBP)</small>
                    </div>
                    <button type="submit" class="btn btn-brand w-100 py-2.5 rounded-3 fw-bold"><i class="fa-solid fa-plus me-2"></i> Tambah Kategori</button>
                </form>
            </div>
        </main>
    </div>
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('coverInput').addEventListener('change', function() {
            const preview = document.getElementById('coverPreview');
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
