-- Batch schema update SDIT Al Fatah
-- Jalankan sekali di phpMyAdmin / MySQL (database alfatah_db)

CREATE TABLE IF NOT EXISTS team (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  position VARCHAR(150) NOT NULL,
  photo VARCHAR(255) NULL,
  display_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS classes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  class_name VARCHAR(50) NOT NULL,
  wali_kelas VARCHAR(150) NOT NULL,
  student_count INT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tambah kolom role ke admins (abaikan error jika sudah ada)
ALTER TABLE admins ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'admin';
