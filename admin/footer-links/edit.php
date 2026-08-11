<?php
// admin/footer-links/edit.php
require_once '../includes/auth.php';
require_admin_role('admin'); // Only admin and superadmin
require_once '../config/db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: list.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM footer_links WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $link = $stmt->fetch();
    if (!$link) {
        header("Location: list.php");
        exit();
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}

$error_msg = '';

// Determine current link type
$current_link_type = 'none';
if (!empty($link['file_path'])) {
    $current_link_type = 'pdf';
} elseif (!empty($link['external_url'])) {
    $current_link_type = 'url';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $_POST['category'] ?? '';
    $title = trim($_POST['title'] ?? '');
    $link_type = $_POST['link_type'] ?? 'none';
    $external_url = trim($_POST['external_url'] ?? '');
    $display_order = intval($_POST['display_order'] ?? 0);

    if ($category === '' || $title === '') {
        $error_msg = 'Kategori dan Judul Menu wajib diisi!';
    } else {
        $file_path = $link['file_path'];
        $db_external_url = null;

        // Process based on new link type selection
        if ($link_type === 'pdf') {
            // Check if user uploaded a new PDF file
            if (isset($_FILES['file_pdf']) && $_FILES['file_pdf']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['file_pdf'];
                $tmp_name = $file['tmp_name'];
                $orig_name = $file['name'];

                $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION));
                if ($ext !== 'pdf') {
                    $error_msg = 'Hanya menerima berkas berformat PDF!';
                } else {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime = finfo_file($finfo, $tmp_name);
                    finfo_close($finfo);

                    if ($mime !== 'application/pdf') {
                        $error_msg = 'Berkas terdeteksi bukan berkas PDF asli yang valid!';
                    } else {
                        $upload_dir = '../uploads/footer-docs/';
                        if (!is_dir($upload_dir)) {
                            mkdir($upload_dir, 0755, true);
                        }

                        // Generate unique file name
                        $safe_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '', $orig_name);
                        if (move_uploaded_file($tmp_name, $upload_dir . $safe_name)) {
                            // Delete old PDF file if it existed
                            if (!empty($link['file_path']) && file_exists($upload_dir . $link['file_path'])) {
                                unlink($upload_dir . $link['file_path']);
                            }
                            $file_path = $safe_name;
                            $db_external_url = null; // Clear external url
                        } else {
                            $error_msg = 'Gagal memindahkan berkas unggahan ke folder server.';
                        }
                    }
                }
            } else {
                // Kept current PDF file, but ensure external_url is cleared
                $db_external_url = null;
                if (empty($file_path)) {
                    $error_msg = 'Harap unggah berkas PDF yang valid!';
                }
            }
        } elseif ($link_type === 'url') {
            if ($external_url === '') {
                $error_msg = 'Harap isi kolom URL eksternal!';
            } else {
                $db_external_url = $external_url;
                // Delete old PDF file from disk if it changes type
                if (!empty($link['file_path']) && file_exists('../uploads/footer-docs/' . $link['file_path'])) {
                    unlink('../uploads/footer-docs/' . $link['file_path']);
                }
                $file_path = null; // Clear file path in DB
            }
        } else {
            // Tipe None (Teks Saja)
            // Delete old PDF file from disk if type changes to None
            if (!empty($link['file_path']) && file_exists('../uploads/footer-docs/' . $link['file_path'])) {
                unlink('../uploads/footer-docs/' . $link['file_path']);
            }
            $file_path = null;
            $db_external_url = null;
        }

        // Save changes to DB
        if ($error_msg === '') {
            try {
                $stmt = $pdo->prepare("UPDATE footer_links SET category = ?, title = ?, file_path = ?, external_url = ?, display_order = ? WHERE id = ?");
                $stmt->execute([$category, $title, $file_path, $db_external_url, $display_order, $id]);
                header("Location: list.php?msg=edit_success");
                exit();
            } catch (PDOException $e) {
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
    <title>Edit Tautan Footer - SDIT Al Fatah</title>
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
                <h4 class="fw-bold text-dark mb-1">Edit Tautan Footer</h4>
                <p class="text-muted small mb-0">Ubah dokumen PDF, judul, urutan tampil, atau URL tautan footer sekolah</p>
            </div>

            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i> <?= htmlspecialchars($error_msg) ?>
                </div>
            <?php endif; ?>

            <div class="card border-0 shadow-sm rounded-4 p-4">
                <form action="edit.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="category" class="form-label fw-semibold">Kategori Menu Footer <span class="text-danger">*</span></label>
                            <select name="category" id="category" class="form-select bg-light border-0 py-2.5 px-3 rounded-3" required>
                                <option value="layanan_kepegawaian" <?= ($link['category'] === 'layanan_kepegawaian') ? 'selected' : '' ?>>Layanan Kepegawaian (Kiri)</option>
                                <option value="tautan" <?= ($link['category'] === 'tautan') ? 'selected' : '' ?>>Tautan (Tengah)</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="title" class="form-label fw-semibold">Judul Menu / Nama Tautan <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control bg-light border-0 py-2.5 px-3 rounded-3" placeholder="Contoh: Kalender Akademik" required value="<?= htmlspecialchars($link['title']) ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="display_order" class="form-label fw-semibold">Urutan Tampil (No. Urut)</label>
                            <input type="number" name="display_order" id="display_order" class="form-control bg-light border-0 py-2.5 px-3 rounded-3" placeholder="Contoh: 1" value="<?= htmlspecialchars($link['display_order']) ?>">
                            <small class="text-muted">Semakin kecil angkanya, semakin atas tampilannya.</small>
                        </div>

                        <div class="col-12 mt-4">
                            <label class="form-label fw-semibold d-block mb-3">Tipe Tautan & Dokumen</label>
                            
                            <div class="form-check form-check-inline me-4">
                                <input class="form-check-input" type="radio" name="link_type" id="type_none" value="none" <?= ($current_link_type === 'none') ? 'checked' : '' ?> onclick="toggleInputs()">
                                <label class="form-check-label fw-medium" for="type_none">Teks Saja (Belum tersedia / Non-aktif)</label>
                            </div>

                            <div class="form-check form-check-inline me-4">
                                <input class="form-check-input" type="radio" name="link_type" id="type_pdf" value="pdf" <?= ($current_link_type === 'pdf') ? 'checked' : '' ?> onclick="toggleInputs()">
                                <label class="form-check-label fw-medium" for="type_pdf">Unggah Berkas PDF</label>
                            </div>

                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="link_type" id="type_url" value="url" <?= ($current_link_type === 'url') ? 'checked' : '' ?> onclick="toggleInputs()">
                                <label class="form-check-label fw-medium" for="type_url">Alamat URL Eksternal</label>
                            </div>
                        </div>

                        <!-- PDF Input Container -->
                        <div id="pdf_container" class="col-md-12 mt-3" style="display: none;">
                            <div class="p-3 border border-dashed rounded-3 bg-light">
                                <label for="file_pdf" class="form-label fw-semibold">Pilih Berkas PDF Baru <small class="text-secondary fw-normal">(Biarkan kosong jika tidak ingin mengubah dokumen)</small></label>
                                <input type="file" name="file_pdf" id="file_pdf" class="form-control bg-white" accept="application/pdf">
                                
                                <?php if ($current_link_type === 'pdf'): ?>
                                    <div class="mt-2 text-danger small">
                                        <i class="fa-solid fa-file-pdf me-1"></i> File aktif saat ini: 
                                        <a href="../uploads/footer-docs/<?= htmlspecialchars($link['file_path']) ?>" target="_blank" class="text-danger fw-semibold text-decoration-underline"><?= htmlspecialchars($link['file_path']) ?></a>
                                    </div>
                                <?php endif; ?>
                                <small class="text-muted mt-2 d-block"><i class="fa-solid fa-circle-info me-1"></i> Hanya menerima berkas berkstensi <strong>.pdf</strong> dengan ukuran maksimal 10MB.</small>
                            </div>
                        </div>

                        <!-- URL Input Container -->
                        <div id="url_container" class="col-md-12 mt-3" style="display: none;">
                            <div class="p-3 border border-dashed rounded-3 bg-light">
                                <label for="external_url" class="form-label fw-semibold">Alamat URL Eksternal <span class="text-danger">*</span></label>
                                <input type="url" name="external_url" id="external_url" class="form-control bg-white py-2.5 px-3 rounded-3" placeholder="https://contoh.com/halaman" value="<?= htmlspecialchars($link['external_url'] ?? '') ?>">
                                <small class="text-muted mt-2 d-block"><i class="fa-solid fa-circle-info me-1"></i> Masukkan tautan lengkap, diawali dengan <strong>http://</strong> atau <strong>https://</strong>.</small>
                            </div>
                        </div>

                        <div class="col-12 mt-4 pt-2">
                            <button type="submit" class="btn btn-brand py-2.5 px-4 rounded-3 me-2"><i class="fa-solid fa-floppy-disk me-1"></i> Simpan Perubahan</button>
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

            // Prefilled values check
            var hasCurrentPdf = <?= (!empty($link['file_path'])) ? 'true' : 'false' ?>;

            if (typePdf.checked) {
                pdfContainer.style.display = 'block';
                urlContainer.style.display = 'none';
                // If it already has a PDF uploaded, new upload is optional. Else, it is required.
                fileInput.required = !hasCurrentPdf;
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

        // Run on load
        window.onload = function() {
            toggleInputs();
        };
    </script>
</body>
</html>
