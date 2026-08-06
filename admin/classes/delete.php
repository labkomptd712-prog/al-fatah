<?php
// admin/classes/delete.php
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
    $pdo->prepare("DELETE FROM classes WHERE id = ?")->execute([$id]);
    header("Location: list.php?msg=delete_success");
} catch (PDOException $e) {
    header("Location: list.php?err=" . urlencode($e->getMessage()));
}
exit();
