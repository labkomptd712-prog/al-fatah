<?php
// admin/classes/add.php
require_once '../includes/auth.php';
require_admin_role();
require_once '../config/db.php';

$error = '';
$class_name = '';
$wali_kelas = '';
$student_count = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = trim($_POST['class_name'] ?? '');
    $wali_kelas = trim($_POST['wali_kelas'] ?? '');
    $student_count_raw = trim($_POST['student_count'] ?? '');

    if ($class_name === '' || $wali_kelas === '') {
        $error = "Nama kelas dan wali kelas wajib diisi!";
    } else {
        $student_count = ($student_count_raw === '') ? null : (int) $student_count_raw;
        try {
            $stmt = $pdo->prepare("INSERT INTO classes (class_name, wali_kelas, student_count) VALUES (?, ?, ?)");
            $stmt->execute([$class_name, $wali_kelas, $student_count]);
            header("Location: list.php?msg=add_success");
            exit();
        } catch (PDOException $e) {
            $error = "Gagal menyimpan: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kelas - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Tambah Kelas</h4>
                    <p class="text-muted small mb-0">Tambahkan kelas dan wali kelas baru</p>
                </div>
                <a href="list.php" class="btn btn-outline-secondary py-2 px-3"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
            </div>
            <?php if ($error): ?>
                <div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <div class="card border-0 rounded-4 shadow-sm p-4" style="max-width:640px;">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Nama Kelas</label>
                        <input type="text" name="class_name" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= htmlspecialchars($class_name) ?>" placeholder="Contoh: Kelas 1A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Nama Wali Kelas</label>
                        <input type="text" name="wali_kelas" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= htmlspecialchars($wali_kelas) ?>" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Jumlah Siswa <span class="text-muted fw-normal">(opsional)</span></label>
                        <input type="number" name="student_count" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= htmlspecialchars($student_count) ?>" min="0">
                    </div>
                    <button type="submit" class="btn btn-brand w-100 py-2.5 rounded-3 fw-bold"><i class="fa-solid fa-floppy-disk me-2"></i> Simpan</button>
                </form>
            </div>
        </main>
    </div>
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
