<?php
// admin/facilities/delete.php
require_once '../includes/auth.php';
require_role('admin'); // Hanya admin & superadmin
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: list.php");
    exit();
}

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    header("Location: list.php?err=" . urlencode("ID fasilitas tidak valid."));
    exit();
}

try {
    // Ambil info fasilitas
    $stmtSelect = $pdo->prepare("SELECT * FROM facilities WHERE id = ?");
    $stmtSelect->execute([$id]);
    $fac = $stmtSelect->fetch();
    
    if ($fac) {
        // Hapus file gambar jika ada
        $image = $fac['image'];
        if (!empty($image) && file_exists('../uploads/' . $image)) {
            unlink('../uploads/' . $image);
        }
        
        // Ambil semua foto pendukung dari facility_photos dan hapus fisiknya
        $stmtPhotos = $pdo->prepare("SELECT photo_path FROM facility_photos WHERE facility_id = ?");
        $stmtPhotos->execute([$id]);
        $photos = $stmtPhotos->fetchAll();
        foreach ($photos as $p) {
            $photo_file = $p['photo_path'];
            if (!empty($photo_file) && file_exists('../uploads/' . $photo_file)) {
                unlink('../uploads/' . $photo_file);
            }
        }
        
        // Hapus data dari database
        $stmtDelete = $pdo->prepare("DELETE FROM facilities WHERE id = ?");
        $stmtDelete->execute([$id]);
        
        header("Location: list.php?msg=delete_success");
        exit();
    } else {
        header("Location: list.php?err=" . urlencode("Fasilitas tidak ditemukan."));
        exit();
    }
} catch (PDOException $e) {
    header("Location: list.php?err=" . urlencode("Gagal menghapus fasilitas: " . $e->getMessage()));
    exit();
}
