<?php
// admin/footer-links/delete.php
require_once '../includes/auth.php';
require_admin_role('admin'); // Only admin and superadmin
require_once '../config/db.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: list.php");
    exit();
}

try {
    // Fetch link info to check if file needs to be deleted
    $stmt = $pdo->prepare("SELECT file_path, title FROM footer_links WHERE id = ? LIMIT 1");
    $stmt->execute([$id]);
    $link = $stmt->fetch();

    if ($link) {
        // If file exists on disk, delete it
        if (!empty($link['file_path'])) {
            $file_on_disk = '../uploads/footer-docs/' . $link['file_path'];
            if (file_exists($file_on_disk)) {
                unlink($file_on_disk);
            }
        }

        // Delete from database
        $stmtDelete = $pdo->prepare("DELETE FROM footer_links WHERE id = ?");
        $stmtDelete->execute([$id]); logActivity($_SESSION['admin_id'], 'delete', 'tautan footer', $link['title'], "Menghapus tautan footer '{$link['title']}'");

        header("Location: list.php?msg=delete_success");
        exit();
    } else {
        header("Location: list.php");
        exit();
    }
} catch (PDOException $e) {
    header("Location: list.php?err=db_error");
    exit();
}
