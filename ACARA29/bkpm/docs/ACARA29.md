# ACARA 29: Integrasi Backend-Frontend (Loading State, Error Feedback, Escaping XSS, Retry)

Catatan acuan buat nyusun laprak. Bahasanya santai, isinya lengkap: konsep, langkah bikin, langkah tes, dan bahan laporan.

**Hasil akhirnya:** semua halaman (publik, login, dashboard, form tambah, form ubah) sekarang punya **indikator loading**, **pesan gagal yang jelas** (bukan gagal senyap), tombol yang **nonaktif selama request berjalan**, **escaping output** supaya tag HTML tampil sebagai teks, dan **retry otomatis 1 kali** kalau jaringan putus sementara.

---

## 1. Ringkasan: apa yang beda dari Acara 27

Backend (PHP) **nggak ada yang diubah**. Semua kerjaan di folder `views/`.

| Acara 27 | Acara 29 |
|---|---|
| Status memuat berupa satu baris teks di atas tabel | Baris **"Memuat data..." + spinner di dalam tabel**, juga untuk "Belum ada data" dan "Gagal memuat" |
| Tombol cuma `disabled = true`, dikembalikan manual di `catch` | Tombol **nonaktif + spinner + teks sementara** ("Menyimpan...", "Menghapus...", "Memproses..."), dikembalikan lewat blok **`finally`** |
| Tombol Hapus tidak diberi tanda apa pun saat request jalan | Tombol Hapus ikut loading, jadi nggak bisa diklik dua kali |
| Form ubah langsung tampil kosong sambil menunggu data lama | Ada tulisan "Memuat data..." dan tombol Simpan nonaktif sampai data lama terisi |
| Fetch gagal karena jaringan langsung jadi error | **Diulang otomatis 1 kali** (tugas mandiri 2), toast kuning "Koneksi bermasalah, mencoba lagi..." |
| Tidak ada fungsi escaping | `escapeHTML()` di `common.js` (tugas mandiri 1) |

---

## 2. Konsep singkat (bahan Pendahuluan)

**a. Loading state.** `fetch` itu asynchronous dan lama-singkatnya tergantung jaringan. Tanpa penanda, pengguna mengira halaman macet dan mengklik berkali-kali. Jadi selama menunggu, tampilkan "Memuat data...", dan nonaktifkan tombol submit supaya request tidak terkirim ganda.

**b. Error feedback (graceful failure).** Gagal senyap (*silent failure*) itu membingungkan: tabel kosong, tapi pengguna nggak tahu itu "belum ada data" atau "server mati". Setiap kegagalan, baik jaringan putus maupun balasan error dari server, harus jadi pesan yang jelas. Di proyek ini pesannya muncul di tabel (merah) atau sebagai toast.

**c. XSS dan escaping.** Kalau data dari API dimasukkan ke `innerHTML`, nama seperti `<img src=x onerror=...>` yang sudah terlanjur tersimpan di database akan **dieksekusi** browser. Pencegahannya: ubah karakter khusus (`< > & " '`) menjadi entitas HTML (`&lt; &gt; ...`) sebelum ditampilkan, atau pakai `textContent` yang memang memperlakukan isinya sebagai teks.

**d. Retry.** `fetch()` hanya melempar error kalau **jaringan** gagal (server mati, koneksi putus). Balasan 400/404/422/500 dari server **bukan** error jaringan, jadi tidak diulang (mengulang request yang memang ditolak server nggak ada gunanya).

**e. try-catch-finally.** `try` jalankan request, `catch` tampilkan pesan gagal, `finally` selalu jalan apa pun hasilnya, dipakai untuk mengembalikan tombol ke keadaan semula.

---

## 3. Struktur folder

```
ACARA29/bkpm/
├── app/
│   ├── config/  core/  models/  controllers/      (tidak berubah dari Acara 27)
│   └── public/
│       ├── index.php  .htaccess
│       ├── uploads/
│       └── views/
│           ├── config.js
│           ├── common.js              (DIUBAH) + escapeHTML, setTombolLoading,
│           │                                    tampilkanBarisInfo, fetchDenganRetry
│           ├── app.js + index.html    (DIUBAH) loading/error di dalam tabel
│           ├── login.js               (DIUBAH) tombol loading + finally + retry
│           ├── dashboard.js           (DIUBAH) loading, hapus dengan tombol loading
│           ├── form_tambah.js         (DIUBAH) try-catch-finally
│           ├── form_edit.js + .html   (DIUBAH) loading data lama + try-catch-finally
│           └── form_tambah.html, login.html, dashboard.html (hanya tambah aria-live)
└── docs/  (ACARA29.md, ...)
```

---

## 4. Persiapan sebelum mulai

1. Taruh folder di `htdocs` (XAMPP) / `www` (Laragon): `...\ACARA29\bkpm\app\public\...`. Jangan sampai folder ganda.
2. Apache + MySQL nyala. Kolom `foto` sudah ada (dari Acara 26).
3. Punya **akun admin** (lihat `docs/buat_admin.sql`). Habis ubah role di database, **logout lalu login lagi**.
4. Buka `.../public/mahasiswa`, harus muncul JSON.
5. Tekan **Ctrl + F5** di browser supaya JS yang lama (cache) tidak terpakai.

---

## 5. Langkah pengerjaan (detail)

### Langkah 0: Salin dari Acara 27
Salin folder `ACARA27` jadi `ACARA29`. Backend tidak disentuh.

### Langkah 1: Indikator loading (modul: `muatDataMahasiswa()`)
Di `common.js` ditambah fungsi bantu supaya baris info bisa dipakai ulang di dua halaman (publik dan dashboard):

```js
function tampilkanBarisInfo(tbody, jumlahKolom, teks, kelas = 'text-muted', denganSpinner = false) {
    const spinner = denganSpinner
        ? '<span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>' : '';
    tbody.innerHTML =
        `<tr><td colspan="${Number(jumlahKolom)}" class="${escapeHTML(kelas)} text-center fst-italic py-3">` +
        `${spinner}${escapeHTML(teks)}</td></tr>`;
}
```

Dipakai di `app.js`:

```js
async function muatDataMahasiswa() {
    tampilkanBarisInfo(tbody, JUMLAH_KOLOM, 'Memuat data...', 'text-primary', true);
    try {
        const hasil = await panggilApi('/mahasiswa');
        if (hasil.data.length === 0) { tampilkanBarisInfo(tbody, JUMLAH_KOLOM, 'Belum ada data'); return; }
        tbody.innerHTML = '';
        hasil.data.forEach((mhs) => tbody.appendChild(buatBaris(mhs)));
    } catch (error) {
        tampilkanBarisInfo(tbody, JUMLAH_KOLOM, 'Gagal memuat data: ' + error.message, 'text-danger');
    }
}
```

Beda dengan modul: modul memakai `colspan="3"`, di proyek ini tabelnya 4 kolom (ada **Foto**), dan 5 kolom untuk admin (ada **Aksi**). Pengecekan `response.ok` yang ditulis di modul sudah ditangani `panggilApi()` sejak Acara 27.

### Langkah 2: Escaping output (modul: `escapeHTML()`)

```js
function escapeHTML(teks) {
    return String(teks ?? '')
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}
```

Beda dengan modul: modul memakai trik `div.innerText` lalu membaca `innerHTML`. Trik itu meng-escape `< > &` tapi **tidak meng-escape tanda kutip**, padahal kutip bisa dipakai membobol atribut HTML (`title="..."`). Versi di atas meng-escape semuanya.

Soal penerapan: data mahasiswa sejak Acara 27 sudah diisi lewat `textContent` (`buatBaris()`), yang otomatis aman. `escapeHTML()` dipakai di tempat yang memang menyusun **string HTML**, yaitu `tampilkanBarisInfo()` (pesan error dari server ikut di-escape karena isinya datang dari luar).

### Langkah 3: try-catch-finally + tombol loading

```js
function setTombolLoading(tombol, sedang, teks = 'Memproses...') {
    if (sedang) {
        if (tombol.dataset.teksAsli === undefined) tombol.dataset.teksAsli = tombol.textContent;
        tombol.disabled = true;
        tombol.textContent = '';
        const spinner = document.createElement('span');
        spinner.className = 'spinner-border spinner-border-sm me-2';
        spinner.setAttribute('aria-hidden', 'true');
        tombol.append(spinner, teks);
    } else {
        tombol.disabled = false;
        if (tombol.dataset.teksAsli !== undefined) {
            tombol.textContent = tombol.dataset.teksAsli;
            delete tombol.dataset.teksAsli;
        }
    }
}
```

Pola di semua form (contoh `form_tambah.js`):

```js
let berhasil = false;
setTombolLoading(tombol, true, 'Menyimpan...');
try {
    await panggilApi('/mahasiswa', { method: 'POST', body: formData });
    berhasil = true;
    setFlash('Data berhasil ditambahkan');
    pindah('dashboard.html');
} catch (error) {
    toast(error.message, 'danger');
} finally {
    if (!berhasil) setTombolLoading(tombol, false);
}
```

Kenapa ada `if (!berhasil)`: kalau berhasil, halaman sedang berpindah. Tombol sengaja dibiarkan nonaktif sampai halaman berganti, supaya nggak bisa terklik lagi di jeda itu.

Diterapkan di: `form_tambah.js` (Simpan), `form_edit.js` (Simpan Perubahan), `login.js` (Masuk), dan `dashboard.js` (tombol **Hapus** per baris, teks "Menghapus...").

### Langkah 3b: Loading data lama di form ubah
`form_edit.html` ditambah `<p id="status-muat">Memuat data...</p>`. Selama data lama diambil, tombol Simpan nonaktif; setelah terisi, tulisan hilang dan tombol aktif.

### Tugas mandiri 1: Escaping di tempat lain
Semua titik yang menampilkan data dinamis sudah ditinjau:

| Lokasi | Data dinamis | Cara aman |
|---|---|---|
| Tabel publik dan dashboard | nim, nama, prodi | `textContent` |
| Baris info tabel | pesan error dari server | `escapeHTML()` |
| Thumbnail foto | nama file, nama untuk `alt` | `encodeURIComponent` dan properti `img.alt` |
| Tombol Ubah | `id` di `href` | `encodeURIComponent` |
| Toast dan flash | pesan | `textContent` |
| Dialog konfirmasi Hapus | nama | `confirm()` (teks polos, bukan HTML) |
| Form ubah | nilai lama | `input.value` |

### Tugas mandiri 2: Retry otomatis 1 kali

```js
const JUMLAH_ULANG = 1;
const JEDA_ULANG_MS = 800;

async function fetchDenganRetry(url, opsi, sisaUlang = JUMLAH_ULANG) {
    try {
        return await fetch(url, opsi);
    } catch (e) {
        if (sisaUlang <= 0) throw e;
        toast('Koneksi bermasalah, mencoba lagi...', 'warning');
        await new Promise((selesai) => setTimeout(selesai, JEDA_ULANG_MS));
        return fetchDenganRetry(url, opsi, sisaUlang - 1);
    }
}
```

`panggilApi()` dan `login.js` memakai fungsi ini, jadi seluruh halaman otomatis dapat retry. Kalau tetap gagal, pesannya: *"Tidak dapat terhubung ke server (sudah dicoba ulang 1 kali)"*.

Catatan untuk pembahasan: retry hanya untuk kegagalan jaringan, bukan untuk balasan 4xx/5xx. Pada `POST` tambah data, ada kemungkinan kecil request pertama sebenarnya sudah sampai ke server tapi balasannya hilang; kalau itu terjadi, percobaan kedua ditolak server dengan "NIM sudah terdaftar" (422) karena ada pengecekan `nimExists()`. Jadi tidak ada data ganda.

---

## 6. Cara pengujian (detail)

**Alat:** browser + DevTools (F12), phpMyAdmin.

**Trik simulasi di DevTools (Chrome):**
- **Loading terlihat jelas:** F12, tab **Network**, ubah *No throttling* jadi **Slow 3G**, lalu muat ulang halaman.
- **Jaringan putus:** Network, centang **Offline**, lalu klik tombolnya. Untuk menguji retry yang berhasil, hilangkan centang Offline dalam 1 detik pertama.
- **Server mati:** matikan Apache di XAMPP lalu muat ulang halaman.

Isi *Hasil Aktual* dan *Status* sendiri. Screenshot langsung tiap skenario, namai misalnya `A29-TC03-loading.png`.

| ID | Skenario | Langkah | Hasil diharapkan | Hasil Aktual | Status |
|---|---|---|---|---|---|
| TC-01 | Loading halaman publik | Network: Slow 3G, buka `index.html` | Baris "Memuat data..." + spinner di dalam tabel, lalu berganti data | | |
| TC-02 | Data kosong | Kosongkan tabel `mahasiswa` (atau pakai DB uji), buka `index.html` | Baris "Belum ada data" | | |
| TC-03 | Server mati | Matikan Apache, muat ulang `index.html` | Baris merah "Gagal memuat data: Tidak dapat terhubung ke server (sudah dicoba ulang 1 kali)". Sebelumnya sempat muncul toast kuning "mencoba lagi" | | |
| TC-04 | Retry berhasil | Network: Offline, muat ulang, lalu hilangkan Offline kurang dari 1 detik | Toast kuning "mencoba lagi...", lalu data tampil (di Network terlihat 2 request) | | |
| TC-05 | XSS pada nama | Admin: Tambah data, Nama `<b>Coba</b>`, NIM `E41230099`, Prodi `TI`. Lihat di `index.html` dan dashboard | Nama tampil **apa adanya sebagai teks** `<b>Coba</b>`, tidak tampil tebal | | |
| TC-06 | XSS dengan skrip | Tambah/ubah nama jadi `<img src=x onerror=alert(1)>` | Tampil sebagai teks, **tidak ada** popup alert | | |
| TC-07 | Login: tombol loading | Network: Slow 3G, login | Tombol nonaktif dengan spinner "Memproses...". Password salah: tombol kembali "Masuk" dan aktif | | |
| TC-08 | Tambah: tombol loading | Slow 3G, Tambah data valid, klik Simpan lalu coba klik lagi | Tombol "Menyimpan..." nonaktif (tidak bisa klik dua kali), lalu pindah ke dashboard + toast hijau | | |
| TC-09 | Tambah gagal | Tambah dengan NIM yang sudah ada | Toast merah "NIM sudah terdaftar", tombol kembali "Simpan" dan aktif | | |
| TC-10 | Ubah: loading data lama | Slow 3G, klik Ubah | Tulisan "Memuat data..." dan tombol Simpan nonaktif, lalu form terisi dan tombol aktif | | |
| TC-11 | Ubah berhasil | Ganti nama, Simpan | Tombol "Menyimpan...", lalu dashboard + toast "Data berhasil diperbarui" | | |
| TC-12 | Hapus: tombol loading | Slow 3G, klik Hapus lalu OK | Tombol Hapus pada baris itu menjadi "Menghapus..." dan nonaktif, lalu baris hilang + toast hijau | | |
| TC-13 | Hapus data yang sudah hilang | Buka dashboard di 2 tab, hapus data X di tab 1, hapus X di tab 2 | Toast merah "Data tidak ditemukan", tombol Hapus kembali normal | | |
| TC-14 | Alur lengkap | login, dashboard, tambah (dengan foto), ubah, hapus | Setiap tahap punya indikator loading dan umpan balik yang jelas | | |
| TC-15 | Unit `escapeHTML` | Console di halaman mana pun: `escapeHTML('<b>"x"</b>')` | `&lt;b&gt;&quot;x&quot;&lt;/b&gt;` | | |

---

## 7. Masalah yang sering muncul

| Gejala | Penyebab | Solusi |
|---|---|---|
| Tampilan nggak berubah / masih tulisan lama | Cache browser | **Ctrl + F5** |
| `escapeHTML is not defined` | `common.js` tidak termuat / urutan `<script>` salah | Urutannya harus `config.js`, `common.js`, baru file halaman |
| Spinner tidak berputar | `bootstrap.min.css` tidak termuat | Network, cari css, kalau 404 taruh di `views/assets/` |
| Tombol tetap nonaktif setelah gagal | Kode `finally` terhapus / `berhasil` tidak di-reset | Cek pola di Langkah 3 |
| Notifikasi "Server tidak membalas JSON..." | `.htaccess` hilang / `mod_rewrite` mati | Cek `.htaccess` di `public/` |

---

## 8. Bahan laporan

**Format (sesuai modul):** A4, PDF, struktur: Cover, Pendahuluan, Hasil Praktik, Kesimpulan, Daftar Pustaka. Cantumkan NIM, Nama, Golongan, Tugas Minggu ke-X. Nama file: `ACARA-29_GOL_NIM_NAMA.pdf`. Kumpulkan di slearn-jti.com.

**Pendahuluan:** tujuan (menyempurnakan kualitas View sebagai persiapan deployment), lalu konsep dari bagian 2 (loading state, error feedback, XSS dan escaping, retry, try-catch-finally).

**Hasil Praktik:**
1. Struktur folder (bagian 3) dan daftar file yang diubah.
2. Langkah 1 sampai 3 dan tugas mandiri dengan potongan kode inti.
3. Tabel pengujian (bagian 6) beserta screenshot.
4. Pembahasan, bagian yang bikin laporan "berisi":
   - **Dua lapis pencegahan XSS (TC-05, TC-06).** `textContent` untuk data biasa, `escapeHTML()` untuk string HTML. Escaping tanda kutip melengkapi versi modul.
   - **Retry hanya untuk kegagalan jaringan (TC-03, TC-04).** Balasan 4xx/5xx tidak diulang karena percuma. Alasan `nimExists()` di server mencegah data ganda.
   - **`finally` dan klik ganda (TC-08, TC-12).** Tombol dikembalikan di `finally`, kecuali saat berhasil dan halaman sedang berpindah.
   - **Gagal senyap vs gagal jelas (TC-02 vs TC-03).** "Belum ada data" dan "Gagal memuat" dibedakan, bukan sama-sama tabel kosong. Nyambung ke kompetensi *graceful failure* dan *alert notification* di skill passport.
   - **Penyesuaian dari modul:** `colspan` mengikuti jumlah kolom nyata (4 atau 5, bukan 3), `escapeHTML` meng-escape kutip, dan `fetch` dibungkus `panggilApi()` sehingga pengecekan `response.ok` tidak ditulis ulang di tiap halaman.

**Kesimpulan (draft, silakan ubah):**
> Integrasi backend-frontend disempurnakan dengan indikator loading, pesan kegagalan yang jelas, serta penonaktifan tombol selama proses pengiriman menggunakan pola try-catch-finally sehingga pengguna tidak mengirim permintaan ganda. Escaping output dan penggunaan textContent memastikan data berisi tag HTML tampil sebagai teks biasa, sehingga risiko XSS pada sisi View berkurang. Mekanisme retry satu kali menangani gangguan jaringan sementara tanpa mengulang permintaan yang memang ditolak server. Seluruh perubahan berada di sisi View, sedangkan validasi dan otorisasi di sisi server tetap menjadi penentu.

**Daftar Pustaka (cek ulang dan isi tanggal akses):**
- MDN Web Docs, "Using the Fetch API", developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch
- MDN Web Docs, "Node.textContent", developer.mozilla.org/en-US/docs/Web/API/Node/textContent
- OWASP, "Cross Site Scripting Prevention Cheat Sheet", cheatsheetseries.owasp.org
- Bootstrap 5.3 Documentation, "Spinners", getbootstrap.com/docs/5.3/components/spinners/
- Modul Workshop BKPM, Acara 29, Politeknik Negeri Jember

**Daftar screenshot yang disarankan:** loading di tabel (TC-01), data kosong (TC-02), pesan gagal + toast retry (TC-03/04), tabel dengan nama ber-tag tampil sebagai teks (TC-05/06), tombol loading saat login/simpan/hapus (TC-07, 08, 12), form ubah saat memuat (TC-10), request ganda di Network saat retry (TC-04).

---

## 9. Catatan

- Perubahan Acara 29 diuji di browser (Chromium) dengan **API tiruan**: loading, pesan gagal, retry (gagal sekali lalu pulih, gagal terus, HTTP 500 tidak diulang), escaping nama dan pesan error ber-tag, tombol loading di login/tambah/ubah/hapus, serta `escapeHTML`. PHP dan MySQL **belum** dijalankan ulang di sini, jadi hasil di mesinmu (tabel bagian 6) yang jadi bukti akhir untuk laporan.
- File `ACARA27.md` tidak disertakan di folder ini; dokumentasi Acara 29 ada di file ini.
