<?php
// admin/news/add.php
require_once '../includes/auth.php';
require_once '../config/db.php';

$error = '';
$title = '';
$slug = '';
$content = '';
$is_published = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    // Generate slug if empty
    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
    }

    if (empty($title) || empty($content)) {
        $error = "Judul dan isi berita wajib diisi!";
    } else {
        $image_name = null;

        // Image upload handling
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file = $_FILES['image'];
            $file_name = $file['name'];
            $file_tmp = $file['tmp_name'];
            $file_size = $file['size'];
            $file_error = $file['error'];

            if ($file_error !== UPLOAD_ERR_OK) {
                $error = "Terjadi kesalahan saat mengupload gambar.";
            } else {
                // getimagesize validation
                $check = getimagesize($file_tmp);
                if ($check === false) {
                    $error = "File yang diupload bukan gambar valid!";
                } else {
                    $mime = $check['mime'];
                    $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!in_array($mime, $allowed_mimes)) {
                        $error = "Hanya file JPG, JPEG, PNG, GIF, dan WEBP yang diperbolehkan.";
                    } elseif ($file_size > 2 * 1024 * 1024) { // Limit 2MB
                        $error = "Ukuran gambar terlalu besar! Maksimal 2MB.";
                    } else {
                        // Generate unique file name
                        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                        $image_name = time() . '_' . uniqid() . '.' . $ext;

                        if (!move_uploaded_file($file_tmp, '../uploads/' . $image_name)) {
                            $error = "Gagal memindahkan file yang diupload.";
                            $image_name = null;
                        }
                    }
                }
            }
        }

        if (empty($error)) {
            try {
                // Ensure slug is unique
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM news WHERE slug = ?");
                $stmt->execute([$slug]);
                if ($stmt->fetchColumn() > 0) {
                    $slug = $slug . '-' . time();
                }

                // Insert into news table
                $stmt = $pdo->prepare("INSERT INTO news (title, slug, content, image, is_published) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$title, $slug, $content, $image_name, $is_published]);

                if (is_editor_role()) {
                    header("Location: ../dashboard.php?msg=news_add_success");
                } else {
                    header("Location: list.php?msg=add_success");
                }
                exit();
            } catch (PDOException $e) {
                $error = "Gagal menyimpan ke database: " . $e->getMessage();
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
    <title>Tambah Berita - SDIT Al Fatah</title>
    <!-- Bootstrap 5 CSS -->
    <link href="../../assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom Admin CSS -->
    <link href="../../assets/css/admin.css" rel="stylesheet">
</head>
<body>

    <div class="admin-container">
        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="main-content">
            <div class="main-header d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold text-dark mb-1">Tambah Berita</h4>
                    <p class="text-muted small mb-0">Tulis dan terbitkan artikel atau pengumuman sekolah baru</p>
                </div>
                <div>
                    <a href="list.php" class="btn btn-outline-secondary py-2 px-3"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
                </div>
            </div>

            <!-- Error Alerts -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Form Card -->
            <div class="card border-0 rounded-4 shadow-sm p-4">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Left column: inputs -->
                        <div class="col-lg-8">
                            <div class="mb-3">
                                <label for="title" class="form-label fw-semibold text-secondary">Judul Berita</label>
                                <input type="text" class="form-control bg-light border-0 py-2.5 rounded-3" id="title" name="title" value="<?= htmlspecialchars($title) ?>" placeholder="Masukkan judul berita" required autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="slug" class="form-label fw-semibold text-secondary">Slug (URL friendly)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0 text-muted">/news/</span>
                                    <input type="text" class="form-control bg-light border-0 py-2.5 rounded-end-3" id="slug" name="slug" value="<?= htmlspecialchars($slug) ?>" placeholder="judul-berita-otomatis" readonly>
                                </div>
                                <small class="text-muted">Slug di-generate otomatis dari judul untuk optimasi URL.</small>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label fw-semibold text-secondary">Isi / Konten Berita</label>
                                <textarea class="form-control bg-light border-0 p-3 rounded-3" id="content" name="content" rows="12" placeholder="Tulis isi berita lengkap di sini..." required><?= htmlspecialchars($content) ?></textarea>
                            </div>
                        </div>

                        <!-- Right column: file upload and publishing -->
                        <div class="col-lg-4">
                            <!-- Image upload card -->
                            <div class="card border-light bg-light rounded-4 mb-4">
                                <div class="card-body p-4 text-center">
                                    <label class="form-label fw-semibold text-secondary d-block text-start mb-3">Gambar Cover</label>
                                    <div class="mb-3">
                                        <div class="border border-2 border-dashed border-secondary border-opacity-25 rounded-3 p-3 bg-white d-flex flex-column align-items-center justify-content-center" style="min-height: 180px;">
                                            <img id="imagePreview" src="#" alt="Pratinjau Gambar" class="img-fluid rounded mb-2 d-none" style="max-height: 150px; object-fit: cover;">
                                            <div id="uploadPlaceholder" class="text-muted py-4">
                                                <i class="fa-regular fa-image fa-3x mb-2 text-secondary text-opacity-50"></i>
                                                <p class="small mb-0">Klik tombol di bawah untuk memilih file</p>
                                            </div>
                                        </div>
                                    </div>
                                    <input class="form-control form-control-sm border-0 bg-white" type="file" id="imageInput" name="image" accept="image/*">
                                    <small class="text-muted d-block mt-2">Maks. 2MB (JPG, JPEG, PNG, WEBP)</small>
                                </div>
                            </div>

                            <!-- Publish card -->
                            <div class="card border-light bg-light rounded-4 mb-4">
                                <div class="card-body p-4">
                                    <label class="form-label fw-semibold text-secondary d-block mb-3">Status Publikasi</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="is_published" name="is_published" value="1" <?= ($is_published == 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label fw-medium" for="is_published">Terbitkan Berita</label>
                                    </div>
                                    <small class="text-muted d-block mt-2">Aktifkan untuk menampilkan berita langsung di website utama.</small>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-brand w-100 py-3 rounded-3 fw-bold"><i class="fa-solid fa-floppy-disk me-2"></i> Simpan Berita</button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- Auto Slug and Preview Script -->
    <script>
        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');
        const imageInput = document.getElementById('imageInput');
        const imagePreview = document.getElementById('imagePreview');
        const uploadPlaceholder = document.getElementById('uploadPlaceholder');

        // Auto Slug
        titleInput.addEventListener('input', function() {
            let titleVal = this.value;
            let slugVal = titleVal.toLowerCase()
                                 .replace(/[^a-z0-9\s-]/g, '') // remove invalid chars
                                 .replace(/\s+/g, '-')         // collapse whitespace and replace by -
                                 .replace(/-+/g, '-')          // collapse dashes
                                 .trim();
            slugInput.value = slugVal;
        });

        // Image Preview
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
