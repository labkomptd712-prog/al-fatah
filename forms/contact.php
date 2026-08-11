<?php
// forms/contact.php
// Penanganan Form Kontak - Opsi 1: Menyimpan ke Database

require_once '../admin/config/db.php';

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $subject === '' || $message === '') {
    echo "Semua kolom input formulir wajib diisi!";
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Alamat email tidak valid!";
    exit();
}

try {
    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $email, $subject, $message]);
    
    // Kembalikan teks "OK" agar dibaca sukses oleh library JS validate.js di frontend
    echo "OK";
} catch (PDOException $e) {
    echo "Gagal menyimpan pesan: " . $e->getMessage();
}
?>
