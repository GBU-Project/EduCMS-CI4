-- =========================================================
-- EduCMS Schema Migration: 003_school.sql
-- Module: School Directories & Achievements
-- =========================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------
-- Table: teachers
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `teachers`;
CREATE TABLE `teachers` (
    `id` INT AUTO_INCREMENT,
    `nip` VARCHAR(50) DEFAULT NULL,
    `name` VARCHAR(150) NOT NULL,
    `gender` ENUM('L', 'P') NOT NULL,
    `place_of_birth` VARCHAR(100) DEFAULT NULL,
    `date_of_birth` DATE DEFAULT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `address` TEXT DEFAULT NULL,
    `photo` VARCHAR(255) DEFAULT NULL,
    `position` VARCHAR(100) NOT NULL,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    `deleted_by` INT DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_teacher_nip` (`nip`),
    INDEX `idx_teachers_deleted` (`deleted_at`),
    INDEX `idx_teachers_status` (`status`),
    CONSTRAINT `fk_teachers_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Table: staff
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `staff`;
CREATE TABLE `staff` (
    `id` INT AUTO_INCREMENT,
    `nik` VARCHAR(50) DEFAULT NULL,
    `name` VARCHAR(150) NOT NULL,
    `gender` ENUM('L', 'P') NOT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `email` VARCHAR(100) DEFAULT NULL,
    `address` TEXT DEFAULT NULL,
    `photo` VARCHAR(255) DEFAULT NULL,
    `position` VARCHAR(100) NOT NULL,
    `status` ENUM('active', 'inactive') DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    `deleted_by` INT DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_staff_nik` (`nik`),
    INDEX `idx_staff_deleted` (`deleted_at`),
    INDEX `idx_staff_status` (`status`),
    CONSTRAINT `fk_staff_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Table: extracurriculars
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `extracurriculars`;
CREATE TABLE `extracurriculars` (
    `id` INT AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(120) NOT NULL,
    `description` TEXT NOT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `coach` VARCHAR(150) DEFAULT NULL,
    `schedule` VARCHAR(100) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    `deleted_by` INT DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_extra_slug` (`slug`),
    INDEX `idx_extras_deleted` (`deleted_at`),
    CONSTRAINT `fk_extras_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- Table: achievements
-- ---------------------------------------------------------
DROP TABLE IF EXISTS `achievements`;
CREATE TABLE `achievements` (
    `id` INT AUTO_INCREMENT,
    `title` VARCHAR(255) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `type` ENUM('academic', 'non-academic') DEFAULT 'academic',
    `level` ENUM('kecamatan', 'kabupaten', 'provinsi', 'nasional', 'internasional') DEFAULT 'kabupaten',
    `date` DATE NOT NULL,
    `winner` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    `deleted_by` INT DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_achievement_slug` (`slug`),
    INDEX `idx_achievements_deleted` (`deleted_at`),
    INDEX `idx_achievements_type` (`type`),
    INDEX `idx_achievements_level` (`level`),
    CONSTRAINT `fk_achievements_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
