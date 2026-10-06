<?php

/** @var Mahasiswa[] $mahasiswa */

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h1 class="text-center">POLITEKNIK NEGERI JEMBER</h1>
    <h2 class="text-center mb-4">Sistem Informasi Akademik</h2>

    <div style="border: 1px solid #ddd; padding: 20px; border-radius: 4px; background: white;">
        <h3>Daftar Mahasiswa</h3>
        <a href="<?= BASE_URL ?>/mahasiswa/create" class="btn btn-primary mb-3">Tambah Mahasiswa</a>

        <table class="table table-bordered table-striped table-hover">
            <thead>
            <tr>
                <th style="background-color: #000000; color: white; text-align: center;">NIM</th>
                <th style="background-color: #000000; color: white; text-align: center;">Nama</th>
                <th style="background-color: #000000; color: white; text-align: center;">Jurusan</th>
                <th style="background-color: #000000; color: white; text-align: center;">Prodi ID</th>
                <th style="background-color: #000000; color: white; text-align: center;">Angkatan</th>
                <th style="background-color: #000000; color: white; text-align: center;">Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($mahasiswa as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item->getNim()) ?></td>
                    <td><?= htmlspecialchars($item->getNama()) ?></td>
                    <td><?= htmlspecialchars($item->getJurusan()) ?></td>
                    <td><?= htmlspecialchars($item->getProdiId()) ?></td>
                    <td><?= htmlspecialchars($item->getAngkatan()) ?></td>
                    <td style="text-align: center;">
                        <a href="<?= BASE_URL ?>/mahasiswa/detail?nim=<?= $item->getNim() ?>" class="btn btn-info btn-sm">Detail</a>
                        <a href="<?= BASE_URL ?>/mahasiswa/edit?nim=<?= $item->getNim() ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="<?= BASE_URL ?>/mahasiswa/delete?nim=<?= $item->getNim() ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <a href="<?= BASE_URL ?>/dashboard" class="btn btn-secondary" style="background-color: red; border-color: red;">Kembali</a>
    </div>
</div>
</body>
</html>