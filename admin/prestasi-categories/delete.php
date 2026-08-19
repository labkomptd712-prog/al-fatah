<?php
// admin/prestasi-categories/delete.php
require_once '../includes/auth.php';
require_role('admin'); // Hanya admin & superadmin
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: list.php");
    exit();
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    header("Location: list.php?err=" . urlencode("ID kategori tidak valid."));
    exit();
}

try {
    // Ambil info kategori
    $stmtSelect = $pdo->prepare("SELECT * FROM prestasi_categories WHERE id = ?");
    $stmtSelect->execute([$id]);
    $cat = $stmtSelect->fetch();
    
    if (!$cat) {
        header("Location: list.php?err=" . urlencode("Kategori tidak ditemukan."));
        exit();
    }
    
    if ($cat['slug'] === 'umum') {
        header("Location: list.php?err=" . urlencode("Kategori default 'Umum' tidak dapat dihapus."));
        exit();
    }
    
    // Cari kategori 'Umum' sebagai penampung
    $stmtUmum = $pdo->prepare("SELECT id FROM prestasi_categories WHERE slug = 'umum' LIMIT 1");
    $stmtUmum->execute();
    $umumId = $stmtUmum->fetchColumn();
    
    // Pindahkan prestasi yang ada di kategori ini ke kategori Umum
    if ($umumId) {
        $stmtMove = $pdo->prepare("UPDATE prestasi SET category_id = ? WHERE category_id = ?");
        $stmtMove->execute([$umumId, $id]);
    }
    
    // Hapus cover image jika ada
    $cover = $cat['cover_image'];
    if (!empty($cover) && file_exists('../uploads/' . $cover)) {
        unlink('../uploads/' . $cover);
    }
    
    // Hapus record kategori
    $stmtDelete = $pdo->prepare("DELETE FROM prestasi_categories WHERE id = ?");
    $stmtDelete->execute([$id]);
    
    header("Location: list.php?msg=delete_success");
    exit();
} catch (PDOException $e) {
    header("Location: list.php?err=" . urlencode("Gagal menghapus kategori: " . $e->getMessage()));
    exit();
}
