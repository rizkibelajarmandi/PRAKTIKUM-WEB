// Halaman publik: hanya menampilkan data (tanpa login)
const tbody = document.querySelector('#tabel-mahasiswa tbody');
const statusEl = document.getElementById('status');
const JUMLAH_KOLOM = 4;   // NIM, Nama, Program Studi, Foto

function tampilkanStatus(teks, kelas = 'text-muted') {
    statusEl.textContent = teks;
    statusEl.className = `${kelas} fst-italic`;
}

// Data dari server diisi lewat textContent (bukan innerHTML), jadi tag HTML tampil sebagai teks biasa.
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

// Langkah 1 modul (Acara 29): indikator loading + pesan gagal yang jelas
async function muatDataMahasiswa() {
    tampilkanStatus('');
    tampilkanBarisInfo(tbody, JUMLAH_KOLOM, 'Memuat data...', 'text-primary', true);
    try {
        const hasil = await panggilApi('/mahasiswa');
        if (hasil.data.length === 0) {
            tampilkanBarisInfo(tbody, JUMLAH_KOLOM, 'Belum ada data');
            return;
        }
        tbody.innerHTML = '';
        hasil.data.forEach((mhs) => tbody.appendChild(buatBaris(mhs)));
        tampilkanStatus(`${hasil.data.length} data berhasil dimuat`);
    } catch (error) {
        console.error('Gagal memuat data:', error);
        tampilkanBarisInfo(tbody, JUMLAH_KOLOM, 'Gagal memuat data: ' + error.message, 'text-danger');
    }
}

document.addEventListener('DOMContentLoaded', muatDataMahasiswa);
