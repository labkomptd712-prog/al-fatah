<?php
// admin/config/db.php
// Konfigurasi Database PDO MySQL

// Silakan ubah kredensial database di bawah ini sesuai dengan server Anda
$db_host = 'localhost';
$db_name = 'alfatah_db';
$db_user = 'root';
$db_pass = ''; // default kosong untuk XAMPP/Laragon lokal

try {
    $dsn = "mysql:host={$db_host};dbname={$db_name};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // Jika gagal koneksi, tampilkan error (baiknya dimatikan saat production)
    die("Koneksi database gagal: " . $e->getMessage());
}
?>
