<?php
// admin/news/approve.php
// Aksi persetujuan berita (oleh admin / superadmin)

require_once '../includes/auth.php';
require_admin_role('admin'); // Membatasi akses: hanya admin dan superadmin yang boleh menyetujui berita
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: list.php");
    exit();
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    header("Location: list.php?err=" . urlencode("ID berita tidak valid."));
    exit();
}

try {
    // Cari berita berdasarkan ID
    $stmt = $pdo->prepare("SELECT id, title FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $news = $stmt->fetch();

    if ($news) {
        // Update status berita menjadi published
        $stmtUpdate = $pdo->prepare("UPDATE news SET status = 'published' WHERE id = ?");
        $stmtUpdate->execute([$id]); logActivity($_SESSION['admin_id'], 'update', 'berita', $news['title'], "Menyetujui berita '{$news['title']}'");
        
        header("Location: list.php?msg=approve_success");
        exit();
    } else {
        header("Location: list.php?err=" . urlencode("Berita tidak ditemukan."));
        exit();
    }
} catch (PDOException $e) {
    header("Location: list.php?err=" . urlencode("Gagal menyetujui berita: " . $e->getMessage()));
    exit();
}
