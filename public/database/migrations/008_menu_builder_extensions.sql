-- =========================================================
-- EduCMS Schema Migration: 008_menu_builder_extensions.sql
-- Adds status, icon, and description fields to menus table
-- =========================================================

ALTER TABLE `menus` ADD COLUMN `status` ENUM('published', 'draft') DEFAULT 'published' AFTER `target`;
ALTER TABLE `menus` ADD COLUMN `icon` VARCHAR(100) DEFAULT NULL AFTER `status`;
ALTER TABLE `menus` ADD COLUMN `description` VARCHAR(255) DEFAULT NULL AFTER `icon`;
