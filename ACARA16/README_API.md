# BKPM – Membuat dan Menguji RESTful API (Mahasiswa)

## Persiapan
1. Jalankan ulang `si-akademik15/database.sql` di phpMyAdmin / MySQL.
   Script ini aman dijalankan ulang dan **memigrasi tabel lama**: menambah kolom `id`
   (AUTO_INCREMENT, primary key); `nim` tetap unik.
2. Letakkan folder di `htdocs` (XAMPP), pastikan `mod_rewrite` aktif dan `AllowOverride All`.
3. Di Postman, import `BKPM_Acara16_Mahasiswa_API.postman_collection.json`,
   lalu sesuaikan variabel collection `base_url`, contoh:
   `http://localhost/PRAKTIKUM-WEB/ACARA15/si-akademik15/app/public`

## Endpoint
| Method | URL | Fungsi | Sukses |
|---|---|---|---|
| GET | `/api/mahasiswa` | Semua mahasiswa | 200 |
| GET | `/api/mahasiswa/{id}` | Satu mahasiswa berdasarkan ID | 200 |
| POST | `/api/mahasiswa` | Tambah mahasiswa | 201 + header `Location` |
| PUT | `/api/mahasiswa/{id}` | Ubah mahasiswa | 200 |
| DELETE | `/api/mahasiswa/{id}` | Hapus mahasiswa | 200 |

Body POST / PUT (`Content-Type: application/json`):
```json
{
  "nim": "E41250761",
  "nama": "Budi Santoso",
  "email": "budi@example.com",
  "jurusan": "Teknologi Informasi",
  "prodi_id": 1,
  "angkatan": 2025,
  "status": "aktif"
}
```
`status` opsional (`aktif` | `cuti` | `lulus`, default `aktif`). Field lain wajib.

## Format response
```json
{ "success": true,  "message": "...", "data": { } }
{ "success": false, "message": "Validasi gagal.", "errors": { "email": "Format email tidak valid." } }
```

## HTTP Status Code yang dipakai
| Kode | Kapan |
|---|---|
| 200 OK | GET / PUT / DELETE berhasil |
| 201 Created | POST berhasil |
| 400 Bad Request | JSON rusak / body kosong / ID bukan angka |
| 404 Not Found | ID tidak ditemukan, endpoint tidak ada |
| 405 Method Not Allowed | Method tidak sesuai endpoint (header `Allow` disertakan) |
| 409 Conflict | NIM sudah dipakai mahasiswa lain |
| 422 Unprocessable Entity | Validasi gagal: NIM kosong, email tidak valid, data tidak lengkap |
| 500 Internal Server Error | Kesalahan database (dicatat di `storage/logs/app.log`) |

## Struktur kode (mengikuti arsitektur praktikum sebelumnya)
- `app/public/index.php` – mengarahkan URL berawalan `api/` ke API (tanpa session/login)
- `app/Controller/MahasiswaApiController.php` – routing method, baca JSON, kirim response
- `app/Service/MahasiswaService.php` – `createApi`, `updateApi`, `deleteApi`, validasi
- `app/repository/MahasiswaRepository.php` – query PDO prepared statement berbasis `id`
- `app/public/.htaccess` – rewrite semua URL ke `index.php`

## Hasil pengujian (sudah dijalankan)
Seluruh skenario di folder "Alur normal" dan "Kondisi error" pada collection Postman
diuji dan menghasilkan status code seperti yang tertera di nama request.
