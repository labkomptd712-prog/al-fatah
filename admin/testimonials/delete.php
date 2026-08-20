<?php
// admin/testimonials/delete.php
require_once '../includes/auth.php';
require_role('admin'); // Hanya admin & superadmin
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: list.php");
    exit();
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    header("Location: list.php?err=" . urlencode("ID testimonial tidak valid."));
    exit();
}

try {
    // Ambil info foto terlebih dahulu untuk dihapus dari server
    $stmtSelect = $pdo->prepare("SELECT photo, name FROM testimonials WHERE id = ?");
    $stmtSelect->execute([$id]);
    $t = $stmtSelect->fetch();
    
    if ($t) {
        $photo = $t['photo'];
        if (!empty($photo) && file_exists('../uploads/' . $photo)) {
            unlink('../uploads/' . $photo);
        }
        
        // Hapus record dari database
        $stmtDelete = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmtDelete->execute([$id]); logActivity($_SESSION['admin_id'], 'delete', 'alumni & testimoni', $t['name'], "Menghapus testimoni alumni '{$t['name']}'");
        
        header("Location: list.php?msg=delete_success");
        exit();
    } else {
        header("Location: list.php?err=" . urlencode("Data testimonial tidak ditemukan."));
        exit();
    }
} catch (PDOException $e) {
    header("Location: list.php?err=" . urlencode("Gagal menghapus testimonial: " . $e->getMessage()));
    exit();
}
