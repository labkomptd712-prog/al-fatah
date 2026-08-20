<?php
// admin/ekskul/delete.php
require_once '../includes/auth.php';
require_admin_role();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);

    if ($id <= 0) {
        header("Location: list.php?err=" . urlencode("ID foto tidak valid."));
        exit();
    }

    try {
        // Fetch the photo filename first
        $stmt = $pdo->prepare("SELECT image, caption FROM ekskul_photos WHERE id = ?");
        $stmt->execute([$id]);
        $photo = $stmt->fetch();

        if ($photo) {
            $image_name = $photo['image'];
            // Delete file from uploads directory
            if (!empty($image_name) && file_exists('../uploads/' . $image_name)) {
                @unlink('../uploads/' . $image_name);
            }

            // Delete from database
            $delete_stmt = $pdo->prepare("DELETE FROM ekskul_photos WHERE id = ?");
            $delete_stmt->execute([$id]); logActivity($_SESSION['admin_id'], 'delete', 'ekskul', $photo['caption'] ?: 'Foto Ekskul', "Menghapus foto ekskul");

            header("Location: list.php?msg=delete_success");
            exit();
        } else {
            header("Location: list.php?err=" . urlencode("Foto tidak ditemukan."));
            exit();
        }
    } catch (PDOException $e) {
        header("Location: list.php?err=" . urlencode("Gagal menghapus foto dari database: " . $e->getMessage()));
        exit();
    }
} else {
    // Redirect if accessed directly
    header("Location: list.php");
    exit();
}
