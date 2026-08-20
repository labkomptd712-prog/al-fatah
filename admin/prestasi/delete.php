<?php
// admin/prestasi/delete.php
require_once '../includes/auth.php';
require_admin_role();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);

    if ($id <= 0) {
        header("Location: list.php?err=" . urlencode("ID prestasi tidak valid."));
        exit();
    }

    try {
        // Fetch the photo filename first
        $stmt = $pdo->prepare("SELECT foto, nama_siswa FROM prestasi WHERE id = ?");
        $stmt->execute([$id]);
        $photo = $stmt->fetch();

        if ($photo) {
            $image_name = $photo['foto'];
            // Delete file from uploads directory
            if (!empty($image_name) && file_exists('../uploads/' . $image_name)) {
                @unlink('../uploads/' . $image_name);
            }

            // Delete from database
            $delete_stmt = $pdo->prepare("DELETE FROM prestasi WHERE id = ?");
            $delete_stmt->execute([$id]); logActivity($_SESSION['admin_id'], 'delete', 'prestasi', $photo['nama_siswa'], "Menghapus prestasi siswa '{$photo['nama_siswa']}'");

            header("Location: list.php?msg=delete_success");
            exit();
        } else {
            header("Location: list.php?err=" . urlencode("Data prestasi tidak ditemukan."));
            exit();
        }
    } catch (PDOException $e) {
        header("Location: list.php?err=" . urlencode("Gagal menghapus data dari database: " . $e->getMessage()));
        exit();
    }
} else {
    // Redirect if accessed directly
    header("Location: list.php");
    exit();
}
