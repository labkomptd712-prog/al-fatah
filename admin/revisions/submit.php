<?php
// admin/revisions/submit.php
require_once '../includes/auth.php';
require_once '../config/db.php';

// Memastikan hanya Kepsek yang bisa submit
if (!is_kepsek_role()) {
    header("Location: ../dashboard.php?err=" . urlencode("Akses ditolak."));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $module_name = trim($_POST['module_name'] ?? '');
    $item_id = (int)($_POST['item_id'] ?? 0);
    $item_title = trim($_POST['item_title'] ?? '');
    $catatan = trim($_POST['catatan'] ?? '');
    $requested_by = (int)($_SESSION['admin_id'] ?? 0);

    // Get referrer safely, default to dashboard
    $referrer = $_SERVER['HTTP_REFERER'] ?? '../dashboard.php';
    // Clean up any existing msg/err parameters in referrer to prevent duplicate params
    $clean_referrer = preg_replace('/([?&])(msg|err)=[^&]*/', '', $referrer);
    $clean_referrer = rtrim($clean_referrer, '?&');
    $separator = (strpos($clean_referrer, '?') !== false) ? '&' : '?';

    if (empty($module_name) || $item_id <= 0 || empty($item_title) || empty($catatan)) {
        header("Location: " . $clean_referrer . $separator . "err=" . urlencode("Data pengajuan revisi tidak lengkap!"));
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO revision_requests (module_name, item_id, item_title, requested_by, catatan, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$module_name, $item_id, $item_title, $requested_by, $catatan]);

        header("Location: " . $clean_referrer . $separator . "msg=" . urlencode("Catatan revisi berhasil dikirim ke Admin!"));
        exit();
    } catch (PDOException $e) {
        header("Location: " . $clean_referrer . $separator . "err=" . urlencode("Gagal menyimpan revisi: " . $e->getMessage()));
        exit();
    }
} else {
    header("Location: ../dashboard.php");
    exit();
}
