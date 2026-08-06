<?php
/**
 * Migrasi aman (idempotent) — buat tabel/kolom jika belum ada.
 * Dipanggil dari login atau bisa dijalankan manual: php admin/sql/migrate.php
 */
require_once __DIR__ . '/../config/db.php';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS team (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(150) NOT NULL,
      position VARCHAR(150) NOT NULL,
      photo VARCHAR(255) NULL,
      display_order INT DEFAULT 0,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS classes (
      id INT AUTO_INCREMENT PRIMARY KEY,
      class_name VARCHAR(50) NOT NULL,
      wali_kelas VARCHAR(150) NOT NULL,
      student_count INT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Kolom role di admins
    $col = $pdo->query("SHOW COLUMNS FROM admins LIKE 'role'")->fetch();
    if (!$col) {
        $pdo->exec("ALTER TABLE admins ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'admin'");
    }

    // Pastikan semua admin existing punya role
    $pdo->exec("UPDATE admins SET role = 'admin' WHERE role IS NULL OR role = ''");

    if (php_sapi_name() === 'cli') {
        echo "Migrasi berhasil.\n";
    }
} catch (PDOException $e) {
    if (php_sapi_name() === 'cli') {
        fwrite(STDERR, "Migrasi gagal: " . $e->getMessage() . "\n");
        exit(1);
    }
    throw $e;
}
