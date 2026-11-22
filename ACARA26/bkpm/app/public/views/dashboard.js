const BASE_URL = 'http://localhost/PRAKTIKUM-WEB/ACARA26/bkpm/app/public';

const token = localStorage.getItem('token');
const role = localStorage.getItem('role');
const isAdmin = role === 'admin';   // hanya untuk TAMPILAN; keamanan sebenarnya di Auth::requireAdmin()

const tbody = document.querySelector('#tabel-mahasiswa tbody');
const statusEl = document.getElementById('status');
const totalEl = document.getElementById('total');
const panelAdmin = document.getElementById('panel-admin');
const kolomAksi = document.getElementById('kolom-aksi');
const form = document.getElementById('form-mahasiswa');
const judulForm = document.getElementById('judul-form');
const btnBatal = document.getElementById('btn-batal');
const inputNim = document.getElementById('nim');
const inputNama = document.getElementById('nama');
const inputProdi = document.getElementById('prodi');
const inputFoto = document.getElementById('foto');
const petunjukFoto = document.getElementById('petunjuk-foto');
const MAKS_UKURAN = 2 * 1024 * 1024; // 2 MB (server tetap memvalidasi ulang)

let dataMahasiswa = [];

function keLogin() {
    localStorage.removeItem('token');
    localStorage.removeItem('role');
    window.location.href = 'login.html';
}

function tampilkanStatus(teks, kelas = '') {
    statusEl.textContent = teks;
    statusEl.className = kelas;
}

// Pembungkus fetch: otomatis menyertakan token dan menangani error umum
async function panggilApi(path, opsi = {}) {
    const headers = {};
    // Jangan set Content-Type manual untuk FormData: browser menambahkan boundary sendiri
    if (typeof opsi.body === 'string') headers['Content-Type'] = 'application/json';
    if (token) headers['Authorization'] = `Bearer ${token}`;

    const response = await fetch(`${BASE_URL}${path}`, { ...opsi, headers });
    const hasil = await response.json();

    // 401 = token tidak ada / salah / kedaluwarsa -> paksa login ulang
    if (response.status === 401) {
        alert('Sesi berakhir, silakan login kembali.');
        keLogin();
        throw new Error(hasil.message || 'Tidak terautentikasi');
    }
    // 403 = login valid tapi bukan admin; pesan dari server ditampilkan apa adanya
    if (!response.ok || !hasil.success) {
        throw new Error(hasil.message || `HTTP ${response.status}`);
    }
    return hasil;
}

function buatSel(teks) {
    const td = document.createElement('td');
    td.textContent = teks;   // textContent: aman dari XSS
    return td;
}

function buatSelFoto(mhs) {
    const td = document.createElement('td');
    if (mhs.foto) {
        const img = document.createElement('img');
        img.className = 'thumb';
        img.src = `${BASE_URL}/uploads/${encodeURIComponent(mhs.foto)}`;
        img.alt = 'Foto ' + mhs.nama;   // property, bukan innerHTML: aman dari XSS
        td.appendChild(img);
    } else {
        td.textContent = '-';
    }
    return td;
}

function buatTombol(label, handler) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.textContent = label;
    btn.addEventListener('click', handler);
    return btn;
}

function buatBaris(mhs) {
    const tr = document.createElement('tr');
    tr.appendChild(buatSel(mhs.nim));
    tr.appendChild(buatSel(mhs.nama));
    tr.appendChild(buatSel(mhs.prodi));
    tr.appendChild(buatSelFoto(mhs));

    if (isAdmin) {
        const tdAksi = document.createElement('td');
        tdAksi.appendChild(buatTombol('Ubah', () => mulaiUbah(mhs.id)));
        tdAksi.append(' ');
        tdAksi.appendChild(buatTombol('Hapus', () => hapusData(mhs.id)));
        tr.appendChild(tdAksi);
    }
    return tr;
}

function tampilkanTabel() {
    totalEl.textContent = dataMahasiswa.length;
    tbody.innerHTML = '';
    dataMahasiswa.forEach((mhs) => tbody.appendChild(buatBaris(mhs)));
}

async function muatData(pesanSukses = '') {
    tampilkanStatus('Memuat data...', 'loading');
    try {
        const hasil = await panggilApi('/mahasiswa');
        dataMahasiswa = hasil.data;
        tampilkanTabel();

        if (dataMahasiswa.length === 0) {
            tampilkanStatus('Belum ada data', '');
        } else {
            tampilkanStatus(pesanSukses, pesanSukses ? 'ok' : '');
        }
    } catch (error) {
        tampilkanStatus('Gagal memuat data: ' + error.message, 'error');
    }
}

function resetForm() {
    form.reset();
    delete form.dataset.editId;
    judulForm.textContent = 'Tambah Mahasiswa';
    petunjukFoto.textContent = '';
    btnBatal.hidden = true;
}

function mulaiUbah(id) {
    const mhs = dataMahasiswa.find((m) => String(m.id) === String(id));
    if (!mhs) return;
    inputNim.value = mhs.nim;
    inputNama.value = mhs.nama;
    inputProdi.value = mhs.prodi;
    form.dataset.editId = mhs.id;
    judulForm.textContent = 'Ubah Mahasiswa';
    petunjukFoto.textContent = 'Kosongkan jika tidak ingin mengganti foto.';
    btnBatal.hidden = false;
    panelAdmin.scrollIntoView({ behavior: 'smooth' });
}

async function hapusData(id) {
    if (!confirm('Yakin ingin menghapus data ini?')) return;
    try {
        await panggilApi(`/mahasiswa?id=${id}`, { method: 'DELETE' });
        await muatData('Data berhasil dihapus');
    } catch (error) {
        tampilkanStatus(error.message, 'error');   // mis. 403 jika bukan admin
    }
}

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const berkas = inputFoto.files[0];
    if (berkas && berkas.size > MAKS_UKURAN) {   // pra-cek di browser; server tetap menolak jika dilanggar
        tampilkanStatus('Ukuran foto maksimal 2MB', 'error');
        return;
    }

    // Acara 26: berkas dikirim sebagai multipart/form-data, bukan JSON
    const formData = new FormData();
    formData.append('nim', inputNim.value.trim());
    formData.append('nama', inputNama.value.trim());
    formData.append('prodi', inputProdi.value.trim());
    if (berkas) formData.append('foto', berkas);

    const editId = form.dataset.editId;
    try {
        if (editId) {
            // PHP tidak mengisi $_POST/$_FILES untuk PUT -> kirim POST dengan ?_method=PUT (dibaca Router)
            await panggilApi(`/mahasiswa?id=${editId}&_method=PUT`, { method: 'POST', body: formData });
        } else {
            await panggilApi('/mahasiswa', { method: 'POST', body: formData });
        }
        const pesan = editId ? 'Data berhasil diperbarui' : 'Data berhasil ditambahkan';
        resetForm();
        await muatData(pesan);
    } catch (error) {
        tampilkanStatus(error.message, 'error');   // 400 / 403 / 413 / 422 dari server
    }
});

btnBatal.addEventListener('click', resetForm);
document.getElementById('btn-logout').addEventListener('click', keLogin);

// ---- Inisialisasi ----
if (!token) {
    keLogin();
} else {
    document.getElementById('info-role').textContent = `Login sebagai: ${role}`;
    // Tugas mandiri 2: tombol/form aksi hanya ditampilkan untuk admin
    if (isAdmin) {
        panelAdmin.hidden = false;
        kolomAksi.hidden = false;
    }
    muatData();
}
