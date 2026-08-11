<?php
// admin/facilities/edit.php
require_once '../includes/auth.php';
require_role('editor'); // Boleh diakses editor, admin, dan superadmin
require_once '../config/db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: list.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM facilities WHERE id = ?");
    $stmt->execute([$id]);
    $facility = $stmt->fetch();
} catch (PDOException $e) {
    die("Error fetching facility: " . $e->getMessage());
}

if (!$facility) {
    header("Location: list.php?err=" . urlencode("Fasilitas tidak ditemukan."));
    exit();
}

$error = '';
$name = $facility['name'];
$description = $facility['description'];
$category_id = $facility['category_id'];
$display_order = $facility['display_order'];
$existing_image = $facility['image'];

// Fetch categories for dropdown
try {
    $stmtCat = $pdo->query("SELECT id, name FROM facility_categories ORDER BY name ASC");
    $categories = $stmtCat->fetchAll();
} catch (PDOException $e) {
    $categories = [];
    $error = "Gagal memuat kategori: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $display_order = intval($_POST['display_order'] ?? 0);

    if ($name === '') {
        $error = "Nama fasilitas wajib diisi!";
    } elseif ($category_id <= 0) {
        $error = "Pilih kategori fasilitas yang valid!";
    } else {
        $image_name = $existing_image;
        
        // Upload file image baru jika ada
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['image']['tmp_name'];
            $file_name = $_FILES['image']['name'];
            $file_size = $_FILES['image']['size'];
            
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
                    $image_name = 'facility_' . time() . '_' . uniqid() . '.' . $ext;
                    if (!is_dir('../uploads')) {
                        mkdir('../uploads', 0755, true);
                    }
                    if (move_uploaded_file($file_tmp, '../uploads/' . $image_name)) {
                        // Hapus image lama jika ada
                        if (!empty($existing_image) && file_exists('../uploads/' . $existing_image)) {
                            unlink('../uploads/' . $existing_image);
                        }
                    } else {
                        $error = "Gagal memindahkan file gambar.";
                        $image_name = $existing_image;
                    }
                }
            }
        }

        if (empty($error)) {
            try {
                $stmtUpdate = $pdo->prepare("UPDATE facilities SET category_id = ?, name = ?, description = ?, image = ?, display_order = ? WHERE id = ?");
                $stmtUpdate->execute([$category_id, $name, $description, $image_name, $display_order, $id]);
                header("Location: list.php?msg=edit_success");
                exit();
            } catch (PDOException $e) {
                $error = "Gagal menyimpan perubahan: " . $e->getMessage();
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
    <title>Edit Fasilitas - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Edit Fasilitas</h4>
                    <p class="text-muted small mb-0">Ubah detail item sarana prasarana sekolah</p>
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
                        <label for="name" class="form-label fw-semibold text-secondary">Nama Fasilitas</label>
                        <input type="text" class="form-control bg-light border-0 py-2.5 px-3 rounded-3" id="name" name="name" value="<?= htmlspecialchars($name) ?>" placeholder="Contoh: Perpustakaan Modern" required>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label fw-semibold text-secondary">Kategori Fasilitas</label>
                        <select name="category_id" id="category_id" class="form-select bg-light border-0 py-2.5 px-3 rounded-3" required>
                            <option value="" disabled>-- Pilih Kategori --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= (int)$category_id === (int)$cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="display_order" class="form-label fw-semibold text-secondary">Urutan Tampilan</label>
                        <input type="number" class="form-control bg-light border-0 py-2.5 px-3 rounded-3" id="display_order" name="display_order" value="<?= htmlspecialchars($display_order ?? '') ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label fw-semibold text-secondary">Deskripsi (Opsional)</label>
                        <textarea class="form-control bg-light border-0 py-2 px-3 rounded-3" id="description" name="description" rows="4" placeholder="Keterangan singkat mengenai fasilitas..."><?= htmlspecialchars($description ?? '') ?></textarea>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Gambar Fasilitas (Opsional)</label>
                        <div class="border border-2 border-dashed border-secondary border-opacity-25 rounded-3 p-3 bg-light text-center mb-2" style="min-height: 150px; cursor: pointer;" onclick="document.getElementById('imageInput').click();">
                            <?php if (!empty($existing_image) && file_exists('../uploads/' . $existing_image)): ?>
                                <img id="imagePreview" src="../uploads/<?= htmlspecialchars($existing_image ?? '') ?>" alt="Preview" class="img-fluid rounded mb-2" style="max-height: 120px; object-fit: cover;">
                                <div id="uploadPlaceholder" class="text-muted py-3 d-none">
                                    <i class="fa-regular fa-image fa-2x mb-2 text-secondary text-opacity-50"></i>
                                    <p class="small mb-0">Klik untuk memilih gambar</p>
                                </div>
                            <?php else: ?>
                                <img id="imagePreview" src="#" alt="Preview" class="img-fluid rounded mb-2 d-none" style="max-height: 120px; object-fit: cover;">
                                <div id="uploadPlaceholder" class="text-muted py-3">
                                    <i class="fa-regular fa-image fa-2x mb-2 text-secondary text-opacity-50"></i>
                                    <p class="small mb-0">Klik untuk memilih gambar</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <input type="file" id="imageInput" name="image" class="d-none" accept="image/*">
                        <small class="text-muted">Maksimal 2MB (JPG, JPEG, PNG, WEBP)</small>
                    </div>

                    <button type="submit" class="btn btn-brand w-100 py-2.5 rounded-3 fw-bold"><i class="fa-solid fa-floppy-disk me-2"></i> Simpan Perubahan</button>
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
                    if (placeholder) placeholder.classList.add('d-none');
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    </script>
</body>
</html>
