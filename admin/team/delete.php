<?php
// admin/team/delete.php
require_once '../includes/auth.php';
require_admin_role();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: list.php");
    exit();
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    header("Location: list.php?err=" . urlencode("ID tidak valid."));
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT photo FROM team WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        if (!empty($row['photo']) && file_exists('../uploads/' . $row['photo'])) {
            @unlink('../uploads/' . $row['photo']);
        }
        $pdo->prepare("DELETE FROM team WHERE id = ?")->execute([$id]); logActivity($_SESSION['admin_id'], 'delete', 'guru & staff', $row['name'], "Menghapus guru/staff '{$row['name']}'");
        header("Location: list.php?msg=delete_success");
        exit();
    }
    header("Location: list.php?err=" . urlencode("Data tidak ditemukan."));
} catch (PDOException $e) {
    header("Location: list.php?err=" . urlencode($e->getMessage()));
}
exit();
