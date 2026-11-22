// Langkah 2 modul: View edit data. id diambil dari alamat halaman: form_edit.html?id=3
async function mulai() {
    const id = new URLSearchParams(window.location.search).get('id');
    if (!/^\d+$/.test(id || '')) {
        setFlash('ID mahasiswa tidak valid', 'danger');
        pindah('dashboard.html');
        return;
    }

    const form = document.getElementById('form-mahasiswa');
    const inputNim = document.getElementById('nim');
    const inputNama = document.getElementById('nama');
    const inputProdi = document.getElementById('prodi');
    const inputFoto = document.getElementById('foto');
    const tombol = document.getElementById('btn-simpan');

    // Muat data lama (GET ?id= dilayani MahasiswaController::index())
    try {
        const hasil = await panggilApi(`/mahasiswa?id=${id}`);
        inputNim.value = hasil.data.nim;
        inputNama.value = hasil.data.nama;
        inputProdi.value = hasil.data.prodi;
        if (hasil.data.foto) {
            document.getElementById('foto-sekarang').appendChild(buatFoto(hasil.data.foto, hasil.data.nama, 80));
        }
    } catch (error) {
        setFlash(error.message, 'danger');   // mis. "Data tidak ditemukan"
        pindah('dashboard.html');
        return;
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const data = {
            nim: inputNim.value.trim(),
            nama: inputNama.value.trim(),
            prodi: inputProdi.value.trim(),
            berkas: inputFoto.files[0],
        };
        const errors = validasiMahasiswa(data);
        tampilkanErrorForm(form, errors);
        if (Object.keys(errors).length > 0) {
            toast('Periksa kembali isian formulir', 'danger');
            return;
        }

        const formData = new FormData();
        formData.append('nim', data.nim);
        formData.append('nama', data.nama);
        formData.append('prodi', data.prodi);
        if (data.berkas) formData.append('foto', data.berkas);

        tombol.disabled = true;
        try {
            // PENTING: bukan method 'PUT'. PHP tidak mengisi $_POST/$_FILES untuk PUT, jadi body
            // FormData tidak terbaca (server membalas 413). Kirim POST + ?_method=PUT; Router
            // meneruskannya ke MahasiswaController::update($id).
            await panggilApi(`/mahasiswa?id=${id}&_method=PUT`, { method: 'POST', body: formData });
            setFlash('Data berhasil diperbarui');
            pindah('dashboard.html');
        } catch (error) {
            toast(error.message, 'danger');
            tombol.disabled = false;
        }
    });
}

if (wajibAdmin()) mulai();
