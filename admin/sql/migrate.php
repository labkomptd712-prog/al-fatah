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

    // Kolom role di admins - pastikan default-nya editor dan modifikasi tipe kolom
    $col = $pdo->query("SHOW COLUMNS FROM admins LIKE 'role'")->fetch();
    if (!$col) {
        $pdo->exec("ALTER TABLE admins ADD COLUMN role VARCHAR(20) NOT NULL DEFAULT 'editor'");
    } else {
        $pdo->exec("ALTER TABLE admins MODIFY COLUMN role VARCHAR(20) NOT NULL DEFAULT 'editor'");
    }

    // Pastikan admin 19231716 diubah ke superadmin, dan admin existing lain punya role (jangan overwrite editor)
    $pdo->exec("UPDATE admins SET role = 'superadmin' WHERE username = '19231716'");
    $pdo->exec("UPDATE admins SET role = 'admin' WHERE (role IS NULL OR role = '') AND username != '19231716'");

    // Kolom status di news
    $colNews = $pdo->query("SHOW COLUMNS FROM news LIKE 'status'")->fetch();
    if (!$colNews) {
        $pdo->exec("ALTER TABLE news ADD COLUMN status ENUM('pending','published') NOT NULL DEFAULT 'published'");
        // Sinkronkan data lama dari is_published ke status
        $pdo->exec("UPDATE news SET status = 'published' WHERE is_published = 1");
        $pdo->exec("UPDATE news SET status = 'pending' WHERE is_published = 0");
    }

    // Kolom created_by di news
    $colCreatedBy = $pdo->query("SHOW COLUMNS FROM news LIKE 'created_by'")->fetch();
    if (!$colCreatedBy) {
        $pdo->exec("ALTER TABLE news ADD COLUMN created_by INT NULL");
        try {
            $pdo->exec("ALTER TABLE news ADD CONSTRAINT fk_news_created_by FOREIGN KEY (created_by) REFERENCES admins(id) ON DELETE SET NULL");
        } catch (PDOException $e) {
            // Abaikan jika FK sudah ada
        }
        // Set default created_by untuk berita existing ke superadmin (19231716)
        $superadminId = $pdo->query("SELECT id FROM admins WHERE username = '19231716'")->fetchColumn();
        if ($superadminId) {
            $pdo->exec("UPDATE news SET created_by = $superadminId WHERE created_by IS NULL");
        }
    }

    // Tabel Struktur Organisasi
    $pdo->exec("CREATE TABLE IF NOT EXISTS org_structure (
      id INT AUTO_INCREMENT PRIMARY KEY,
      position_title VARCHAR(150) NOT NULL,
      person_name VARCHAR(150) NULL,
      display_order INT DEFAULT 0,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Idempotent Seed Data awal org_structure (Hanya jalan sekali jika tabel kosong)
    $countOrg = (int) $pdo->query("SELECT COUNT(*) FROM org_structure")->fetchColumn();
    if ($countOrg === 0) {
        $positions = [
            ['title' => 'Kepala Sekolah', 'order' => 1],
            ['title' => 'Wakil Kepala Sekolah', 'order' => 2],
            ['title' => 'Kepala Tata Usaha', 'order' => 3],
            ['title' => 'Koordinator Kurikulum', 'order' => 4],
            ['title' => 'Koordinator Kesiswaan', 'order' => 5],
            ['title' => 'Koordinator Sarpras', 'order' => 6],
            ['title' => 'Wali Kelas 1A', 'order' => 7],
            ['title' => 'Wali Kelas 1B', 'order' => 8],
            ['title' => 'Wali Kelas 2A', 'order' => 9],
            ['title' => 'Wali Kelas 2B', 'order' => 10],
            ['title' => 'Wali Kelas 3A', 'order' => 11],
            ['title' => 'Wali Kelas 3B', 'order' => 12],
            ['title' => 'Wali Kelas 4A', 'order' => 13],
            ['title' => 'Wali Kelas 4B', 'order' => 14],
            ['title' => 'Wali Kelas 5A', 'order' => 15],
            ['title' => 'Wali Kelas 5B', 'order' => 16],
            ['title' => 'Wali Kelas 6A', 'order' => 17],
            ['title' => 'Wali Kelas 6B', 'order' => 18],
        ];
        $stmtInsert = $pdo->prepare("INSERT INTO org_structure (position_title, person_name, display_order) VALUES (?, NULL, ?)");
        foreach ($positions as $p) {
            $stmtInsert->execute([$p['title'], $p['order']]);
        }
    }

    // Pengaturan phone_number default
    $pdo->exec("INSERT INTO settings (setting_key, setting_value) VALUES ('phone_number', '6282122229862') ON DUPLICATE KEY UPDATE setting_value = setting_value");

    // Tabel Testimonials
    $pdo->exec("CREATE TABLE IF NOT EXISTS testimonials (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(150) NOT NULL,
      position VARCHAR(150) NOT NULL,
      quote TEXT NOT NULL,
      photo VARCHAR(255) NULL,
      display_order INT DEFAULT 0,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Tabel Pesan Hubungi Kami
    $pdo->exec("CREATE TABLE IF NOT EXISTS contact_messages (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(150) NOT NULL,
      email VARCHAR(150) NOT NULL,
      subject VARCHAR(200) NOT NULL,
      message TEXT NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Tabel gallery_categories
    $pdo->exec("CREATE TABLE IF NOT EXISTS gallery_categories (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL,
      slug VARCHAR(100) NOT NULL UNIQUE,
      cover_image VARCHAR(255) NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Kolom category_id di gallery
    $colCatId = $pdo->query("SHOW COLUMNS FROM gallery LIKE 'category_id'")->fetch();
    if (!$colCatId) {
        $pdo->exec("ALTER TABLE gallery ADD COLUMN category_id INT NULL");
        try {
            $pdo->exec("ALTER TABLE gallery ADD CONSTRAINT fk_gallery_category FOREIGN KEY (category_id) REFERENCES gallery_categories(id) ON DELETE SET NULL");
        } catch (PDOException $e) {
            // Abaikan jika FK sudah ada
        }
    }

    // Buat kategori "Umum" default jika tabel gallery_categories kosong
    $countCat = (int) $pdo->query("SELECT COUNT(*) FROM gallery_categories")->fetchColumn();
    if ($countCat === 0) {
        $pdo->exec("INSERT INTO gallery_categories (name, slug, cover_image) VALUES ('Umum', 'umum', NULL)");
        
        // Pindahkan semua foto galeri lama yang belum memiliki kategori ke kategori "Umum"
        $umumId = $pdo->query("SELECT id FROM gallery_categories WHERE slug = 'umum'")->fetchColumn();
        if ($umumId) {
            $pdo->exec("UPDATE gallery SET category_id = $umumId WHERE category_id IS NULL");
        }
    }

    // Tabel footer_links
    $pdo->exec("CREATE TABLE IF NOT EXISTS footer_links (
      id INT AUTO_INCREMENT PRIMARY KEY,
      category ENUM('layanan_kepegawaian','tautan') NOT NULL,
      title VARCHAR(150) NOT NULL,
      file_path VARCHAR(255) NULL,
      external_url VARCHAR(255) NULL,
      display_order INT DEFAULT 0,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Idempotent Seed Data awal footer_links (Hanya jalan sekali jika tabel kosong)
    $countFooter = (int) $pdo->query("SELECT COUNT(*) FROM footer_links")->fetchColumn();
    if ($countFooter === 0) {
        $links = [
            // layanan_kepegawaian
            ['category' => 'layanan_kepegawaian', 'title' => 'Administrasi Umum', 'order' => 1],
            ['category' => 'layanan_kepegawaian', 'title' => 'Surat Keluar Masuk', 'order' => 2],
            ['category' => 'layanan_kepegawaian', 'title' => 'Surat PPDB', 'order' => 3],
            ['category' => 'layanan_kepegawaian', 'title' => 'Administrasi Penilaian', 'order' => 4],
            // tautan
            ['category' => 'tautan', 'title' => 'Profil Dapodik', 'order' => 5],
            ['category' => 'tautan', 'title' => 'Periksa NISN', 'order' => 6],
            ['category' => 'tautan', 'title' => 'Informasi KJP', 'order' => 7],
            ['category' => 'tautan', 'title' => 'PPDB', 'order' => 8],
            ['category' => 'tautan', 'title' => 'Prestasi Sekolah', 'order' => 9],
            ['category' => 'tautan', 'title' => 'Kalender Akademik', 'order' => 10],
            ['category' => 'tautan', 'title' => 'Jadwal Pelajaran', 'order' => 11],
            ['category' => 'tautan', 'title' => 'Download Formulir', 'order' => 12],
            ['category' => 'tautan', 'title' => 'Agenda Sekolah', 'order' => 13],
            ['category' => 'tautan', 'title' => 'FAQ', 'order' => 14],
        ];
        $stmtInsertLink = $pdo->prepare("INSERT INTO footer_links (category, title, display_order) VALUES (?, ?, ?)");
        foreach ($links as $link) {
            $stmtInsertLink->execute([$link['category'], $link['title'], $link['order']]);
        }
    }

    // Kolom is_read di contact_messages
    $colIsRead = $pdo->query("SHOW COLUMNS FROM contact_messages LIKE 'is_read'")->fetch();
    if (!$colIsRead) {
        $pdo->exec("ALTER TABLE contact_messages ADD COLUMN is_read TINYINT(1) DEFAULT 0");
    }

    // Tabel page_visits
    $pdo->exec("CREATE TABLE IF NOT EXISTS page_visits (
      id INT AUTO_INCREMENT PRIMARY KEY,
      page_url VARCHAR(255) NOT NULL,
      visitor_ip VARCHAR(45) NULL,
      visited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Tabel facility_categories
    $pdo->exec("CREATE TABLE IF NOT EXISTS facility_categories (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL,
      slug VARCHAR(100) NOT NULL UNIQUE,
      cover_image VARCHAR(255) NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Tabel facilities
    $pdo->exec("CREATE TABLE IF NOT EXISTS facilities (
      id INT AUTO_INCREMENT PRIMARY KEY,
      category_id INT NULL,
      name VARCHAR(150) NOT NULL,
      description TEXT NULL,
      image VARCHAR(255) NULL,
      display_order INT DEFAULT 0,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (category_id) REFERENCES facility_categories(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Tabel facility_photos
    $pdo->exec("CREATE TABLE IF NOT EXISTS facility_photos (
      id INT AUTO_INCREMENT PRIMARY KEY,
      facility_id INT NOT NULL,
      photo_path VARCHAR(255) NOT NULL,
      urutan INT DEFAULT 0,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (facility_id) REFERENCES facilities(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Seed kategori default "Umum" jika belum ada
    $stmtCat = $pdo->prepare("SELECT id FROM facility_categories WHERE slug = 'umum'");
    $stmtCat->execute();
    $umumId = $stmtCat->fetchColumn();
    if (!$umumId) {
        $stmtInsertCat = $pdo->prepare("INSERT INTO facility_categories (name, slug) VALUES ('Umum', 'umum')");
        $stmtInsertCat->execute();
        $umumId = $pdo->lastInsertId();
    }

    // Seed data facilities awal jika tabel facilities masih kosong
    $stmtCount = $pdo->query("SELECT COUNT(*) FROM facilities");
    if ((int)$stmtCount->fetchColumn() === 0) {
        $facilities_data = [
            ['name' => 'Lapangan luas', 'order' => 1],
            ['name' => 'lab PTD', 'order' => 2],
            ['name' => 'Lab Komputer', 'order' => 3],
            ['name' => 'Masjid', 'order' => 4],
            ['name' => 'WC', 'order' => 5],
            ['name' => 'flying fox', 'order' => 6],
            ['name' => 'Ruangan Kelas Full AC', 'order' => 7],
            ['name' => 'Kantin', 'order' => 8],
            ['name' => 'Area Belajar', 'order' => 9],
            ['name' => 'perpustakaan', 'order' => 10],
            ['name' => 'Hotspot area', 'order' => 11],
            ['name' => 'Taman', 'order' => 12],
        ];
        $stmtInsertFac = $pdo->prepare("INSERT INTO facilities (category_id, name, display_order) VALUES (?, ?, ?)");
        foreach ($facilities_data as $fac) {
            $stmtInsertFac->execute([$umumId, $fac['name'], $fac['order']]);
        }
    }

    // Tabel ekskul_categories
    $pdo->exec("CREATE TABLE IF NOT EXISTS ekskul_categories (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL,
      slug VARCHAR(100) NOT NULL UNIQUE,
      cover_image VARCHAR(255) NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Tabel ekskul_photos
    $pdo->exec("CREATE TABLE IF NOT EXISTS ekskul_photos (
      id INT AUTO_INCREMENT PRIMARY KEY,
      category_id INT NULL,
      caption VARCHAR(255) NULL,
      image VARCHAR(255) NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (category_id) REFERENCES ekskul_categories(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Seed kategori default "Umum" untuk ekskul jika tabel ekskul_categories kosong
    $countEkskulCat = (int) $pdo->query("SELECT COUNT(*) FROM ekskul_categories")->fetchColumn();
    if ($countEkskulCat === 0) {
        $pdo->exec("INSERT INTO ekskul_categories (name, slug, cover_image) VALUES ('Umum', 'umum', NULL)");
    }

    // Tabel prestasi_categories
    $pdo->exec("CREATE TABLE IF NOT EXISTS prestasi_categories (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL,
      slug VARCHAR(100) NOT NULL UNIQUE,
      cover_image VARCHAR(255) NULL,
      urutan INT DEFAULT 0,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Tabel prestasi
    $pdo->exec("CREATE TABLE IF NOT EXISTS prestasi (
      id INT AUTO_INCREMENT PRIMARY KEY,
      foto VARCHAR(255) NOT NULL,
      nama_siswa VARCHAR(255) NOT NULL,
      jenis_lomba VARCHAR(255) NOT NULL,
      keterangan TEXT NULL,
      category_id INT NULL,
      urutan INT DEFAULT 0,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (category_id) REFERENCES prestasi_categories(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Seed kategori default "Umum" untuk prestasi jika tabel prestasi_categories kosong
    $countPrestasiCat = (int) $pdo->query("SELECT COUNT(*) FROM prestasi_categories")->fetchColumn();
    if ($countPrestasiCat === 0) {
        $pdo->exec("INSERT INTO prestasi_categories (name, slug, cover_image) VALUES ('Umum', 'umum', NULL)");
    }

    // Add photo_position column to team table if it doesn't exist
    $colTeamPos = $pdo->query("SHOW COLUMNS FROM team LIKE 'photo_position'")->fetch();
    if (!$colTeamPos) {
        $pdo->exec("ALTER TABLE team ADD COLUMN photo_position VARCHAR(50) DEFAULT 'center'");
    }

    // Add photo_position column to testimonials table if it doesn't exist
    $colTestiPos = $pdo->query("SHOW COLUMNS FROM testimonials LIKE 'photo_position'")->fetch();
    if (!$colTestiPos) {
        $pdo->exec("ALTER TABLE testimonials ADD COLUMN photo_position VARCHAR(50) DEFAULT 'center'");
    }

    // CREATE TABLE revision_requests
    $pdo->exec("CREATE TABLE IF NOT EXISTS revision_requests (
      id INT AUTO_INCREMENT PRIMARY KEY,
      module_name VARCHAR(50) NOT NULL,
      item_id INT NOT NULL,
      item_title VARCHAR(255) NOT NULL,
      requested_by INT NOT NULL,
      catatan TEXT NOT NULL,
      status ENUM('pending', 'selesai') DEFAULT 'pending',
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      resolved_at TIMESTAMP NULL,
      FOREIGN KEY (requested_by) REFERENCES admins(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Seed dummy kepala sekolah account (username: kepsek, role: kepsek)
    $stmtKepsek = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = 'kepsek'");
    $stmtKepsek->execute();
    if ((int)$stmtKepsek->fetchColumn() === 0) {
        $hashKepsek = password_hash('kepsek', PASSWORD_DEFAULT);
        $stmtInsertKepsek = $pdo->prepare("INSERT INTO admins (username, password, role) VALUES ('kepsek', ?, 'kepsek')");
        $stmtInsertKepsek->execute([$hashKepsek]);
    }

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
