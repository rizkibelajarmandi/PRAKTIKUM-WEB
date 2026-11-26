// Sudah login? langsung ke dashboard
if (getToken()) {
    pindah('dashboard.html');
}

tampilkanFlash();   // mis. "Sesi berakhir, silakan login kembali"

const form = document.getElementById('form-login');
const inputUsername = document.getElementById('username');
const inputPassword = document.getElementById('password');
const tombol = document.getElementById('btn-login');

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const username = inputUsername.value.trim();
    const password = inputPassword.value;

    // Validasi sisi View
    inputUsername.classList.toggle('is-invalid', !username);
    document.getElementById('err-username').textContent = username ? '' : 'Username wajib diisi';
    inputPassword.classList.toggle('is-invalid', !password);
    document.getElementById('err-password').textContent = password ? '' : 'Password wajib diisi';
    if (!username || !password) return;

    let berhasil = false;
    setTombolLoading(tombol, true, 'Memproses...');
    try {
        // Login tidak memakai panggilApi(): 401 di sini artinya "password salah", bukan "sesi berakhir"
        const res = await fetchDenganRetry(`${BASE_URL}/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ username, password }),
        });
        const hasil = await res.json();
        if (!res.ok || !hasil.success) {
            throw new Error(hasil.message || `HTTP ${res.status}`);
        }
        localStorage.setItem('token', hasil.data.token);
        localStorage.setItem('role', hasil.data.role);
        berhasil = true;
        pindah('dashboard.html');
    } catch (error) {
        let pesan = error.message;
        if (error instanceof TypeError) pesan = 'Tidak dapat terhubung ke server (sudah dicoba ulang 1 kali)';
        else if (error instanceof SyntaxError) pesan = 'Server tidak membalas JSON (cek alamat API / file .htaccess)';
        toast(pesan, 'danger');
    } finally {
        // Kalau berhasil, halaman sedang berpindah: tombol dibiarkan nonaktif agar tidak terklik dua kali.
        if (!berhasil) setTombolLoading(tombol, false);
    }
});
