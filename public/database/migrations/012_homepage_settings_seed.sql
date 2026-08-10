-- =========================================================
-- EduCMS Schema Migration: 012_homepage_settings_seed.sql
-- STATUS: RC4 — Homepage Section Toggles (Blueprint v1.2, TASK 2)
--
-- Seeds the new `homepage` settings group used by the RC4 Homepage
-- Section Toggle feature, with a key structure that is already shaped
-- for the future Homepage Builder (RC5+):
--
--     homepage.<section>.enabled   -- '1' / '0', drives conditional
--                                      query + render in RC4
--     homepage.<section>.title     -- editable in Admin now, not yet
--                                      consumed by the RC4 view (see
--                                      home.php: RC4 only wires up
--                                      *.enabled per the locked scope)
--     homepage.<section>.subtitle  -- ditto, default '' per spec
--     homepage.<section>.order     -- seeded but inert in RC4 per
--                                      spec ("belum wajib digunakan")
--
-- Sections seeded, mapped against the existing homepage sections in
-- application/views/portal/home.php:
--   hero           -> Hero / Slider section
--   videos         -> "Video Profil Sekolah" section (singular content,
--                      plural setting key per spec)
--   news           -> covers BOTH "Berita Unggulan" and "Berita
--                      Terbaru" (the two news-driven sections; the
--                      locked spec has one "news" group, not separate
--                      featured/latest groups, so both are gated
--                      together)
--   stats          -> "Bar Statistik" section
--   announcements  -> "Pengumuman Terbaru" section
--   agenda         -> "Agenda Mendatang" section
--   programs, testimonials, partners
--                  -> seeded per spec for Homepage Builder readiness,
--                     but RC4's home.php/Home.php does NOT render a
--                     section for these: there is no "programs"
--                     content source at all, and while `testimonials`
--                     and `school_partners` tables exist in the
--                     schema, no model/controller/admin CRUD/query
--                     exists for them anywhere in the app yet. Building
--                     that is new feature work, out of scope for a
--                     one-pass RC4 (see Blueprint v1.2 preamble:
--                     "Jangan menambah fitur RC5"). Flagged in the RC4
--                     report for RC5 planning.
--
-- `is_autoload = 1` so Portal_Controller's `$this->site_settings`
-- picks these up automatically, same pattern as migration 010.
--
-- Idempotent: INSERT ... ON DUPLICATE KEY UPDATE against the existing
-- `uk_group_key` (group_name, key) unique key, matching migration
-- 010's pattern. Safe to run again / on a partially-applied site.
--
-- Depends on 011_settings_group_name_varchar.sql having already
-- widened `group_name` to VARCHAR — Db_upgrade runs migrations in
-- ascending numeric order, so 011 always runs first.
-- =========================================================

INSERT INTO `settings` (`group_name`, `key`, `value`, `is_autoload`) VALUES
('homepage', 'hero.enabled', '1', 1),
('homepage', 'hero.title', 'Selamat Datang', 1),
('homepage', 'hero.subtitle', '', 1),
('homepage', 'hero.order', '1', 1),

('homepage', 'videos.enabled', '1', 1),
('homepage', 'videos.title', 'Video Profil Sekolah', 1),
('homepage', 'videos.subtitle', '', 1),
('homepage', 'videos.order', '2', 1),

('homepage', 'news.enabled', '1', 1),
('homepage', 'news.title', 'Berita Terbaru', 1),
('homepage', 'news.subtitle', '', 1),
('homepage', 'news.order', '3', 1),

('homepage', 'stats.enabled', '1', 1),
('homepage', 'stats.title', 'Statistik Sekolah', 1),
('homepage', 'stats.subtitle', '', 1),
('homepage', 'stats.order', '4', 1),

('homepage', 'announcements.enabled', '1', 1),
('homepage', 'announcements.title', 'Pengumuman Terbaru', 1),
('homepage', 'announcements.subtitle', '', 1),
('homepage', 'announcements.order', '5', 1),

('homepage', 'agenda.enabled', '1', 1),
('homepage', 'agenda.title', 'Agenda Mendatang', 1),
('homepage', 'agenda.subtitle', '', 1),
('homepage', 'agenda.order', '6', 1),

('homepage', 'programs.enabled', '1', 1),
('homepage', 'programs.title', 'Program Unggulan', 1),
('homepage', 'programs.subtitle', '', 1),
('homepage', 'programs.order', '7', 1),

('homepage', 'testimonials.enabled', '1', 1),
('homepage', 'testimonials.title', 'Testimoni', 1),
('homepage', 'testimonials.subtitle', '', 1),
('homepage', 'testimonials.order', '8', 1),

('homepage', 'partners.enabled', '1', 1),
('homepage', 'partners.title', 'Mitra Sekolah', 1),
('homepage', 'partners.subtitle', '', 1),
('homepage', 'partners.order', '9', 1)

ON DUPLICATE KEY UPDATE `value` = `value`;
