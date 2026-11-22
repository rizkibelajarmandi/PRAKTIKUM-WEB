// Fungsi bersama untuk semua halaman. Muat SETELAH config.js.

const MAKS_FOTO = 2 * 1024 * 1024; // 2 MB (server tetap memvalidasi ulang)

function getToken() { return localStorage.getItem('token'); }
function getRole() { return localStorage.getItem('role'); }

// Semua perpindahan halaman lewat satu fungsi ini
function pindah(url) { window.location.href = url; }

function keLogin() {
    localStorage.removeItem('token');
    localStorage.removeItem('role');
    pindah('login.html');
}

// ---------- Notifikasi visual (tugas mandiri 1) ----------
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

// Pesan yang harus tetap tampil setelah pindah halaman (mis. "Data berhasil ditambahkan")
function setFlash(teks, tipe = 'success') {
    sessionStorage.setItem('flash', JSON.stringify({ teks, tipe }));
}
function tampilkanFlash() {
    try {
        const f = JSON.parse(sessionStorage.getItem('flash'));
        if (f) toast(f.teks, f.tipe);
    } catch (e) { /* abaikan */ }
    sessionStorage.removeItem('flash');
}

// ---------- Penjaga halaman ----------
function wajibLogin() {
    if (!getToken()) { keLogin(); return false; }
    return true;
}
function wajibAdmin() {
    if (!wajibLogin()) return false;
    if (getRole() !== 'admin') {
        setFlash('Halaman ini hanya untuk admin', 'danger');
        pindah('dashboard.html');
        return false;
    }
    return true;
}

// ---------- Pembungkus fetch: token otomatis + penanganan error umum ----------
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

// ---------- Validasi sisi View (tugas mandiri 2) ----------
// Lapisan tambahan; server (Controller/Model) tetap yang menentukan.
// Batas panjang mengikuti kolom tabel: nim 15, nama 100, prodi 50.
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

function tampilkanErrorForm(form, errors) {
    ['nim', 'nama', 'prodi', 'foto'].forEach((id) => {
        const input = form.querySelector('#' + id);
        const pesan = form.querySelector('#err-' + id);
        input.classList.toggle('is-invalid', Boolean(errors[id]));
        pesan.textContent = errors[id] || '';
    });
}

// ---------- Elemen foto (thumbnail dari folder uploads/) ----------
function buatFoto(foto, nama, ukuran = 48) {
    if (!foto) {
        const strip = document.createElement('span');
        strip.className = 'text-muted';
        strip.textContent = '-';
        return strip;
    }
    const img = document.createElement('img');
    img.src = `${BASE_URL}/uploads/${encodeURIComponent(foto)}`;
    img.alt = 'Foto ' + nama;          // property, bukan innerHTML: aman dari XSS
    img.width = ukuran;
    img.height = ukuran;
    img.className = 'rounded border object-fit-cover';
    return img;
}
