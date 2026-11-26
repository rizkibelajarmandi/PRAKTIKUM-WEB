const isAdmin = getRole() === 'admin';   // hanya untuk TAMPILAN; keamanan sebenarnya di Auth::requireAdmin()

const tbody = document.querySelector('#tabel-mahasiswa tbody');
const totalEl = document.getElementById('total');
// NIM, Nama, Program Studi, Foto (+ Aksi untuk admin)
const JUMLAH_KOLOM = isAdmin ? 5 : 4;

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
        hapus.addEventListener('click', () => hapusData(mhs.id, mhs.nama, hapus));

        tdAksi.append(ubah, hapus);
        tr.appendChild(tdAksi);
    }
    return tr;
}

// Langkah 1 modul (Acara 29): indikator loading + pesan gagal yang jelas.
// tampilLoading=false dipakai setelah Hapus supaya tabel tidak berkedip.
async function muatData(tampilLoading = true) {
    if (tampilLoading) {
        tampilkanBarisInfo(tbody, JUMLAH_KOLOM, 'Memuat data...', 'text-primary', true);
    }
    try {
        const hasil = await panggilApi('/mahasiswa');
        totalEl.textContent = hasil.data.length;
        if (hasil.data.length === 0) {
            tampilkanBarisInfo(tbody, JUMLAH_KOLOM, 'Belum ada data');
            return;
        }
        tbody.innerHTML = '';
        hasil.data.forEach((mhs) => tbody.appendChild(buatBaris(mhs)));
    } catch (error) {
        totalEl.textContent = '-';
        tampilkanBarisInfo(tbody, JUMLAH_KOLOM, 'Gagal memuat data: ' + error.message, 'text-danger');
    }
}

// Langkah 3 modul (Acara 29): try-catch-finally. Tombol Hapus dinonaktifkan selama request berjalan.
async function hapusData(id, nama, tombol) {
    if (!confirm(`Hapus data "${nama}"?`)) return;
    setTombolLoading(tombol, true, 'Menghapus...');
    try {
        await panggilApi(`/mahasiswa?id=${encodeURIComponent(id)}`, { method: 'DELETE' });
        toast('Data berhasil dihapus');
        await muatData(false);
    } catch (error) {
        toast(error.message, 'danger');   // 403 bukan admin, 404 sudah terhapus, jaringan putus, dst.
    } finally {
        setTombolLoading(tombol, false);
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
