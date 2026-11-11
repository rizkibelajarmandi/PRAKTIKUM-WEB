# Dokumentasi API Sistem Informasi Akademik

API ini menyediakan autentikasi pengguna dan pengelolaan data mahasiswa.

## Ringkasan endpoint

| Method | Endpoint | Controller | Method Controller | Auth | Keterangan |
|---|---|---|---|---|---|
| `GET` | `/mahasiswa` | `MahasiswaController` | `index()` | Tidak | Mengambil seluruh data mahasiswa |
| `GET` | `/mahasiswa?id={id}` | `MahasiswaController` | `index()` | Tidak | Mengambil satu mahasiswa berdasarkan id |
| `POST` | `/mahasiswa` | `MahasiswaController` | `store()` | Bearer | Menambah data mahasiswa baru |
| `PUT` | `/mahasiswa?id={id}` | `MahasiswaController` | `update($id)` | Bearer | Memperbarui data mahasiswa |
| `DELETE` | `/mahasiswa?id={id}` | `MahasiswaController` | `destroy($id)` | Bearer | Menghapus data mahasiswa |
| `POST` | `/register` | `AuthController` | `register()` | Tidak | Mendaftarkan pengguna baru |
| `POST` | `/login` | `AuthController` | `login()` | Tidak | Login dan mendapatkan token JWT |

Pemetaan URL ke controller dilakukan oleh `Router::dispatch()`. Untuk
`/mahasiswa`, method controller dipilih dari metode HTTP (`GET` ke `index()`,
`POST` ke `store()`, `PUT` ke `update()`, `DELETE` ke `destroy()`). Pada
`/register` dan `/login`, router langsung memanggil `register()` dan `login()`.

## Base URL

```text
http://localhost/ACARA21/bkpm/app/public
```

Pastikan server web dikonfigurasi untuk meneruskan permintaan endpoint ke
`index.php`.

## Format respons

Semua respons API menggunakan JSON dengan bentuk berikut:

```json
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": [],
  "timestamp": "2026-11-01T15:52:58+00:00"
}
```

- `success`: `true` jika operasi berhasil, selain itu `false`.
- `message`: keterangan hasil operasi.
- `data`: data hasil operasi atau `null` jika tidak ada.
- `timestamp`: waktu respons dalam format ISO 8601.

## Autentikasi

Endpoint tambah, ubah, dan hapus mahasiswa memerlukan token Bearer. Dapatkan
token melalui endpoint login dan kirim pada header:

```http
Authorization: Bearer <token>
```

Token berlaku selama satu jam.

## Endpoint

### Daftar mahasiswa

```http
GET /mahasiswa
```

Tidak memerlukan autentikasi. Data diurutkan berdasarkan `id` dari yang
terbaru.

Contoh:

```bash
curl "http://localhost/ACARA21/bkpm/app/public/mahasiswa"
```

Contoh respons `200 OK`:

```json
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": [
    {
      "id": "1",
      "nim": "2201001",
      "nama": "Siti Aminah",
      "prodi": "Informatika"
    }
  ],
  "timestamp": "2026-11-01T15:52:58+00:00"
}
```

### Detail mahasiswa

```http
GET /mahasiswa?id={id}
```

Parameter query:

| Nama | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `id` | integer | Ya | ID mahasiswa yang dicari |

Contoh:

```bash
curl "http://localhost/ACARA21/bkpm/app/public/mahasiswa?id=1"
```

Berhasil menghasilkan `200 OK` dengan satu objek mahasiswa pada `data`.
Mahasiswa yang tidak ditemukan menghasilkan `404 Not Found`.

### Tambah mahasiswa

```http
POST /mahasiswa
```

Memerlukan autentikasi Bearer. Kirim `Content-Type: application/json` dengan
isi:

| Nama | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `nim` | string | Ya | Nomor induk mahasiswa |
| `nama` | string | Ya | Nama mahasiswa |
| `prodi` | string | Ya | Program studi |

Contoh:

```bash
curl -X POST "http://localhost/ACARA21/bkpm/app/public/mahasiswa" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  -d '{"nim":"2201001","nama":"Siti Aminah","prodi":"Informatika"}'
```

Berhasil menghasilkan `201 Created` dengan ID baru:

```json
{
  "success": true,
  "message": "Data berhasil ditambahkan",
  "data": {
    "id": "1"
  },
  "timestamp": "2026-11-01T15:52:58+00:00"
}
```

### Ubah mahasiswa

```http
PUT /mahasiswa?id={id}
```

Memerlukan autentikasi Bearer dan ketiga field `nim`, `nama`, serta `prodi`
dalam JSON body. Semua field tersebut wajib dikirim.

Contoh:

```bash
curl -X PUT "http://localhost/ACARA21/bkpm/app/public/mahasiswa?id=1" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer <token>" \
  -d '{"nim":"2201001","nama":"Siti Aminah","prodi":"Sistem Informasi"}'
```

Berhasil menghasilkan `200 OK`. Jika `id` tidak dikirim, API menghasilkan
`400 Bad Request`; jika mahasiswa tidak ditemukan, API menghasilkan
`404 Not Found`.

### Hapus mahasiswa

```http
DELETE /mahasiswa?id={id}
```

Memerlukan autentikasi Bearer.

Contoh:

```bash
curl -X DELETE "http://localhost/ACARA21/bkpm/app/public/mahasiswa?id=1" \
  -H "Authorization: Bearer <token>"
```

Berhasil menghasilkan `200 OK`. Jika `id` tidak dikirim, API menghasilkan
`400 Bad Request`; jika mahasiswa tidak ditemukan, API menghasilkan
`404 Not Found`.

### Registrasi pengguna

```http
POST /register
```

Kirim `Content-Type: application/json` dengan body:

| Nama | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `username` | string | Ya | Nama pengguna |
| `email` | string | Ya | Alamat email |
| `password` | string | Ya | Kata sandi |

Contoh:

```bash
curl -X POST "http://localhost/ACARA21/bkpm/app/public/register" \
  -H "Content-Type: application/json" \
  -d '{"username":"siti","email":"siti@example.com","password":"password123"}'
```

Berhasil menghasilkan `201 Created` dengan ID pengguna baru pada `data.id`.
Username atau email yang sudah terdaftar menghasilkan `409 Conflict`.

### Login

```http
POST /login
```

Kirim `Content-Type: application/json` dengan body:

| Nama | Tipe | Wajib | Keterangan |
|---|---|---|---|
| `username` | string | Ya | Username terdaftar |
| `password` | string | Ya | Kata sandi |

Contoh:

```bash
curl -X POST "http://localhost/ACARA21/bkpm/app/public/login" \
  -H "Content-Type: application/json" \
  -d '{"username":"siti","password":"password123"}'
```

Berhasil menghasilkan `200 OK`:

```json
{
  "success": true,
  "message": "Login berhasil",
  "data": {
    "token": "<token>",
    "role": "<role pengguna>"
  },
  "timestamp": "2026-11-01T15:52:58+00:00"
}
```

Kredensial yang salah menghasilkan `401 Unauthorized`.

## Kode status

| HTTP status | Makna |
|---|---|
| `200 OK` | Permintaan berhasil |
| `201 Created` | Data atau pengguna berhasil dibuat |
| `400 Bad Request` | Field wajib atau parameter `id` belum dikirim |
| `401 Unauthorized` | Token tidak ada/tidak valid/kedaluwarsa atau kredensial login salah |
| `404 Not Found` | Endpoint atau data mahasiswa tidak ditemukan |
| `405 Method Not Allowed` | Metode HTTP tidak didukung pada endpoint mahasiswa |
| `409 Conflict` | Username atau email sudah terdaftar |
| `500 Internal Server Error` | Terjadi kesalahan server |

