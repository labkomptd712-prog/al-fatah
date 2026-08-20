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

try { $stmtSelect = $pdo->prepare("SELECT name, subject FROM contact_messages WHERE id = ?"); $stmtSelect->execute([$id]); $msg = $stmtSelect->fetch(); $nameVal = $msg ? ($msg['name'] ?: 'Unknown') : 'Unknown'; $subjectVal = $msg ? ($msg['subject'] ?: 'No Subject') : 'No Subject';
    $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->execute([$id]); logActivity($_SESSION['admin_id'], 'delete', 'pesan masuk', $subjectVal, "Menghapus pesan dari '{$nameVal}' dengan subjek '{$subjectVal}'");
    
    header("Location: list.php?msg=delete_success");
    exit();
} catch (PDOException $e) {
    header("Location: list.php?err=" . urlencode("Gagal menghapus pesan: " . $e->getMessage()));
    exit();
}
