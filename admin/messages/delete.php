<?php
// admin/messages/delete.php
require_once '../includes/auth.php';
require_role('admin'); // Hanya admin & superadmin
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: list.php");
    exit();
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    header("Location: list.php?err=" . urlencode("ID pesan tidak valid."));
    exit();
}

try {
    $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->execute([$id]);
    
    header("Location: list.php?msg=delete_success");
    exit();
} catch (PDOException $e) {
    header("Location: list.php?err=" . urlencode("Gagal menghapus pesan: " . $e->getMessage()));
    exit();
}
