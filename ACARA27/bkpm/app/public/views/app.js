// Halaman publik: hanya menampilkan data (tanpa login)
const tbody = document.querySelector('#tabel-mahasiswa tbody');
const statusEl = document.getElementById('status');

function tampilkanStatus(teks, kelas = 'text-muted') {
    statusEl.textContent = teks;
    statusEl.className = `${kelas} fst-italic`;
}

function buatBaris(mhs) {
    const tr = document.createElement('tr');
    [mhs.nim, mhs.nama, mhs.prodi].forEach((nilai) => {
        const td = document.createElement('td');
        td.textContent = nilai;
        tr.appendChild(td);
    });
    const tdFoto = document.createElement('td');
    tdFoto.appendChild(buatFoto(mhs.foto, mhs.nama));
    tr.appendChild(tdFoto);
    return tr;
}

async function muatDataMahasiswa() {
    tbody.innerHTML = '';
    tampilkanStatus('Memuat data...', 'text-primary');
    try {
        const hasil = await panggilApi('/mahasiswa');
        if (hasil.data.length === 0) {
            tampilkanStatus('Belum ada data');
            return;
        }
        hasil.data.forEach((mhs) => tbody.appendChild(buatBaris(mhs)));
        tampilkanStatus(`${hasil.data.length} data berhasil dimuat`);
    } catch (error) {
        console.error('Gagal memuat data:', error);
        tampilkanStatus('Gagal memuat data: ' + error.message, 'text-danger');
    }
}

document.addEventListener('DOMContentLoaded', muatDataMahasiswa);
