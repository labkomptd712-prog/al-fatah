<?php
// admin/admins/delete.php
require_once '../includes/auth.php';
require_role('superadmin');
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: list.php");
    exit();
}

$id = intval($_POST['id'] ?? 0);
$current_admin_id = (int) ($_SESSION['admin_id'] ?? 0);

if ($id <= 0) {
    header("Location: list.php?err=" . urlencode("ID tidak valid."));
    exit();
}

if ($id === $current_admin_id) {
    header("Location: list.php?err=" . urlencode("Tidak dapat menghapus akun sendiri."));
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE id = ?");
    $stmt->execute([$id]);
    if (!$stmt->fetch()) {
        header("Location: list.php?err=" . urlencode("Akun tidak ditemukan."));
        exit();
    }

    $pdo->prepare("DELETE FROM admins WHERE id = ?")->execute([$id]);
    header("Location: list.php?msg=delete_success");
} catch (PDOException $e) {
    header("Location: list.php?err=" . urlencode($e->getMessage()));
}
exit();
