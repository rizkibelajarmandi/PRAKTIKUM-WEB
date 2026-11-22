# Dokumentasi Acara 26 — Upload Foto & Database Management

**Ringkasan.** Admin dapat mengunggah foto mahasiswa (JPG/PNG, maks 2 MB). Berkas disimpan di `public/uploads/`, nama file di
kolom `foto`. Ada juga praktik backup dan restore database lewat phpMyAdmin.

## URL penting

| Fungsi | Alamat |
|---|---|
| Login | `http://localhost/ACARA26/bkpm/app/public/views/login.html` |
| Dashboard (input foto ada di sini, **khusus admin**) | `http://localhost/ACARA26/bkpm/app/public/views/dashboard.html` |
| Tabel publik | `http://localhost/ACARA26/bkpm/app/public/views/index.html` |
| Cek API (harus JSON) | `http://localhost/ACARA26/bkpm/app/public/mahasiswa` |

## File yang dibuat / diubah

| File | Perubahan |
|---|---|
| `docs/tambah_kolom_foto.sql` | Tambah kolom `foto` (**jalankan sekali** di phpMyAdmin) |
| `app/public/uploads/` | Folder foto + `.htaccess` (blokir eksekusi PHP) + `index.html` kosong |
| `app/models/Mahasiswa.php` | `create` / `update` menyimpan `foto` |
| `app/controllers/MahasiswaController.php` | Baca multipart, validasi foto, hapus foto lama (tugas mandiri 1) |
| `app/core/Router.php` | Dukungan `?_method=PUT` (PHP tidak mengisi `$_FILES` untuk PUT) |
| `views/dashboard.*`, `views/index.html`, `views/app.js` | Input file, `FormData`, kolom Foto + thumbnail |
| `docs/PANDUAN_BACKUP_RESTORE.md` | Langkah backup / restore |

`BASE_URL` ada di **baris 2** pada `app.js`, `login.js`, dan `dashboard.js`.

## Urutan persiapan (jangan terbalik)

1. Folder ada di `www\ACARA26\bkpm\app\public\...` (bukan folder ganda).
2. Jalankan `docs/tambah_kolom_foto.sql` di phpMyAdmin → `db_workshop` → tab SQL. **Tanpa ini tambah data gagal (500).**
3. Siapkan akun admin dan user (lihat dokumentasi Acara 25, bagian *Akun admin*).
4. Cek `.../public/mahasiswa` menampilkan JSON.
5. Login sebagai **admin**, buka dashboard: input **Foto** ada di kotak "Tambah Mahasiswa".
6. Siapkan file uji: `foto.jpg`, `foto2.png`, `catatan.txt` (ganti nama jadi `.jpg`), satu gambar di atas 2 MB.

## ⚠ Jika kamu memindah / mengganti nama folder

Alamat di halaman web **tidak ikut berubah otomatis**. Setiap folder dipindah atau di-rename, ubah `BASE_URL`:

1. Catat lokasi baru folder `public`, mis. `C:\laragon\www\kampus\acara26\bkpm\app\public`.
2. Rumus: `http://localhost/` + semua folder dari `www` sampai `public`
   → `http://localhost/kampus/acara26/bkpm/app/public`
3. Buka file di folder `views/`: `app.js`, `login.js`, `dashboard.js`. Ganti **baris 2**:
   ```js
   const BASE_URL = 'http://localhost/kampus/acara26/bkpm/app/public';
   ```
4. Simpan, lalu buka halaman dengan **Ctrl + F5** (agar JS lama dari cache tidak terpakai).
5. Postman: Environment → variabel `base_url` diisi alamat yang sama (tanpa `/` di akhir), lalu **Ctrl + S**.
6. Cek: buka `<BASE_URL>/mahasiswa` di browser. Harus muncul **JSON**.
7. Pastikan `.htaccess` ikut pindah (file tersembunyi: Explorer → View → Show → Hidden items), termasuk `uploads/.htaccess`.

File PHP **tidak perlu diubah**; jalurnya dihitung otomatis dari lokasi file. Database juga tidak terpengaruh.

> Foto yang sudah ada tetap terbaca setelah dipindah selama folder `uploads/` ikut dipindah (database hanya menyimpan nama file).

## Skenario pengujian

| ID | Skenario | Langkah | Hasil diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| TC-01 | Tambah + foto | Admin: isi form, pilih `foto.jpg`, Simpan | Berhasil, thumbnail tampil, file baru di `uploads/` | | |
| TC-02 | Tambah tanpa foto | Isi form tanpa foto | Berhasil, kolom Foto `-` | | |
| TC-03 | Berkas palsu | Pilih `catatan.txt` yang diganti nama `.jpg` | Ditolak: "Berkas harus berupa gambar JPG/PNG yang valid", tidak ada file baru | | |
| TC-04 | Ukuran berlebih | Pilih gambar > 2 MB | "Ukuran foto maksimal 2MB" | | |
| TC-05 | Ganti foto | Ubah, pilih foto baru, Simpan | Thumbnail berganti, **foto lama hilang** dari `uploads/` | | |
| TC-06 | Ubah tanpa foto | Ubah nama saja | Foto tetap | | |
| TC-07 | Hapus data | Klik Hapus | Baris hilang, berkas foto ikut terhapus | | |
| TC-08 | Hak akses user | Login user | Form & tombol aksi tidak tampil, thumbnail tetap terlihat | | |
| TC-09 | Upload oleh user | Postman `POST {{base_url}}/mahasiswa`, Body **form-data** (`nim`, `nama`, `prodi`, `foto` bertipe *File*), token user | **403** | | |
| TC-10 | Upload tanpa token | Request yang sama tanpa Authorization | **401** | | |
| TC-11 | Update via Postman | `POST {{base_url}}/mahasiswa?id=1&_method=PUT`, form-data, token admin | **200** | | |
| TC-12 | Folder uploads aman | Taruh `tes.php` di `uploads/`, buka `.../uploads/tes.php` | **403**, skrip tidak jalan (hapus lagi) | | |
| TC-13 | Backup | phpMyAdmin → `db_workshop` → Export | File `.sql` terunduh | | |
| TC-14 | Restore | Buat DB `db_workshop_restore` → Import file tadi | Jumlah baris `mahasiswa` & `users` sama; kolom `foto` ada | | |

Bukti di DevTools → Network (request upload): `Content-Type: multipart/form-data; boundary=...` (dibuat browser otomatis)
dan header `Authorization: Bearer ...`.

## Masalah umum

| Gejala | Penyebab | Solusi |
|---|---|---|
| `Unexpected token '<'` | API membalas HTML | `.htaccess` di `public/` hilang, `mod_rewrite` mati, atau `BASE_URL` salah |
| Input foto tidak ada | Bukan di `index.html`; hanya di `dashboard.html` untuk **admin** | Login admin; cek `localStorage.getItem('role')` |
| Tambah data error 500 | Kolom `foto` belum ada | Jalankan `tambah_kolom_foto.sql` |
| Gambar tidak muncul / 500 di `uploads/` | `uploads/.htaccess` tidak diizinkan Apache | Hapus file itu sementara, laporkan |
| Gambar rusak setelah restore | Backup DB tidak membawa foto | Salin juga folder `uploads/` |
| Pesan "Ukuran request melebihi batas server" | File melewati `post_max_size` di `php.ini` | Pakai gambar lebih kecil |

**Ingat:** file backup `.sql` berisi hash password tabel `users`. Jangan di-commit ke GitHub.