-- =========================================================
-- EduCMS Schema Migration: 009_content_status_workflow.sql
-- STATUS: APPROVED (RC3 Blocker Review — Content Module Audit)
--
-- Brings Announcements, Agendas, and Achievements up to the same
-- Draft/Published workflow that Posts and Pages already have,
-- per the RC3 regression fix (BUG #1) content-module consistency
-- audit.
--
-- Design notes (per approved revision):
--   * Column type is VARCHAR(20), not ENUM. Posts/Pages/Menus use
--     ENUM for their existing status columns; those are left
--     untouched (changing a live ENUM column is a separate,
--     higher-risk change and was not requested). For these three
--     NEW columns, VARCHAR(20) avoids an ALTER TABLE ... MODIFY
--     every time a new status value (e.g. "archived",
--     "scheduled") is needed later — no schema dependency on the
--     fixed value list the way ENUM has.
--   * Column DEFAULT is 'draft' (matches the CMS convention that
--     brand-new content starts unpublished). This is intentionally
--     the OPPOSITE of what keeps existing rows visible, which is
--     why the UPDATE statements below immediately follow each
--     ALTER TABLE to explicitly re-publish everything that existed
--     before this migration ran. From this point forward, only
--     genuinely new inserts get the 'draft' default.
--   * NOT NULL is added (existing status columns on posts/pages
--     don't specify it) since a NULL status is not a meaningful
--     state for this workflow and would need extra NULL-handling
--     in every query otherwise.
--
-- Safe to run on a live database: each statement only adds a
-- column or updates existing rows; no data is dropped, no table
-- is locked exclusively for longer than a typical ADD COLUMN.
-- =========================================================

-- Announcements (Pengumuman)
ALTER TABLE `announcements`
    ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'draft' AFTER `is_pinned`;
ALTER TABLE `announcements`
    ADD INDEX `idx_announcements_status` (`status`);
UPDATE `announcements` SET `status` = 'published' WHERE `status` = 'draft';

-- Agendas
ALTER TABLE `agendas`
    ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'draft' AFTER `coordinator`;
ALTER TABLE `agendas`
    ADD INDEX `idx_agendas_status` (`status`);
UPDATE `agendas` SET `status` = 'published' WHERE `status` = 'draft';

-- Achievements (Prestasi)
ALTER TABLE `achievements`
    ADD COLUMN `status` VARCHAR(20) NOT NULL DEFAULT 'draft' AFTER `winner`;
ALTER TABLE `achievements`
    ADD INDEX `idx_achievements_status` (`status`);
UPDATE `achievements` SET `status` = 'published' WHERE `status` = 'draft';
