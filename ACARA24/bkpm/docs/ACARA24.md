# Dokumentasi Acara 24 — Integrasi API dengan Frontend

**Ringkasan.** Halaman `views/index.html` + `app.js` mengambil data dari `GET /mahasiswa` memakai Fetch API
(async/await) dan menampilkannya sebagai tabel. Server mengizinkan akses lintas origin lewat header CORS di `public/index.php`.

## URL penting (sesuaikan nama folder jika berbeda)

| Fungsi | Alamat |
|---|---|
| Halaman | `http://localhost/ACARA24/bkpm/app/public/views/index.html` |
| Cek API (harus JSON) | `http://localhost/ACARA24/bkpm/app/public/mahasiswa` |

## File yang dibuat / diubah

| File | Isi |
|---|---|
| `app/public/index.php` | Header CORS + balas **204** untuk method `OPTIONS` (preflight) |
| `app/public/views/index.html` | Tabel data + elemen `#status` |
| `app/public/views/app.js` | `fetch`, status loading / kosong / error. **`BASE_URL` ada di baris 2** |

> Path `require` di `index.php` tetap `'/../../app/core/autoload.php'` karena folder `public` berada **di dalam** `app`.
> Jangan menyalin versi modul (`'/../app/...'`), itu menyebabkan fatal error.

## Sebelum testing

- [ ] Apache dan MySQL menyala
- [ ] Tabel `mahasiswa` berisi minimal 3 data
- [ ] `.htaccess` ada di `app/public/`
- [ ] `.../public/mahasiswa` menampilkan JSON (bukan "Not Found")

## ⚠ Jika kamu memindah / mengganti nama folder

Alamat di halaman web **tidak ikut berubah otomatis**. Setiap folder dipindah atau di-rename, ubah `BASE_URL`:

1. Catat lokasi baru folder `public`, mis. `C:\laragon\www\kampus\acara24\bkpm\app\public`.
2. Rumus: `http://localhost/` + semua folder dari `www` sampai `public`
   → `http://localhost/kampus/acara24/bkpm/app/public`
3. Buka file di folder `views/`: `app.js`. Ganti **baris 2**:
   ```js
   const BASE_URL = 'http://localhost/kampus/acara24/bkpm/app/public';
   ```
4. Simpan, lalu buka halaman dengan **Ctrl + F5** (agar JS lama dari cache tidak terpakai).
5. Postman: Environment → variabel `base_url` diisi alamat yang sama (tanpa `/` di akhir), lalu **Ctrl + S**.
6. Cek: buka `<BASE_URL>/mahasiswa` di browser. Harus muncul **JSON**.
7. Pastikan `.htaccess` ikut pindah (file tersembunyi: Explorer → View → Show → Hidden items).

File PHP **tidak perlu diubah**; jalurnya dihitung otomatis dari lokasi file. Database juga tidak terpengaruh.

## Skenario pengujian

Alat: browser + DevTools (**F12**: tab Console & Network), Postman. Isi kolom *Hasil Aktual* dan *Status* sendiri.

| ID | Skenario | Langkah | Hasil diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| TC-01 | Data tampil | Buka `index.html`, bandingkan dengan `GET /mahasiswa` di Postman | Isi tabel sama; Network 200; Console bersih | | |
| TC-02 | Indikator loading | Network → *Slow 3G* → refresh | "Memuat data..." muncul lalu tabel terisi | | |
| TC-03 | Data kosong | Hapus semua data, refresh | "Belum ada data" (restore data setelahnya) | | |
| TC-04 | Server mati | Matikan Apache, refresh | "Gagal memuat data: Failed to fetch" | | |
| TC-05 | URL API salah | Ubah `BASE_URL` ke folder tidak ada | Pesan error tampil di halaman (kembalikan setelah uji) | | |
| TC-06 | XSS | POST nama berisi `<img src=x onerror=alert(1)>` (token + JSON) | Tampil sebagai teks, tanpa popup | | |
| TC-07 | Lintas origin | Buka `index.html` via Live Server (`127.0.0.1:5500`) | Tabel terisi, tanpa error CORS | | |
| TC-08 | Tanpa header CORS | Komentari header `Access-Control-Allow-Origin`, uji via Live Server | Console: error *blocked by CORS policy* (kembalikan barisnya) | | |
| TC-09 | Preflight | `curl -i -X OPTIONS <API>/mahasiswa -H "Origin: http://127.0.0.1:5500" -H "Access-Control-Request-Method: POST"` | **204** + header `Access-Control-*` | | |
| TC-10 | Regresi | Postman: login, GET, POST dengan/tanpa token | Sesuai Acara 23 (200 / 201 / 401) | | |

## Masalah umum

| Gejala | Penyebab | Solusi |
|---|---|---|
| `Unexpected token '<' ... is not valid JSON` | `/mahasiswa` membalas HTML (404) | `.htaccess` hilang / `mod_rewrite` mati / `BASE_URL` salah. Buka URL API langsung |
| 404 "Not Found" bawaan Apache | Folder tidak ada di `www` atau namanya beda | Cek struktur: `www\ACARA24\bkpm\app\public\views\index.html` |
| `Failed to fetch` | Apache mati / host `BASE_URL` salah | Nyalakan Apache, cek `BASE_URL` |
| Tabel tidak berubah setelah edit JS | Cache browser | **Ctrl + F5** |