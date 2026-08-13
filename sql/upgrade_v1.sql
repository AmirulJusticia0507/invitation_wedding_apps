-- Upgrade script: sinkronkan schema DB lama agar cocok dengan kode aplikasi
-- Jalankan di database weddingku_db yang sudah ada (MySQL 8.0+)
-- Cara pakai: mysql -u root weddingku_db < sql/upgrade_v1.sql
-- Atau import lewat phpMyAdmin.

USE `weddingku_db`;

-- 1) Kolom status_rsvp pada tabel undangan_url (dipakai index.php & rsvp_handler.php)
ALTER TABLE `undangan_url`
    ADD COLUMN `status_rsvp` ENUM('belum','hadir','tidak_hadir') NOT NULL DEFAULT 'belum' AFTER `url_undangan`;

-- 2) Tabel log_akses_undangan (dipakai index.php & log_akses_undangan.php)
CREATE TABLE IF NOT EXISTS `log_akses_undangan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `undangan_url_id` int NOT NULL,
  `tamu_id` int DEFAULT NULL,
  `user_agent` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `accessed_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `undangan_url_id` (`undangan_url_id`),
  KEY `tamu_id` (`tamu_id`),
  CONSTRAINT `log_akses_undangan_ibfk_1` FOREIGN KEY (`undangan_url_id`) REFERENCES `undangan_url` (`id`) ON DELETE CASCADE,
  CONSTRAINT `log_akses_undangan_ibfk_2` FOREIGN KEY (`tamu_id`) REFERENCES `tamu` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 3) Tabel password_resets (dipakai auth_forgot_password.php & auth_reset_password.php)
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- 4) Tabel folders (dipakai generate_folder.php & folder_action.php)
CREATE TABLE IF NOT EXISTS `folders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_folder` varchar(255) NOT NULL,
  `deskripsi` text,
  `pengantin_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pengantin_id` (`pengantin_id`),
  CONSTRAINT `folders_ibfk_1` FOREIGN KEY (`pengantin_id`) REFERENCES `pengantin` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
