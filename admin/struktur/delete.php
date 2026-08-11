<?php
// admin/struktur/delete.php
require_once '../includes/auth.php';
require_role('admin'); // Memastikan editor tidak memiliki akses
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: list.php");
    exit();
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    header("Location: list.php?err=" . urlencode("ID jabatan tidak valid."));
    exit();
}

try {
    // Delete from database
    $stmt = $pdo->prepare("DELETE FROM org_structure WHERE id = ?");
    $stmt->execute([$id]);

    header("Location: list.php?msg=delete_success");
    exit();
} catch (PDOException $e) {
    header("Location: list.php?err=" . urlencode("Gagal menghapus jabatan dari struktur organisasi: " . $e->getMessage()));
    exit();
}
