<?php
// admin/gallery-categories/delete.php
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
    $stmtSelect = $pdo->prepare("SELECT * FROM gallery_categories WHERE id = ?");
    $stmtSelect->execute([$id]);
    $cat = $stmtSelect->fetch();
    
    if ($cat) {
        // Jangan izinkan menghapus kategori "Umum" default jika itu adalah kategori utama
        if ($cat['slug'] === 'umum') {
            header("Location: list.php?err=" . urlencode("Kategori 'Umum' adalah kategori bawaan dan tidak boleh dihapus."));
            exit();
        }
        
        // Cari atau buat kategori "Umum"
        $stmtUmum = $pdo->query("SELECT id FROM gallery_categories WHERE slug = 'umum'");
        $umum_id = $stmtUmum->fetchColumn();
        if (!$umum_id) {
            $pdo->exec("INSERT INTO gallery_categories (name, slug, cover_image) VALUES ('Umum', 'umum', NULL)");
            $umum_id = $pdo->lastInsertId();
        }
        
        // Pindahkan semua foto di kategori ini ke kategori "Umum"
        $stmtMove = $pdo->prepare("UPDATE gallery SET category_id = ? WHERE category_id = ?");
        $stmtMove->execute([$umum_id, $id]);
        
        // Hapus file cover kategori jika ada
        $cover = $cat['cover_image'];
        if (!empty($cover) && file_exists('../uploads/' . $cover)) {
            unlink('../uploads/' . $cover);
        }
        
        // Hapus kategori dari database
        $stmtDelete = $pdo->prepare("DELETE FROM gallery_categories WHERE id = ?");
        $stmtDelete->execute([$id]); logActivity($_SESSION['admin_id'], 'delete', 'kategori galeri', $cat['name'], "Menghapus kategori galeri '{$cat['name']}'");
        
        header("Location: list.php?msg=delete_success");
        exit();
    } else {
        header("Location: list.php?err=" . urlencode("Kategori tidak ditemukan."));
        exit();
    }
} catch (PDOException $e) {
    header("Location: list.php?err=" . urlencode("Gagal menghapus kategori: " . $e->getMessage()));
    exit();
}
