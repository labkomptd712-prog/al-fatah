<?php
// admin/footer-links/add.php
require_once '../includes/auth.php';
require_admin_role('admin'); // Only admin and superadmin
require_once '../config/db.php';

$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'] ?? '';
    $title = trim($_POST['title'] ?? '');
    $link_type = $_POST['link_type'] ?? 'none';
    $external_url = trim($_POST['external_url'] ?? '');
    $display_order = intval($_POST['display_order'] ?? 0);

    if ($category === '' || $title === '') {
        $error_msg = 'Kategori dan Judul Menu wajib diisi!';
    } else {
        $file_path = null;
        $db_external_url = null;

        // Process based on link type
        if ($link_type === 'pdf') {
            if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['file_pdf'];
                $tmp_name = $file['tmp_name'];
                $orig_name = $file['name'];

                // Validate extension
                $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
                if ($ext !== 'pdf') {
                    $error_msg = 'Hanya menerima berkas berformat PDF!';
                } else {
                    // Validate actual MIME Type
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $tmp_name);
                    finfo_close($finfo);

                    if ($mime !== 'application/pdf') {
                        $error_msg = 'Berkas terdeteksi bukan berkas PDF asli yang valid!';
                    } else {
                        // Ensure upload dir exists
                        $upload_dir = '../uploads/footer-docs/';
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }

                        // Generate unique file name
                        $safe_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', $orig_name);
                        if (move_uploaded_file($tmp_name, $upload_dir . $safe_name)) {
                            $file_path = $safe_name;
                        } else {
                            $error_msg = 'Gagal memindahkan berkas unggahan ke folder server.';
                        }
                    }
                }
            } else {
                $error_msg = 'Anda memilih tipe PDF, harap unggah berkas PDF yang valid!';
            }
        } elseif ($link_type === 'url') {
            if ($external_url === '') {
                $error_msg = 'Anda memilih tipe URL, harap isi kolom URL eksternal!';
            } else {
                $db_external_url = $external_url;
            }
        }

        // If no errors, insert into DB
        if ($error_msg === '') {
            try {
                $stmt = $pdo->prepare("INSERT INTO footer_links (category, title, file_path, external_url, display_order) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$category, $title, $file_path, $db_external_url, $display_order]);
                header("Location: list.php?msg=add_success");
                exit();
            } catch (PDOException $e) {
                // Remove file if database insertion fails
                if ($file_path && file_exists('../uploads/footer-docs/' . $file_path)) {
                    unlink('../uploads/footer-docs/' . $file_path);
                }
                $error_msg = 'Database Error: ' . $e->getMessage();
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
    <title>Tambah Tautan Footer - SDIT Al Fatah</title>
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
            <div class="main-header mb-4">
                <h4 class="fw-bold text-dark mb-1">Tambah Tautan Footer Baru</h4>
                <p class="text-muted small mb-0">Tambahkan dokumen PDF atau link eksternal baru ke dalam menu footer website</p>
            </div>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> <?= htmlspecialchars($error_msg) ?>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <form action="add.php" method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="category" class="form-label fw-semibold">Kategori Menu Footer <span class="text-danger">*</span></label>
                            <select name="category" id="category" class="form-select bg-light border-0 py-2.5 px-3 rounded-3" required>
                                <option value="" disabled selected>-- Pilih Kategori --</option>
                                <option value="layanan_kepegawaian">Layanan Kepegawaian (Kiri)</option>
                                <option value="tautan">Tautan (Tengah)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="title" class="form-label fw-semibold">Judul Menu / Nama Tautan <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control bg-light border-0 py-2.5 px-3 rounded-3" placeholder="Contoh: Kalender Akademik" required value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="display_order" class="form-label fw-semibold">Urutan Tampil (No. Urut)</label>
                            <input type="number" name="display_order" id="display_order" class="form-control bg-light border-0 py-2.5 px-3 rounded-3" placeholder="Contoh: 1" value="<?= htmlspecialchars($_POST['display_order'] ?? '0') ?>">
                            <small class="text-muted">Semakin kecil angkanya, semakin atas tampilannya.</small>
                        </div>

                        <div class="col-12 mt-4">
                            <label class="form-label fw-semibold d-block mb-3">Tipe Tautan & Dokumen</label>
                            
                            <div class="form-check form-check-inline me-4">
                                <input class="form-check-input" type="radio" name="link_type" id="type_none" value="none" <?= (!isset($_POST['link_type']) || $_POST['link_type'] === 'none') ? 'checked' : '' ?> onclick="toggleInputs()">
                                <label class="form-check-label fw-medium" for="type_none">Teks Saja (Belum tersedia / Non-aktif)</label>
                            </div>

                            <div class="form-check form-check-inline me-4">
                                <input class="form-check-input" type="radio" name="link_type" id="type_pdf" value="pdf" <?= (($_POST['link_type'] ?? '') === 'pdf') ? 'checked' : '' ?> onclick="toggleInputs()">
                                <label class="form-check-label fw-medium" for="type_pdf">Unggah Berkas PDF</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="link_type" id="type_url" value="url" <?= (($_POST['link_type'] ?? '') === 'url') ? 'checked' : '' ?> onclick="toggleInputs()">
                                <label class="form-check-label fw-medium" for="type_url">Alamat URL Eksternal</label>
                            </div>
                        </div>

                        <!-- PDF Input Container -->
                        <div id="pdf_container" class="col-md-12 mt-3" style="display: none;">
                            <div class="p-3 border border-dashed rounded-3 bg-light">
                                <label for="file_pdf" class="form-label fw-semibold">Pilih Berkas PDF <span class="text-danger">*</span></label>
                                <input type="file" name="file_pdf" id="file_pdf" class="form-control bg-white" accept="application/pdf">
                                <small class="text-muted mt-2 d-block"><i class="fa-solid fa-circle-info me-1"></i> Hanya menerima berkas berkstensi <strong>.pdf</strong> dengan ukuran maksimal 10MB.</small>
                            </div>
                        </div>

                        <!-- URL Input Container -->
                        <div id="url_container" class="col-md-12 mt-3" style="display: none;">
                            <div class="p-3 border border-dashed rounded-3 bg-light">
                                <label for="external_url" class="form-label fw-semibold">Alamat URL Eksternal <span class="text-danger">*</span></label>
                                <input type="url" name="external_url" id="external_url" class="form-control bg-white py-2.5 px-3 rounded-3" placeholder="https://contoh.com/halaman" value="<?= htmlspecialchars($_POST['external_url'] ?? '') ?>">
                                <small class="text-muted mt-2 d-block"><i class="fa-solid fa-circle-info me-1"></i> Masukkan tautan lengkap, diawali dengan <strong>http://</strong> atau <strong>https://</strong>.</small>
                            </div>
                        </div>

                        <div class="col-12 mt-4 pt-2">
                            <button type="submit" class="btn btn-brand py-2.5 px-4 rounded-3 me-2"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Tautan</button>
                            <a href="list.php" class="btn btn-light py-2.5 px-4 rounded-3 text-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleInputs() {
            var typeNone = document.getElementById('type_none');
            var typePdf = document.getElementById('type_pdf');
            var typeUrl = document.getElementById('type_url');
            
            var pdfContainer = document.getElementById('pdf_container');
            var urlContainer = document.getElementById('url_container');
            
            var fileInput = document.getElementById('file_pdf');
            var urlInput = document.getElementById('external_url');

            if (typePdf.checked) {
                pdfContainer.style.display = 'block';
                urlContainer.style.display = 'none';
                fileInput.required = true;
                urlInput.required = false;
            } else if (typeUrl.checked) {
                pdfContainer.style.display = 'none';
                urlContainer.style.display = 'block';
                fileInput.required = false;
                urlInput.required = true;
            } else {
                pdfContainer.style.display = 'none';
                urlContainer.style.display = 'none';
                fileInput.required = false;
                urlInput.required = false;
            }
        }

        // Run on load to restore state if errors occurred
        window.onload = function() {
            toggleInputs();
        };
    </script>
</body>
</html>
