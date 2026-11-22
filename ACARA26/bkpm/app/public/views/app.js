const BASE_URL = 'http://localhost/PRAKTIKUM-WEB/ACARA26/bkpm/app/public';

const tbody = document.querySelector('#tabel-mahasiswa tbody');
const statusEl = document.getElementById('status');

function tampilkanStatus(teks, kelas = '') {
    statusEl.textContent = teks;
    statusEl.className = kelas;
}

// Membuat baris tabel dengan textContent (bukan innerHTML),
// sehingga data dari API tidak dieksekusi sebagai HTML/script (aman dari XSS).
function buatBaris(mhs) {
    const tr = document.createElement('tr');
    [mhs.nim, mhs.nama, mhs.prodi].forEach((nilai) => {
        const td = document.createElement('td');
        td.textContent = nilai;
        tr.appendChild(td);
    });

    // Acara 26: kolom foto (thumbnail dari folder uploads/)
    const tdFoto = document.createElement('td');
    if (mhs.foto) {
        const img = document.createElement('img');
        img.className = 'thumb';
        img.src = `${BASE_URL}/uploads/${encodeURIComponent(mhs.foto)}`;
        img.alt = 'Foto ' + mhs.nama;
        tdFoto.appendChild(img);
    } else {
        tdFoto.textContent = '-';
    }
    tr.appendChild(tdFoto);
    return tr;
}

async function muatDataMahasiswa() {
    tbody.innerHTML = '';
    tampilkanStatus('Memuat data...', 'loading');   // Tugas mandiri 1

    try {
        const response = await fetch(`${BASE_URL}/mahasiswa`);
        const hasil = await response.json();

        // fetch() TIDAK error pada status 4xx/5xx, jadi cek manual
        if (!response.ok || !hasil.success) {
            throw new Error(hasil.message || `HTTP ${response.status}`);
        }

        if (hasil.data.length === 0) {              // Tugas mandiri 2
            tampilkanStatus('Belum ada data', 'empty');
            return;
        }

        hasil.data.forEach((mhs) => tbody.appendChild(buatBaris(mhs)));
        tampilkanStatus(`${hasil.data.length} data berhasil dimuat`);
    } catch (error) {
        console.error('Gagal memuat data:', error);
        tampilkanStatus('Gagal memuat data: ' + error.message, 'error');
    }
}

document.addEventListener('DOMContentLoaded', muatDataMahasiswa);
