<?php
// admin/revisions/resolve.php
require_once '../includes/auth.php';
require_once '../config/db.php';

// Memastikan hanya Admin/Superadmin yang bisa memproses
if (!is_admin_role()) {
    header("Location: ../dashboard.php?err=" . urlencode("Akses ditolak."));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $redirect = trim($_POST['redirect'] ?? 'list');

    if ($id <= 0) {
        header("Location: ../dashboard.php?err=" . urlencode("ID revisi tidak valid."));
        exit();
    }

    try {
        $stmt = $pdo->prepare("UPDATE revision_requests SET status = 'selesai', resolved_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$id]);

        $msg = "Revisi berhasil ditandai selesai!";
        if ($redirect === 'dashboard') {
            header("Location: ../dashboard.php?msg=" . urlencode($msg));
        } else {
            header("Location: list.php?msg=" . urlencode($msg));
        }
        exit();
    } catch (PDOException $e) {
        if ($redirect === 'dashboard') {
            header("Location: ../dashboard.php?err=" . urlencode("Gagal menyelesaikan revisi: " . $e->getMessage()));
        } else {
            header("Location: list.php?err=" . urlencode("Gagal menyelesaikan revisi: " . $e->getMessage()));
        }
        exit();
    }
} else {
    header("Location: ../dashboard.php");
    exit();
}
