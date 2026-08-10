-- =========================================================
-- EduCMS Schema Migration: 007_system.sql
-- Module: System, Configurations, Layouts, Backups, Partners
-- =========================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------
-- Table: sliders
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `sliders`;
CREATE TABLE `sliders` (
    `id` INT AUTO_INCREMENT,
    `title` VARCHAR(200) DEFAULT NULL,
    `subtitle` VARCHAR(255) DEFAULT NULL,
    `image` VARCHAR(255) NOT NULL,
    `link` VARCHAR(255) DEFAULT NULL,
    `order_num` INT DEFAULT 0,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    `deleted_by` INT DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_sliders_deleted` (`deleted_at`),
    CONSTRAINT `fk_sliders_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Table: menu_groups
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `menu_groups`;
CREATE TABLE `menu_groups` (
    `id` INT AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    `deleted_by` INT DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_menu_groups_name` (`name`),
    UNIQUE KEY `uk_menu_groups_slug` (`slug`),
    INDEX `idx_menu_groups_deleted` (`deleted_at`),
    CONSTRAINT `fk_menu_groups_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Table: menus
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `menus`;
CREATE TABLE `menus` (
    `id` INT AUTO_INCREMENT,
    `group_id` INT NOT NULL,
    `title` VARCHAR(100) NOT NULL,
    `url` VARCHAR(255) NOT NULL,
    `parent_id` INT DEFAULT NULL,
    `order_num` INT DEFAULT 0,
    `target` ENUM('_self', '_blank') DEFAULT '_self',
    `is_system` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    `deleted_by` INT DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_menus_deleted` (`deleted_at`),
    CONSTRAINT `fk_menus_group_id` FOREIGN KEY (`group_id`) REFERENCES `menu_groups` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_menus_parent_id` FOREIGN KEY (`parent_id`) REFERENCES `menus` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_menus_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Table: settings
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
    `id` INT AUTO_INCREMENT,
    `group_name` ENUM('general', 'school', 'smtp', 'social', 'appearance', 'seo', 'system') NOT NULL,
    `key` VARCHAR(100) NOT NULL,
    `value` TEXT DEFAULT NULL,
    `is_autoload` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    `deleted_by` INT DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_group_key` (`group_name`, `key`),
    INDEX `idx_settings_deleted` (`deleted_at`),
    INDEX `idx_settings_autoload` (`is_autoload`),
    CONSTRAINT `fk_settings_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Table: seo_settings
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `seo_settings`;
CREATE TABLE `seo_settings` (
    `id` INT AUTO_INCREMENT,
    `page_name` VARCHAR(100) NOT NULL,
    `meta_title` VARCHAR(150) DEFAULT NULL,
    `meta_description` VARCHAR(255) DEFAULT NULL,
    `keywords` VARCHAR(255) DEFAULT NULL,
    `og_title` VARCHAR(150) DEFAULT NULL,
    `og_description` VARCHAR(255) DEFAULT NULL,
    `og_image` VARCHAR(255) DEFAULT NULL,
    `canonical_url` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    `deleted_by` INT DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_seo_page_name` (`page_name`),
    INDEX `idx_seo_deleted` (`deleted_at`),
    CONSTRAINT `fk_seo_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Table: redirects
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `redirects`;
CREATE TABLE `redirects` (
    `id` INT AUTO_INCREMENT,
    `source_url` VARCHAR(255) NOT NULL,
    `target_url` VARCHAR(255) NOT NULL,
    `status_code` INT DEFAULT 301,
    `hit_count` INT DEFAULT 0,
    `last_hit` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    `deleted_by` INT DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_redirect_source` (`source_url`),
    INDEX `idx_redirects_deleted` (`deleted_at`),
    CONSTRAINT `fk_redirects_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Table: downloads
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `downloads`;
CREATE TABLE `downloads` (
    `id` INT AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `file_size` VARCHAR(50) DEFAULT NULL,
    `download_count` INT DEFAULT 0,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    `deleted_by` INT DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_downloads_slug` (`slug`),
    INDEX `idx_downloads_deleted` (`deleted_at`),
    INDEX `idx_downloads_status` (`status`),
    CONSTRAINT `fk_downloads_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Table: activity_logs
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
    `id` INT AUTO_INCREMENT,
    `user_id` INT DEFAULT NULL,
    `module` VARCHAR(100) NOT NULL,
    `action` VARCHAR(100) NOT NULL,
    `old_value` LONGTEXT DEFAULT NULL,
    `new_value` LONGTEXT DEFAULT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `browser` VARCHAR(100) NOT NULL,
    `operating_system` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_activity_user` (`user_id`),
    CONSTRAINT `fk_activity_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Table: database_backups
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `database_backups`;
CREATE TABLE `database_backups` (
    `id` INT AUTO_INCREMENT,
    `filename` VARCHAR(255) NOT NULL,
    `filepath` VARCHAR(255) NOT NULL,
    `filesize` VARCHAR(50) NOT NULL,
    `created_by` INT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_backups_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Table: school_partners
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `school_partners`;
CREATE TABLE `school_partners` (
    `id` INT AUTO_INCREMENT,
    `name` VARCHAR(150) NOT NULL,
    `logo` VARCHAR(255) NOT NULL,
    `link` VARCHAR(255) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    `deleted_by` INT DEFAULT NULL,
    PRIMARY KEY (`id`),
    INDEX `idx_partners_deleted` (`deleted_at`),
    CONSTRAINT `fk_partners_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
