<?php
// admin/gallery/list.php
require_once '../includes/auth.php';
require_once '../config/db.php';

$error = '';
$message = $_GET['msg'] ?? '';
$err_msg = $_GET['err'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caption = trim($_POST['caption'] ?? '');

    if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        $error = "Silakan pilih foto untuk diupload.";
    } else {
        $file = $_FILES['image'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_size = $file['size'];
        $file_error = $file['error'];

        if ($file_error !== UPLOAD_ERR_OK) {
            $error = "Terjadi kesalahan saat mengunggah foto.";
        } else {
            // Validate using getimagesize()
            $check = getimagesize($file_tmp);
            if ($check === false) {
                $error = "File yang diupload bukan gambar valid!";
            } else {
                $mime = $check['mime'];
                $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($mime, $allowed_mimes)) {
                    $error = "Hanya file gambar JPG, JPEG, PNG, GIF, dan WEBP yang diperbolehkan.";
                } elseif ($file_size > 2 * 1024 * 1024) { // Limit 2MB
                    $error = "Ukuran gambar terlalu besar! Maksimal 2MB.";
                } else {
                    // Generate unique filename
                    $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                    $image_name = 'gallery_' . time() . '_' . uniqid() . '.' . $ext;

                    // Ensure folder exists
                    if (!is_dir('../uploads')) {
                        mkdir('../uploads', 0755, true);
                    }

                    if (move_uploaded_file($file_tmp, '../uploads/' . $image_name)) {
                        try {
                            $stmt = $pdo->prepare("INSERT INTO gallery (caption, image) VALUES (?, ?)");
                            $stmt->execute([$caption, $image_name]);

                            header("Location: list.php?msg=upload_success");
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

// Fetch all gallery items
try {
    $stmt = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC");
    $gallery_list = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Galeri - SDIT Al Fatah</title>
    <!-- Bootstrap 5 CSS -->
    <link href="../../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom Admin CSS -->
    <link href="../../assets/css/admin.css" rel="stylesheet">
    <style>
        .gallery-card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            background: #fff;
        }
        .gallery-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .gallery-img-wrapper {
            position: relative;
            width: 100%;
            height: 180px;
            overflow: hidden;
            background-color: #f3f4f6;
        }
        .gallery-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .gallery-card:hover .gallery-img {
            transform: scale(1.08);
        }
        .gallery-caption {
            font-size: 13px;
            color: #4b5563;
            min-height: 40px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body>

    <div class="admin-container">
        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="main-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Galeri Foto</h4>
                    <p class="text-muted small mb-0">Unggah dan kelola foto dokumentasi kegiatan sekolah</p>
                </div>
            </div>

            <!-- Notifications -->
            <?php if ($message === 'upload_success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> Foto baru berhasil diunggah!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($message === 'delete_success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> Foto berhasil dihapus!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($err_msg)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= htmlspecialchars($err_msg) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Left panel: Upload Form -->
                <div class="col-lg-4">
                    <div class="card border-0 rounded-4 shadow-sm p-4 sticky-lg-top" style="top: 30px; z-index: 10;">
                        <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-cloud-arrow-up text-success me-2"></i> Unggah Foto Baru</h5>
                        <hr class="mt-0 mb-3 border-light">
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
                                <small class="text-muted d-block text-start">Maksimal 2MB (JPG, JPEG, PNG, WEBP)</small>
                            </div>

                            <div class="mb-4">
                                <label for="caption" class="form-label fw-semibold text-secondary">Keterangan / Caption</label>
                                <textarea class="form-control bg-light border-0 py-2 px-3 rounded-3" id="caption" name="caption" rows="3" placeholder="Masukkan deskripsi singkat tentang foto ini..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-brand w-100 py-2.5 rounded-3 fw-bold"><i class="fa-solid fa-upload me-2"></i> Unggah Sekarang</button>
                        </form>
                    </div>
                </div>

                <!-- Right panel: Photos Grid -->
                <div class="col-lg-8">
                    <div class="card border-0 rounded-4 shadow-sm p-4">
                        <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-images text-success me-2"></i> Koleksi Galeri</h5>
                        
                        <?php if (empty($gallery_list)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fa-regular fa-images fa-4x mb-3 text-secondary text-opacity-30"></i>
                                <p class="mb-0">Belum ada foto di galeri. Mulai unggah di panel sebelah kiri.</p>
                            </div>
                        <?php else: ?>
                            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                                <?php foreach ($gallery_list as $photo): ?>
                                    <div class="col">
                                        <div class="gallery-card">
                                            <div class="gallery-img-wrapper">
                                                <?php if (file_exists('../uploads/' . $photo['image'])): ?>
                                                    <img src="../uploads/<?= htmlspecialchars($photo['image']) ?>" alt="Gallery Image" class="gallery-img">
                                                <?php else: ?>
                                                    <div class="d-flex align-items-center justify-content-center h-100 bg-secondary text-white small">File Hilang</div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="p-3">
                                                <p class="gallery-caption mb-2 fw-medium">
                                                    <?= !empty($photo['caption']) ? htmlspecialchars($photo['caption']) : '<em class="text-muted small">Tanpa keterangan</em>' ?>
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center mt-2 border-top pt-2">
                                                    <span class="text-muted" style="font-size: 11px;"><i class="fa-regular fa-clock me-1"></i> <?= date('d/m/Y', strtotime($photo['created_at'])) ?></span>
                                                    <form action="delete.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto ini?')">
                                                        <input type="hidden" name="id" value="<?= $photo['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1 px-2" title="Hapus Foto">
                                                            <i class="fa-solid fa-trash-can"></i> Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Upload Image Preview Script -->
    <script>
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');

        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.classList.remove('d-none');
                    uploadPlaceholder.classList.add('d-none');
                }
                reader.readAsDataURL(file);
            } else {
                imagePreview.src = '#';
                imagePreview.classList.add('d-none');
                uploadPlaceholder.classList.remove('d-none');
            }
        });
    </script>
</body>
</html>
