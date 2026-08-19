<?php
// admin/prestasi/list.php
require_once '../includes/auth.php';
require_role('editor'); // Boleh diakses editor, admin, dan superadmin
require_once '../config/db.php';

$error = '';
$message = $_GET['msg'] ?? '';
$err_msg = $_GET['err'] ?? '';
$preselected_cat = intval($_GET['category_id'] ?? 0);

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
                    $image_name = 'prestasi_' . time() . '_' . uniqid() . '.' . $ext;

                    // Ensure folder exists
                    if (!is_dir('../uploads')) {
                        mkdir('../uploads', 0755, true);
                    }

                    if (move_uploaded_file($file_tmp, '../uploads/' . $image_name)) {
                        try {
                            $stmt = $pdo->prepare("INSERT INTO prestasi (nama_siswa, jenis_lomba, keterangan, foto, category_id) VALUES (?, ?, ?, ?, ?)");
                            $stmt->execute([$nama_siswa, $jenis_lomba, $keterangan, $image_name, $category_id]);

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

// Fetch all categories for the dropdown
try {
    $stmtCategories = $pdo->query("SELECT id, name FROM prestasi_categories ORDER BY name ASC");
    $categories = $stmtCategories->fetchAll();
} catch (PDOException $e) {
    $categories = [];
}

// Fetch all achievements
try {
    $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM prestasi p LEFT JOIN prestasi_categories c ON p.category_id = c.id ORDER BY p.created_at DESC");
    $prestasi_list = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Prestasi - SDIT Al Fatah</title>
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
            background: #blank;
            border: 1px solid #f1f1f1;
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
                    <h4 class="fw-bold text-dark mb-1">Daftar Prestasi Siswa</h4>
                    <p class="text-muted small mb-0">Unggah dan kelola data prestasi serta penghargaan siswa sekolah</p>
                </div>
            </div>

            <!-- Notifications -->
            <?php if ($message === 'upload_success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> Prestasi baru berhasil ditambahkan!
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($message === 'delete_success'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> Data prestasi berhasil dihapus!
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
                        <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-trophy text-success me-2"></i> Tambah Prestasi Baru</h5>
                        <hr class="mt-0 mb-3 border-light">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-secondary">Pilih Foto Siswa / Penghargaan</label>
                                <div class="border border-2 border-dashed border-secondary border-opacity-25 rounded-3 p-3 bg-light text-center mb-2" style="min-height: 140px; cursor: pointer;" onclick="document.getElementById('imageInput').click();">
                                    <img id="imagePreview" src="#" alt="Preview" class="img-fluid rounded mb-2 d-none" style="max-height: 120px; object-fit: cover;">
                                    <div id="uploadPlaceholder" class="text-muted py-3">
                                        <i class="fa-regular fa-image fa-2x mb-2 text-secondary text-opacity-50"></i>
                                        <p class="small mb-0">Klik untuk memilih foto</p>
                                    </div>
                                </div>
                                <input type="file" id="imageInput" name="image" class="d-none" accept="image/*" required>
                                <small class="text-muted d-block text-start">Maksimal 2MB (JPG, JPEG, PNG, WEBP)</small>
                            </div>

                            <div class="mb-3">
                                <label for="nama_siswa" class="form-label fw-semibold text-secondary">Nama Siswa</label>
                                <input type="text" class="form-control bg-light border-0 py-2.5 px-3 rounded-3" id="nama_siswa" name="nama_siswa" placeholder="Contoh: Ahmad Fauzi" required>
                            </div>

                            <div class="mb-3">
                                <label for="jenis_lomba" class="form-label fw-semibold text-secondary">Jenis Lomba / Kategori</label>
                                <input type="text" class="form-control bg-light border-0 py-2.5 px-3 rounded-3" id="jenis_lomba" name="jenis_lomba" placeholder="Contoh: Olimpiade Matematika" required>
                            </div>

                            <div class="mb-3">
                                <label for="category_id" class="form-label fw-semibold text-secondary">Kategori Prestasi (Folder)</label>
                                <select name="category_id" id="category_id" class="form-select bg-light border-0 py-2.5 px-3 rounded-3" required>
                                    <option value="" disabled <?= $preselected_cat === 0 ? 'selected' : '' ?>>-- Pilih Kategori --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= (int) $cat['id'] ?>" <?= $preselected_cat === (int)$cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="keterangan" class="form-label fw-semibold text-secondary">Keterangan Prestasi / Juara</label>
                                <textarea class="form-control bg-light border-0 py-2 px-3 rounded-3" id="keterangan" name="keterangan" rows="3" placeholder="Contoh: Juara 1 Tingkat Kecamatan Aren Jaya"></textarea>
                            </div>

                            <button type="submit" class="btn btn-brand w-100 py-2.5 rounded-3 fw-bold"><i class="fa-solid fa-plus me-2"></i> Simpan Prestasi</button>
                        </form>
                    </div>
                </div>

                <!-- Right panel: Photos Grid -->
                <div class="col-lg-8">
                    <div class="card border-0 rounded-4 shadow-sm p-4">
                        <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-trophy text-success me-2"></i> Koleksi Prestasi Siswa</h5>
                        
                        <?php if (empty($prestasi_list)): ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fa-solid fa-trophy fa-4x mb-3 text-secondary text-opacity-30"></i>
                                <p class="mb-0">Belum ada prestasi yang ditambahkan. Mulai unggah di panel sebelah kiri.</p>
                            </div>
                        <?php else: ?>
                            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
                                <?php foreach ($prestasi_list as $photo): ?>
                                    <div class="col">
                                        <div class="gallery-card bg-white rounded-3 shadow-sm border border-light d-flex flex-column h-100">
                                            <div class="gallery-img-wrapper">
                                                <?php if (file_exists('../uploads/' . $photo['foto'])): ?>
                                                    <img src="../uploads/<?= htmlspecialchars($photo['foto']) ?>" alt="Prestasi Image" class="gallery-img">
                                                <?php else: ?>
                                                    <div class="d-flex align-items-center justify-content-center h-100 bg-secondary text-white small">File Hilang</div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="p-3 d-flex flex-column flex-grow-1">
                                                <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-2.5 py-1 mb-2 d-inline-block alignment-self-start" style="font-size: 10px; font-weight: 600; width: fit-content;">
                                                    <i class="fa-solid fa-folder me-1"></i> <?= htmlspecialchars($photo['category_name'] ?? 'Umum') ?>
                                                </span>
                                                <h6 class="fw-bold text-dark mb-1" style="font-size:14px;"><?= htmlspecialchars($photo['nama_siswa']) ?></h6>
                                                <span class="text-success small d-block mb-2 fw-medium" style="font-size:12px;"><?= htmlspecialchars($photo['jenis_lomba']) ?></span>
                                                
                                                <p class="gallery-caption mb-2 text-muted" style="font-size:12px; line-height:1.5;">
                                                    <?= !empty($photo['keterangan']) ? htmlspecialchars($photo['keterangan']) : '<em class="text-muted small">Tanpa keterangan</em>' ?>
                                                </p>
                                                <div class="d-flex justify-content-between align-items-center mt-auto border-top pt-2">
                                                    <span class="text-muted" style="font-size: 11px;"><i class="fa-regular fa-clock me-1"></i> <?= date('d/m/Y', strtotime($photo['created_at'])) ?></span>
                                                    <?php if (!is_editor_role()): ?>
                                                    <form action="delete.php" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data prestasi ini?')">
                                                        <input type="hidden" name="id" value="<?= $photo['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-1 px-2" title="Hapus Prestasi">
                                                            <i class="fa-solid fa-trash-can"></i> Hapus
                                                        </button>
                                                    </form>
                                                    <?php endif; ?>
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
