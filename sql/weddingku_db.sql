/*
SQLyog Ultimate v12.5.1 (64 bit)
MySQL - 8.0.30 : Database - weddingku_db
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`weddingku_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `weddingku_db`;

/*Table structure for table `bank_list` */

DROP TABLE IF EXISTS `bank_list`;

CREATE TABLE `bank_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_bank` varchar(100) NOT NULL,
  `kode_bank` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `bank_list` */

insert  into `bank_list`(`id`,`nama_bank`,`kode_bank`) values 
(1,'Bank BRI','002'),
(2,'Bank Mandiri','008'),
(3,'Bank BNI','009'),
(4,'Bank BTN','200'),
(5,'Bank BJB','110'),
(6,'Bank DKI','111'),
(7,'Bank BPD D.I.Y','112'),
(8,'Bank Jateng','113'),
(9,'Bank Jatim','114'),
(10,'Bank Jambi','115'),
(11,'Bank Aceh','116'),
(12,'Bank Sumut','117'),
(13,'Bank Sumbar','118'),
(14,'Bank Kepri','119'),
(15,'Bank Sumsel dan Babel','120'),
(16,'Bank Lampung','121'),
(17,'Bank Kalsel','122'),
(18,'Bank Kalbar','123'),
(19,'Bank Kaltim','124'),
(20,'Bank Kalteng','125'),
(21,'Bank Sulsel','126'),
(22,'Bank Sulut','127'),
(23,'Bank NTB','128'),
(24,'Bank Bali','129'),
(25,'Bank NTT','130'),
(26,'Bank Maluku','131'),
(27,'Bank Papua','132'),
(28,'Bank Bengkulu','133'),
(29,'Bank Sulteng','134'),
(30,'Bank Sultra','135'),
(31,'Bank Banten','137'),
(32,'Bank Ekspor Indonesia','003'),
(33,'Bank Danamon Indonesia','011'),
(34,'Bank Permata','013'),
(35,'Bank BCA','014'),
(36,'Bank Maybank','016'),
(37,'Bank Panin','019'),
(38,'Bank Arta Niaga Kencana','020'),
(39,'Bank CIMB Niaga','022'),
(40,'Bank UOB Indonesia','023'),
(41,'Bank Lippo','026'),
(42,'Bank OCBC NISP','028'),
(43,'Bank Multicor','036'),
(44,'Bank Artha Graha','037'),
(45,'Bank Pesona Perdania','047'),
(46,'Bank ABN Amro','052'),
(47,'Bank Keppel Tatlee Buana','053'),
(48,'Bank BNP Paribas Indonesia','057'),
(49,'Bank Woori Indonesia','068'),
(50,'Bank Bumi Arta','076');

/*Table structure for table `gallery` */

DROP TABLE IF EXISTS `gallery`;

CREATE TABLE `gallery` (
  `id` int NOT NULL AUTO_INCREMENT,
  `judul` varchar(100) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `tanggal_upload` datetime DEFAULT CURRENT_TIMESTAMP,
  `pengantin_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pengantin_id` (`pengantin_id`),
  CONSTRAINT `gallery_ibfk_1` FOREIGN KEY (`pengantin_id`) REFERENCES `pengantin` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `gallery` */

/*Table structure for table `message` */

DROP TABLE IF EXISTS `message`;

CREATE TABLE `message` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pengantin_id` int DEFAULT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `pesan` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pengantin_id` (`pengantin_id`),
  CONSTRAINT `message_ibfk_1` FOREIGN KEY (`pengantin_id`) REFERENCES `pengantin` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `message` */

insert  into `message`(`id`,`pengantin_id`,`nama`,`pesan`,`created_at`) values 
(1,1,'Irul','tesssttt','2025-04-12 23:19:30');

/*Table structure for table `pengantin` */

DROP TABLE IF EXISTS `pengantin`;

CREATE TABLE `pengantin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_pria` varchar(100) DEFAULT NULL,
  `nama_wanita` varchar(100) DEFAULT NULL,
  `alamat_wanita` text,
  `ortu_pria` varchar(100) DEFAULT NULL,
  `ortu_wanita` varchar(100) DEFAULT NULL,
  `nama_panggilan_pria` varchar(100) DEFAULT NULL,
  `nama_panggilan_wanita` varchar(100) DEFAULT NULL,
  `tanggal_akad` date DEFAULT NULL,
  `jam_akad` time DEFAULT NULL,
  `tanggal_resepsi` date DEFAULT NULL,
  `jam_resepsi` time DEFAULT NULL,
  `foto_pria` varchar(255) DEFAULT NULL,
  `foto_wanita` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `pengantin` */

insert  into `pengantin`(`id`,`nama_pria`,`nama_wanita`,`alamat_wanita`,`ortu_pria`,`ortu_wanita`,`nama_panggilan_pria`,`nama_panggilan_wanita`,`tanggal_akad`,`jam_akad`,`tanggal_resepsi`,`jam_resepsi`,`foto_pria`,`foto_wanita`) values 
(1,'Zaini Ngabdilah','Arisa Chandra Pusparini','','Muhajir dan Romiyah','','Zain','Chandra','2025-06-20','08:00:00',NULL,NULL,NULL,NULL);

/*Table structure for table `story` */

DROP TABLE IF EXISTS `story`;

CREATE TABLE `story` (
  `id` int NOT NULL AUTO_INCREMENT,
  `bulan` varchar(20) DEFAULT NULL,
  `tahun` int DEFAULT NULL,
  `deskripsi` text,
  `pengantin_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pengantin_id` (`pengantin_id`),
  CONSTRAINT `story_ibfk_1` FOREIGN KEY (`pengantin_id`) REFERENCES `pengantin` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `story` */

/*Table structure for table `tamu` */

DROP TABLE IF EXISTS `tamu`;

CREATE TABLE `tamu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) DEFAULT NULL,
  `alamat` text,
  `pengantin_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pengantin_id` (`pengantin_id`),
  CONSTRAINT `tamu_ibfk_1` FOREIGN KEY (`pengantin_id`) REFERENCES `pengantin` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `tamu` */

insert  into `tamu`(`id`,`nama`,`alamat`,`pengantin_id`) values 
(1,'Keluarga Muhajir','Jl. Pesuruhan, Kaliwiro, Wonosobo',1);

/*Table structure for table `undangan_url` */

DROP TABLE IF EXISTS `undangan_url`;

CREATE TABLE `undangan_url` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pengantin_id` int NOT NULL,
  `tamu_id` int NOT NULL,
  `encrypted_token` varchar(255) NOT NULL,
  `url_undangan` text,
  `status_rsvp` enum('belum','hadir','tidak_hadir') DEFAULT 'belum',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `encrypted_token` (`encrypted_token`),
  KEY `fk_pengantin` (`pengantin_id`),
  KEY `fk_tamu` (`tamu_id`),
  CONSTRAINT `fk_pengantin` FOREIGN KEY (`pengantin_id`) REFERENCES `pengantin` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tamu` FOREIGN KEY (`tamu_id`) REFERENCES `tamu` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `undangan_url` */

insert  into `undangan_url`(`id`,`pengantin_id`,`tamu_id`,`encrypted_token`,`url_undangan`,`created_at`,`updated_at`) values 
(4,1,1,'fc43b3594b521c8380678cbf8f3b36de','http://localhost/invitation_wedding_apps/index.php?uid=fc43b3594b521c8380678cbf8f3b36de&guest=Keluarga+Muhajir','2025-04-12 19:42:24','2025-04-12 19:42:24');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`nama_lengkap`,`email`,`password`,`created_at`) values 
(1,'Amirul Putra Justicia, A.Md., S.Kom.','admin@gmail.com','$2y$10$FJkbHG04Um4uU2COfdjLDurnUJrld4oQObNyJT43445XshbBEgTgC','2025-04-11 10:18:10');

/*Table structure for table `wedding_gift` */

DROP TABLE IF EXISTS `wedding_gift`;

CREATE TABLE `wedding_gift` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pengantin_id` int NOT NULL,
  `bank_id` int DEFAULT NULL,
  `nama_penerima` varchar(100) DEFAULT NULL,
  `nomor_rekening` varchar(100) DEFAULT NULL,
  `catatan` text,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pengantin_id` (`pengantin_id`),
  KEY `bank_id` (`bank_id`),
  CONSTRAINT `wedding_gift_ibfk_1` FOREIGN KEY (`pengantin_id`) REFERENCES `pengantin` (`id`),
  CONSTRAINT `wedding_gift_ibfk_2` FOREIGN KEY (`bank_id`) REFERENCES `bank_list` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `wedding_gift` */

insert  into `wedding_gift`(`id`,`pengantin_id`,`bank_id`,`nama_penerima`,`nomor_rekening`,`catatan`,`created_at`) values 
(1,1,3,NULL,'1795464186','Arisa Chandra Pusparini','2025-04-14 10:32:15');

/*Table structure for table `log_akses_undangan` */

DROP TABLE IF EXISTS `log_akses_undangan`;

CREATE TABLE `log_akses_undangan` (
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

/*Data for the table `log_akses_undangan` */

/*Table structure for table `password_resets` */

DROP TABLE IF EXISTS `password_resets`;

CREATE TABLE `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `password_resets` */

/*Table structure for table `folders` */

DROP TABLE IF EXISTS `folders`;

CREATE TABLE `folders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_folder` varchar(255) NOT NULL,
  `deskripsi` text,
  `pengantin_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `pengantin_id` (`pengantin_id`),
  CONSTRAINT `folders_ibfk_1` FOREIGN KEY (`pengantin_id`) REFERENCES `pengantin` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*Data for the table `folders` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
