// ======================================================================
// ALAMAT API (BASE_URL)
// Normalnya TIDAK PERLU DIUBAH: alamat dideteksi otomatis dari alamat halaman,
//   .../public/views/xxx.html  ->  .../public
// Jadi kalau folder proyek dipindah atau di-rename, semua halaman ikut menyesuaikan.
//
// Isi BASE_URL_MANUAL HANYA jika halaman dibuka dari origin LAIN
// (misalnya Live Server di http://127.0.0.1:5500). Contoh:
//   const BASE_URL_MANUAL = 'http://localhost/ACARA29/bkpm/app/public';
// ======================================================================
const BASE_URL_MANUAL = '';
const BASE_URL = BASE_URL_MANUAL ||
    (window.location.origin + window.location.pathname.replace(/\/views\/[^/]*$/, ''));
