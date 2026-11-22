const isAdmin = getRole() === 'admin';   // hanya untuk TAMPILAN; keamanan sebenarnya di Auth::requireAdmin()

const tbody = document.querySelector('#tabel-mahasiswa tbody');
const statusEl = document.getElementById('status');
const totalEl = document.getElementById('total');

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

    if (isAdmin) {
        const tdAksi = document.createElement('td');
        tdAksi.className = 'text-nowrap';

        const ubah = document.createElement('a');
        ubah.className = 'btn btn-sm btn-warning me-1';
        ubah.href = `form_edit.html?id=${encodeURIComponent(mhs.id)}`;
        ubah.textContent = 'Ubah';

        const hapus = document.createElement('button');
        hapus.type = 'button';
        hapus.className = 'btn btn-sm btn-danger';
        hapus.textContent = 'Hapus';
        hapus.addEventListener('click', () => hapusData(mhs.id, mhs.nama));

        tdAksi.append(ubah, hapus);
        tr.appendChild(tdAksi);
    }
    return tr;
}

async function muatData() {
    tampilkanStatus('Memuat data...', 'text-primary');
    try {
        const hasil = await panggilApi('/mahasiswa');
        totalEl.textContent = hasil.data.length;
        tbody.innerHTML = '';
        hasil.data.forEach((mhs) => tbody.appendChild(buatBaris(mhs)));
        tampilkanStatus(hasil.data.length === 0 ? 'Belum ada data' : '');
    } catch (error) {
        tampilkanStatus('Gagal memuat data: ' + error.message, 'text-danger');
    }
}

// Langkah 3 modul: tombol Hapus (kini dengan penanganan hasil + notifikasi)
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

document.getElementById('btn-logout').addEventListener('click', keLogin);

// ---- Inisialisasi ----
if (wajibLogin()) {
    document.getElementById('info-role').textContent = `Login sebagai: ${getRole()}`;
    if (isAdmin) {
        document.getElementById('btn-tambah').hidden = false;
        document.getElementById('kolom-aksi').hidden = false;
    }
    tampilkanFlash();   // notifikasi dari halaman form (mis. "Data berhasil ditambahkan")
    muatData();
}
