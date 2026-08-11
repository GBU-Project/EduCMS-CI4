# Ringkasan Perbaikan Audit Keamanan EduCMS-CI4

Dokumen ini mencatat seluruh perbaikan temuan audit keamanan yang telah diselesaikan pada repository `EduCMS-CI4` branch `develop`. Setiap temuan diselesaikan dalam **1 commit terpisah** dan dilengkapi dengan pengujian unit test.

---

## Tabel Ringkasan Temuan & Commit

| No | Tingkat Keparahan | Temuan Audit | Commit Hash | Pesan Commit | Status Test |
| :-: | :--- | :--- | :-: | :--- | :-: |
| **1** | **KRITIS** | Eskalasi Hak Akses ke Super Admin via Users/Roles Management (`create`, `edit`, & rename-swap) | `b9db786`<br>`2aa49d9` | `fix(security): prevent privilege escalation to Super Admin via users/roles management`<br>`fix(security): prevent privilege escalation via Super Admin role rename-swap and strengthen security tests` | `PASSED` |
| **2** | **KRITIS** | Route Admin Tanpa Filter Otentikasi/Otorisasi (`/admin/*` fail-closed by default) | `5de5713` | `fix(security): require authentication on all /admin/* routes by default (fail-closed)` | `PASSED` |
| **3** | **KRITIS** | Upload File Publik Tanpa Whitelist & Proteksi Eksekusi Script (PPDB Form) | `c1a01f2` | `fix(security): enforce upload allowlist and block PHP execution in public/uploads (PPDB form)` | `PASSED` |
| **4** | **KRITIS** | Upload File Admin Tanpa Whitelist Ekstensi (Media Library) | `0c53a99` | `refactor(security): extract shared upload validation guard and apply to Media controller` | `PASSED` |
| **5** | **SEDANG** | RBAC Fail-Open Saat Tabel Database Tidak Ditemukan | `d76b0ee` | `fix(security): make RbacNative fail closed instead of fail open when RBAC tables are missing` | `PASSED` |
| **6** | **SEDANG** | Permission `dashboard.view` Tidak Pernah Di-seed di Data Awal | `dc0121c` | `fix(seed): add missing dashboard.view permission to default seed data` | `PASSED` |
| **7** | **SEDANG** | Proteksi `.sql`, Regenerasi Session pada Login, & Secure Cookies | `b8ab7ab` | `fix(security): block public access to migration SQL files, regenerate session on login, enforce secure cookies in production` | `PASSED` |

---

## Rincian Perubahan & Pengamanan

### 1. Proteksi Privilege Escalation & Rename-Swap (Commit `b9db786` & `2aa49d9`)
- **`Users.php`**: `create()` dan `edit()` memeriksa apakah aktor yang sedang login (`session()->get('user_id')`) adalah Super Admin via `RbacNative::is_super_admin()`.
  - Actor non-Super-Admin yang mencoba menetapkan role Super Admin (ID 1 atau role bernama `'Super Admin'`/`'admin'`) pada akun manapun (baru, orang lain, atau diri sendiri) langsung ditolak dengan HTTP 403 & dicatat di audit log (`escalation_attempt_denied`).
  - Self-edit role oleh actor non-Super-Admin diblokir total.
- **`Roles.php`**:
  - **Penutupan Celah Rename-Swap (Commit `2aa49d9`)**: Actor non-Super-Admin diblokir dari mengubah/mengedit role yang bernama `'Super Admin'` / `'admin'`, serta diblokir dari merename role APAPUN menjadi `'Super Admin'` / `'admin'` (HTTP 403).
  - `create()` dan `edit()` membatasi permission yang dapat ditetapkan oleh actor non-Super-Admin hanya pada subset permission yang sudah mereka miliki.

### 2. Failure-Closed Route Filtering (Commit `5de5713`)
- Menghapus 4 baris un-filtered route di bagian awal `app/Config/Routes.php`.
- Menambahkan global URI pattern filter di `app/Config/Filters.php`:
  ```php
  'perm' => [
      'before' => ['admin', 'admin/*'],
      'except' => ['admin/login', 'admin/logout', 'admin/forgot'],
  ]
  ```
- Dibatasi secara fail-closed sehingga route admin baru otomatis memerlukan otentikasi login.
- Diuji dengan negative test untuk memastikan `/admin/login` tidak terjebak dalam infinite redirect loop.

### 3 & 4. Validasi Upload & Proteksi Eksekusi (Commit `c1a01f2` & `0c53a99`)
- Dibuat library terpusat `app/Libraries/UploadGuard.php`:
  - Validasi ekstensi & real MIME type via `finfo_file()`.
  - Deteksi double extension (`file.jpg.php`) & null-byte injection (`\0`).
  - Whitelist PPDB: `jpg, jpeg, png, pdf`.
  - Whitelist Media: `jpg, jpeg, png, gif, webp, pdf, docx, xlsx, mp4` (SVG dikeluarkan dari default whitelist untuk mencegah serangan XSS).
- Dibuat `public/uploads/.htaccess` untuk menolak eksekusi file `.php`, `.phtml`, `.phar`, `.py`, `.sh`, `.cgi`, dll.

### 5. RBAC Fail-Closed (Commit `d76b0ee`)
- Di `app/Libraries/RbacNative.php`, saat tabel `user_roles` / `role_permissions` tidak ditemukan di DB:
  - `has_permission()` mengembalikan `false` (sebelumnya `true`).
  - `is_super_admin()` mengembalikan `false` (sebelumnya `true`).
  - `get_user_permissions()` mengembalikan `[]` (sebelumnya `['all']`).
  - `get_user_roles()` mengembalikan `[]` (sebelumnya `['Super Admin']`).

### 6. Seeding Permission Dashboard (Commit `dc0121c`)
- Di `educms_database.sql`, ditambahkan permission ID 54 (`dashboard.view`) dan dipetakan ke role Admin (ID 2) dan Editor (ID 3).

### 7. Hardening Session, Cookie, & `.sql` (Commit `b8ab7ab`)
- Di `public/.htaccess`, ditambahkan aturan `<FilesMatch "\.sql$"> Require all denied </FilesMatch>`.
- Di `AuthNative::login()`, dipanggil `session()->regenerate(true)` untuk mencegah serangan Session Fixation.
- Di `app/Config/Cookie.php`, `$secure` diatur kondisional `ENVIRONMENT === 'production'`.

---

## Catatan Technical Debt

> [!NOTE]
> **Relokasi Folder Migrasi SQL**:
> Saat ini file migrasi `.sql` berada di folder `public/database/migrations/` dan diproteksi dengan aturan blokir di `public/.htaccess`.
> **Saran Jangka Panjang**: Pindahkan seluruh folder migrasi dari `public/database/migrations/` ke luar webroot (misalnya ke `app/Database/Migrations/` atau `database/migrations/`) dan perbarui properti `$migrationsPath` pada `DbUpgradeNative`. Hal ini untuk mengantisipasi lingkungan server Nginx yang tidak membaca file `.htaccess`.

> [!TIP]
> **Perbaikan 5 Query Column Mismatch pada Homepage (Commit `4ae0f11`)**:
> Mengoreksi 5 nama kolom yang mismatch pada `app/Controllers/Portal/Home.php` agar cocok 1:1 dengan skema database real:
> - `announcements`: `published_at` $\rightarrow$ `created_at`
> - `agendas`: `event_date` $\rightarrow$ `start_date`
> - `galleries`: menghapus filter `status` (kolom tidak ada di tabel)
> - `testimonials`: `status` $\rightarrow$ `is_active`
> - `school_partners`: menghapus filter `status` & `order_num` (kolom tidak ada di tabel)

> [!TIP]
> **Pencegahan Fatal Exception pada Koneksi Local HTTP (Commit `785ef49`)**:
> - **`app/Config/Cookie.php`**: Memperbarui `$this->secure` agar mengevaluasi ketersediaan koneksi HTTPS secara dinamis (`$isHttps`) sebelum mewajibkan cookie secure. Mencegah `SecurityException: Attempted to send a secure cookie over a non-secure connection` pada HTTP local.
> - **`app/Views/portal/partials/header.php` & `footer.php`**: Menggunakan null-coalescing `$header_menu ?? []` dan `$footer_menu ?? []` untuk mencegah `Undefined variable` notice/exception.
> - **`app/Config/Routes.php`**: Mengubah huruf kecil `['get', 'post']` menjadi kapital `['GET', 'POST']` untuk menghilangkan PHP Deprecation warning di CI4 4.7+.
> - **HTDOCS Sync & DB Connection**: Memperbarui kredensial database `.env` ke user `root` XAMPP lokal yang aktif.

---

## Hasil Akhir Unit Testing

```bash
PHPUnit 10.5.64 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.12
Configuration: C:\Users\koko1\Downloads\educms-ci4-FINAL\phpunit.dist.xml

.............                                                     13 / 13 (100%)

Time: 00:00.575, Memory: 18.00 MB

OK (13 tests, 23 assertions)
```
