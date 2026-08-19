<?php
// admin/testimonials/add.php
require_once '../includes/auth.php';
require_role('admin'); // Hanya admin & superadmin
require_once '../config/db.php';

$error = '';
$name = '';
$position = '';
$quote = '';
$display_order = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $quote = trim($_POST['quote'] ?? '');
    $display_order = intval($_POST['display_order'] ?? 0);
    $photo_position = trim($_POST['photo_position'] ?? 'center');

    if ($name === '' || $position === '' || $quote === '') {
        $error = "Nama, jabatan/posisi, dan quote wajib diisi!";
    } else {
        $photo_name = null;
        
        // Cek jika ada upload foto
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['photo']['tmp_name'];
            $file_name = $_FILES['photo']['name'];
            $file_size = $_FILES['photo']['size'];
            
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
                    $photo_name = 'testimonial_' . time() . '_' . uniqid() . '.' . $ext;
                    if (!is_dir('../uploads')) {
                        mkdir('../uploads', 0755, true);
                    }
                    if (!move_uploaded_file($file_tmp, '../uploads/' . $photo_name)) {
                        $error = "Gagal mengunggah foto.";
                        $photo_name = null;
                    }
                }
            }
        }

        if (empty($error)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO testimonials (name, position, quote, photo, display_order, photo_position) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$name, $position, $quote, $photo_name, $display_order, $photo_position]);
                header("Location: list.php?msg=add_success");
                exit();
            } catch (PDOException $e) {
                $error = "Gagal menyimpan testimonial: " . $e->getMessage();
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
    <title>Tambah Testimonial - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Tambah Testimonial</h4>
                    <p class="text-muted small mb-0">Tambahkan ulasan atau pesan dari tokoh/pengunjung baru</p>
                </div>
                <a href="list.php" class="btn btn-outline-secondary py-2 px-3"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="card border-0 rounded-4 shadow-sm p-4" style="max-width:720px;">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Nama Tokoh / Pengunjung</label>
                        <input type="text" name="name" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= htmlspecialchars($name) ?>" placeholder="Contoh: Dr. H. Ahmad Fauzi, M.Pd." required autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Jabatan / Keterangan</label>
                        <input type="text" name="position" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= htmlspecialchars($position) ?>" placeholder="Contoh: Wali Murid Kelas 3 / Pengawas Sekolah" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Foto Profil <span class="text-muted fw-normal">(opsional)</span></label>
                        <input type="file" name="photo" class="form-control bg-light border-0 py-2.5 rounded-3" accept="image/*">
                        <small class="text-muted">Format: JPG, JPEG, PNG, WEBP. Maks. 2MB. Disarankan foto berasio 1:1.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Atur Fokus Foto (Drag Mouse)</label>
                        <div class="d-flex align-items-center gap-3">
                            <!-- Drag Frame Preview -->
                            <div id="drag_frame" style="width: 180px; height: 180px; border-radius: 50%; border: 4px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.15); background: #eee; overflow: hidden; position: relative; cursor: move; user-select: none;">
                                <img id="drag_image" src="" alt="Preview" style="position: absolute; top: 0; left: 0; display: none; max-width: none; max-height: none; pointer-events: none; user-select: none;">
                                <div id="drag_placeholder" class="d-flex flex-column align-items-center justify-content-center h-100 text-muted text-center p-2">
                                    <i class="fa-regular fa-image fa-2x mb-2 text-opacity-50"></i>
                                    <span class="small">Upload foto untuk atur posisi</span>
                                </div>
                            </div>
                            <div class="text-muted small">
                                <p class="mb-1"><i class="fa-solid fa-up-down-left-right me-1"></i> Klik dan drag foto di dalam lingkaran untuk memindahkan posisi fokus.</p>
                                <p class="mb-0">Posisi saat ini: <strong id="photo_position_label">50% 50%</strong></p>
                            </div>
                        </div>
                        <input type="hidden" name="photo_position" id="photo_position_input" value="50% 50%">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Isi Testimonial / Quote</label>
                        <textarea name="quote" class="form-control bg-light border-0 p-3 rounded-3" rows="4" placeholder="Tuliskan ulasan atau kutipan pesan di sini..." required><?= htmlspecialchars($quote) ?></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Urutan Tampil</label>
                        <input type="number" name="display_order" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= (int) $display_order ?>" min="0">
                        <small class="text-muted">Urutan angka terkecil akan tampil paling awal di homepage.</small>
                    </div>
                    <button type="submit" class="btn btn-brand w-100 py-3 rounded-3 fw-bold"><i class="fa-solid fa-floppy-disk me-2"></i> Simpan Testimonial</button>
                </form>
            </div>
        </main>
    </div>
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const photoInput = document.querySelector('input[name="photo"]');
        const dragFrame = document.getElementById('drag_frame');
        const dragImage = document.getElementById('drag_image');
        const dragPlaceholder = document.getElementById('drag_placeholder');
        const photoPositionInput = document.getElementById('photo_position_input');
        const photoPositionLabel = document.getElementById('photo_position_label');

        let isDragging = false;
        let startX = 0;
        let startY = 0;
        let imgLeft = 0;
        let imgTop = 0;
        let containerWidth = 180;
        let containerHeight = 180;
        let imgWidth = 0;
        let imgHeight = 0;

        // Handle File upload change
        photoInput.addEventListener('change', function(e) {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    dragImage.src = evt.target.result;
                    dragImage.style.display = 'block';
                    dragPlaceholder.classList.add('d-none');
                    
                    // Reset position to center default
                    imgLeft = 0;
                    imgTop = 0;
                    dragImage.style.left = '0px';
                    dragImage.style.top = '0px';
                    photoPositionInput.value = '50% 50%';
                    photoPositionLabel.textContent = '50% 50%';
                };
                reader.readAsDataURL(file);
            }
        });

        // Handle image layout adjustments once loaded
        dragImage.addEventListener('load', function() {
            const naturalWidth = this.naturalWidth;
            const naturalHeight = this.naturalHeight;
            const aspect = naturalWidth / naturalHeight;

            // Mimic object-fit: cover to fill 180x180 container
            if (aspect > 1) {
                // Landscape
                imgHeight = containerHeight;
                imgWidth = containerHeight * aspect;
            } else {
                // Portrait or Square
                imgWidth = containerWidth;
                imgHeight = containerWidth / aspect;
            }

            dragImage.style.width = imgWidth + 'px';
            dragImage.style.height = imgHeight + 'px';

            // Center by default
            imgLeft = (containerWidth - imgWidth) / 2;
            imgTop = (containerHeight - imgHeight) / 2;
            dragImage.style.left = imgLeft + 'px';
            dragImage.style.top = imgTop + 'px';

            updatePercentages();
        });

        // Mouse drag handlers
        dragFrame.addEventListener('mousedown', function(e) {
            if (!dragImage.src || dragImage.style.display === 'none') return;
            isDragging = true;
            startX = e.clientX - imgLeft;
            startY = e.clientY - imgTop;
            dragFrame.style.cursor = 'grabbing';
            e.preventDefault();
        });

        window.addEventListener('mousemove', function(e) {
            if (!isDragging) return;
            imgLeft = e.clientX - startX;
            imgTop = e.clientY - startY;

            // Apply constraints so image doesn't leave the 180x180 container bounds
            const minLeft = containerWidth - imgWidth;
            const minTop = containerHeight - imgHeight;

            if (imgLeft > 0) imgLeft = 0;
            if (imgLeft < minLeft) imgLeft = minLeft;
            if (imgTop > 0) imgTop = 0;
            if (imgTop < minTop) imgTop = minTop;

            dragImage.style.left = imgLeft + 'px';
            dragImage.style.top = imgTop + 'px';

            updatePercentages();
        });

        window.addEventListener('mouseup', function() {
            if (isDragging) {
                isDragging = false;
                dragFrame.style.cursor = 'move';
            }
        });

        // Touch event handlers for mobile support
        dragFrame.addEventListener('touchstart', function(e) {
            if (!dragImage.src || dragImage.style.display === 'none') return;
            isDragging = true;
            const touch = e.touches[0];
            startX = touch.clientX - imgLeft;
            startY = touch.clientY - imgTop;
            e.preventDefault();
        });

        window.addEventListener('touchmove', function(e) {
            if (!isDragging) return;
            const touch = e.touches[0];
            imgLeft = touch.clientX - startX;
            imgTop = touch.clientY - startY;

            const minLeft = containerWidth - imgWidth;
            const minTop = containerHeight - imgHeight;

            if (imgLeft > 0) imgLeft = 0;
            if (imgLeft < minLeft) imgLeft = minLeft;
            if (imgTop > 0) imgTop = 0;
            if (imgTop < minTop) imgTop = minTop;

            dragImage.style.left = imgLeft + 'px';
            dragImage.style.top = imgTop + 'px';

            updatePercentages();
        });

        window.addEventListener('touchend', function() {
            isDragging = false;
        });

        function updatePercentages() {
            const diffX = containerWidth - imgWidth;
            const diffY = containerHeight - imgHeight;

            let pctX = 50;
            let pctY = 50;

            if (diffX !== 0) {
                pctX = Math.round((imgLeft / diffX) * 100);
            }
            if (diffY !== 0) {
                pctY = Math.round((imgTop / diffY) * 100);
            }

            // Clamp just in case
            pctX = Math.max(0, Math.min(100, pctX));
            pctY = Math.max(0, Math.min(100, pctY));

            const positionVal = `${pctX}% ${pctY}%`;
            photoPositionInput.value = positionVal;
            photoPositionLabel.textContent = positionVal;
        }
    });
    </script>
</body>
</html>
