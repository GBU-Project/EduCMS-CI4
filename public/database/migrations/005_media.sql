-- =========================================================
-- EduCMS Schema Migration: 005_media.sql
-- Module: Media Library & Galleries
-- =========================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------
-- Table: media_library
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `media_library`;
CREATE TABLE `media_library` (
    `id` INT AUTO_INCREMENT,
    `filename` VARCHAR(255) NOT NULL,
    `disk_name` VARCHAR(255) NOT NULL,
    `directory` VARCHAR(100) NOT NULL,
    `extension` VARCHAR(10) NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `width` INT DEFAULT NULL,
    `height` INT DEFAULT NULL,
    `size` INT NOT NULL,
    `checksum` VARCHAR(64) DEFAULT NULL,
    `alt_text` VARCHAR(255) DEFAULT NULL,
    `caption` VARCHAR(255) DEFAULT NULL,
    `uploaded_by` INT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    `deleted_by` INT DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_media_disk_name` (`disk_name`),
    INDEX `idx_media_deleted` (`deleted_at`),
    INDEX `idx_media_checksum` (`checksum`),
    CONSTRAINT `fk_media_uploaded_by` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_media_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Table: galleries
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `galleries`;
CREATE TABLE `galleries` (
    `id` INT AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `cover_image` VARCHAR(255) DEFAULT NULL,
    `type` ENUM('photo', 'video') DEFAULT 'photo',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    `deleted_by` INT DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_gallery_slug` (`slug`),
    INDEX `idx_galleries_deleted` (`deleted_at`),
    CONSTRAINT `fk_galleries_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Table: gallery_items
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `gallery_items`;
CREATE TABLE `gallery_items` (
    `id` INT AUTO_INCREMENT,
    `gallery_id` INT NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_type` ENUM('image', 'video_url') DEFAULT 'image',
    `caption` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    `deleted_by` INT DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_gallery_items_deleted` (`deleted_at`),
    CONSTRAINT `fk_gallery_items_gallery_id` FOREIGN KEY (`gallery_id`) REFERENCES `galleries` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_gallery_items_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
