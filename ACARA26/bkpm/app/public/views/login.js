const BASE_URL = 'http://localhost/PRAKTIKUM-WEB/ACARA26/bkpm/app/public';

const form = document.getElementById('form-login');
const pesan = document.getElementById('pesan');
const tombol = form.querySelector('button');

// Sudah login? langsung ke dashboard
if (localStorage.getItem('token')) {
    window.location.href = 'dashboard.html';
}

form.addEventListener('submit', async (e) => {
    e.preventDefault();
    pesan.textContent = 'Memproses...';
    tombol.disabled = true;

    try {
        const res = await fetch(`${BASE_URL}/login`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                username: document.getElementById('username').value.trim(),
                password: document.getElementById('password').value,
            }),
        });
        const hasil = await res.json();

        if (!res.ok || !hasil.success) {
            throw new Error(hasil.message || `HTTP ${res.status}`);
        }

        localStorage.setItem('token', hasil.data.token);
        localStorage.setItem('role', hasil.data.role);
        window.location.href = 'dashboard.html';
    } catch (error) {
        pesan.textContent = error.message;   // mis. "Username atau password salah"
        tombol.disabled = false;
    }
});
