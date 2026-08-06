<?php
// admin/news/delete.php
require_once '../includes/auth.php';
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);

    if ($id <= 0) {
        header("Location: list.php?err=" . urlencode("ID berita tidak valid."));
        exit();
    }

    try {
        // Retrieve image name first
        $stmt = $pdo->prepare("SELECT image FROM news WHERE id = ?");
        $stmt->execute([$id]);
        $news = $stmt->fetch();

        if ($news) {
            $image_name = $news['image'];
            // Delete old image file
            if (!empty($image_name) && file_exists('../uploads/' . $image_name)) {
                @unlink('../uploads/' . $image_name);
            }

            // Delete from database
            $delete_stmt = $pdo->prepare("DELETE FROM news WHERE id = ?");
            $delete_stmt->execute([$id]);

            header("Location: list.php?msg=delete_success");
            exit();
        } else {
            header("Location: list.php?err=" . urlencode("Berita tidak ditemukan."));
            exit();
        }
    } catch (PDOException $e) {
        header("Location: list.php?err=" . urlencode("Gagal menghapus berita: " . $e->getMessage()));
        exit();
    }
} else {
    // If accessed directly via GET method, redirect to list
    header("Location: list.php");
    exit();
}
