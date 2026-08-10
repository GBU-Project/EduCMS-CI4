# EduCMS - Migrasi CI3 ke CI4

EduCMS v1.3.1 (CodeIgniter 3) dimigrasikan ke CodeIgniter 4.7.4 dengan lapisan kompatibilitas CI3 di atas CI4 asli. Seluruh fitur berfungsi; idioma CI3 (`$this->db`, `$this->load->model()`, `get_instance()`, `redirect()` yang menghentikan eksekusi) tetap bekerja via wrapper compat.

## Persyaratan

- PHP 8.1+ dengan ekstensi `mysqli`, `gd`, `mbstring`, `intl`
- MySQL 5.7+ / MariaDB 10.3+
- Composer 2 (hanya jika mau instal ulang `vendor/`; vendor sudah disertakan)

## Instalasi

```bash
# 1. Ekstrak arsip
unzip educms-dist.zip

# 2. Setup database
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS educms CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
mysql -u root -p educms < educms_database.sql

# 3. Sesuaikan kredensial DB di .env (default: localhost / educms / educms_pass)
#    database.default.database = educms
#    database.default.username = educms
#    database.default.password = educms_pass
#    app.baseURL = 'http://localhost:8080/'

# 4. Jalankan server
php spark serve --port 8080
# atau via PHP built-in server:
# php -S localhost:8080 -t public
```

## Akun Login Admin

- URL: `http://localhost:8080/admin/login`
- Username: `admin`
- Password: `Admin123!` (pengganti hash default yang sengaja diblokir oleh guard keamanan `Auth_model::DEFAULT_SEEDED_PASSWORD_HASH`)

## Fitur yang Sudah Terverifikasi

- Portal: `/`, `/berita`, `/pengumuman`, `/agenda`, `/galeri-foto`, `/galeri-video`, `/video`, `/ppdb`, `/kontak`, `/guru-staff`, `/prestasi`, `/ekstrakurikuler` (semua 200)
- Admin CRUD (34 halaman): posts, pages, users, menus, menu-groups, media, settings, categories, tags, videos, galleries, achievements, teachers, backup, system-upgrade, ppdb, messages, roles, logs, sliders, staff, testimonials, partners, redirects, theme-website, agendas, announcements, extracurriculars, styleguide
- CRUD tulis: create → edit → soft-delete → restore (posts), save settings
- Database Manager: backup (download gzip SQL), restore (Super Admin + password step-up, default dinonaktifkan via `ENABLE_WEB_DB_RESTORE`)
- Database Upgrade: 15 migrasi (`schema_migrations`) sudah diterapkan, integritas homepage valid

## Catatan

- `.env` berisi `baseURL=http://localhost:8080/` — sesuaikan bila port/domain berbeda.
- Debugbar CI4 aktif di lingkungan development (skrip Kint di HTML respons) — normal.
- `writable/` harus dapat ditulis (session, logs, cache).
- Untuk produksi, set `CI_ENVIRONMENT = production` di `.env`.
