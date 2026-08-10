# Panduan Migrasi Penuh EduCMS: CI3-Compat-Shim → Native CodeIgniter 4

**Untuk:** AI coding agent (mis. Antigravity) yang akan mengeksekusi migrasi.
**Konteks:** Dokumen ini ditulis berdasarkan audit langsung + sesi debugging nyata
terhadap codebase ini. Setiap "jangan lakukan X" di bawah adalah bug yang **benar-benar
terjadi** di aplikasi ini sebelum diperbaiki, bukan teori.

---

## 0. Baca ini dulu sebelum menyentuh kode apapun

Aplikasi ini **sudah berjalan di atas skeleton CodeIgniter 4** (composer, entry point,
struktur folder — semua standar CI4). Yang **belum** migrasi adalah **kode bisnisnya**:
47 controller dan 28 model masih ditulis 100% gaya CodeIgniter 3
(`$this->load->model()`, `$this->db`, `$this->input->post()`, `extends CI_Controller`,
`extends MY_Model`, dst), dijalankan lewat **compatibility shim** buatan sendiri di
`app/Libraries/CI_*.php`, `MY_Controller.php`, `MY_Model.php`, dan `Compat.php`.

**Tugasnya:** menghilangkan ketergantungan pada shim tersebut, modul demi modul, sampai
seluruh `app/Libraries/CI_*.php`, `MY_Controller.php`, `MY_Model.php`, `Compat.php`,
`LegacyAutoRouter.php`, `LegacyRouter.php`, `Legacy_View.php` bisa **dihapus total**.

**Strategi wajib: strangler pattern (bertahap per modul), BUKAN big-bang rewrite.**
Shim dan kode native boleh hidup berdampingan selama masa transisi. Jangan pernah
mencoba mengganti semua modul dalam satu commit/PR besar.

---

## 1. Aturan keras (non-negotiable)

1. **Satu modul = satu unit kerja selesai + teruji, baru lanjut ke modul berikutnya.**
   Jangan buka banyak modul setengah-jadi sekaligus.
2. **Jangan ubah skema database** kecuali diminta eksplisit. Migrasi ini soal
   *kode*, bukan *data*. Tabel yang sudah ada (lihat `educms_database.sql`) tetap dipakai
   apa adanya.
3. **Jangan hapus file `app/Libraries/CI_*.php` dkk sampai SEMUA modul yang masih
   memakainya sudah dipindah.** Cek dengan `grep -rl` sebelum menghapus apapun (lihat
   §6 Checklist Selesai).
4. **Setiap modul yang sudah "native", TIDAK BOLEH lagi memanggil** `$this->load->`,
   `$this->db->` (versi shim), `$this->input->`, `$this->session->` (versi shim),
   `$this->security->` (versi shim), `extends MY_Model`, `extends MY_Controller`,
   `extends Admin_Controller`/`Portal_Controller`/`Admin_CRUD_Controller` (semua ini
   turunan `MY_Controller`).
5. **Setiap perubahan HARUS dites secara fungsional** (bukan cuma `php -l`) sebelum
   dianggap selesai — jalankan aplikasinya, coba CRUD-nya (create/read/update/delete)
   sungguhan, cek juga halaman publik terkait kalau modul itu tampil di frontend.
6. **CSRF, cookie, dan cache adalah area paling rawan di aplikasi ini** (lihat §7 —
   ini bukan teori, ini bug yang benar-benar terjadi dan sudah diperbaiki). Jangan
   sentuh `app/Config/Security.php`, `app/Config/Cache.php`, atau `app/Common.php`
   tanpa memahami §7 secara penuh terlebih dahulu.
7. **Jangan pernah membuat helper/wrapper baru yang meniru-niru pola CI3.** Tujuan
   migrasi ini adalah *menghilangkan* pola itu, bukan menambahnya.

---

## 2. Inventaris modul & urutan prioritas

Urutkan dari yang **paling aman dikerjakan duluan** (kecil, jarang berubah, resiko
rendah) ke yang **paling berisiko** (kompleks, dipakai modul lain, banyak AJAX).

### Tier 1 — Kerjakan pertama (modul CRUD sederhana, tanpa file upload/AJAX kompleks)
`Redirects`, `Testimonials`, `Partners`, `Tags`, `Categories`, `Menu_groups`, `Menus`

### Tier 2 — Setelah Tier 1 lancar (masih CRUD sederhana + kaitan portal)
`Achievements`, `Agendas`, `Announcements`, `Extracurriculars`, `Staff`, `Teachers`,
`Videos`, `Galleries`

### Tier 3 — Kompleksitas menengah (relasi lebih banyak / rich text)
`Pages`, `Posts`, `Sliders`

### Tier 4 — Kompleks / sensitif, kerjakan paling akhir, extra hati-hati
`Media` (upload, lihat §7 — SUDAH PERNAH JADI SUMBER BANYAK BUG), `Ppdb`
(pendaftaran online, form kompleks), `Settings` (site-wide config), `Users`, `Roles`
(RBAC — kalau salah, bisa mengunci semua orang dari admin panel), `Auth` (login/logout
— **lihat §7.2, ada bug fatal yang baru diperbaiki di sini**), `Theme_website`,
`Backup`, `System_upgrade`

Setiap controller di Admin punya pasangan model masing-masing di `app/Models/` —
migrasikan keduanya bersamaan (controller + model) dalam satu unit kerja, jangan
dipisah.

Controller `app/Controllers/Portal/*.php` (13 file, halaman publik) juga masih CI3-style
— migrasikan setelah modul Admin pasangannya selesai (mis. `Portal/News.php` setelah
`Admin/Posts.php`), karena keduanya biasanya berbagi model yang sama.

---

## 3. Pola konversi (before → after)

### 3.1 Controller

**SEBELUM (CI3 shim):**
```php
<?php
namespace App\Controllers\Admin;

use \Admin_CRUD_Controller;
#[AllowDynamicProperties]
class Testimonials extends Admin_CRUD_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('testimonial_model');
    }

    public function index() {
        $this->check_permission('testimonials.view');
        $data['items'] = $this->testimonial_model->get_all();
        $this->template->load_admin('admin/testimonials/index', $data);
    }
}
```

**SESUDAH (native CI4):**
```php
<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TestimonialModel;

class Testimonials extends BaseController
{
    protected TestimonialModel $testimonialModel;

    public function __construct()
    {
        $this->testimonialModel = new TestimonialModel();
    }

    public function index()
    {
        // RBAC: lihat §3.4 di bawah — jangan panggil check_permission() shim.
        if (! auth()->hasPermission('testimonials.view')) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        $data['items'] = $this->testimonialModel->findAll();

        return view('admin/testimonials/index', $data);
    }
}
```

Catatan:
- `BaseController` sudah disediakan CI4 di `app/Controllers/BaseController.php` — **cek
  dulu isinya**, mungkin perlu ditambah properti bersama (mis. current user, RBAC
  helper) yang tadinya disuntik oleh `MY_Controller`. Tambahkan di `BaseController`,
  **jangan** buat class dasar baru yang meniru `MY_Controller`.
- Hapus `#[AllowDynamicProperties]` — kalau controller native tidak butuh properti
  dinamis, atribut ini tidak perlu sama sekali. (Ingat: di codebase lama, atribut ini
  pernah salah ditulis tanpa `\` di depan dan bikin error di 13 file — jangan ulangi
  pola ini di kode baru.)

### 3.2 Model

**SEBELUM (CI3 shim / `MY_Model`):**
```php
<?php
class Testimonial_model extends MY_Model {
    protected $table = 'testimonials';

    public function get_all() {
        return $this->db->order_by('created_at', 'DESC')->get($this->table)->result();
    }
}
```

**SESUDAH (native CI4 `CodeIgniter\Model`):**
```php
<?php

namespace App\Models;

use CodeIgniter\Model;

class TestimonialModel extends Model
{
    protected $table            = 'testimonials';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array'; // atau Entity class kalau mau lebih jauh
    protected $useSoftDeletes   = false;   // cek dulu skema tabel: ada kolom deleted_at?
    protected $allowedFields    = ['name', 'role', 'quote', 'photo', 'status'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        // isi sesuai validasi yang SEBELUMNYA ada di CI_Form_validation config
        // untuk modul ini — JANGAN dihilangkan, cuma dipindah ke sini.
    ];

    public function getAllOrdered()
    {
        return $this->orderBy('created_at', 'DESC')->findAll();
    }
}
```

**Penting soal nama file & namespace:** CI4 pakai PSR-4. `Testimonial_model.php` (CI3
style, snake_case + suffix `_model`) harus jadi `TestimonialModel.php` (PascalCase,
suffix `Model`) di `app/Models/`, class-nya `App\Models\TestimonialModel`. Update
**semua** pemanggil model lama ke nama/namespace baru — jangan biarkan file lama dan
baru berdua ada untuk model yang sama (kalau modul lain masih butuh versi CI3-nya
untuk sementara, biarkan file lama itu **utuh tidak disentuh** sampai modul itu juga
dimigrasikan, dengan nama class lama).

### 3.3 View

**SEBELUM:**
```php
$this->load->view('admin/testimonials/index', $data);
// atau lewat Template library
$this->template->load_admin('admin/testimonials/index', $data);
```

**SESUDAH:**
```php
return view('admin/testimonials/index', $data);
```

Kalau modul itu butuh layout admin (header/sidebar/footer) yang tadinya disuntik
`Template::load_admin()`, **cek dulu logic di `app/Libraries/Template.php`**
(§5 di bawah) sebelum menulis ulang — ada logic tema (`public/themes/{theme}/views/`)
di situ yang harus dipertahankan perilakunya kalau modul itu juga tampil di portal
publik.

### 3.4 Request, Session, Security, RBAC — API pengganti

| Gaya lama (shim) | Gaya native CI4 |
|---|---|
| `$this->input->post('x')` | `$this->request->getPost('x')` |
| `$this->input->post()` (semua) | `$this->request->getPost()` |
| `$this->input->get('x')` | `$this->request->getGet('x')` |
| `$this->input->is_ajax_request()` | `$this->request->isAJAX()` |
| `$this->session->set_userdata(...)` | `session()->set(...)` |
| `$this->session->set_flashdata(...)` | `session()->setFlashdata(...)` |
| `$this->security->get_csrf_hash()` | `csrf_hash()` (helper bawaan CI4) |
| `$this->load->helper('x')` | `helper('x')` (function helper CI4) atau `use` langsung |
| `$this->load->library('x')` | `service('x')` atau instansiasi langsung |
| `check_permission('x.y')` (shim) | Buat **satu** filter CI4 resmi, lihat di bawah |

**RBAC — jangan port `check_permission()` mentah-mentah.** Bikin CI4 Filter resmi
(`app/Filters/PermissionFilter.php`) yang membaca argumen permission dari
`Routes.php`, dan daftarkan sebagai alias filter. Ini menggantikan pola "panggil
`check_permission()` di baris pertama tiap method" dengan pola native CI4 (filter per
route), jauh lebih aman karena tidak bisa lupa dipanggil. Logic `Rbac.php` yang sudah
ada (`app/Libraries/Rbac.php` — pengecekan Super Admin bypass, dsb) **boleh dipakai
lagi isinya**, cuma dibungkus jadi Filter, bukan dipanggil manual di tiap controller.

### 3.5 Routing

Modul yang sudah native **wajib** didaftarkan eksplisit di `app/Config/Routes.php`
(pakai `$routes->get()`, `$routes->post()`, `$routes->resource()`, dst), **tidak boleh**
mengandalkan `$routes->setAutoRoute(true)` yang masih aktif untuk modul-modul lama.
Auto-routing CI4 secara resmi didokumentasikan sebagai risiko keamanan (bisa
mengekspos method publik yang tidak dimaksud diakses langsung) — begitu semua modul
sudah punya route eksplisit, `setAutoRoute(true)` di `Routes.php` **dimatikan**
sebagai langkah terakhir migrasi (§6).

---

## 4. Migrasi database (skema)

Saat ini skema hanya ada sebagai SQL mentah (`educms_database.sql` +
`public/database/migrations/001..015.sql`, memakai `DROP TABLE IF EXISTS` —
destruktif, bukan additive). Sebagai bagian dari migrasi (boleh dikerjakan paralel,
tidak harus nunggu semua controller selesai):

1. Untuk **setiap** tabel yang sudah ada, buat migration class asli:
   `php spark make:migration CreateTestimonialsTable` (dst per tabel), isi method
   `up()`/`down()` **mencerminkan skema yang SUDAH ADA** di `educms_database.sql`
   (jangan ubah struktur kolom — ini port, bukan desain ulang).
2. Setelah semua tabel punya migration class, seed data awal (`roles`, `permissions`
   default) pindahkan ke `app/Database/Seeds/`.
3. Web installer (`public/install/index.php`, sudah ada) saat ini import lewat raw
   SQL dump — setelah migration class selesai dibuat, **update installer supaya
   memanggil `php spark migrate` + seeder**, bukan lagi import SQL mentah. Jangan
   hapus `educms_database.sql` sampai proses ini benar-benar sudah dites end-to-end.

---

## 5. Hal-hal yang harus dipertahankan perilakunya (jangan hilang saat migrasi)

Ini fitur/logic yang **bukan** bug, harus tetap ada setelah migrasi, cuma pindah cara
implementasi:

- **Sistem tema** (`app/Libraries/Template.php`): setting `active_theme` menentukan
  apakah view diambil dari `app/Views/portal/...` (default) atau
  `public/themes/{slug}/views/...` (custom, mis. tema "islamic"). Kalau
  memigrasikan controller Portal, pastikan mekanisme resolusi tema ini tetap jalan
  (boleh disederhanakan strukturnya, tapi perilakunya — theme switching harus tetap
  berfungsi — wajib diverifikasi manual dengan cara ganti tema di admin lalu cek
  halaman publik).
- **RBAC granular** (`roles`, `permissions`, `role_permissions`, `permission_groups`)
  dengan Super Admin bypass semua pengecekan, dan pembatasan *force delete permanen*
  hanya untuk role Super Admin (lihat `Rbac.php` & pemakaiannya di `Admin/Media.php`).
- **Deteksi password default belum diganti** (`Auth_model::DEFAULT_SEEDED_PASSWORD_HASH`)
  — mekanisme ini memaksa admin ganti password seed sebelum bisa lanjut, jangan hilang.
- **Sanitasi HTML** untuk field WYSIWYG (`sanitize_html()`) sebelum disimpan ke DB —
  ini pencegahan stored-XSS, wajib tetap dipanggil di controller native yang
  menerima input rich-text (Posts, Pages, Announcements, dst).
- **Validasi upload berlapis** di `upload_helper.php` (bukan cuma cek ekstensi, tapi
  juga baca MIME asli file via `finfo` dan bandingkan dengan ekstensi) — kalau modul
  Media dimigrasikan, logic keamanan ini **wajib dipertahankan**, idealnya
  dipetakan ke CI4 native File Validation Rules (`is_image`, `mime_in`, `max_size`)
  ditambah pengecekan MIME manual yang sudah ada karena native CI4 rules saja belum
  tentu cukup ketat untuk kasus ini.

---

## 6. Checklist "selesai migrasi" (jangan hapus shim sebelum ini semua ✅)

```bash
# Jalankan dari root project sebelum menghapus app/Libraries/CI_*.php dkk:

grep -rl '\$this->load->' app/Controllers app/Models          # harus KOSONG
grep -rl 'extends MY_Controller\|extends MY_Model' app/        # harus KOSONG
grep -rl 'extends CI_Controller' app/                           # harus KOSONG
grep -rl 'extends Admin_Controller\|extends Portal_Controller\|extends Admin_CRUD_Controller' app/  # harus KOSONG
grep -n 'setAutoRoute' app/Config/Routes.php                    # harus false / dihapus
```

Baru setelah semua perintah di atas mengonfirmasi KOSONG (atau `setAutoRoute(false)`),
hapus:
`app/Libraries/CI_*.php`, `MY_Controller.php`, `MY_Model.php`, `Compat.php`,
`LegacyAutoRouter.php`, `LegacyRouter.php`, `Legacy_View.php`, `Db_upgrade.php`
(kalau sudah tidak dipakai setelah migration class asli terpasang).

Jalankan test suite (`tests/` sudah ada strukturnya, isi test untuk tiap modul yang
dimigrasikan sebagai bagian dari "selesai"-nya modul itu, jangan ditunda ke akhir).

---

## 7. Bug nyata yang PERNAH terjadi di aplikasi ini — jangan diulang

Bagian ini bukan teori — semua ini adalah bug sungguhan yang ditemukan dan diperbaiki
lewat sesi debugging langsung terhadap instalasi aplikasi ini. Kalau agent menulis
kode baru yang mengulang pola yang sama, bug yang sama akan muncul lagi.

### 7.1 `substr()` untuk parsing path — hati-hati off-by-one
Bug lama di `CI_Loader::_resolve_theme_file()`: prefix `'../../themes/'` panjangnya
13 karakter, tapi kode memotong pakai `substr($view, 14)` — kelebihan 1, membuang
huruf pertama nama tema, sehingga file tidak pernah ketemu dan selalu jatuh ke
halaman 404. **Pelajaran:** kalau menulis ulang logic resolusi path apapun (termasuk
untuk sistem tema di §5), hitung ulang panjang string secara eksplisit, jangan
menebak, dan tulis unit test untuk kasus ini.

### 7.2 Jangan panggil `Response::setCookie()` dengan array sebagai argumen ke-3
Bug lama di `app/Common.php`: helper `set_cookie()` memanggil
`service('response')->setCookie($name, $value, [...])` — array opsi dikirim sebagai
parameter posisi ke-3, padahal signature asli CI4 di parameter itu mengharapkan nilai
`$expire` tunggal (bukan array). Akibatnya: **setiap logout selalu Fatal Error**
(`CookieException`), sesi lama tidak pernah bersih-bersih. Kalau modul Auth
dimigrasikan dan butuh cookie (remember-me dsb), pakai salah satu dari:
`service('response')->setCookie(['name'=>..., 'value'=>..., 'expire'=>..., ...])`
(array sebagai argumen **pertama**), atau `service('response')->deleteCookie(...)`
untuk hapus cookie — **jangan** pernah kirim array ke posisi argumen kedua/ketiga.

### 7.3 CSRF `redirect` config + AJAX = kombinasi berbahaya
`app/Config/Security.php` sempat berisi `$redirect = (ENVIRONMENT === 'production')`.
Untuk aplikasi admin panel yang AJAX-heavy seperti ini, itu bikin kegagalan CSRF pada
request AJAX **diam-diam redirect** alih-alih melempar error yang bisa ditangani JS —
sangat menyulitkan debugging dan bikin pengalaman pengguna buruk (pesan generik
"gagal" tanpa detail). Nilai ini **sudah diubah permanen ke `false`** — pertahankan
ini, jangan dikembalikan ke kondisi semula walau saat migrasi native.

### 7.4 Page caching CI4 aktif secara default — matikan untuk endpoint AJAX/API
`app/Config/Cache.php` defaultnya `$ttl = 60` dengan `$handler = 'file'` — filter
`PageCache` bawaan CI4 otomatis men-cache **semua** response (termasuk hasil POST ke
endpoint upload/delete) berdasarkan URL, tanpa peduli isi body/file yang dikirim.
Ini menyebabkan endpoint AJAX mengembalikan response basi selama TTL berlaku. Nilai
ini **sudah diset ke `0` (nonaktif)** — kalau nanti mau mengaktifkan page cache lagi
untuk halaman publik yang benar-benar statis, aktifkan cache **per-halaman** pakai
`$this->response->cache()` di controller spesifik, jangan nyalakan `Config\Cache::$ttl`
secara global lagi.

### 7.5 `json_encode()` gagal diam-diam untuk nama file non-UTF8
Bug lama di `upload_helper.php`: nama file asli dari `$_FILES[...]['name']` dipakai
mentah tanpa validasi encoding. Kalau ada 1 byte saja yang bukan UTF-8 valid (umum
terjadi untuk nama file dari Windows/WhatsApp/Google Drive), `json_encode()` pada
seluruh response **gagal total** (return `false`, body jadi kosong) **tanpa
exception, tanpa log** — sangat sulit dilacak. Ini **sudah diperbaiki** dengan
`mb_convert_encoding()` + `htmlspecialchars(... ENT_SUBSTITUTE ...)` sanitasi nama
file di titik upload, ditambah flag `JSON_INVALID_UTF8_SUBSTITUTE` di semua
`json_encode()` response Media sebagai lapisan pengaman kedua. **Kalau memigrasikan
modul manapun yang menerima input nama file / teks bebas dari user lalu di-`json_encode`
untuk response AJAX, terapkan pola sanitasi + flag yang sama.**

### 7.6 `db_debug` yang selalu `true` membuat error DB jadi fatal
`CI_DB::$db_debug = true` (hardcoded) membuat exception database apapun (constraint
violation, dsb) di dalam operasi shim langsung dilempar mentah sebagai uncaught
exception, bukan ditangani rapi. Untuk model native (`CodeIgniter\Model`), pastikan
error database ditangkap dan diteruskan sebagai pesan validasi/error yang jelas ke
user, jangan biarkan jadi fatal error mentah.

---

## 8. Definition of Done per modul

Sebuah modul dianggap **selesai** migrasi kalau semua ini terpenuhi:

- [ ] Controller native, tidak ada pemanggilan API shim (lihat §3.4)
- [ ] Model native (`CodeIgniter\Model`), file & namespace sesuai PSR-4
- [ ] View tetap render benar (cek manual di browser, bukan cuma `php -l`)
- [ ] Route didaftarkan eksplisit di `Routes.php` (tidak mengandalkan auto-route)
- [ ] Fitur RBAC/permission modul itu tetap berfungsi (test sebagai non-Super-Admin
      juga, bukan cuma sebagai Super Admin)
- [ ] Semua operasi CRUD dites manual: Create, Read (list + detail), Update, Delete
- [ ] Kalau ada file upload di modul ini: terapkan pola sanitasi §7.5
- [ ] Kalau modul ini juga tampil di Portal (frontend publik): halaman publiknya
      dicek juga, termasuk dengan tema non-default aktif (lihat §5)
- [ ] Tidak ada error/warning baru muncul di `writable/logs/` setelah dites
- [ ] File model/controller CI3 lama-nya **dihapus** (bukan dibiarkan nyampah)
