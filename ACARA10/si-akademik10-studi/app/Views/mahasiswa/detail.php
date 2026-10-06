<?php

/** @var Mahasiswa $mahasiswa */
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h1 class="text-center">POLITEKNIK NEGERI JEMBER</h1>
    <h2 class="text-center mb-4">Sistem Informasi Akademik</h2>

    <div style="border: 1px solid #ddd; padding: 20px; border-radius: 4px; background: white;">
        <h3>Detail Mahasiswa</h3>
        <hr>
        <p><strong>NIM :</strong> <?= htmlspecialchars($mahasiswa->getNim()) ?></p>
        <p><strong>Nama :</strong> <?= htmlspecialchars($mahasiswa->getNama()) ?></p>
        <p><strong>Jurusan :</strong> <?= htmlspecialchars($mahasiswa->getJurusan()) ?></p>
        <p><strong>Prodi ID :</strong> <?= htmlspecialchars($mahasiswa->getProdiId()) ?></p>
        <p><strong>Angkatan :</strong> <?= htmlspecialchars($mahasiswa->getAngkatan()) ?></p>

        <a href="<?= BASE_URL ?>/mahasiswa" class="btn btn-secondary" style="background-color: red; border-color: red;">Kembali</a>
    </div>
</div>
</body>
</html>