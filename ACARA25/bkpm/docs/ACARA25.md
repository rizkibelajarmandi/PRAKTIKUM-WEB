# Dokumentasi Acara 25 — Authentication, Authorization & Dashboard Admin

**Ringkasan.** Ada halaman login dan dashboard. Token JWT + role disimpan di `localStorage`. Method tulis
(`POST`/`PUT`/`DELETE`) hanya boleh dilakukan **admin** (`Auth::requireAdmin()`); `user` biasa hanya bisa melihat data.

## URL penting

| Fungsi | Alamat |
|---|---|
| Login | `http://localhost/ACARA25/bkpm/app/public/views/login.html` |
| Dashboard | `http://localhost/ACARA25/bkpm/app/public/views/dashboard.html` |
| Tabel publik | `http://localhost/ACARA25/bkpm/app/public/views/index.html` |
| Cek API (harus JSON) | `http://localhost/ACARA25/bkpm/app/public/mahasiswa` |

## File yang dibuat / diubah

| File | Perubahan |
|---|---|
| `app/core/Auth.php` | + `requireAdmin()`: 401 jika token bermasalah, **403** jika role bukan admin |
| `app/controllers/MahasiswaController.php` | `store`, `update`, `destroy` memakai `requireAdmin()` |
| `views/login.html`, `login.js` | Form login, simpan `token` & `role` |
| `views/dashboard.html`, `dashboard.js` | Penjaga halaman, total data, form tambah/ubah, hapus, logout |
| `docs/buat_admin.sql` | SQL menjadikan akun sebagai admin |

`BASE_URL` ada di **baris 2** pada `app.js`, `login.js`, dan `dashboard.js`.

## Akun admin (WAJIB dibaca)

Tidak ada akun bawaan. `register` selalu membuat role `user` (sengaja, agar tidak ada yang bisa mendaftar sebagai admin).
Pilih salah satu:

**A. Naikkan akun yang sudah ada.** Edit baris 3 `docs/buat_admin.sql`, jalankan di phpMyAdmin → `db_workshop` → tab SQL:
```sql
UPDATE users SET role = 'admin' WHERE username = 'GANTI_USERNAME';
```

**B. Buat admin langsung** (tanpa Postman). Login: username `admin`, password `admin12345`.
```sql
INSERT INTO users (username, email, password, role)
VALUES ('admin', 'admin@example.com',
        '$2y$10$VgEvm8EzPZiFHDFzkN381OUCIXwreTctWmC6c6UbcVmv2zzuLpz2u', 'admin');
```
Setelah mengubah role di database, **logout lalu login ulang** (role tersimpan di token).

## Sebelum testing

- [ ] `.../public/mahasiswa` menampilkan JSON
- [ ] Ada 1 akun admin dan 1 akun user (daftarkan lewat Postman `POST /register`)
- [ ] Kalau pindah dari acara lain: DevTools → Application → Local Storage → **Clear** (localStorage dipakai bersama semua folder di `localhost`)

## ⚠ Jika kamu memindah / mengganti nama folder

Alamat di halaman web **tidak ikut berubah otomatis**. Setiap folder dipindah atau di-rename, ubah `BASE_URL`:

1. Catat lokasi baru folder `public`, mis. `C:\laragon\www\kampus\acara25\bkpm\app\public`.
2. Rumus: `http://localhost/` + semua folder dari `www` sampai `public`
   → `http://localhost/kampus/acara25/bkpm/app/public`
3. Buka file di folder `views/`: `app.js`, `login.js`, `dashboard.js`. Ganti **baris 2**:
   ```js
   const BASE_URL = 'http://localhost/kampus/acara25/bkpm/app/public';
   ```
4. Simpan, lalu buka halaman dengan **Ctrl + F5** (agar JS lama dari cache tidak terpakai).
5. Postman: Environment → variabel `base_url` diisi alamat yang sama (tanpa `/` di akhir), lalu **Ctrl + S**.
6. Cek: buka `<BASE_URL>/mahasiswa` di browser. Harus muncul **JSON**.
7. Pastikan `.htaccess` ikut pindah (file tersembunyi: Explorer → View → Show → Hidden items).

File PHP **tidak perlu diubah**; jalurnya dihitung otomatis dari lokasi file. Database juga tidak terpengaruh.

## Skenario pengujian

| ID | Skenario | Langkah | Hasil diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| TC-01 | Dashboard tanpa login | Buka `dashboard.html` langsung | Dialihkan ke `login.html` | | |
| TC-02 | Login salah | Password salah | "Username atau password salah", tidak ada token | | |
| TC-03 | Login admin | Username/password admin | Masuk dashboard; form & tombol Ubah/Hapus tampil | | |
| TC-04 | Login user | Akun `user` | Data tampil, **tanpa** form & tombol aksi | | |
| TC-05 | Tambah (admin) | Isi form, Simpan | "Data berhasil ditambahkan", total bertambah | | |
| TC-06 | Ubah / Hapus (admin) | Klik Ubah lalu Simpan; klik Hapus | Data berubah / hilang | | |
| TC-07 | NIM duplikat | Tambah NIM yang sama | "NIM sudah terdaftar" (422) | | |
| TC-08 | User menembak API | Postman `POST {{base_url}}/mahasiswa` + token **user** | **403** | | |
| TC-09 | Tanpa token | Request yang sama tanpa header Authorization | **401** | | |
| TC-10 | Role dipalsukan | Login sebagai user, Console: `localStorage.setItem('role','admin')`, refresh, coba Simpan | Form muncul, tetapi server membalas **403** (bukti keamanan ada di backend) | | |
| TC-11 | Token palsu / kedaluwarsa | Ubah `token` di localStorage, klik Hapus | Alert sesi berakhir, kembali ke login | | |
| TC-12 | Logout | Klik Logout | Ke login; `token` & `role` hilang dari Local Storage | | |

## Masalah umum

| Gejala | Penyebab | Solusi |
|---|---|---|
| Form "Tambah Mahasiswa" tidak muncul | Role bukan `admin` | Console: `localStorage.getItem('role')`. Ubah role di DB lalu **login ulang** |
| `Unexpected token '<'` | API membalas HTML | Cek `.htaccess` / `BASE_URL` (lihat bagian pindah folder) |
| Login gagal terus padahal benar | `BASE_URL` di `login.js` salah, atau akun tidak ada | Buka tab Network, lihat status request `login` |
| Halaman 404 | Folder tidak di `www` / nama beda | Cek struktur folder |

**Catatan desain:** dashboard tetap terbuka walau token kedaluwarsa (GET `/mahasiswa` memang publik). Kedaluwarsa baru terdeteksi saat
admin melakukan aksi (401). Menyembunyikan tombol hanya untuk tampilan, keamanan sebenarnya di `requireAdmin()`.