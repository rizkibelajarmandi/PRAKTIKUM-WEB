# ACARA 27: Integrasi Backend-Frontend (CRUD Penuh dari View)

Catatan acuan buat nyusun laprak. Bahasanya sengaja santai, isinya lengkap: konsep, langkah bikin, langkah tes, dan bahan laporan.

**Hasil akhirnya:** dashboard admin yang bisa **Tambah, Ubah, Hapus** data mahasiswa (plus foto) lewat halaman-halaman terpisah, semuanya nyambung ke `MahasiswaController` lewat Router. Tampilannya sudah pakai **Bootstrap**, ada **notifikasi** sukses/gagal, dan ada **validasi di sisi View**.

---

## 1. Ringkasan: apa yang beda dari Acara 26

Backend (PHP) **nggak ada yang diubah**. Semua kerjaan di folder `views/`.

| Acara 26 | Acara 27 |
|---|---|
| Tambah/ubah dalam satu form di dashboard | Halaman terpisah: `form_tambah.html` dan `form_edit.html` |
| `BASE_URL` ditulis manual di 3 file JS | **Satu file `config.js`**, alamat dideteksi otomatis |
| Kode dobel di tiap halaman | Fungsi bersama di `common.js` |
| Status berupa teks biasa | **Notifikasi** (alert Bootstrap yang hilang sendiri) |
| Validasi cuma atribut `required` | **Validasi JavaScript** sebelum kirim ke server |
| CSS polos | **Bootstrap 5.3.3** (file lokal, jadi jalan tanpa internet) |

---

## 2. Konsep singkat (bahan Pendahuluan)

**a. FormData + `Content-Type`.** Kalau kirim berkas, datanya dikirim sebagai `multipart/form-data` lewat `FormData`. Header `Content-Type` **jangan diisi manual**, biarin browser yang isi karena dia perlu nambahin `boundary` (pembatas antar field). Kalau diisi manual, upload gagal.

**b. Kenapa edit pakai `POST ?_method=PUT`, bukan `PUT`.** Di modul, edit ditulis `method: 'PUT'` + `FormData`. Ini **gagal**. PHP cuma mengisi `$_POST` dan `$_FILES` untuk request POST, jadi body PUT multipart nggak kebaca. Hasil uji kita: server membalas **413 "Ukuran request melebihi batas server"** (pesannya nyesatin, aslinya bukan soal ukuran). Solusi: kirim `POST` dengan `?_method=PUT`, Router yang mengubahnya jadi PUT dan diteruskan ke `MahasiswaController::update()`.

**c. Pesan lintas halaman (flash message).** Habis simpan, kita pindah ke dashboard. Pesan "berhasil" disimpan sebentar di `sessionStorage`, lalu ditampilkan dashboard dan langsung dihapus.

**d. Validasi dua lapis.** Lapis 1 di browser (cepat, hemat request, enak buat pengguna). Lapis 2 di server (Controller/Model), yang **tetap jadi penentu**, karena validasi browser bisa dilewati lewat Postman atau DevTools.

**e. Otorisasi.** Tombol/halaman admin disembunyikan buat `user` biasa, tapi itu cuma tampilan. Yang beneran menolak adalah `Auth::requireAdmin()` di backend (403).

---

## 3. Struktur folder

```
ACARA27/bkpm/
├── app/
│   ├── config/  core/  models/  controllers/      (tidak berubah dari Acara 26)
│   └── public/
│       ├── index.php  .htaccess
│       ├── uploads/                                 (foto + .htaccess pengaman)
│       └── views/
│           ├── assets/bootstrap.min.css             (BARU)
│           ├── config.js                            (BARU)  alamat API
│           ├── common.js                            (BARU)  fungsi bersama
│           ├── index.html + app.js                  tabel publik
│           ├── login.html + login.js
│           ├── dashboard.html + dashboard.js
│           ├── form_tambah.html + form_tambah.js    (BARU)
│           └── form_edit.html + form_edit.js        (BARU)
└── docs/  (buat_admin.sql, tambah_kolom_foto.sql, PANDUAN_BACKUP_RESTORE.md, ACARA27.md)
```

URL yang dipakai (kalau nama foldernya `ACARA27`):

| Halaman | Alamat |
|---|---|
| Login | `http://localhost/ACARA27/bkpm/app/public/views/login.html` |
| Dashboard | `http://localhost/ACARA27/bkpm/app/public/views/dashboard.html` |
| Tambah | `http://localhost/ACARA27/bkpm/app/public/views/form_tambah.html` |
| Ubah | `http://localhost/ACARA27/bkpm/app/public/views/form_edit.html?id=1` |
| Cek API (harus JSON) | `http://localhost/ACARA27/bkpm/app/public/mahasiswa` |

---

## 4. Persiapan sebelum mulai

1. **Folder.** Taruh di `www` (Laragon) / `htdocs` (XAMPP) jadi `...\ACARA27\bkpm\app\public\...`. Jangan sampai folder ganda (`ACARA27\ACARA27\...`).
2. **Apache + MySQL** nyala.
3. **Kolom `foto` harus sudah ada** (dari Acara 26). Kalau belum, jalankan `docs/tambah_kolom_foto.sql` di phpMyAdmin (db_workshop, tab SQL).
4. **File `.htaccess` ada** di `app/public/` dan `app/public/uploads/`. Ini file tersembunyi: di Explorer aktifkan *View, Show, Hidden items*. Kalau hilang, API balas HTML dan muncul error `Unexpected token '<'`.
5. **Cek API:** buka `.../public/mahasiswa`, harus muncul JSON.
6. **Punya akun admin dan akun user.** Tidak ada akun bawaan, dan `register` selalu bikin role `user`. Pilih salah satu:
   - Naikkan akun yang ada (edit baris 3 `docs/buat_admin.sql`):
     ```sql
     UPDATE users SET role = 'admin' WHERE username = 'GANTI_USERNAME';
     ```
   - Atau bikin langsung (login: `admin` / `admin12345`):
     ```sql
     INSERT INTO users (username, email, password, role)
     VALUES ('admin', 'admin@example.com',
             '$2y$10$VgEvm8EzPZiFHDFzkN381OUCIXwreTctWmC6c6UbcVmv2zzuLpz2u', 'admin');
     ```
   Habis ubah role di database, **logout lalu login lagi** (role ikut tersimpan di token).
7. **Bersihkan sisa login lama:** F12, tab Application, Local Storage, Clear (localStorage dipakai bareng semua folder di `localhost`).

---

## 5. Langkah pengerjaan (detail)

### Langkah 0: Kloning dari Acara 26
1. Copy folder `ACARA26` jadi `ACARA27`.
2. Di `app/public/views/`, file lama (`dashboard.*`, `login.*`, `index.html`, `app.js`) akan **ditimpa** di langkah-langkah berikut. Folder `app/` (backend) dan `uploads/` dibiarkan.

### Langkah 1: Pasang Bootstrap (lokal)
1. Buat folder `app/public/views/assets/`.
2. Taruh `bootstrap.min.css` (versi 5.3.3) di situ. Cara dapetinnya: unduh "Compiled CSS and JS" dari getbootstrap.com lalu ambil `css/bootstrap.min.css`, atau `npm pack bootstrap@5.3.3`.
3. Tiap halaman dipanggil dengan:
   ```html
   <link rel="stylesheet" href="assets/bootstrap.min.css">
   ```
Kenapa lokal, bukan CDN? Supaya tetap tampil bagus walau lab/demo nggak ada internet.

### Langkah 2: `config.js` (alamat API, satu-satunya tempat)
Buat `views/config.js`:

```js
// ======================================================================
// ALAMAT API (BASE_URL)
// Normalnya TIDAK PERLU DIUBAH: alamat dideteksi otomatis dari alamat halaman,
//   .../public/views/xxx.html  ->  .../public
// Jadi kalau folder proyek dipindah atau di-rename, semua halaman ikut menyesuaikan.
//
// Isi BASE_URL_MANUAL HANYA jika halaman dibuka dari origin LAIN
// (misalnya Live Server di http://127.0.0.1:5500). Contoh:
//   const BASE_URL_MANUAL = 'http://localhost/ACARA27/bkpm/app/public';
// ======================================================================
const BASE_URL_MANUAL = '';
const BASE_URL = BASE_URL_MANUAL ||
    (window.location.origin + window.location.pathname.replace(/\/views\/[^/]*$/, ''));
```

Penjelasan: `BASE_URL` diambil dari alamat halaman, `.../public/views/xxx.html` dipotong jadi `.../public`. Jadi **kalau folder dipindah atau di-rename, nggak ada yang perlu diedit**. Isi `BASE_URL_MANUAL` cuma kalau halaman dibuka dari origin lain (Live Server).

> Penting: `BASE_URL` **hanya boleh dideklarasikan di `config.js`**. Kalau di file JS lain masih ada baris `const BASE_URL = ...` (sisa Acara 26), hapus, kalau nggak muncul error `Identifier 'BASE_URL' has already been declared`.

### Langkah 3: `common.js` (fungsi bersama)
Buat `views/common.js`. Isinya dipakai semua halaman, jadi urutan `<script>` di HTML harus: `config.js`, lalu `common.js`, lalu file halaman itu sendiri. Bagian-bagian pentingnya:

**3a. `panggilApi()`: pembungkus `fetch`** (token otomatis, error ramah, 401 otomatis ke login):

```js
async function panggilApi(path, opsi = {}) {
    const headers = {};
    // Jangan set Content-Type manual untuk FormData: browser menambahkan boundary sendiri
    if (typeof opsi.body === 'string') headers['Content-Type'] = 'application/json';
    if (getToken()) headers['Authorization'] = `Bearer ${getToken()}`;

    let response;
    try {
        response = await fetch(`${BASE_URL}${path}`, { ...opsi, headers });
    } catch (e) {
        throw new Error('Tidak dapat terhubung ke server');
    }

    let hasil;
    try {
        hasil = await response.json();
    } catch (e) {
        throw new Error('Server tidak membalas JSON (cek alamat API / file .htaccess)');
    }

    // 401 = token tidak ada / salah / kedaluwarsa -> paksa login ulang
    if (response.status === 401) {
        setFlash('Sesi berakhir, silakan login kembali', 'warning');
        keLogin();
        throw new Error(hasil.message || 'Tidak terautentikasi');
    }
    // 403 / 404 / 422 / dst: pesan dari server dilempar apa adanya
    if (!response.ok || !hasil.success) {
        throw new Error(hasil.message || `HTTP ${response.status}`);
    }
    return hasil;
}
```

Hal yang perlu diperhatiin:
- `Content-Type: application/json` hanya dipasang kalau body berupa **string** (JSON). Kalau body `FormData`, header dibiarkan kosong.
- Kalau server balas HTML (misal `.htaccess` hilang), pesannya jadi "Server tidak membalas JSON (cek alamat API / file .htaccess)", bukan pesan teknis `Unexpected token '<'`.
- 401 berarti token bermasalah, langsung hapus token dan lempar ke login. Status 403/404/422 pesan dari server dilempar apa adanya buat ditampilkan.

**3b. Notifikasi (tugas mandiri 1)**:

```js
function toast(teks, tipe = 'success') {   // tipe: success | danger | warning | info
    let wadah = document.getElementById('toast-wadah');
    if (!wadah) {
        wadah = document.createElement('div');
        wadah.id = 'toast-wadah';
        wadah.className = 'position-fixed top-0 end-0 p-3';
        wadah.style.zIndex = 1080;
        document.body.appendChild(wadah);
    }
    const el = document.createElement('div');
    el.className = `alert alert-${tipe} shadow-sm mb-2`;
    el.setAttribute('role', 'alert');
    el.textContent = teks;                       // textContent: aman dari XSS
    el.addEventListener('click', () => el.remove());
    wadah.appendChild(el);
    setTimeout(() => el.remove(), 4000);
}
```

```js
function setFlash(teks, tipe = 'success') {
    sessionStorage.setItem('flash', JSON.stringify({ teks, tipe }));
}
```

```js
function tampilkanFlash() {
    try {
        const f = JSON.parse(sessionStorage.getItem('flash'));
        if (f) toast(f.teks, f.tipe);
    } catch (e) { /* abaikan */ }
    sessionStorage.removeItem('flash');
}
```

`toast()` bikin kotak alert Bootstrap di pojok kanan atas yang hilang sendiri setelah 4 detik (atau klik buat nutup). Warnanya: `success` hijau, `danger` merah, `warning` kuning. Teksnya dimasukkan pakai `textContent`, jadi aman dari XSS.

**3c. Penjaga halaman**:

```js
function wajibAdmin() {
    if (!wajibLogin()) return false;
    if (getRole() !== 'admin') {
        setFlash('Halaman ini hanya untuk admin', 'danger');
        pindah('dashboard.html');
        return false;
    }
    return true;
}
```

**3d. Validasi sisi View (tugas mandiri 2)**:

```js
function validasiMahasiswa({ nim, nama, prodi, berkas }) {
    const e = {};
    if (!nim) e.nim = 'NIM wajib diisi';
    else if (!/^[A-Za-z0-9]+$/.test(nim)) e.nim = 'NIM hanya boleh huruf dan angka, tanpa spasi';
    else if (nim.length > 15) e.nim = 'NIM maksimal 15 karakter';

    if (!nama) e.nama = 'Nama wajib diisi';
    else if (nama.length < 3) e.nama = 'Nama minimal 3 karakter';
    else if (nama.length > 100) e.nama = 'Nama maksimal 100 karakter';

    if (!prodi) e.prodi = 'Program studi wajib diisi';
    else if (prodi.length > 50) e.prodi = 'Program studi maksimal 50 karakter';

    if (berkas) {   // foto opsional
        const ekstensi = berkas.name.split('.').pop().toLowerCase();
        if (!['jpg', 'jpeg', 'png'].includes(ekstensi)) e.foto = 'Foto harus berformat JPG atau PNG';
        else if (berkas.size > MAKS_FOTO) e.foto = 'Ukuran foto maksimal 2MB';
    }
    return e;
}
```

Aturannya mengikuti ukuran kolom tabel (nim 15, nama 100, prodi 50). **NIM boleh huruf dan angka** (bukan angka saja), karena NIM Polije berhuruf, contohnya `E1234356`.

### Langkah 4: Halaman publik dan login
- `index.html` + `app.js`: tabel baca-saja tanpa login (Bootstrap `table`), ada tombol Login.
- `login.html` + `login.js`: form username/password. Validasi kosong dicek dulu di browser. Kalau berhasil, `token` dan `role` disimpan di `localStorage`, lalu pindah ke dashboard. Login **sengaja nggak pakai `panggilApi()`**, karena 401 di login artinya "password salah", bukan "sesi berakhir".

### Langkah 5: Dashboard (`dashboard.html` + `dashboard.js`)
Isi dashboard: navbar (badge role + tombol Logout), kartu **Total Mahasiswa**, tombol **+ Tambah Mahasiswa** (link ke `form_tambah.html`), dan tabel.

Perubahan dari Acara 26: form inline **dihapus**. Tombol **Ubah** sekarang cuma link `form_edit.html?id=<id>`, jadi nggak ada form di dashboard. Tombol Tambah dan kolom Aksi cuma tampil buat admin. Notifikasi dari halaman form ditampilkan lewat `tampilkanFlash()`.

### Langkah 6: Tambah data (`form_tambah.html` + `form_tambah.js`)
HTML: card Bootstrap dengan field `nim`, `nama`, `prodi`, `foto` (file), tiap field punya `<div id="err-...">` buat pesan error. Form pakai atribut `novalidate` supaya yang tampil pesan validasi kita, bukan pop-up bawaan browser.

JavaScript lengkap:

```js
// Langkah 1 modul: View tambah data (FormData + header Authorization)
async function mulai() {
    const form = document.getElementById('form-mahasiswa');
    const inputFoto = document.getElementById('foto');
    const tombol = document.getElementById('btn-simpan');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const data = {
            nim: document.getElementById('nim').value.trim(),
            nama: document.getElementById('nama').value.trim(),
            prodi: document.getElementById('prodi').value.trim(),
            berkas: inputFoto.files[0],
        };

        // Validasi sisi View (tugas mandiri 2): berhenti di sini jika ada yang salah, tanpa request ke server
        const errors = validasiMahasiswa(data);
        tampilkanErrorForm(form, errors);
        if (Object.keys(errors).length > 0) {
            toast('Periksa kembali isian formulir', 'danger');
            return;
        }

        // Berkas dikirim sebagai multipart/form-data (bukan JSON)
        const formData = new FormData();
        formData.append('nim', data.nim);
        formData.append('nama', data.nama);
        formData.append('prodi', data.prodi);
        if (data.berkas) formData.append('foto', data.berkas);

        tombol.disabled = true;   // cegah klik ganda
        try {
            await panggilApi('/mahasiswa', { method: 'POST', body: formData });
            setFlash('Data berhasil ditambahkan');   // tampil di dashboard setelah pindah halaman
            pindah('dashboard.html');
        } catch (error) {
            toast(error.message, 'danger');          // 403 / 413 / 422 dari server
            tombol.disabled = false;
        }
    });
}

if (wajibAdmin()) mulai();
```

Alurnya: cek admin, validasi di browser (kalau ada yang salah, **berhenti, tanpa request ke server**), bikin `FormData`, kirim POST dengan token, kunci tombol (anti klik ganda), sukses simpan flash dan pindah ke dashboard, gagal tampilkan toast merah.

### Langkah 7: Ubah data (`form_edit.html` + `form_edit.js`)
Ini langkah 2 di modul, tapi dengan koreksi `PUT` jadi `POST ?_method=PUT`.

```js
// Langkah 2 modul: View edit data. id diambil dari alamat halaman: form_edit.html?id=3
async function mulai() {
    const id = new URLSearchParams(window.location.search).get('id');
    if (!/^\d+$/.test(id || '')) {
        setFlash('ID mahasiswa tidak valid', 'danger');
        pindah('dashboard.html');
        return;
    }

    const form = document.getElementById('form-mahasiswa');
    const inputNim = document.getElementById('nim');
    const inputNama = document.getElementById('nama');
    const inputProdi = document.getElementById('prodi');
    const inputFoto = document.getElementById('foto');
    const tombol = document.getElementById('btn-simpan');

    // Muat data lama (GET ?id= dilayani MahasiswaController::index())
    try {
        const hasil = await panggilApi(`/mahasiswa?id=${id}`);
        inputNim.value = hasil.data.nim;
        inputNama.value = hasil.data.nama;
        inputProdi.value = hasil.data.prodi;
        if (hasil.data.foto) {
            document.getElementById('foto-sekarang').appendChild(buatFoto(hasil.data.foto, hasil.data.nama, 80));
        }
    } catch (error) {
        setFlash(error.message, 'danger');   // mis. "Data tidak ditemukan"
        pindah('dashboard.html');
        return;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const data = {
            nim: inputNim.value.trim(),
            nama: inputNama.value.trim(),
            prodi: inputProdi.value.trim(),
            berkas: inputFoto.files[0],
        };
        const errors = validasiMahasiswa(data);
        tampilkanErrorForm(form, errors);
        if (Object.keys(errors).length > 0) {
            toast('Periksa kembali isian formulir', 'danger');
            return;
        }

        const formData = new FormData();
        formData.append('nim', data.nim);
        formData.append('nama', data.nama);
        formData.append('prodi', data.prodi);
        if (data.berkas) formData.append('foto', data.berkas);

        tombol.disabled = true;
        try {
            // PENTING: bukan method 'PUT'. PHP tidak mengisi $_POST/$_FILES untuk PUT, jadi body
            // FormData tidak terbaca (server membalas 413). Kirim POST + ?_method=PUT; Router
            // meneruskannya ke MahasiswaController::update($id).
            await panggilApi(`/mahasiswa?id=${id}&_method=PUT`, { method: 'POST', body: formData });
            setFlash('Data berhasil diperbarui');
            pindah('dashboard.html');
        } catch (error) {
            toast(error.message, 'danger');
            tombol.disabled = false;
        }
    });
}

if (wajibAdmin()) mulai();
```

Alurnya: ambil `id` dari alamat (`?id=3`), kalau nggak valid kembali ke dashboard. Muat data lama lewat `GET /mahasiswa?id=3` (dilayani `index()`), isi form, tampilkan foto yang sekarang. Saat simpan, kirim `POST ?id=3&_method=PUT`. Kolom foto dikosongkan kalau nggak mau ganti foto, dan di server foto lama otomatis dihapus kalau ada foto baru (`unlink()`, tugas mandiri Acara 26).

### Langkah 8: Hapus data (di `dashboard.js`)
Langkah 3 di modul, dengan tambahan penanganan hasil:

```js
async function hapusData(id, nama) {
    if (!confirm(`Hapus data "${nama}"?`)) return;
    try {
        await panggilApi(`/mahasiswa?id=${id}`, { method: 'DELETE' });
        toast('Data berhasil dihapus');
        await muatData();
    } catch (error) {
        toast(error.message, 'danger');   // 403 bukan admin, 404 sudah terhapus, dst.
    }
}
```

Di modul, `fetch` dipanggil tanpa ngecek hasil, jadi kalau gagal (token habis, bukan admin, data sudah hilang) pengguna nggak dapat kabar apa-apa. Di sini semua kasus muncul sebagai toast.

---

## 6. Cara pengujian (detail)

**Alat:** browser + DevTools (F12), Postman, phpMyAdmin, File Explorer (buat lihat isi folder `uploads/`).

**File uji yang disiapkan:** `foto.jpg`, `foto2.png`, `catatan.txt`, `palsu.jpg` (copy `catatan.txt` lalu ganti namanya jadi `palsu.jpg`), `besar.jpg` (gambar di atas 2 MB).

**Cara ngambil bukti di DevTools:**
- **Network** (refresh dulu setelah buka DevTools): klik request, lihat *Headers* (URL, method, status) dan *Payload*.
- **Console**: lihat error merah. Ketik `BASE_URL` lalu Enter buat lihat alamat API yang dipakai.
- **Application, Local Storage**: lihat/ubah `token` dan `role`.

Isi kolom *Hasil Aktual* dan *Status* sendiri. Screenshot langsung tiap skenario, namai misalnya `A27-TC05-tambah-berhasil.png`.

| ID | Skenario | Langkah | Hasil diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| TC-01 | Alamat API otomatis | Buka `login.html`, F12, Console, ketik `BASE_URL` | Berisi alamat `.../public` sesuai folder yang dipakai | | |
| TC-02 | Login kosong | Login, klik Masuk tanpa isi apa-apa. Cek tab Network | Dua field merah + pesan "wajib diisi", **tidak ada request** | | |
| TC-03 | Login salah | Isi username benar, password salah, Masuk | Notifikasi merah "Username atau password salah" | | |
| TC-04 | Login admin | Login pakai akun admin | Masuk dashboard. Badge "admin", tombol **+ Tambah** dan kolom **Aksi** tampil | | |
| TC-05 | Login user | Logout, login pakai akun user | Data tampil, **tanpa** tombol Tambah/Ubah/Hapus | | |
| TC-06 | Tambah berhasil + foto | Admin, klik **+ Tambah**, isi NIM `E41230010`, Nama `Rina Contoh`, Prodi `TI`, pilih `foto.jpg`, Simpan | Pindah ke dashboard, notifikasi hijau "Data berhasil ditambahkan", baris baru + thumbnail, file baru muncul di `uploads/` | | |
| TC-07 | Validasi: kosong | Buka form tambah, langsung Simpan. Cek Network | 3 field merah + toast "Periksa kembali isian formulir", **tidak ada request** | | |
| TC-08 | Validasi: NIM dan nama | NIM `AB 12` (ada spasi), lalu nama `ab` | "NIM hanya boleh huruf dan angka" dan "Nama minimal 3 karakter" | | |
| TC-09 | Validasi: foto | Pilih `catatan.txt`, lalu `besar.jpg` | "Foto harus berformat JPG atau PNG" dan "Ukuran foto maksimal 2MB", tanpa request | | |
| TC-10 | Berkas palsu (lolos browser, ditolak server) | Pilih `palsu.jpg`, isi data valid, Simpan | Toast merah "Berkas harus berupa gambar JPG/PNG yang valid", nggak ada data/berkas baru | | |
| TC-11 | NIM duplikat | Tambah dengan NIM yang sudah ada | Toast merah "NIM sudah terdaftar", tetap di halaman form | | |
| TC-12 | Ubah tanpa ganti foto | Dashboard, klik **Ubah**, ganti nama saja, Simpan. Cek Network | Data lama terisi dulu. Request: **POST** ke `mahasiswa?id=..&_method=PUT`. Notifikasi "Data berhasil diperbarui", foto tetap | | |
| TC-13 | Ubah + foto baru | Ubah data berfoto, pilih `foto2.png`, Simpan. Lihat isi `uploads/` | Thumbnail berganti, **foto lama hilang** dari `uploads/` | | |
| TC-14 | ID tidak valid | Buka `form_edit.html` tanpa `?id`, lalu `?id=999999` | Balik ke dashboard dengan notifikasi "ID mahasiswa tidak valid" / "Data tidak ditemukan" | | |
| TC-15 | Hapus | Klik **Hapus**, OK. Cek phpMyAdmin dan `uploads/` | Notifikasi hijau, baris hilang, hilang dari tabel `mahasiswa`, **file foto ikut terhapus** | | |
| TC-16 | Hapus data yang sudah hilang | Buka dashboard di **2 tab**. Hapus data X di tab 1, lalu klik Hapus data X di tab 2 | Toast merah "Data tidak ditemukan" | | |
| TC-17 | Token palsu | Local Storage, ubah nilai `token` jadi `abc`. Di form tambah isi data valid, Simpan | Dilempar ke login, notifikasi kuning "Sesi berakhir, silakan login kembali", token terhapus | | |
| TC-18 | Akses halaman admin lewat URL | (a) Tanpa login buka `form_tambah.html`. (b) Login sebagai user, buka `form_tambah.html` | (a) ke login. (b) ke dashboard + notifikasi "Halaman ini hanya untuk admin" | | |
| TC-19 | User menembak API | Postman: `POST {{base_url}}/mahasiswa` + token **user** | **403** | | |
| TC-20 | Bukti PUT asli gagal (bahan pembahasan) | Postman: `PUT {{base_url}}/mahasiswa?id=1`, Authorization token admin, Body **form-data** (`nim`, `nama`, `prodi`) | **413** "Ukuran request melebihi batas server" (padahal bukan soal ukuran, body PUT multipart nggak dibaca PHP). Bandingkan dengan TC-12 yang berhasil | | |
| TC-21 | Folder dipindah | Copy folder proyek ke `www\kampus\coba\`. Buka `http://localhost/kampus/coba/bkpm/app/public/views/login.html`. Console, `BASE_URL` | `BASE_URL` ikut berubah, login dan dashboard jalan **tanpa edit file apa pun** | | |

---

## 7. Kalau pindah/ganti nama folder

Sekarang jauh lebih gampang:
1. Pindahkan seluruh folder proyek, **pastikan `.htaccess` ikut** (file tersembunyi, termasuk `uploads/.htaccess`).
2. Buka halaman dari alamat baru. `BASE_URL` menyesuaikan sendiri, **nggak ada file JS yang perlu diedit**.
3. Cek: `<alamat baru>/public/mahasiswa` harus keluar JSON.
4. Ganti `base_url` di **Postman** (Environment, tanpa `/` di akhir, lalu Ctrl+S) kalau tes pakai Postman.
5. Kalau buka halaman dari **Live Server** (`127.0.0.1:5500`), isi `BASE_URL_MANUAL` di `config.js` dengan alamat Apache-nya.
6. File PHP dan database nggak perlu diubah.

---

## 8. Masalah yang sering muncul

| Gejala | Penyebab | Solusi |
|---|---|---|
| Tampilan polos, nggak ada gaya Bootstrap | `assets/bootstrap.min.css` nggak ada / salah folder | Network, cari request css, kalau 404 taruh file di `views/assets/` |
| Notifikasi "Server tidak membalas JSON..." | API balas HTML: `.htaccess` hilang, `mod_rewrite` mati | Cek `.htaccess` di `public/`, buka `.../public/mahasiswa` langsung |
| Error `BASE_URL has already been declared` | Ada `const BASE_URL` sisa di file JS lain | Hapus baris itu, `BASE_URL` cuma di `config.js` |
| Tombol **+ Tambah** nggak muncul | Role bukan `admin` | Console: `localStorage.getItem('role')`. Ubah role di DB lalu login ulang |
| Edit langsung balik ke dashboard | `id` salah / data sudah dihapus / bukan admin | Lihat notifikasi yang muncul di dashboard |
| Tambah data error 500 | Kolom `foto` belum ada | Jalankan `docs/tambah_kolom_foto.sql` |
| Edit gagal 413 | Kode masih memakai `method: 'PUT'` + FormData | Pakai `POST` dengan `?id=..&_method=PUT` |
| Perubahan JS nggak kelihatan | Cache browser | **Ctrl + F5** |
| Halaman 404 "Not Found" bawaan Apache | Folder nggak ada di `www` / nama beda | Cek struktur folder |

---

## 9. Bahan laporan

**Format (sesuai modul):** A4, PDF, struktur: Cover, Pendahuluan, Hasil Praktik, Kesimpulan, Daftar Pustaka. Cantumkan NIM, Nama, Golongan, Tugas Minggu ke-X. Nama file: `ACARA-27_GOL_NIM_NAMA.pdf`. Kumpulkan di slearn-jti.com.

**Pendahuluan:** tujuan (integrasi backend-frontend: CRUD penuh dari View), lalu konsep dari bagian 2 (FormData, method spoofing, flash message, validasi dua lapis, otorisasi).

**Hasil Praktik:**
1. Struktur folder (bagian 3) dan daftar file baru.
2. Langkah 1-8 dengan potongan kode inti (`config.js`, `panggilApi`, `form_tambah.js`, `form_edit.js`, `hapusData`).
3. Tabel pengujian (bagian 6) beserta screenshot.
4. Pembahasan, bagian ini yang bikin laporan "berisi":
   - **Temuan PUT + FormData gagal (TC-20 vs TC-12).** Modul menulis `PUT`, hasil uji 413. Penyebab: PHP hanya mengisi `$_POST`/`$_FILES` untuk POST. Solusi: `POST ?_method=PUT` yang dibaca Router.
   - **Validasi dua lapis (TC-07 sampai TC-10).** Browser menolak dulu tanpa request, tapi berkas palsu `.jpg` lolos browser dan **tetap ditolak server**. Jadi validasi server tidak bisa digantikan.
   - **Keamanan role (TC-18 dan TC-19).** Menyembunyikan tombol hanya tampilan, penolakan sebenarnya di `requireAdmin()` (403).
   - **Kerapihan data.** Foto lama dihapus saat diganti (TC-13) dan saat data dihapus (TC-15), jadi nggak ada berkas yatim.
   - **Penanganan gagal yang ramah (graceful failure).** Server mati / balas HTML / token habis, semuanya jadi notifikasi yang jelas, bukan halaman kosong. Nyambung ke kompetensi "alert notification" di skill passport.
   - **Perbaikan dari modul:** `fetch` hapus/edit di modul nggak mengecek hasil, di sini ditambah penanganan dan notifikasi (tugas mandiri 1).

**Kesimpulan (draft, silakan ubah):**
> Integrasi backend-frontend berhasil: admin dapat menambah, mengubah, dan menghapus data mahasiswa beserta foto melalui halaman terpisah yang terhubung ke `MahasiswaController` lewat Router. Pengiriman berkas memakai `FormData` tanpa mengatur `Content-Type` manual, dan perubahan data dikirim dengan `POST` ber-`?_method=PUT` karena PHP tidak membaca body multipart pada method PUT. Validasi di sisi View mengurangi request yang tidak perlu, sedangkan validasi dan otorisasi di sisi server tetap menjadi penentu. Notifikasi visual membuat hasil setiap operasi jelas bagi pengguna.

**Daftar Pustaka (cek ulang dan isi tanggal akses):**
- MDN Web Docs, "FormData", developer.mozilla.org/en-US/docs/Web/API/FormData
- MDN Web Docs, "Using the Fetch API", developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch
- PHP Manual, "Handling file uploads", php.net/manual/en/features.file-upload.php
- Bootstrap 5.3 Documentation, getbootstrap.com/docs/5.3/
- Modul Workshop BKPM, Acara 27, Politeknik Negeri Jember

**Daftar screenshot yang disarankan:** login (TC-02/03), dashboard admin vs user (TC-04/05), form tambah + notifikasi hijau (TC-06), validasi merah (TC-07 sampai TC-09), penolakan berkas palsu (TC-10), request edit di Network (TC-12), isi `uploads/` sebelum dan sesudah ganti foto (TC-13), hapus + phpMyAdmin (TC-15), Postman 403 dan 413 (TC-19, TC-20), `BASE_URL` setelah folder dipindah (TC-21).

---

## 10. Catatan

- Indikator Acara 27 menyebut studi kasus **smart village**, dan rubrik **Produk (40%)** meminta aplikasi yang bisa diterapkan di Agroinformatika/Smart Village. Tanyakan ke dosen apakah data mahasiswa masih boleh dipakai. Kalau harus diganti, polanya sama persis: bikin Model + Controller baru, daftarkan di Router, ganti field di form.
- Backend dan frontend ini sudah diuji otomatis (57 skenario) di Apache + PHP + MariaDB, tapi belum di Windows/Laragon dan browser sungguhan. Hasil di mesinmu yang jadi bukti akhir untuk laporan.
