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
    $stmtSelect = $pdo->prepare("SELECT person_name, position_title FROM org_structure WHERE id = ?"); $stmtSelect->execute([$id]); $slot = $stmtSelect->fetch(); $personName = $slot ? ($slot['person_name'] ?: $slot['position_title']) : ''; $stmt = $pdo->prepare("DELETE FROM org_structure WHERE id = ?");
    $stmt->execute([$id]); logActivity($_SESSION['admin_id'], 'delete', 'struktur organisasi', $personName, "Menghapus anggota struktur organisasi '{$personName}'");

    header("Location: list.php?msg=delete_success");
    exit();
} catch (PDOException $e) {
    header("Location: list.php?err=" . urlencode("Gagal menghapus jabatan dari struktur organisasi: " . $e->getMessage()));
    exit();
}
