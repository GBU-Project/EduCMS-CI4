/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.18-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: educms
-- ------------------------------------------------------
-- Server version	10.11.18-MariaDB-0+deb12u1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `achievements`
--

DROP TABLE IF EXISTS `achievements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `achievements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `type` enum('academic','non-academic') DEFAULT 'academic',
  `level` enum('kecamatan','kabupaten','provinsi','nasional','internasional') DEFAULT 'kabupaten',
  `date` date NOT NULL,
  `winner` varchar(255) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_achievement_slug` (`slug`),
  KEY `idx_achievements_deleted` (`deleted_at`),
  KEY `idx_achievements_type` (`type`),
  KEY `idx_achievements_level` (`level`),
  KEY `fk_achievements_deleted_by` (`deleted_by`),
  KEY `idx_achievements_status` (`status`),
  CONSTRAINT `fk_achievements_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `achievements`
--

LOCK TABLES `achievements` WRITE;
/*!40000 ALTER TABLE `achievements` DISABLE KEYS */;
/*!40000 ALTER TABLE `achievements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `module` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `old_value` longtext DEFAULT NULL,
  `new_value` longtext DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `browser` varchar(100) NOT NULL,
  `operating_system` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_activity_user` (`user_id`),
  CONSTRAINT `fk_activity_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES
(1,1,'Auth','login',NULL,'{\"ip\":\"127.0.0.1\"}','127.0.0.1',' ','Unknown Platform','2026-08-07 23:31:47'),
(2,1,'Auth','login',NULL,'{\"ip\":\"127.0.0.1\"}','127.0.0.1',' ','Unknown Platform','2026-08-07 23:32:45'),
(3,1,'Auth','login',NULL,'{\"ip\":\"127.0.0.1\"}','127.0.0.1',' ','Unknown Platform','2026-08-08 23:53:49'),
(4,1,'Auth','login',NULL,'{\"ip\":\"127.0.0.1\"}','127.0.0.1',' ','Unknown Platform','2026-08-08 23:54:42');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `agendas`
--

DROP TABLE IF EXISTS `agendas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `agendas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime DEFAULT NULL,
  `coordinator` varchar(150) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_agenda_slug` (`slug`),
  KEY `idx_agendas_deleted` (`deleted_at`),
  KEY `idx_agendas_start` (`start_date`),
  KEY `fk_agendas_deleted_by` (`deleted_by`),
  KEY `idx_agendas_status` (`status`),
  CONSTRAINT `fk_agendas_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `agendas`
--

LOCK TABLES `agendas` WRITE;
/*!40000 ALTER TABLE `agendas` DISABLE KEYS */;
/*!40000 ALTER TABLE `agendas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `is_pinned` tinyint(1) DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_announcement_slug` (`slug`),
  KEY `idx_announcements_deleted` (`deleted_at`),
  KEY `idx_announcements_pinned` (`is_pinned`),
  KEY `fk_announcements_deleted_by` (`deleted_by`),
  KEY `idx_announcements_status` (`status`),
  CONSTRAINT `fk_announcements_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_category_slug` (`slug`),
  KEY `idx_categories_deleted` (`deleted_at`),
  KEY `fk_categories_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_categories_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contact_messages`
--

DROP TABLE IF EXISTS `contact_messages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `reply` text DEFAULT NULL,
  `replied_at` datetime DEFAULT NULL,
  `replied_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_contact_msg_deleted` (`deleted_at`),
  KEY `idx_contact_msg_read` (`is_read`),
  KEY `fk_contact_msg_replied_by` (`replied_by`),
  KEY `fk_contact_msg_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_contact_msg_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_contact_msg_replied_by` FOREIGN KEY (`replied_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contact_messages`
--

LOCK TABLES `contact_messages` WRITE;
/*!40000 ALTER TABLE `contact_messages` DISABLE KEYS */;
/*!40000 ALTER TABLE `contact_messages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `database_backups`
--

DROP TABLE IF EXISTS `database_backups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `database_backups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `filepath` varchar(255) NOT NULL,
  `filesize` varchar(50) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_backups_created_by` (`created_by`),
  CONSTRAINT `fk_backups_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `database_backups`
--

LOCK TABLES `database_backups` WRITE;
/*!40000 ALTER TABLE `database_backups` DISABLE KEYS */;
/*!40000 ALTER TABLE `database_backups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `downloads`
--

DROP TABLE IF EXISTS `downloads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `downloads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_size` varchar(50) DEFAULT NULL,
  `download_count` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_downloads_slug` (`slug`),
  KEY `idx_downloads_deleted` (`deleted_at`),
  KEY `idx_downloads_status` (`status`),
  KEY `fk_downloads_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_downloads_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `downloads`
--

LOCK TABLES `downloads` WRITE;
/*!40000 ALTER TABLE `downloads` DISABLE KEYS */;
/*!40000 ALTER TABLE `downloads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `extracurriculars`
--

DROP TABLE IF EXISTS `extracurriculars`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `extracurriculars` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `coach` varchar(150) DEFAULT NULL,
  `schedule` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_extra_slug` (`slug`),
  KEY `idx_extras_deleted` (`deleted_at`),
  KEY `fk_extras_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_extras_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `extracurriculars`
--

LOCK TABLES `extracurriculars` WRITE;
/*!40000 ALTER TABLE `extracurriculars` DISABLE KEYS */;
/*!40000 ALTER TABLE `extracurriculars` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `faqs`
--

DROP TABLE IF EXISTS `faqs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `faqs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  `order_position` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_faqs_deleted` (`deleted_at`),
  KEY `fk_faqs_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_faqs_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `faqs`
--

LOCK TABLES `faqs` WRITE;
/*!40000 ALTER TABLE `faqs` DISABLE KEYS */;
/*!40000 ALTER TABLE `faqs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `galleries`
--

DROP TABLE IF EXISTS `galleries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `galleries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `type` enum('photo','video') DEFAULT 'photo',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_gallery_slug` (`slug`),
  KEY `idx_galleries_deleted` (`deleted_at`),
  KEY `fk_galleries_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_galleries_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `galleries`
--

LOCK TABLES `galleries` WRITE;
/*!40000 ALTER TABLE `galleries` DISABLE KEYS */;
/*!40000 ALTER TABLE `galleries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gallery_items`
--

DROP TABLE IF EXISTS `gallery_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gallery_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gallery_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` enum('image','video_url') DEFAULT 'image',
  `caption` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_gallery_items_deleted` (`deleted_at`),
  KEY `fk_gallery_items_gallery_id` (`gallery_id`),
  KEY `fk_gallery_items_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_gallery_items_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_gallery_items_gallery_id` FOREIGN KEY (`gallery_id`) REFERENCES `galleries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gallery_items`
--

LOCK TABLES `gallery_items` WRITE;
/*!40000 ALTER TABLE `gallery_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `gallery_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media_library`
--

DROP TABLE IF EXISTS `media_library`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `media_library` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `disk_name` varchar(255) NOT NULL,
  `directory` varchar(100) NOT NULL,
  `extension` varchar(10) NOT NULL,
  `mime_type` varchar(100) NOT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `size` int(11) NOT NULL,
  `checksum` varchar(64) DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_media_disk_name` (`disk_name`),
  KEY `idx_media_deleted` (`deleted_at`),
  KEY `idx_media_checksum` (`checksum`),
  KEY `fk_media_uploaded_by` (`uploaded_by`),
  KEY `fk_media_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_media_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_media_uploaded_by` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media_library`
--

LOCK TABLES `media_library` WRITE;
/*!40000 ALTER TABLE `media_library` DISABLE KEYS */;
/*!40000 ALTER TABLE `media_library` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menu_groups`
--

DROP TABLE IF EXISTS `menu_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `menu_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_menu_groups_name` (`name`),
  UNIQUE KEY `uk_menu_groups_slug` (`slug`),
  KEY `idx_menu_groups_deleted` (`deleted_at`),
  KEY `fk_menu_groups_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_menu_groups_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menu_groups`
--

LOCK TABLES `menu_groups` WRITE;
/*!40000 ALTER TABLE `menu_groups` DISABLE KEYS */;
INSERT INTO `menu_groups` VALUES
(1,'Navigasi Utama','header','Menu navigasi utama di bagian atas website','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(2,'Link Kaki','footer','Menu cepat di bagian kaki website (footer)','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL);
/*!40000 ALTER TABLE `menu_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `url` varchar(255) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `order_num` int(11) DEFAULT 0,
  `target` enum('_self','_blank') DEFAULT '_self',
  `status` enum('published','draft') DEFAULT 'published',
  `icon` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_system` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_menus_deleted` (`deleted_at`),
  KEY `fk_menus_group_id` (`group_id`),
  KEY `fk_menus_parent_id` (`parent_id`),
  KEY `fk_menus_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_menus_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_menus_group_id` FOREIGN KEY (`group_id`) REFERENCES `menu_groups` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_menus_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `menus` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `menus`
--

LOCK TABLES `menus` WRITE;
/*!40000 ALTER TABLE `menus` DISABLE KEYS */;
INSERT INTO `menus` VALUES
(1,1,'Beranda','/',NULL,1,'_self','published',NULL,NULL,1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(2,1,'Tentang Kami','/page/profil',NULL,2,'_self','published',NULL,NULL,0,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(3,1,'Direktori','#',NULL,3,'_self','published',NULL,NULL,0,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(4,1,'Berita','/posts',NULL,4,'_self','published',NULL,NULL,1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(5,1,'PPDB Online','/ppdb',NULL,5,'_self','published',NULL,NULL,1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(6,1,'Hubungi Kami','/contact',NULL,6,'_self','published',NULL,NULL,1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(7,1,'Direktori Guru','/guru',3,1,'_self','published',NULL,NULL,1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(8,1,'Direktori Staf','/staff',3,2,'_self','published',NULL,NULL,1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(9,1,'Prestasi Sekolah','/prestasi',3,3,'_self','published',NULL,NULL,1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(10,1,'Ekstrakurikuler','/ekstrakurikuler',3,4,'_self','published',NULL,NULL,1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(11,2,'PPDB Online','/ppdb',NULL,1,'_self','published',NULL,NULL,1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(12,2,'Hubungi Kontak','/contact',NULL,2,'_self','published',NULL,NULL,1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(13,2,'Halaman Admin','/admin/login',NULL,3,'_blank','published',NULL,NULL,1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL);
/*!40000 ALTER TABLE `menus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pages`
--

DROP TABLE IF EXISTS `pages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `template` varchar(50) DEFAULT 'default',
  `status` enum('draft','published') DEFAULT 'draft',
  `author_id` int(11) DEFAULT NULL,
  `view_count` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_page_slug` (`slug`),
  KEY `idx_pages_deleted` (`deleted_at`),
  KEY `idx_pages_status` (`status`),
  KEY `fk_pages_author_id` (`author_id`),
  KEY `fk_pages_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_pages_author_id` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_pages_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pages`
--

LOCK TABLES `pages` WRITE;
/*!40000 ALTER TABLE `pages` DISABLE KEYS */;
/*!40000 ALTER TABLE `pages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permission_groups`
--

DROP TABLE IF EXISTS `permission_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permission_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_permission_group_name` (`name`),
  UNIQUE KEY `uk_permission_group_slug` (`slug`),
  KEY `idx_perm_groups_deleted` (`deleted_at`),
  KEY `fk_perm_groups_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_perm_groups_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permission_groups`
--

LOCK TABLES `permission_groups` WRITE;
/*!40000 ALTER TABLE `permission_groups` DISABLE KEYS */;
INSERT INTO `permission_groups` VALUES
(1,'System Management','system','Configure general parameters, logs, backups, and redirects','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(2,'User Authorization','auth','Kelola pengguna dan hak akses peran (RBAC)','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(3,'Content Management System','cms','Menerbitkan berita, halaman statis, menu, dan berkas media','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(4,'School Directories','school','Mengelola direktori guru, staf, prestasi, dan ekstrakurikuler','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(5,'Admissions (PPDB)','ppdb','Kelola pendaftaran siswa baru (PPDB Online)','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL);
/*!40000 ALTER TABLE `permission_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_permission_name` (`name`),
  KEY `idx_permissions_deleted` (`deleted_at`),
  KEY `fk_permissions_group_id` (`group_id`),
  KEY `fk_permissions_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_permissions_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_permissions_group_id` FOREIGN KEY (`group_id`) REFERENCES `permission_groups` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES
(1,1,'system.settings','Mengakses dan menyunting setelan website global','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(2,1,'system.logs','Melihat log audit aktivitas pengguna','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(3,1,'system.backups','Melakukan ekspor/import backup database SQL','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(4,1,'system.redirects','Mengelola URL redirects (301/302)','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(5,2,'users.view','Melihat daftar pengguna sistem','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(6,2,'users.manage','Membuat, menyunting, dan menonaktifkan pengguna','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(7,2,'roles.manage','Mengatur hak akses peran dan permissions','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(8,3,'pages.view','Melihat daftar halaman statis','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(9,3,'pages.manage','Menulis, mengedit, dan menghapus halaman statis','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(10,3,'posts.view','Melihat daftar artikel berita sekolah','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(11,3,'posts.manage','Menulis, mengedit, menerbitkan, dan menghapus berita sekolah','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(12,3,'media.manage','Mengunggah dan mengelola file di Media Library','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(13,3,'menus.manage','Mengatur menu navigasi website (Menu Builder)','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(14,3,'sliders.manage','Mengelola gambar slider di beranda utama','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(15,4,'teachers.manage','Mengelola data direktori guru sekolah','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(16,4,'staff.manage','Mengelola data direktori staf kependidikan','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(17,4,'extracurriculars.manage','Mengelola data ekstrakurikuler sekolah','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(18,4,'achievements.manage','Mengelola prestasi sekolah dan siswa','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(19,5,'ppdb.settings','Mengubah periode pendaftaran dan syarat PPDB','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(20,5,'ppdb.applicants','Memverifikasi berkas pendaftar baru PPDB','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(21,3,'sliders.view','Melihat daftar slider beranda','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(22,3,'menus.view','Melihat daftar menu navigasi','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(23,4,'teachers.view','Melihat daftar direktori guru','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(24,4,'staff.view','Melihat daftar direktori staf','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(25,4,'extracurriculars.view','Melihat daftar ekstrakurikuler','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(26,4,'achievements.view','Melihat daftar prestasi sekolah','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(27,1,'settings.view','Melihat halaman setelan website','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(28,1,'settings.manage','Menyunting setelan website (alias system.settings)','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(29,1,'redirects.view','Melihat daftar URL redirects','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(30,1,'redirects.manage','Mengelola URL redirects (alias system.redirects)','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(31,2,'messages.view','Melihat pesan kontak masuk','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(32,2,'messages.manage','Menghapus pesan kontak','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(33,5,'ppdb.view','Melihat daftar pendaftar PPDB (alias ppdb.applicants)','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(34,5,'ppdb.manage','Memverifikasi/menghapus pendaftar PPDB (alias ppdb.applicants)','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(35,2,'roles.view','Melihat daftar peran & hak akses','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(36,3,'media.view','Melihat daftar file di Media Library','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(37,3,'agendas.view','Melihat daftar agenda kegiatan sekolah','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(38,3,'agendas.manage','Mengelola agenda kegiatan sekolah','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(39,3,'announcements.view','Melihat daftar pengumuman sekolah','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(40,3,'announcements.manage','Mengelola pengumuman sekolah','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(41,3,'videos.view','Melihat daftar video sekolah','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(42,3,'videos.manage','Mengelola video sekolah (YouTube/Vimeo/Facebook/TikTok)','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(43,3,'partners.view','Melihat daftar mitra sekolah','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(44,3,'partners.manage','Mengelola logo dan data mitra sekolah','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(45,3,'testimonials.view','Melihat daftar testimoni','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(46,3,'testimonials.manage','Mengelola testimoni orang tua/alumni/siswa','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(47,3,'gallery.view','Melihat daftar galeri foto/video','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(48,3,'gallery.manage','Mengelola album galeri foto/video sekolah','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(49,3,'menu.view','Melihat item menu navigasi','2026-08-07 23:31:47','2026-08-07 23:31:47',NULL,NULL),
(50,3,'menu.create','Membuat item menu navigasi baru','2026-08-07 23:31:47','2026-08-07 23:31:47',NULL,NULL),
(51,3,'menu.edit','Mengedit item menu navigasi','2026-08-07 23:31:47','2026-08-07 23:31:47',NULL,NULL),
(52,3,'menu.delete','Menghapus item menu navigasi','2026-08-07 23:31:47','2026-08-07 23:31:47',NULL,NULL),
(53,3,'menu.restore','Memulihkan item menu navigasi dari sampah','2026-08-07 23:31:47','2026-08-07 23:31:47',NULL,NULL);
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_categories`
--

DROP TABLE IF EXISTS `post_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_categories` (
  `post_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  PRIMARY KEY (`post_id`,`category_id`),
  KEY `fk_post_categories_cat_id` (`category_id`),
  CONSTRAINT `fk_post_categories_cat_id` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_post_categories_post_id` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_categories`
--

LOCK TABLES `post_categories` WRITE;
/*!40000 ALTER TABLE `post_categories` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `post_tags`
--

DROP TABLE IF EXISTS `post_tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `post_tags` (
  `post_id` int(11) NOT NULL,
  `tag_id` int(11) NOT NULL,
  PRIMARY KEY (`post_id`,`tag_id`),
  KEY `fk_post_tags_tag_id` (`tag_id`),
  CONSTRAINT `fk_post_tags_post_id` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_post_tags_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `post_tags`
--

LOCK TABLES `post_tags` WRITE;
/*!40000 ALTER TABLE `post_tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `post_tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('draft','published','archived') DEFAULT 'draft',
  `author_id` int(11) DEFAULT NULL,
  `published_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `view_count` int(11) DEFAULT 0,
  `is_featured` tinyint(1) DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_post_slug` (`slug`),
  KEY `idx_posts_deleted` (`deleted_at`),
  KEY `idx_posts_status` (`status`),
  KEY `idx_posts_published_at` (`published_at`),
  KEY `fk_posts_author_id` (`author_id`),
  KEY `fk_posts_published_by` (`published_by`),
  KEY `fk_posts_updated_by` (`updated_by`),
  KEY `fk_posts_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_posts_author_id` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_posts_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_posts_published_by` FOREIGN KEY (`published_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_posts_updated_by` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ppdb_applicants`
--

DROP TABLE IF EXISTS `ppdb_applicants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppdb_applicants` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `registration_number` varchar(50) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `gender` enum('L','P') NOT NULL,
  `nisn` varchar(20) DEFAULT NULL,
  `nik` varchar(20) NOT NULL,
  `place_of_birth` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `parent_name` varchar(150) NOT NULL,
  `parent_phone` varchar(20) NOT NULL,
  `previous_school` varchar(150) NOT NULL,
  `status` enum('pending','verified','accepted','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ppdb_registration` (`registration_number`),
  KEY `idx_ppdb_applicants_deleted` (`deleted_at`),
  KEY `idx_ppdb_applicants_status` (`status`),
  KEY `fk_ppdb_applicants_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_ppdb_applicants_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ppdb_applicants`
--

LOCK TABLES `ppdb_applicants` WRITE;
/*!40000 ALTER TABLE `ppdb_applicants` DISABLE KEYS */;
/*!40000 ALTER TABLE `ppdb_applicants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ppdb_documents`
--

DROP TABLE IF EXISTS `ppdb_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppdb_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `applicant_id` int(11) NOT NULL,
  `document_type` varchar(50) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_ppdb_documents_applicant_id` (`applicant_id`),
  CONSTRAINT `fk_ppdb_documents_applicant_id` FOREIGN KEY (`applicant_id`) REFERENCES `ppdb_applicants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ppdb_documents`
--

LOCK TABLES `ppdb_documents` WRITE;
/*!40000 ALTER TABLE `ppdb_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `ppdb_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ppdb_settings`
--

DROP TABLE IF EXISTS `ppdb_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `ppdb_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_ppdb_setting_name` (`name`),
  KEY `idx_ppdb_settings_deleted` (`deleted_at`),
  KEY `fk_ppdb_settings_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_ppdb_settings_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ppdb_settings`
--

LOCK TABLES `ppdb_settings` WRITE;
/*!40000 ALTER TABLE `ppdb_settings` DISABLE KEYS */;
/*!40000 ALTER TABLE `ppdb_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `redirects`
--

DROP TABLE IF EXISTS `redirects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `redirects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source_url` varchar(255) NOT NULL,
  `target_url` varchar(255) NOT NULL,
  `status_code` int(11) DEFAULT 301,
  `hit_count` int(11) DEFAULT 0,
  `last_hit` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_redirect_source` (`source_url`),
  KEY `idx_redirects_deleted` (`deleted_at`),
  KEY `fk_redirects_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_redirects_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `redirects`
--

LOCK TABLES `redirects` WRITE;
/*!40000 ALTER TABLE `redirects` DISABLE KEYS */;
/*!40000 ALTER TABLE `redirects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permissions` (
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`role_id`,`permission_id`),
  KEY `fk_role_perms_perm_id` (`permission_id`),
  CONSTRAINT `fk_role_perms_perm_id` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_role_perms_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES
(2,1),
(2,2),
(2,4),
(2,5),
(2,6),
(2,8),
(2,9),
(2,10),
(2,11),
(2,12),
(2,13),
(2,14),
(2,15),
(2,16),
(2,17),
(2,18),
(2,19),
(2,20),
(2,21),
(2,22),
(2,23),
(2,24),
(2,25),
(2,26),
(2,27),
(2,28),
(2,29),
(2,30),
(2,31),
(2,32),
(2,33),
(2,34),
(2,36),
(2,37),
(2,38),
(2,39),
(2,40),
(2,41),
(2,42),
(2,43),
(2,44),
(2,45),
(2,46),
(2,47),
(2,48),
(2,49),
(2,50),
(2,51),
(2,52),
(2,53),
(3,8),
(3,9),
(3,10),
(3,11),
(3,12),
(3,14),
(3,36),
(3,37),
(3,38),
(3,39),
(3,40),
(3,41),
(3,42),
(3,43),
(3,44),
(3,45),
(3,46),
(3,47),
(3,48),
(5,19),
(5,20),
(5,33),
(5,34);
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_role_name` (`name`),
  KEY `idx_roles_deleted` (`deleted_at`),
  KEY `fk_roles_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_roles_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'Super Admin','Full system access bypasses all checks','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(2,'Administrator','Administrative access to CMS, Directories, and Settings','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(3,'Editor','Write, edit, and publish school news posts and static pages','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(4,'Guru','Manage teacher achievements and specific student logs','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(5,'PPDB Admin','Review and process student admissions (PPDB)','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `schema_migrations`
--

DROP TABLE IF EXISTS `schema_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `schema_migrations` (
  `version` varchar(20) NOT NULL,
  `migration_name` varchar(255) NOT NULL,
  `applied_at` datetime NOT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `schema_migrations`
--

LOCK TABLES `schema_migrations` WRITE;
/*!40000 ALTER TABLE `schema_migrations` DISABLE KEYS */;
INSERT INTO `schema_migrations` VALUES
('001','auth','2026-08-07 23:30:26'),
('002','cms','2026-08-07 23:30:26'),
('003','school','2026-08-07 23:30:26'),
('004','communication','2026-08-07 23:30:26'),
('005','media','2026-08-07 23:30:26'),
('006','ppdb','2026-08-07 23:30:26'),
('007','system','2026-08-07 23:30:27'),
('008','menu_builder_extensions','2026-08-07 23:30:27'),
('009','content_status_workflow','2026-08-07 23:30:27'),
('010','homepage_settings','2026-08-07 23:30:27'),
('011','settings_group_name_varchar','2026-08-07 23:30:27'),
('012','homepage_settings_seed','2026-08-07 23:30:27'),
('013','rc5_modules','2026-08-07 23:30:27'),
('014','program_to_ekstrakurikuler','2026-08-07 23:30:27'),
('015','theme_website','2026-08-07 23:30:27');
/*!40000 ALTER TABLE `schema_migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `school_partners`
--

DROP TABLE IF EXISTS `school_partners`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `school_partners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `logo` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_partners_deleted` (`deleted_at`),
  KEY `fk_partners_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_partners_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `school_partners`
--

LOCK TABLES `school_partners` WRITE;
/*!40000 ALTER TABLE `school_partners` DISABLE KEYS */;
/*!40000 ALTER TABLE `school_partners` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `seo_settings`
--

DROP TABLE IF EXISTS `seo_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `seo_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_name` varchar(100) NOT NULL,
  `meta_title` varchar(150) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `og_title` varchar(150) DEFAULT NULL,
  `og_description` varchar(255) DEFAULT NULL,
  `og_image` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_seo_page_name` (`page_name`),
  KEY `idx_seo_deleted` (`deleted_at`),
  KEY `fk_seo_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_seo_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `seo_settings`
--

LOCK TABLES `seo_settings` WRITE;
/*!40000 ALTER TABLE `seo_settings` DISABLE KEYS */;
INSERT INTO `seo_settings` VALUES
(1,'home','Portal Resmi SMA Negeri 1 EduCMS','Selamat datang di website resmi SMA Negeri 1 EduCMS. Dapatkan berita terbaru, profil guru, direktori prestasi, dan pendaftaran siswa baru (PPDB Online).','sekolah, sma negeri, educms, profil sekolah','Beranda Utama SMA Negeri 1 EduCMS','Akses berita resmi, pengumuman, agenda kegiatan akademik sekolah.',NULL,NULL,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(2,'posts','Berita & Pengumuman Terbaru','Temukan berita terkini, agenda kegiatan, pengumuman siswa, prestasi akademik dan non-akademik di SMA Negeri 1 EduCMS.','berita sekolah, pengumuman siswa, agenda sekolah','Portal Berita SMA Negeri 1 EduCMS','Daftar rilis pers sekolah terbaru dan arsip pengumuman.',NULL,NULL,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(3,'ppdb','PPDB Online | Pendaftaran Peserta Didik Baru','Pendaftaran Peserta Didik Baru (PPDB) Online SMA Negeri 1 EduCMS tahun ajaran baru telah dibuka. Isi formulir sekarang.','ppdb online, pendaftaran sekolah, daftar sma','Pendaftaran PPDB Online SMA Negeri 1 EduCMS','Panduan, alur, persyaratan dokumen, dan formulir aplikasi PPDB Online.',NULL,NULL,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL);
/*!40000 ALTER TABLE `seo_settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(50) NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `is_autoload` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_group_key` (`group_name`,`key`),
  KEY `idx_settings_deleted` (`deleted_at`),
  KEY `idx_settings_autoload` (`is_autoload`),
  KEY `fk_settings_deleted_by` (`deleted_by`),
  KEY `idx_group_name` (`group_name`),
  CONSTRAINT `fk_settings_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=138 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES
(1,'school','video_profile_url','',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(2,'homepage','hero.enabled','1',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(3,'homepage','hero.title','Selamat Datang',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(4,'homepage','hero.subtitle','',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(5,'homepage','hero.order','1',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(6,'homepage','videos.enabled','1',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(7,'homepage','videos.title','Video Profil Sekolah',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(8,'homepage','videos.subtitle','',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(9,'homepage','videos.order','2',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(10,'homepage','news.enabled','1',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(11,'homepage','news.title','Berita Terbaru',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(12,'homepage','news.subtitle','',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(13,'homepage','news.order','3',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(14,'homepage','stats.enabled','1',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(15,'homepage','stats.title','Statistik Sekolah',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(16,'homepage','stats.subtitle','',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(17,'homepage','stats.order','4',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(18,'homepage','announcements.enabled','1',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(19,'homepage','announcements.title','Pengumuman Terbaru',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(20,'homepage','announcements.subtitle','',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(21,'homepage','announcements.order','5',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(22,'homepage','agenda.enabled','1',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(23,'homepage','agenda.title','Agenda Mendatang',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(24,'homepage','agenda.subtitle','',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(25,'homepage','agenda.order','6',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(26,'homepage','programs.enabled','1',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(27,'homepage','programs.title','Ekstrakurikuler',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(28,'homepage','programs.subtitle','',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(29,'homepage','programs.order','7',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(30,'homepage','testimonials.enabled','1',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(31,'homepage','testimonials.title','Testimoni',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(32,'homepage','testimonials.subtitle','',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(33,'homepage','testimonials.order','8',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(34,'homepage','partners.enabled','1',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(35,'homepage','partners.title','Mitra Sekolah',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(36,'homepage','partners.subtitle','',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(37,'homepage','partners.order','9',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(38,'school','maps_embed','',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(39,'homepage','gallery.enabled','1',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(40,'homepage','gallery.title','Galeri Sekolah',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(41,'homepage','gallery.subtitle','',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(42,'homepage','gallery.order','10',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(43,'homepage','ppdb.enabled','1',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(44,'homepage','ppdb.title','Pendaftaran Siswa Baru',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(45,'homepage','ppdb.subtitle','',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(46,'homepage','ppdb.order','11',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(47,'theme','active_theme','default',1,'2026-08-07 23:30:27','2026-08-07 23:30:27',NULL,NULL),
(48,'general','site_name','EduCMS',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(49,'general','site_tagline','Modern School Portal Framework',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(50,'general','site_description','EduCMS adalah Content Management System (CMS) website sekolah berbasis CodeIgniter 3 yang modern, ringan, dan SEO-friendly.',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(51,'general','site_keywords','website sekolah, cms sekolah, educms, codeigniter 3 sekolah',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(52,'general','site_logo','uploads/settings/logo_default.png',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(53,'general','site_favicon','uploads/settings/favicon_default.ico',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(54,'school','name','SMA Negeri 1 EduCMS',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(55,'school','npsn','10293847',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(56,'school','accreditation','A',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(57,'school','headmaster','Drs. H. Budi Santoso, M.Pd.',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(58,'school','address','Jl. Raya Pendidikan No. 45, Kecamatan Sukamaju, Kota Metropolitan',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(59,'school','phone','(021) 555-0199',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(60,'school','email','info@sman1educms.sch.id',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(61,'homepage','welcome.enabled','1',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(62,'homepage','welcome.order','20',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(63,'homepage','vision.enabled','1',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(64,'homepage','vision.order','30',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(65,'homepage','cta.enabled','1',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(66,'homepage','cta.order','140',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(67,'social','facebook','https://facebook.com/sman1educms',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(68,'social','instagram','https://instagram.com/sman1educms',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(69,'social','youtube','https://youtube.com/c/sman1educms',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(70,'social','twitter','https://twitter.com/sman1educms',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(71,'appearance','theme_color','#6366f1',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(72,'appearance','primary_font','Outfit',1,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(73,'system','maintenance_mode','0',0,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(74,'system','allow_ppdb_registration','1',0,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(75,'smtp','host','smtp.mailtrap.io',0,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(76,'smtp','port','2525',0,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(77,'smtp','user','sman1educms_smtp',0,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(78,'smtp','password','smtp_pass_placeholder',0,'2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(126,'school','welcome_speech','',1,'2026-08-07 23:32:50','2026-08-07 23:32:50',NULL,NULL),
(127,'school','principal_photo','',1,'2026-08-07 23:32:50','2026-08-07 23:32:50',NULL,NULL),
(128,'school','vision','',1,'2026-08-07 23:32:50','2026-08-07 23:32:50',NULL,NULL),
(129,'school','mission','',1,'2026-08-07 23:32:50','2026-08-07 23:32:50',NULL,NULL),
(130,'school','wa_default_message','Halo Admin Sekolah, saya ingin bertanya informasi seputar sekolah.',1,'2026-08-07 23:32:50','2026-08-07 23:32:50',NULL,NULL),
(131,'seo','og_image','',1,'2026-08-07 23:32:50','2026-08-07 23:32:50',NULL,NULL),
(132,'homepage','welcome.title','Sambutan Kepala Sekolah',1,'2026-08-07 23:32:50','2026-08-07 23:32:50',NULL,NULL),
(133,'homepage','welcome.subtitle','',1,'2026-08-07 23:32:50','2026-08-07 23:32:50',NULL,NULL),
(134,'homepage','vision.title','Visi & Misi Sekolah',1,'2026-08-07 23:32:50','2026-08-07 23:32:50',NULL,NULL),
(135,'homepage','vision.subtitle','',1,'2026-08-07 23:32:50','2026-08-07 23:32:50',NULL,NULL),
(136,'homepage','cta.title','Penerimaan Peserta Didik Baru (PPDB)',1,'2026-08-07 23:32:50','2026-08-07 23:32:50',NULL,NULL),
(137,'homepage','cta.subtitle','',1,'2026-08-07 23:32:50','2026-08-07 23:32:50',NULL,NULL);
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sliders`
--

DROP TABLE IF EXISTS `sliders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sliders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `order_num` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_sliders_deleted` (`deleted_at`),
  KEY `fk_sliders_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_sliders_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sliders`
--

LOCK TABLES `sliders` WRITE;
/*!40000 ALTER TABLE `sliders` DISABLE KEYS */;
INSERT INTO `sliders` VALUES
(1,'Selamat Datang di EduCMS','Platform Web Sekolah Berbasis CodeIgniter 3 Modern, Cepat & Ringan','uploads/gallery/slider_default_1.jpg','/page/profil',1,'active','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL),
(2,'Penerimaan Siswa Baru (PPDB) Online','Pendaftaran PPDB Sekolah Telah Dibuka. Daftarkan Putra-Putri Anda Sekarang.','uploads/gallery/slider_default_2.jpg','/ppdb',2,'active','2026-08-07 23:31:29','2026-08-07 23:31:29',NULL,NULL);
/*!40000 ALTER TABLE `sliders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nik` varchar(50) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `gender` enum('L','P') NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `position` varchar(100) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_staff_nik` (`nik`),
  KEY `idx_staff_deleted` (`deleted_at`),
  KEY `idx_staff_status` (`status`),
  KEY `fk_staff_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_staff_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff`
--

LOCK TABLES `staff` WRITE;
/*!40000 ALTER TABLE `staff` DISABLE KEYS */;
/*!40000 ALTER TABLE `staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `tags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_tag_slug` (`slug`),
  KEY `idx_tags_deleted` (`deleted_at`),
  KEY `fk_tags_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_tags_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tags`
--

LOCK TABLES `tags` WRITE;
/*!40000 ALTER TABLE `tags` DISABLE KEYS */;
/*!40000 ALTER TABLE `tags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teachers`
--

DROP TABLE IF EXISTS `teachers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `teachers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nip` varchar(50) DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `gender` enum('L','P') NOT NULL,
  `place_of_birth` varchar(100) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `position` varchar(100) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_teacher_nip` (`nip`),
  KEY `idx_teachers_deleted` (`deleted_at`),
  KEY `idx_teachers_status` (`status`),
  KEY `fk_teachers_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_teachers_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teachers`
--

LOCK TABLES `teachers` WRITE;
/*!40000 ALTER TABLE `teachers` DISABLE KEYS */;
/*!40000 ALTER TABLE `teachers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testimonials`
--

DROP TABLE IF EXISTS `testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `role` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `rating` int(11) DEFAULT 5,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_testimonials_deleted` (`deleted_at`),
  KEY `fk_testimonials_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_testimonials_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testimonials`
--

LOCK TABLES `testimonials` WRITE;
/*!40000 ALTER TABLE `testimonials` DISABLE KEYS */;
/*!40000 ALTER TABLE `testimonials` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `fk_user_roles_role_id` (`role_id`),
  CONSTRAINT `fk_user_roles_role_id` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_roles_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` VALUES
(1,1);
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_username` (`username`),
  UNIQUE KEY `uk_email` (`email`),
  KEY `idx_users_deleted` (`deleted_at`),
  KEY `idx_users_status` (`status`),
  KEY `fk_users_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_users_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'admin','$2y$10$CXbv5sw8LjxGc5/FW8MUhuOqAjtUYbfjjKnSUWizGfyWJM.aHv1MK','admin@educms.local','Super Administrator',NULL,'active','2026-08-07 23:31:29','2026-08-07 23:31:42',NULL,NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `videos`
--

DROP TABLE IF EXISTS `videos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `platform` enum('youtube','vimeo','facebook','tiktok','other') NOT NULL DEFAULT 'youtube',
  `video_url` varchar(500) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT 0,
  `order_num` int(11) DEFAULT 0,
  `status` enum('draft','published') DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_video_slug` (`slug`),
  KEY `idx_videos_deleted` (`deleted_at`),
  KEY `fk_videos_deleted_by` (`deleted_by`),
  CONSTRAINT `fk_videos_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `videos`
--

LOCK TABLES `videos` WRITE;
/*!40000 ALTER TABLE `videos` DISABLE KEYS */;
/*!40000 ALTER TABLE `videos` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-08-09  1:53:01
