-- =========================================================
-- EduCMS Schema Migration: 014_program_to_ekstrakurikuler.sql
-- STATUS: RC5 Sprint 1 — RC5-DISCUSSION-001
--
-- Decision: "Program Unggulan" homepage section is NOT a new module.
-- It now reuses the existing, mature Ekstrakurikuler module (table,
-- CRUD, portal pages at /ekstrakurikuler) instead of adding a new table
-- or CRUD, keeping RC5 scope minimal.
--
-- This migration only updates the DEFAULT display title of the
-- existing `homepage.programs.title` setting from "Program Unggulan"
-- to "Ekstrakurikuler" — and only where it still holds that original
-- default value, so an admin who already customized this title keeps
-- their own text. The setting KEY itself (`programs.title`) is left
-- unchanged on purpose (same principle as RC5-003: relabel, don't
-- rename keys) — only the admin-facing LABEL and the frontend data
-- source change; the toggle plumbing stays identical.
-- =========================================================

UPDATE `settings`
SET `value` = 'Ekstrakurikuler'
WHERE `group_name` = 'homepage'
  AND `key` = 'programs.title'
  AND `value` = 'Program Unggulan';
