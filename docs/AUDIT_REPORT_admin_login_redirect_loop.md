# Audit Report — EduCMS-CI4 Migration
## Bug: Infinite Redirect Loop pada `/admin/login`

**Repo:** GBU-Project/EduCMS-CI4 (branch `develop`)
**Sumber temuan:** `chromewebdata.har` (capture 10 Agu 2026, 13:51–14:04 UTC)
**Commit HEAD saat capture:** `e179cfb` — "fix(auth): resolve redirect loop on admin login endpoints and update route method handlers" (10 Agu 2026 20:49 WIB / 13:49 UTC — ±2 menit sebelum capture dimulai)

---

## 1. Ringkasan Temuan

Dari 206 request dalam `.har`, **200 di antaranya** adalah GET ke
`http://localhost/educms/public/admin/login`, dan **setiap satu** dijawab
server dengan **302 redirect ke URL itu sendiri**. Tidak ada satupun yang
berhasil sampai ke 200 OK. Pola ini berulang dalam beberapa gelombang
selama ±13 menit — konsisten dengan user mencoba reload berkali-kali
setelah browser gagal (`ERR_TOO_MANY_REDIRECTS`).

| Metrik | Nilai |
|---|---|
| Total request di HAR | 206 |
| Request ke `admin/login` | 200 |
| Status semua request tsb | 302 → dirinya sendiri |
| `Set-Cookie` (`ci_session`) di seluruh capture | **0** (termasuk pada homepage 200 OK) |
| Rentang waktu | 13:51:36 – 14:04:29 UTC (~13 menit) |

---

## 2. Root Cause

### 2.1 Sesi PHP tidak pernah ter-persist (bukti utama)

Tidak ada header `Set-Cookie` yang dikirim server di **seluruh** capture —
bahkan pada request pertama yang sukses (200 OK ke homepage). Artinya
`ci_session` **tidak pernah terbentuk** di sisi browser sepanjang sesi
browsing ini.

`PermissionFilter::before()` (di `app/Filters/PermissionFilter.php`) sudah
memiliki whitelist eksplisit untuk `admin/login`, `admin/logout`, dan
`admin/forgot` — jadi secara kode, filter ini seharusnya tidak pernah
memblokir halaman login. Tapi karena sesi tidak pernah persist, kondisi
runtime di server yang diaudit tidak konsisten dengan asumsi kode: setiap
request efektif dianggap sesi baru, memicu alur
`session()->set('redirect_to', ...)` → `redirect()->to('admin/login')`
berulang tanpa pernah "settle".

**Kemungkinan penyebab (perlu verifikasi langsung di server):**
- Folder `writable/session/` tidak *writable* oleh user proses PHP/Apache —
  umum terjadi pasca migrasi CI3 → CI4, karena CI3 pada proyek ini
  kemungkinan menyimpan sesi ke lokasi/driver berbeda.
- Server yang direkam di `.har` sedang menjalankan build/deploy yang
  **tidak sinkron** dengan commit `develop` terbaru (misalnya OPcache
  belum di-clear setelah deploy, atau file lama masih ter-*serve*).
- `CI_ENVIRONMENT=production` di `.env` menekan tampilan error PHP,
  sehingga kegagalan `session_start()` (jika ada) tidak terlihat sebagai
  halaman error, melainkan sebagai redirect loop yang "senyap".

### 2.2 Bug tambahan yang ditemukan — case-sensitive HTTP method check (root cause independen)

```php
// app/Controllers/Auth.php (sebelum fix)
if ($this->request->getMethod() === 'post') {
```

Proyek ini mensyaratkan `codeigniter4/framework: ^4.7`. Sejak CI4 v4.5.0,
`Request::getMethod()` **selalu mengembalikan huruf besar** (`'POST'`) —
parameter `$upper` lama yang mendukung lowercase sudah **dihapus**
(dikonfirmasi dari CI4 upgrade guide v4.5.0 resmi). Akibatnya kondisi
`=== 'post'` **tidak pernah bernilai true**, sehingga:

- Submit form login (kredensial) tidak pernah diproses — request POST
  jatuh ke `return view('auth/login')` seolah-olah GET biasa, form
  seakan "tidak merespons".
- Bug identik juga ada di `Auth::forgot()`.

Ini bug independen dari redirect loop (tidak menyebabkan 302), tapi sama
fatalnya: **form login secara efektif non-fungsional** pada versi CI4
project ini walaupun sesi berhasil terbentuk.

> **Status:** Sudah diperbaiki di working tree lokal — lihat
> `fix-auth-getmethod-case.patch` yang disertakan bersama laporan ini.

---

## 3. Rekomendasi Perbaikan

1. **[Selesai]** Ganti `getMethod() === 'post'` → `strtoupper($this->request->getMethod()) === 'POST'` di `Auth::login()` dan `Auth::forgot()`. Patch terlampir: `fix-auth-getmethod-case.patch`.
2. **[Perlu verifikasi server]** Cek permission folder `writable/session/` — harus writable oleh user proses web server (`www-data`, `apache`, dll). Pastikan juga `session.savePath` di `.env` valid dan konsisten dengan driver yang dipakai (`FileHandler`).
3. **[Perlu verifikasi deployment]** Pastikan server yang diaudit benar-benar menjalankan commit `develop` terbaru (`e179cfb`), bukan build lama yang di-cache (OPcache) atau folder deploy yang belum di-sync.
4. **[Rekomendasi CI]** Tambahkan test HTTP negatif otomatis: request GET ke `/admin/login` **tanpa cookie sama sekali** harus menghasilkan 200, bukan 302. Ini akan menangkap regresi redirect-loop seperti ini sebelum deploy — cocok ditambahkan ke `tests/unit/AdminRouteProtectionTest.php` yang sudah ada di repo.
5. **[Opsional, hardening]** Pertimbangkan menambahkan logging eksplisit saat `session_start()` gagal (bukan hanya redirect diam-diam), agar masalah seperti ini lebih cepat terdeteksi di production.

---

## 4. Catatan Tambahan

Repo ini sudah memiliki `AUDIT_FIX_SUMMARY.md` yang mencatat 7 temuan
audit keamanan sebelumnya (termasuk perbaikan redirect-loop `admin/login`
di commit `5de5713`, dengan klaim sudah diuji negative test). Temuan pada
laporan ini menunjukkan bahwa **meskipun perbaikan level-kode untuk
masalah tersebut sudah ada**, gejala redirect loop **masih terekam** di
`.har` — mengindikasikan masalah kemungkinan besar ada di **lapisan
konfigurasi/deployment server** (sesi tidak persist), bukan (lagi) di
logika filter itu sendiri. Poin 2 dan 3 di atas adalah langkah investigasi
lanjutan yang disarankan untuk memastikan akar masalah benar-benar
tertutup di lingkungan production.
