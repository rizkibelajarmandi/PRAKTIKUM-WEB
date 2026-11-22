// Langkah 1 modul: View tambah data (FormData + header Authorization)
async function mulai() {
    const form = document.getElementById('form-mahasiswa');
    const inputFoto = document.getElementById('foto');
    const tombol = document.getElementById('btn-simpan');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const data = {
            nim: document.getElementById('nim').value.trim(),
            nama: document.getElementById('nama').value.trim(),
            prodi: document.getElementById('prodi').value.trim(),
            berkas: inputFoto.files[0],
        };

        // Validasi sisi View (tugas mandiri 2): berhenti di sini jika ada yang salah, tanpa request ke server
        const errors = validasiMahasiswa(data);
        tampilkanErrorForm(form, errors);
        if (Object.keys(errors).length > 0) {
            toast('Periksa kembali isian formulir', 'danger');
            return;
        }

        // Berkas dikirim sebagai multipart/form-data (bukan JSON)
        const formData = new FormData();
        formData.append('nim', data.nim);
        formData.append('nama', data.nama);
        formData.append('prodi', data.prodi);
        if (data.berkas) formData.append('foto', data.berkas);

        tombol.disabled = true;   // cegah klik ganda
        try {
            await panggilApi('/mahasiswa', { method: 'POST', body: formData });
            setFlash('Data berhasil ditambahkan');   // tampil di dashboard setelah pindah halaman
            pindah('dashboard.html');
        } catch (error) {
            toast(error.message, 'danger');          // 403 / 413 / 422 dari server
            tombol.disabled = false;
        }
    });
}

if (wajibAdmin()) mulai();