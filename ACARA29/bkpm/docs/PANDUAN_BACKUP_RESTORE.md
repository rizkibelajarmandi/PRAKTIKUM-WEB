# Panduan Backup dan Restore Database (Acara 26)

Database: `db_workshop` (tabel `mahasiswa` dan `users`).

## 1. Backup (Export)

1. Buka phpMyAdmin, klik database **db_workshop** di panel kiri.
2. Buka tab **Export**.
3. Pilih metode **Custom**, format **SQL**.
4. Di bagian *Object creation options*, centang **Add DROP TABLE / VIEW / PROCEDURE / FUNCTION / EVENT / TRIGGER statement**
   (agar saat restore, tabel lama ditimpa bersih).
5. Klik **Export**. Simpan dengan nama bertanggal, misalnya `db_workshop_2026-11-15.sql`.

## 2. Restore (Import)

**Selalu coba dulu ke database percobaan**, jangan langsung menimpa database asli.

1. Di phpMyAdmin klik **New**, buat database `db_workshop_restore` (collation `utf8mb4_general_ci`).
2. Klik `db_workshop_restore`, buka tab **Import**, pilih file `.sql`, klik **Import**.
3. Verifikasi: jumlah baris tabel `mahasiswa` dan `users` harus sama dengan database asli,
   dan kolom `foto` ada (tab *Structure*).

Simulasi pemulihan bencana: hapus data di `db_workshop` (atau DROP tabelnya), lalu Import file backup
langsung ke `db_workshop`.

## 3. Hal yang sering terlewat

- **Backup database tidak menyertakan foto.** Database hanya menyimpan *nama file*; gambarnya ada di
  `app/public/uploads/`. Backup juga folder tersebut (salin/zip). Jika hanya database yang dipulihkan,
  kolom `foto` terisi tetapi gambarnya tidak ditemukan.
- **File `.sql` berisi hash password tabel `users`.** Jangan di-commit ke GitHub dan jangan dibagikan sembarangan.
- Selalu uji restore. Backup yang belum pernah dicoba dipulihkan belum bisa dianggap aman.

## 4. Usulan jadwal backup (tugas mandiri 2)

| Jenis | Frekuensi | Isi | Retensi |
|---|---|---|---|
| Database (`.sql`) | Harian | `db_workshop` | simpan 7 hari terakhir |
| Penuh | Mingguan | `.sql` + folder `uploads/` | simpan 4 minggu |
| Sebelum perubahan besar | Manual | `.sql` + `uploads/` | sampai perubahan terverifikasi |

Contoh perintah untuk dijadwalkan (Windows Task Scheduler), sesuaikan lokasi `mysqldump` di Laragon:

    "C:\laragon\bin\mysql\<versi-mysql>\bin\mysqldump.exe" -u root --add-drop-table db_workshop > D:\backup\db_workshop.sql

Simpan salinan di lokasi terpisah dari komputer utama (flashdisk atau penyimpanan cloud) agar tetap aman
bila komputer rusak.
