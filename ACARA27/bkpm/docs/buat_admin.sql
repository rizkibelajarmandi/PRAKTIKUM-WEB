-- Acara 25: register selalu membuat role 'user'.
-- Jalankan di phpMyAdmin > db_workshop > tab SQL untuk menjadikan satu akun sebagai admin.
UPDATE users SET role = 'admin' WHERE username = 'GANTI_USERNAME';

-- Cek hasilnya
SELECT id, username, email, role FROM users;
