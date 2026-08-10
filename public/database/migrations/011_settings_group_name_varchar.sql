-- =========================================================
-- EduCMS Schema Migration: 011_settings_group_name_varchar.sql
-- STATUS: RC4 — Settings Group Structure (Blueprint v1.2, TASK 1)
--
-- Converts `settings.group_name` from a fixed ENUM to VARCHAR(50) so
-- future setting groups (e.g. 'homepage' in migration 012, and any
-- Homepage Builder / RC5 group after that) never require another
-- ALTER TABLE — they only ever need an INSERT.
--
-- Design notes:
--   * Verified before writing this migration: no PHP code anywhere in
--     the app validates or hardcodes the ENUM value list. Setting_model,
--     the Settings library, and the admin Settings controller all treat
--     `group_name` as an opaque string. Safe to widen the column type
--     without touching application code.
--   * VARCHAR(50) is generous versus the longest existing value
--     ('appearance' / 'smtp' / etc.) while keeping the index compact.
--   * MODIFY COLUMN on an ENUM -> VARCHAR that already contains only
--     values which fit the new type is a safe, non-destructive change;
--     no data is lost or reinterpreted (VARCHAR is a superset here).
--   * Idempotent: running MODIFY COLUMN again against a column that is
--     already VARCHAR(50) is a harmless no-op (MySQL/MariaDB does not
--     error on a redundant MODIFY to the same definition).
--   * Adds a dedicated single-column index on group_name. The existing
--     UNIQUE KEY `uk_group_key` (group_name, key) already allows the
--     optimizer to use group_name as a leftmost-prefix lookup, but this
--     migration adds the plain index explicitly as instructed, guarded
--     so it is skipped (not a hard failure) if it already exists —
--     Db_upgrade treats error 1061 (ER_DUP_KEYNAME) as benign.
--
-- Backward compatible with an RC3 database: no rows are dropped or
-- rewritten, only the column type and one index change.
-- =========================================================

ALTER TABLE `settings`
MODIFY `group_name` VARCHAR(50) NOT NULL;

ALTER TABLE `settings`
ADD INDEX `idx_group_name` (`group_name`);
