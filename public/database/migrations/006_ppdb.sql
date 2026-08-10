-- =========================================================
-- EduCMS Schema Migration: 006_ppdb.sql
-- Module: PPDB Admissions
-- =========================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------
-- Table: ppdb_settings
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `ppdb_settings`;
CREATE TABLE `ppdb_settings` (
    `id` INT AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `value` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    `deleted_by` INT DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_ppdb_setting_name` (`name`),
    INDEX `idx_ppdb_settings_deleted` (`deleted_at`),
    CONSTRAINT `fk_ppdb_settings_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Table: ppdb_applicants
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `ppdb_applicants`;
CREATE TABLE `ppdb_applicants` (
    `id` INT AUTO_INCREMENT,
    `registration_number` VARCHAR(50) NOT NULL,
    `full_name` VARCHAR(150) NOT NULL,
    `gender` ENUM('L', 'P') NOT NULL,
    `nisn` VARCHAR(20) DEFAULT NULL,
    `nik` VARCHAR(20) NOT NULL,
    `place_of_birth` VARCHAR(100) NOT NULL,
    `date_of_birth` DATE NOT NULL,
    `address` TEXT NOT NULL,
    `phone` VARCHAR(20) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `parent_name` VARCHAR(150) NOT NULL,
    `parent_phone` VARCHAR(20) NOT NULL,
    `previous_school` VARCHAR(150) NOT NULL,
    `status` ENUM('pending', 'verified', 'accepted', 'rejected') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    `deleted_by` INT DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_ppdb_registration` (`registration_number`),
    INDEX `idx_ppdb_applicants_deleted` (`deleted_at`),
    INDEX `idx_ppdb_applicants_status` (`status`),
    CONSTRAINT `fk_ppdb_applicants_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Table: ppdb_documents
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `ppdb_documents`;
CREATE TABLE `ppdb_documents` (
    `id` INT AUTO_INCREMENT,
    `applicant_id` INT NOT NULL,
    `document_type` VARCHAR(50) NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk_ppdb_documents_applicant_id` FOREIGN KEY (`applicant_id`) REFERENCES `ppdb_applicants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
