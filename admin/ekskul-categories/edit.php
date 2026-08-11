<?php
// admin/ekskul-categories/edit.php
require_once '../includes/auth.php';
require_role('admin'); // Hanya admin & superadmin
require_once '../config/db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: list.php");
    exit();
}

$error = '';

// Ambil data kategori saat ini
try {
    $stmt = $pdo->prepare("SELECT * FROM ekskul_categories WHERE id = ?");
    $stmt->execute([$id]);
    $category = $stmt->fetch();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

if (!$category) {
    header("Location: list.php?err=" . urlencode("Kategori tidak ditemukan."));
    exit();
}

$name = $category['name'];
$slug = $category['slug'];
$current_cover = $category['cover_image'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $remove_cover = isset($_POST['remove_cover']);

    // Generate slug jika kosong
    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    } else {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slug)));
    }

    if (empty($name)) {
        $error = "Nama kategori wajib diisi.";
    } elseif (empty($slug)) {
        $error = "Slug kategori tidak valid.";
    } else {
        // Cek duplikasi slug pada ID lain
        $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM ekskul_categories WHERE slug = ? AND id != ?");
        $stmtCheck->execute([$slug, $id]);
        if ($stmtCheck->fetchColumn() > 0) {
            $error = "Slug sudah digunakan oleh kategori lain!";
        } else {
            $cover_image = $current_cover;

            if ($remove_cover && !empty($current_cover)) {
                if (file_exists('../uploads/' . $current_cover)) {
                    unlink('../uploads/' . $current_cover);
                }
                $cover_image = null;
            }

            // Upload cover baru jika ada
            if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['cover_image'];
                $file_tmp = $file['tmp_name'];
                $file_size = $file['size'];
                $file_error = $file['error'];
                $file_name = $file['name'];

                if ($file_error !== UPLOAD_ERR_OK) {
                    $error = "Terjadi kesalahan saat mengunggah foto cover.";
                } else {
                    $check = getimagesize($file_tmp);
                    if ($check === false) {
                        $error = "File cover yang diupload bukan gambar valid!";
                    } else {
                        $mime = $check['mime'];
                        $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];
                        if (!in_array($mime, $allowed_mimes)) {
                            $error = "Format cover harus JPG, JPEG, PNG, atau WEBP.";
                        } elseif ($file_size > 2 * 1024 * 1024) {
                            $error = "Ukuran gambar cover maksimal 2MB.";
                        } else {
                            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                            $new_cover = 'ekskul_cat_' . time() . '_' . uniqid() . '.' . $ext;

                            if (!is_dir('../uploads')) {
                                mkdir('../uploads', 0755, true);
                            }

                            if (move_uploaded_file($file_tmp, '../uploads/' . $new_cover)) {
                                // Hapus cover lama jika ada
                                if (!empty($cover_image) && file_exists('../uploads/' . $cover_image)) {
                                    unlink('../uploads/' . $cover_image);
                                }
                                $cover_image = $new_cover;
                            } else {
                                $error = "Gagal memindahkan file cover baru ke direktori upload.";
                            }
                        }
                    }
                }
            }

            if (empty($error)) {
                try {
                    $stmtUpdate = $pdo->prepare("UPDATE ekskul_categories SET name = ?, slug = ?, cover_image = ? WHERE id = ?");
                    $stmtUpdate->execute([$name, $slug, $cover_image, $id]);
                    header("Location: list.php?msg=edit_success");
                    exit();
                } catch (PDOException $e) {
                    $error = "Gagal memperbarui kategori: " . $e->getMessage();
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
    <title>Edit Kategori Ekskul - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Edit Kategori Ekskul</h4>
                    <p class="text-muted small mb-0">Ubah folder atau cover kategori</p>
                </div>
                <a href="list.php" class="btn btn-outline-secondary py-2 px-3"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card border-0 rounded-4 shadow-sm p-4" style="max-width: 600px;">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold text-secondary">Nama Kategori</label>
                        <input type="text" class="form-control bg-light border-0 py-2 px-3 rounded-3" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="slug" class="form-label fw-semibold text-secondary">Slug URL</label>
                        <input type="text" class="form-control bg-light border-0 py-2 px-3 rounded-3" id="slug" name="slug" value="<?= htmlspecialchars($slug) ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Foto Cover Folder</label>
                        <?php if (!empty($current_cover)): ?>
                            <div class="mb-3 d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                                <img src="../uploads/<?= htmlspecialchars($current_cover) ?>" alt="Cover Saat Ini" class="rounded shadow-sm" style="width: 90px; height: 65px; object-fit: cover;">
                                <div>
                                    <span class="d-block small text-muted mb-1">Cover saat ini aktif</span>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remove_cover" id="removeCover">
                                        <label class="form-check-label text-danger small" for="removeCover">Hapus cover ini</label>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="border border-2 border-dashed border-secondary border-opacity-25 rounded-3 p-3 bg-light text-center mb-2" style="min-height: 120px; cursor: pointer;" onclick="document.getElementById('coverInput').click();">
                            <img id="coverPreview" src="#" alt="Preview" class="img-fluid rounded mb-2 d-none" style="max-height: 100px; object-fit: cover;">
                            <div id="uploadPlaceholder" class="text-muted py-2">
                                <i class="fa-regular fa-image fa-2x mb-1 text-secondary text-opacity-50"></i>
                                <p class="small mb-0"><?= !empty($current_cover) ? 'Klik untuk mengganti cover' : 'Klik untuk memilih cover' ?></p>
                            </div>
                        </div>
                        <input type="file" id="coverInput" name="cover_image" class="d-none" accept="image/*">
                        <small class="text-muted">Format JPG, JPEG, PNG, WEBP (maksimal 2MB).</small>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 py-2.5 rounded-3 fw-bold"><i class="fa-solid fa-floppy-disk me-2"></i> Simpan Perubahan</button>
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
