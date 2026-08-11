-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for alfatah_db
CREATE DATABASE IF NOT EXISTS `alfatah_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `alfatah_db`;

-- Dumping structure for table alfatah_db.admins
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table alfatah_db.admins: ~1 rows (approximately)
INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
	(1, '19231716', '$2y$10$05XMp9nTdYaqJl0Oh44VB.4UdF47cJ7JepXCUj7qa66uU.PVj/nvu', '2026-08-06 00:10:28');

-- Dumping structure for table alfatah_db.gallery
CREATE TABLE IF NOT EXISTS `gallery` (
  `id` int NOT NULL AUTO_INCREMENT,
  `caption` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table alfatah_db.gallery: ~0 rows (approximately)

-- Dumping structure for table alfatah_db.news
CREATE TABLE IF NOT EXISTS `news` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_published` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table alfatah_db.news: ~0 rows (approximately)

-- Dumping structure for table alfatah_db.settings
CREATE TABLE IF NOT EXISTS `settings` (
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table alfatah_db.settings: ~9 rows (approximately)
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
	('fb_link', 'https://www.facebook.com/share/198ty81jhk/'),
	('ig_link', 'https://www.instagram.com/sdit_al_fatah_bekasi?igsh=Z3l4ZHN5ZzVvMXZp'),
	('misi', 'Membangun kemandirian siswa sadar beribadah.\r\nMembiasakan siswa berakhlakul karimah.\r\nMengembangkan sikap belajar siswa yang bertanggung jawab, terampil, dan percaya diri.\r\nMengembangkan Pendidikan Teknologi Dasar.\r\nMewujudkan siswa berprestasi dalam bidang Al Qur’an, Akademik, dan Olahraga.\r\nMembangun kemitraan yang harmonis dan menumbuhkan kepercayaan dengan orang tua siswa.'),
	('qa_list', 'Sholat Dengan Kesadaran\r\nBerbakti Pada Orang Tua\r\nDisiplin\r\nPercaya Diri\r\nSenang Membaca\r\nPerilaku Sosial Baik\r\nMemiliki Budaya Bersih\r\nNilai Semua Bidang Studi Tuntas\r\nTartil Baca Al Qur’an\r\nHafal Juz ‘Amma\r\nMemiliki Kemampuan Membaca Efektif\r\nKemampuan Komunikasi Baik'),
	('tiktok_link', 'https://www.tiktok.com/@sdit_alfatah_bekasi?lang=id-ID'),
	('visi', 'Terwujudnya Generasi yang Berkarakter Islami, Tangguh, Terampil, dan Berprestasi'),
	('wa_message', 'Assalamualaikum, saya ingin bertanya tentang SDIT Al Fatah'),
	('wa_number', '6285724237806'),
	('yt_link', 'https://youtube.com/@sditalfathbekasi?si=lio9zSDCj2ZsQWfr');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
