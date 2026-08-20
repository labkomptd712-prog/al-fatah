<?php
// admin/classes/edit.php
require_once '../includes/auth.php';
require_admin_role();
require_once '../config/db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: list.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->execute([$id]);
$class = $stmt->fetch();
if (!$class) {
    header("Location: list.php?err=" . urlencode("Data tidak ditemukan."));
    exit();
}

$error = '';
$class_name = $class['class_name'];
$wali_kelas = $class['wali_kelas'];
$student_count = $class['student_count'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_name = trim($_POST['class_name'] ?? '');
    $wali_kelas = trim($_POST['wali_kelas'] ?? '');
    $student_count_raw = trim($_POST['student_count'] ?? '');

    if ($class_name === '' || $wali_kelas === '') {
        $error = "Nama kelas dan wali kelas wajib diisi!";
    } else {
        $student_count = ($student_count_raw === '') ? null : (int) $student_count_raw;
        try {
            $stmt = $pdo->prepare("UPDATE classes SET class_name = ?, wali_kelas = ?, student_count = ? WHERE id = ?");
            $stmt->execute([$class_name, $wali_kelas, $student_count, $id]); logActivity($_SESSION['admin_id'], 'update', 'kelas', $class_name, "Mengubah kelas '{$class_name}'");
            header("Location: list.php?msg=edit_success");
            exit();
        } catch (PDOException $e) {
            $error = "Gagal memperbarui: " . $e->getMessage();
        }
    }
}

$student_count_display = ($student_count !== null && $student_count !== '') ? (int) $student_count : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kelas - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Edit Kelas</h4>
                </div>
                <a href="list.php" class="btn btn-outline-secondary py-2 px-3"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
            </div>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <div class="card border-0 rounded-4 shadow-sm p-4" style="max-width:640px;">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Nama Kelas</label>
                        <input type="text" name="class_name" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= htmlspecialchars($class_name) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Nama Wali Kelas</label>
                        <input type="text" name="wali_kelas" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= htmlspecialchars($wali_kelas) ?>" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Jumlah Siswa <span class="text-muted fw-normal">(opsional)</span></label>
                        <input type="number" name="student_count" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= htmlspecialchars((string) $student_count_display) ?>" min="0">
                    </div>
                    <button type="submit" class="btn btn-brand w-100 py-2.5 rounded-3 fw-bold"><i class="fa-solid fa-floppy-disk me-2"></i> Simpan Perubahan</button>
                </form>
            </div>
        </main>
    </div>
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
