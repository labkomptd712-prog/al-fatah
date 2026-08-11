<?php
// admin/struktur/edit.php
require_once '../includes/auth.php';
require_role('admin'); // Memastikan editor tidak memiliki akses
require_once '../config/db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: list.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM org_structure WHERE id = ?");
$stmt->execute([$id]);
$slot = $stmt->fetch();

if (!$slot) {
    header("Location: list.php?err=" . urlencode("Data posisi tidak ditemukan."));
    exit();
}

$error = '';
$position_title = $slot['position_title'];
$person_name = $slot['person_name'] ?? '';
$display_order = (int) $slot['display_order'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $position_title = trim($_POST['position_title'] ?? '');
    $person_name = trim($_POST['person_name'] ?? '');
    $display_order = intval($_POST['display_order'] ?? 0);

    if ($position_title === '') {
        $error = "Nama jabatan wajib diisi!";
    } else {
        $person_name_val = ($person_name === '') ? null : $person_name;
        try {
            $stmtUpdate = $pdo->prepare("UPDATE org_structure SET position_title = ?, person_name = ?, display_order = ? WHERE id = ?");
            $stmtUpdate->execute([$position_title, $person_name_val, $display_order, $id]);
            header("Location: list.php?msg=edit_success");
            exit();
        } catch (PDOException $e) {
            $error = "Gagal memperbarui data jabatan: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Jabatan - SDIT Al Fatah</title>
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
                    <h4 class="fw-bold text-dark mb-1">Edit Jabatan / Posisi</h4>
                </div>
                <a href="list.php" class="btn btn-outline-secondary py-2 px-3"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="card border-0 rounded-4 shadow-sm p-4" style="max-width:640px;">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Nama Jabatan</label>
                        <input type="text" name="position_title" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= htmlspecialchars($position_title) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary">Nama Pejabat / Staf <span class="text-muted fw-normal">(opsional)</span></label>
                        <input type="text" name="person_name" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= htmlspecialchars($person_name) ?>" placeholder="Kosongkan jika belum ada pejabat definitif">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-secondary">Urutan Tampil</label>
                        <input type="number" name="display_order" class="form-control bg-light border-0 py-2.5 rounded-3" value="<?= (int) $display_order ?>" min="0">
                    </div>
                    <button type="submit" class="btn btn-brand w-100 py-2.5 rounded-3 fw-bold"><i class="fa-solid fa-floppy-disk me-2"></i> Simpan Perubahan</button>
                </form>
            </div>
        </main>
    </div>
    <script src="../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
