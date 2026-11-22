const BASE_URL = 'http://localhost/PRAKTIKUM-WEB/ACARA25/bkpm/app/public';

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
    if (opsi.body) headers['Content-Type'] = 'application/json';
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
    const payload = {
        nim: inputNim.value.trim(),
        nama: inputNama.value.trim(),
        prodi: inputProdi.value.trim(),
    };
    const editId = form.dataset.editId;

    try {
        if (editId) {
            await panggilApi(`/mahasiswa?id=${editId}`, { method: 'PUT', body: JSON.stringify(payload) });
        } else {
            await panggilApi('/mahasiswa', { method: 'POST', body: JSON.stringify(payload) });
        }
        const pesan = editId ? 'Data berhasil diperbarui' : 'Data berhasil ditambahkan';
        resetForm();
        await muatData(pesan);
    } catch (error) {
        tampilkanStatus(error.message, 'error');   // 400 / 403 / 422 dari server
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
