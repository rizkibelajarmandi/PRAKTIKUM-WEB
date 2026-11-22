# Laporan Pengujian Manual (Blackbox Testing) - ACARA 23

Pengujian dilakukan secara manual menggunakan Postman dengan teknik **Equivalence Partitioning** terhadap `AuthController` dan `MahasiswaController`.

## 1. Ruang Lingkup

| No | Method | Endpoint | Controller | Method Controller |
|---|---|---|---|---|
| 1 | `POST` | `/register` | `AuthController` | `register()` |
| 2 | `POST` | `/login` | `AuthController` | `login()` |
| 3 | `GET` | `/mahasiswa` | `MahasiswaController` | `index()` |
| 4 | `POST` | `/mahasiswa` | `MahasiswaController` | `store()` |
| 5 | `PUT` | `/mahasiswa?id={id}` | `MahasiswaController` | `update($id)` |
| 6 | `DELETE` | `/mahasiswa?id={id}` | `MahasiswaController` | `destroy($id)` |

Test case difokuskan pada `login`, `store`, `update`, dan `destroy`.

## 2. Lingkungan Pengujian

- Server: Laragon (Apache, PHP, MySQL)
- Alat uji: Postman (environment `LOCAL 22`, `base_url` = `http://localhost/ACARA23/bkpm/app/public`)
- Autentikasi: Bearer Token (JWT) dari endpoint `/login`
- Tanggal pengujian: 12 November 2026

## 3. Test Case dan Hasil Pengujian (sebelum perbaikan)

| ID | Skenario | Data Uji | Hasil Diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| TC-01 | NIM valid dan lengkap (`store`) | `nim=220100123`, `nama=Budi Santoso`, `prodi=Informatika`, token valid | 201 Created | 201 Created | Pass |
| TC-02 | NIM kosong (partisi tidak valid) | `nim=""` | 400/422 | 400 Bad Request | Pass |
| TC-03 | NIM duplikat (partisi tidak valid) | `nim=220100123` (milik data lain) | 422 Unprocessable Entity | **201 Created** (data duplikat tersimpan) | **Fail** |
| TC-04 | Login kredensial benar | `username=siti`, `password=password123` | 200 OK, token diterima | 200 OK, token diterima | Pass |
| TC-05 | Login kredensial salah | `password=passwordSalah` | 401 Unauthorized | 401 Unauthorized | Pass |
| TC-06 | Akses `store` tanpa token | tanpa header Authorization | 401 Unauthorized | 401 Unauthorized | Pass |
| TC-07 | Update data valid (`update`) | `id` ada, body lengkap, token valid | 200 OK | 200 OK | Pass |
| TC-08 | Update dengan id tidak ada | `?id=99`, body lengkap, token valid | 404 Not Found | 404 Not Found | Pass |
| TC-09 | Update dengan field kosong | `nama=""`, token valid | 400 Bad Request | 400 Bad Request | Pass |
| TC-10 | Update tanpa token | tanpa header Authorization | 401 Unauthorized | 401 Unauthorized | Pass |
| TC-11 | Update dengan NIM milik data lain | `nim=220100123` (milik data lain), token valid | 422 Unprocessable Entity | **200 OK** (NIM duplikat lolos) | **Fail** |
| TC-12 | Hapus data dengan id ada (`destroy`) | `id` ada, token valid | 200 OK | 200 OK | Pass |
| TC-13 | Hapus data dengan id tidak ada | `?id=99`, token valid | 404 Not Found | 404 Not Found | Pass |
| TC-14 | Hapus data tanpa token | tanpa header Authorization | 401 Unauthorized | 401 Unauthorized | Pass |

Ringkasan awal: 14 test case, **12 Pass** dan **2 Fail** (TC-03 dan TC-11).

## 4. Analisis Kegagalan

Kolom `nim` pada tabel `mahasiswa` tidak memiliki index Unique (hanya `PRIMARY` pada `id`), dan `store()` maupun `update()` tidak memeriksa NIM duplikat. Akibatnya NIM yang sama dapat tersimpan lebih dari satu kali. Kegagalan ini berada pada lapisan **Model dan Controller**.

## 5. Perbaikan

**Model** (`app/models/Mahasiswa.php`): menambahkan method pengecekan NIM.

```php
public function nimExists(string $nim, ?int $excludeId = null): bool
{
    $sql = 'SELECT COUNT(*) FROM mahasiswa WHERE nim = :nim';
    $params = [':nim' => $nim];
    if ($excludeId !== null) {
        $sql .= ' AND id <> :id';
        $params[':id'] = $excludeId;
    }
    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return (int) $stmt->fetchColumn() > 0;
}
```

**Controller** (`app/controllers/MahasiswaController.php`):

- `store()`: sebelum `create()`, tolak dengan 422 bila NIM sudah ada.
- `update()`: sebelum `update()`, tolak dengan 422 bila NIM dipakai data lain. Parameter `$excludeId` menjaga agar NIM milik data itu sendiri tidak dianggap duplikat.

```php
if ($this->model->nimExists((string) $data['nim'])) {                // store()
    Response::json(422, false, 'NIM sudah terdaftar');
}

if ($this->model->nimExists((string) $data['nim'], (int) $id)) {     // update()
    Response::json(422, false, 'NIM sudah terdaftar');
}
```

## 6. Hasil Retest (sesudah perbaikan)

| ID | Skenario | Hasil Diharapkan | Hasil Aktual Sebelum | Hasil Aktual Sesudah | Status |
|---|---|---|---|---|---|
| TC-03 | NIM duplikat pada `store` | 422 | 201 Created | 422 Unprocessable Entity | **Pass** |
| TC-11 | NIM milik data lain pada `update` | 422 | 200 OK | 422 Unprocessable Entity | **Pass** |
| TC-07 | Update data valid (regresi) | 200 | 200 OK | 200 OK | Pass |

TC-07 dijalankan ulang untuk memastikan perbaikan tidak menolak update yang sah (NIM milik data itu sendiri).

## 7. Kesimpulan

Dari 14 test case, 12 lulus pada pengujian awal dan 2 (TC-03, TC-11) gagal akibat tidak adanya validasi NIM duplikat. Setelah perbaikan pada Model dan Controller, kedua test case lulus pada retest dan tidak ditemukan regresi pada update yang valid. Seluruh test case kini berstatus **Pass**.