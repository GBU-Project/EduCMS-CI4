-- =========================================================
-- EduCMS Schema Migration: 013_rc5_modules.sql
-- STATUS: RC5 Sprint 1 — New content modules + homepage sync
--
-- Design notes (per RC5 "Minimal changes, maximum stability"):
--   * `videos` is the ONLY brand new table in this migration. It stores
--     metadata + an external URL/embed only (YouTube/Vimeo/Facebook/
--     TikTok/other) — no video file is ever uploaded to this server.
--     `thumbnail` is a media path exactly like every other image field
--     in this project (Media Library / Image Picker compatible).
--   * `school_partners` and `testimonials` already exist since the
--     original schema — this migration does NOT touch either table,
--     only adds the CRUD permissions that were always missing for them.
--   * Every permission/role_permission INSERT here is idempotent via
--     INSERT IGNORE against the existing PKs/unique keys, same pattern
--     as every prior migration.
--   * Homepage settings additions (`homepage.gallery.*`,
--     `homepage.ppdb.*`) are data-only INSERTs, same pattern as
--     migration 010/012 — no ALTER TABLE, `settings.group_name` already
--     supports arbitrary group names since migration 011.
--   * `school.maps_embed` is a single new Data Sekolah field (Google
--     Maps iframe embed), also data-only.
--   * Safe to run on a live database: no existing table is dropped, no
--     column removed, no value belonging to an existing row is changed.
-- =========================================================

-- ---------------------------------------------------------
-- 1. New table: videos (RC5-005 — Modul Video)
-- ---------------------------------------------------------
CREATE TABLE IF NOT EXISTS `videos` (
    `id` INT AUTO_INCREMENT,
    `title` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(220) NOT NULL,
    `platform` ENUM('youtube', 'vimeo', 'facebook', 'tiktok', 'other') NOT NULL DEFAULT 'youtube',
    `video_url` VARCHAR(500) NOT NULL,
    `thumbnail` VARCHAR(255) DEFAULT NULL,
    `description` TEXT DEFAULT NULL,
    `is_featured` TINYINT(1) DEFAULT 0,
    `order_num` INT DEFAULT 0,
    `status` ENUM('draft', 'published') DEFAULT 'draft',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    `deleted_by` INT DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_video_slug` (`slug`),
    INDEX `idx_videos_deleted` (`deleted_at`),
    CONSTRAINT `fk_videos_deleted_by` FOREIGN KEY (`deleted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------
-- 2. New permissions (group_id 3 = Content Management System)
--    Videos, Mitra (school_partners), Testimoni, Galeri
-- ---------------------------------------------------------
INSERT IGNORE INTO `permissions` (`id`, `group_id`, `name`, `description`) VALUES
(41, 3, 'videos.view', 'Melihat daftar video sekolah'),
(42, 3, 'videos.manage', 'Mengelola video sekolah (YouTube/Vimeo/Facebook/TikTok)'),
(43, 3, 'partners.view', 'Melihat daftar mitra sekolah'),
(44, 3, 'partners.manage', 'Mengelola logo dan data mitra sekolah'),
(45, 3, 'testimonials.view', 'Melihat daftar testimoni'),
(46, 3, 'testimonials.manage', 'Mengelola testimoni orang tua/alumni/siswa'),
(47, 3, 'gallery.view', 'Melihat daftar galeri foto/video'),
(48, 3, 'gallery.manage', 'Mengelola album galeri foto/video sekolah');

-- Administrator (Role ID: 2) gets full access to all 4 new modules
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(2, 41), (2, 42), (2, 43), (2, 44), (2, 45), (2, 46), (2, 47), (2, 48);

-- Editor (Role ID: 3) also gets these — same content-editor role that
-- already manages Posts/Pages/Sliders/Media
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(3, 41), (3, 42), (3, 43), (3, 44), (3, 45), (3, 46), (3, 47), (3, 48);

-- ---------------------------------------------------------
-- 3. Data Sekolah: Google Maps Embed (RC5-002)
-- ---------------------------------------------------------
INSERT INTO `settings` (`group_name`, `key`, `value`, `is_autoload`)
VALUES ('school', 'maps_embed', '', 1)
ON DUPLICATE KEY UPDATE `group_name` = `group_name`;

-- ---------------------------------------------------------
-- 4. Homepage Modules: new toggles for Gallery and PPDB CTA
--    (RC5-009 audit). `videos.*` / `partners.*` / `testimonials.*`
--    toggles already exist since migration 012 and are reused as-is.
-- ---------------------------------------------------------
INSERT INTO `settings` (`group_name`, `key`, `value`, `is_autoload`) VALUES
('homepage', 'gallery.enabled', '1', 1),
('homepage', 'gallery.title', 'Galeri Sekolah', 1),
('homepage', 'gallery.subtitle', '', 1),
('homepage', 'gallery.order', '10', 1),
('homepage', 'ppdb.enabled', '1', 1),
('homepage', 'ppdb.title', 'Pendaftaran Siswa Baru', 1),
('homepage', 'ppdb.subtitle', '', 1),
('homepage', 'ppdb.order', '11', 1)
ON DUPLICATE KEY UPDATE `group_name` = `group_name`;
