<?php
/** @var Mahasiswa $mahasiswa */
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h1 class="text-center">POLITEKNIK NEGERI JEMBER</h1>
    <h2 class="text-center mb-4">Sistem Informasi Akademik</h2>

    <div style="border: 1px solid #ddd; padding: 20px; border-radius: 4px; background: white;">
        <h3>Edit Mahasiswa</h3>
        <form method="post" action="<?= BASE_URL ?>/mahasiswa/update">
            <input type="hidden" name="nim" value="<?= htmlspecialchars($mahasiswa->getNim()) ?>">
            <div class="mb-3">
                <label class="form-label">NIM</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($mahasiswa->getNim()) ?>" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($mahasiswa->getNama()) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Jurusan</label>
                <input type="text" name="jurusan" class="form-control" value="<?= htmlspecialchars($mahasiswa->getJurusan()) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Prodi ID</label>
                <input type="number" name="prodi_id" class="form-control" value="<?= htmlspecialchars($mahasiswa->getProdiId()) ?>" min="1" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Angkatan</label>
                <input type="number" name="angkatan" class="form-control" value="<?= htmlspecialchars($mahasiswa->getAngkatan()) ?>" min="2020" max="2100" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="<?= BASE_URL ?>/mahasiswa" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
</body>
</html>
