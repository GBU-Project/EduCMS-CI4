-- =========================================================
-- EduCMS Schema Migration: 015_theme_website.sql
-- RC5-010 — Role-Based Theme Website & UI Styleguide split
--
-- Data-only, same pattern as migration 010/012/013: no ALTER TABLE, no
-- new table. `settings.group_name`/`key` already support arbitrary
-- values since migration 011.
--
-- Foundation only per RC5 scope: seeds a single `theme.active_theme`
-- key so the CMS has somewhere to read/write the active template from.
-- Only 'default' is a working template in RC5 — Modern/Corporate/Islamic
-- are reserved names (see themes/ directory structure) with no template
-- files yet, enforced server-side in Theme_website::save().
--
-- No new permissions are added: Theme Website reuses the existing
-- 'settings.view' / 'settings.manage' permissions (ids 27/28), which
-- Administrator already holds and Editor does not — this alone
-- satisfies the RC5-010 HAK AKSES table without any role/permission
-- changes.
--
-- Safe to run on a live database: no existing table is dropped, no
-- column removed, no value belonging to an existing row is changed.
-- =========================================================

INSERT INTO `settings` (`group_name`, `key`, `value`, `is_autoload`)
VALUES ('theme', 'active_theme', 'default', 1)
ON DUPLICATE KEY UPDATE `group_name` = `group_name`;
