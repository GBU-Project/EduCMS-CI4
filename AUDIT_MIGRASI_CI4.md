# Audit Migrasi EduCMS: CodeIgniter 3 → CodeIgniter 4

Tanggal audit: 9 Agustus 2026
Sumber: `educms-dist.zip` (build hasil migrasi)

## Ringkasan Eksekutif

Aplikasi **sudah berjalan di atas skeleton CodeIgniter 4** (composer `codeigniter4/framework ^4.7`, PHP `^8.2`, entry point `public/index.php` via `CodeIgniter\Boot`, struktur folder `app/Config`, `app/Controllers`, dsb — semua standar CI4).

Namun ini **bukan migrasi/rewrite penuh ke gaya native CI4**. Yang sebenarnya terjadi: seluruh kode aplikasi (47 controller, semua model, semua library bisnis) **masih ditulis persis gaya CodeIgniter 3** (`$this->load->model()`, `$this->db`, `$this->input->post()`, `$this->session->set_flashdata()`, `extends CI_Controller`, `extends MY_Model`, dst). Di atasnya dipasang **compatibility shim** (lapisan kompatibilitas) buatan sendiri di `app/Libraries/CI_*.php` dan `Compat.php` yang mengemulasikan API loader/DB/session/input CI3 di atas request lifecycle CI4.

Ini adalah strategi migrasi yang **valid dan cukup umum** (strangler-fig / compat-layer) untuk menekan risiko rewrite besar — tapi penting untuk disadari bahwa ini **bukan "migrasi native ke CI4"** dalam arti memakai Model/Controller/Validation/Query Builder asli CI4. Ini perlu dikonfirmasi apakah memang strategi yang diinginkan, karena mempengaruhi maintainability, kompatibilitas dengan fitur CI4 baru (Filters, native Migrations, Entity, dsb), dan upgrade path ke depan.

---

## 1. Temuan Arsitektur

### 1.1 Compatibility Shim CI3-di-atas-CI4
File-file di `app/Libraries/`:

| File | Fungsi |
|---|---|
| `CI_Controller.php` | Base controller yang meniru `$this->load`, `$this->db`, `$this->input`, `$this->session`, dll., dibangun di atas `CodeIgniter\Controller` asli |
| `CI_Loader.php`, `CI_DB.php`, `CI_DB_utility.php`, `CI_Input.php`, `CI_Output.php`, `CI_Session.php`, `CI_Security.php`, `CI_URI.php`, `CI_Config.php`, `CI_Encryption.php`, `CI_Form_validation.php`, `CI_Upload.php`, `CI_User_agent.php`, `CI_Model.php` | Emulasi 1:1 API library inti CI3 |
| `Compat.php` | Registry statis untuk meniru `get_instance()` ala CI3 |
| `LegacyAutoRouter.php`, `LegacyRouter.php` | Emulasi auto-routing URI→Controller/Method gaya CI3 |
| `MY_Controller.php` | Base class `Admin_Controller`, `Portal_Controller`, `Admin_CRUD_Controller` — port langsung dari `application/core/MY_Controller.php` CI3 |
| `MY_Model.php` | Base model gaya CI3 (Active Record wrapper) |

**Dampak:** 61 file memakai `$this->load->...`, 15 file memakai `get_instance()`, 2 class masih `extends CI_Controller`. Ini berarti kode bisnis (controller, model) **belum** memakai fitur native CI4 seperti `$this->request`, `$this->validate()`, Query Builder `$this->db->table()`, Entity, atau native Filters untuk auth — semua masih lewat shim.

### 1.2 Routing
`app/Config/Routes.php` mengaktifkan **`$routes->setAutoRoute(true)`** ("Legacy Auto Routing") secara eksplisit untuk mempertahankan pola URL admin CI3 (`admin/posts/create`, dst). Ini didokumentasikan resmi oleh CI4 sebagai **berisiko keamanan** (bisa mengekspos method publik controller yang tidak dimaksudkan untuk diakses via URL) kecuali disiplin ketat menandai method internal sebagai `protected`/`private`. Perlu verifikasi bahwa semua method publik di controller Admin memang dimaksudkan untuk diakses langsung.

### 1.3 Database & Migrations
- `app/Database/Migrations/` — **kosong**. Tidak ada migration class native CI4 (`CodeIgniter\Database\Migration`), sehingga `php spark migrate` **tidak dapat dipakai** untuk instalasi/upgrade skema.
- Skema sesungguhnya berada di dua tempat yang harus disinkronkan manual:
  - `public/database/migrations/001_auth.sql` … `015_theme_website.sql` — 15 file SQL mentah, `DROP TABLE IF EXISTS` + `CREATE TABLE` (destruktif, bukan additive migration).
  - `educms_database.sql` — full dump (34 tabel) termasuk data seed (5 role, 1 user admin, sample content).
- Ada `Db_upgrade.php` (library custom, dipakai di `Admin_Controller` untuk mendeteksi "pending migrations" tiap request admin) — tapi ini mengecek tabel `schema_migrations`, bukan mekanisme migration resmi CI4.
- **Belum ada installer/wizard** untuk deploy awal: mengisi `.env`, membuat database, menjalankan skema, dan membuat akun admin pertama masih manual.

### 1.4 Konfigurasi & Kredensial
- `.env` yang ikut ter-bundle **berisi kredensial contoh** (`database.default.username = educms`, password, dan `encryption.key` yang di-hardcode) — file ini **tidak boleh** ikut dalam paket rilis/produksi. Harus digenerate ulang per-instalasi (ini justru salah satu alasan kuat perlunya web installer).
- `app.baseURL` hardcode ke `http://localhost:8080/`.

### 1.5 Autentikasi & Keamanan (temuan positif)
Beberapa hal sudah ditangani dengan baik dan terdokumentasi via komentar kode ("audit finding #N") — sepertinya sudah pernah ada audit internal sebelumnya:
- Password memakai `password_hash(..., PASSWORD_BCRYPT)` (bukan md5/sha1 gaya CI3 lama).
- Ada mekanisme deteksi "password default belum diganti" (`DEFAULT_SEEDED_PASSWORD_HASH` di `Auth_model`).
- Ada sanitasi HTML untuk field WYSIWYG (`sanitize_html`) sebelum disimpan — mencegah stored XSS dari konten CMS.
- RBAC granular (`roles`, `permissions`, `role_permissions`, `permission_groups`) dengan `check_permission()` di setiap aksi CRUD, termasuk pembatasan **force delete permanen hanya untuk Super Admin**.
- CSRF token di-refresh dan diekspos ulang via header untuk kompatibilitas AJAX berulang (multi-upload TinyMCE) — pendekatan masuk akal, tapi pastikan token lama benar-benar tidak bisa dipakai ulang (replay).

### 1.6 Testing
`tests/` berisi struktur PHPUnit CI4 standar (`tests/unit`, `tests/session`, `tests/database`) — bagus bahwa test suite sudah disiapkan, tapi belum sempat ditelusuri cakupannya (di luar ruang lingkup audit cepat ini; disarankan audit lanjutan khusus test coverage).

---

## 2. Rekomendasi Prioritas

| Prioritas | Temuan | Rekomendasi |
|---|---|---|
| Tinggi | Tidak ada native CI4 migration | Buat migration class asli (`php spark make:migration`) dari 15 file SQL yang ada, agar `spark migrate` bisa dipakai untuk instalasi & upgrade versi berikutnya secara additive (bukan `DROP TABLE`) |
| Tinggi | `.env` produksi berisi kredensial contoh & encryption key statis | Jangan bundling `.env` asli — sediakan `.env.example`, dan **web installer** men-generate `.env` + encryption key baru per-instalasi (sudah dibuatkan, lihat bagian 3) |
| Sedang | `setAutoRoute(true)` | Audit semua method publik di `app/Controllers/Admin/*` — pastikan tidak ada method yang harusnya internal tapi bisa diakses langsung via URL karena auto-routing |
| Sedang | Shim CI3 penuh (`CI_*`, `MY_Controller`, `MY_Model`) | Putuskan secara sadar: pertahankan sebagai strategi jangka panjang (dan dokumentasikan sebagai keputusan arsitektur), atau jadikan roadmap bertahap pindah ke native CI4 Controller/Model/Validation per modul |
| Rendah | Belum ada installer | Selesai — lihat bagian 3 |

---

## 3. Web Installer

Dibuat di `public/install/` (satu file mandiri, tidak bergantung bootstrap CI4 karena `.env` belum ada saat instalasi berjalan). Alur wizard 5 langkah gaya WordPress:

1. **Welcome** — info aplikasi & tombol mulai.
2. **Requirements Check** — PHP ≥ 8.2, ekstensi (`mysqli`, `intl`, `mbstring`, `json`, `curl`, `gd`), permission tulis ke `writable/`, `public/uploads/`, dan file `.env`.
3. **Database** — form host/port/nama DB/user/password + tombol test koneksi (validasi PDO sebelum lanjut).
4. **Install** — menulis `.env` (baseURL auto-detect, encryption key baru digenerate acak, kredensial DB dari langkah 3), lalu mengimpor skema+seed dari `educms_database.sql`.
5. **Buat Akun Admin** — form username/email/nama/password menggantikan akun admin seed bawaan (id=1), password di-hash `password_hash(BCRYPT)`, role Super Admin tetap terpasang.
6. **Selesai** — ringkasan + link login, mengunci installer (`public/install/install.lock`) agar tidak bisa dijalankan ulang tanpa dihapus manual, dan mengingatkan untuk menghapus folder `install/` di server produksi.

Lihat file yang sudah dibuat di `public/install/index.php`.
