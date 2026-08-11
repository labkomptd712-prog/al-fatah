<?php
// admin/facilities/photos.php
require_once '../includes/auth.php';
require_role('editor'); // Boleh diakses editor, admin, dan superadmin
require_once '../config/db.php';

$facility_id = intval($_GET['facility_id'] ?? 0);
if ($facility_id <= 0) {
    header("Location: list.php");
    exit();
}

// Ambil info fasilitas
try {
    $stmt = $pdo->prepare("SELECT * FROM facilities WHERE id = ?");
    $stmt->execute([$facility_id]);
    $facility = $stmt->fetch();
} catch (PDOException $e) {
    die("Error fetching facility: " . $e->getMessage());
}

if (!$facility) {
    header("Location: list.php?err=" . urlencode("Fasilitas tidak ditemukan."));
    exit();
}

$error = '';
$success = '';

// Proses Hapus Foto
if (isset($_POST['delete_photo'])) {
    $photo_id = intval($_POST['photo_id'] ?? 0);
    try {
        $stmtSel = $pdo->prepare("SELECT photo_path FROM facility_photos WHERE id = ? AND facility_id = ?");
        $stmtSel->execute([$photo_id, $facility_id]);
        $photo_path = $stmtSel->fetchColumn();
        
        if ($photo_path) {
            // Hapus file fisik di disk
            if (file_exists('../uploads/' . $photo_path)) {
                unlink('../uploads/' . $photo_path);
            }
            
            // Hapus record di database
            $stmtDel = $pdo->prepare("DELETE FROM facility_photos WHERE id = ?");
            $stmtDel->execute([$photo_id]);
            $success = "Foto berhasil dihapus!";
        } else {
            $error = "Foto tidak ditemukan.";
        }
    } catch (PDOException $e) {
        $error = "Gagal menghapus foto: " . $e->getMessage();
    }
}

// Proses Update Urutan
if (isset($_POST['update_orders'])) {
    $orders = $_POST['urutan'] ?? [];
    try {
        $pdo->beginTransaction();
        $stmtUp = $pdo->prepare("UPDATE facility_photos SET urutan = ? WHERE id = ? AND facility_id = ?");
        foreach ($orders as $p_id => $val) {
            $stmtUp->execute([intval($val), intval($p_id), $facility_id]);
        }
        $pdo->commit();
        $success = "Urutan foto berhasil diperbarui!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $error = "Gagal memperbarui urutan: " . $e->getMessage();
    }
}

// Proses Upload Foto (Multiple)
if (isset($_POST['upload_photos'])) {
    if (isset($_FILES['new_photos']) && !empty($_FILES['new_photos']['name'][0])) {
        $files = $_FILES['new_photos'];
        $uploaded_count = 0;
        $errors = [];

        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                $errors[] = "File '{$files['name'][$i]}' gagal diunggah (error code: {$files['error'][$i]}).";
                continue;
            }

            $file_tmp = $files['tmp_name'][$i];
            $file_name = $files['name'][$i];
            $file_size = $files['size'][$i];

            // Validasi MIME dengan getimagesize()
            $check = getimagesize($file_tmp);
            if ($check === false) {
                $errors[] = "File '{$file_name}' bukan gambar valid.";
                continue;
            }

            $mime = $check['mime'];
            $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($mime, $allowed_mimes)) {
                $errors[] = "Format '{$file_name}' harus JPG, JPEG, PNG, atau WEBP.";
                continue;
            }

            if ($file_size > 2 * 1024 * 1024) {
                $errors[] = "File '{$file_name}' melebihi batas maksimal 2MB.";
                continue;
            }

            // Generate unique filename (uniqid() + time())
            $ext = pathinfo($file_name, PATHINFO_EXTENSION);
            $unique_name = 'facility_gal_' . time() . '_' . uniqid() . '.' . $ext;

            if (!is_dir('../uploads')) {
                mkdir('../uploads', 0755, true);
            }

            if (move_uploaded_file($file_tmp, '../uploads/' . $unique_name)) {
                try {
                    // Ambil urutan terakhir
                    $stmtMax = $pdo->prepare("SELECT MAX(urutan) FROM facility_photos WHERE facility_id = ?");
                    $stmtMax->execute([$facility_id]);
                    $max_order = (int)$stmtMax->fetchColumn();

                    // Simpan ke database
                    $stmtIns = $pdo->prepare("INSERT INTO facility_photos (facility_id, photo_path, urutan) VALUES (?, ?, ?)");
                    $stmtIns->execute([$facility_id, $unique_name, $max_order + 1]);
                    $uploaded_count++;
                } catch (PDOException $e) {
                    // Hapus file kembali jika insert DB gagal
                    if (file_exists('../uploads/' . $unique_name)) {
                        unlink('../uploads/' . $unique_name);
                    }
                    $errors[] = "Gagal menyimpan data '{$file_name}' ke database: " . $e->getMessage();
                }
            } else {
                $errors[] = "Gagal memindahkan file '{$file_name}' ke folder tujuan.";
            }
        }

        if ($uploaded_count > 0) {
            $success = "{$uploaded_count} foto berhasil ditambahkan!";
        }
        if (!empty($errors)) {
            $error = implode("<br>", $errors);
        }
    } else {
        $error = "Pilih minimal satu file foto untuk diunggah.";
    }
}

// Ambil seluruh foto fasilitas
try {
    $stmtPhotos = $pdo->prepare("SELECT * FROM facility_photos WHERE facility_id = ? ORDER BY urutan ASC, created_at DESC");
    $stmtPhotos->execute([$facility_id]);
    $photos = $stmtPhotos->fetchAll();
} catch (PDOException $e) {
    die("Error fetching photos: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Foto Fasilitas - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Galeri Foto: <?= htmlspecialchars($facility['name']) ?></h4>
                    <p class="text-muted small mb-0">Kelola album detail foto pendukung sarana prasarana</p>
                </div>
                <a href="list.php" class="btn btn-outline-secondary py-2 px-3"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
            </div>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> <?= $success ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Form Upload -->
                <div class="col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm p-4">
                        <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-upload me-2 text-success"></i> Upload Foto Baru</h5>
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-secondary">Pilih File (Bisa Multiple)</label>
                                <div class="border border-2 border-dashed border-secondary border-opacity-25 rounded-3 p-3 bg-light text-center cursor-pointer" onclick="document.getElementById('photoInput').click();" style="min-height: 150px;">
                                    <div id="uploadPlaceholder" class="text-muted py-3">
                                        <i class="fa-regular fa-images fa-2x mb-2 text-secondary text-opacity-50"></i>
                                        <p class="small mb-0">Klik untuk memilih gambar-gambar</p>
                                    </div>
                                    <div id="fileList" class="small text-start d-none mt-2 text-success"></div>
                                </div>
                                <input type="file" id="photoInput" name="new_photos[]" class="d-none" accept="image/*" multiple required>
                                <small class="text-muted mt-2 d-block">Maksimal 2MB per file (JPG, JPEG, PNG, WEBP)</small>
                            </div>
                            <button type="submit" name="upload_photos" class="btn btn-brand w-100 py-2.5 rounded-3 fw-bold"><i class="fa-solid fa-cloud-arrow-up me-2"></i> Mulai Upload</button>
                        </form>
                    </div>
                </div>

                <!-- Grid Galeri -->
                <div class="col-lg-8">
                    <div class="card border-0 rounded-4 shadow-sm p-4 h-100">
                        <form action="" method="POST">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold text-dark mb-0"><i class="fa-solid fa-photo-film me-2 text-success"></i> Koleksi Foto Detail</h5>
                                <?php if (!empty($photos)): ?>
                                    <button type="submit" name="update_orders" class="btn btn-sm btn-brand px-3 py-2"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Urutan</button>
                                <?php endif; ?>
                            </div>

                            <?php if (empty($photos)): ?>
                                <div class="text-center py-5 text-muted">
                                    <i class="fa-regular fa-image fa-3x mb-3 text-secondary opacity-25"></i>
                                    <p class="mb-0">Belum ada foto detail yang diupload.</p>
                                    <p class="small text-muted">Gunakan form di sebelah kiri untuk mengunggah.</p>
                                </div>
                            <?php else: ?>
                                <div class="row row-cols-2 row-cols-md-3 g-3">
                                    <?php foreach ($photos as $p): ?>
                                        <div class="col">
                                            <div class="card h-100 border rounded-3 overflow-hidden position-relative shadow-sm">
                                                <img src="../uploads/<?= htmlspecialchars($p['photo_path']) ?>" alt="Detail" class="card-img-top w-100" style="height: 120px; object-fit: cover;">
                                                <div class="card-body p-2 bg-light">
                                                    <!-- Input Urutan -->
                                                    <div class="input-group input-group-sm mb-1">
                                                        <span class="input-group-text bg-white border-0 text-secondary" style="font-size: 11px;">Urutan</span>
                                                        <input type="number" class="form-control text-center py-0" name="urutan[<?= (int)$p['id'] ?>]" value="<?= (int)$p['urutan'] ?>" style="max-width: 60px;">
                                                    </div>
                                                </div>
                                                
                                                <!-- Tombol Hapus Pojok Atas -->
                                                <button type="submit" name="delete_photo" value="1" onclick="document.getElementById('delete_id').value='<?= (int)$p['id'] ?>'; return confirm('Apakah Anda yakin ingin menghapus foto ini?')" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 rounded-circle d-flex align-items-center justify-content-center shadow" style="width: 28px; height: 28px; padding:0;">
                                                    <i class="fa-solid fa-trash-can" style="font-size: 11px;"></i>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                            <input type="hidden" name="photo_id" id="delete_id" value="">
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('photoInput').addEventListener('change', function() {
            const placeholder = document.getElementById('uploadPlaceholder');
            const fileList = document.getElementById('fileList');
            
            if (this.files && this.files.length > 0) {
                placeholder.classList.add('d-none');
                fileList.classList.remove('d-none');
                fileList.innerHTML = `<i class="fa-solid fa-circle-check me-1"></i> ${this.files.length} file dipilih:<br>`;
                
                const ul = document.createElement('ul');
                ul.className = 'mb-0 ps-3 mt-1 text-muted small';
                for (let i = 0; i < Math.min(this.files.length, 5); i++) {
                    const li = document.createElement('li');
                    li.textContent = this.files[i].name;
                    ul.appendChild(li);
                }
                if (this.files.length > 5) {
                    const li = document.createElement('li');
                    li.textContent = `... dan ${this.files.length - 5} lainnya.`;
                    ul.appendChild(li);
                }
                fileList.appendChild(ul);
            } else {
                placeholder.classList.remove('d-none');
                fileList.classList.add('d-none');
            }
        });
    </script>
</body>
</html>
