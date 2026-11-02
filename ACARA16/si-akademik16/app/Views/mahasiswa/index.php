<?php
/** @var Mahasiswa[] $mahasiswa */
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h1 class="text-center">POLITEKNIK NEGERI JEMBER</h1>
    <h2 class="text-center mb-4">Sistem Informasi Akademik</h2>

    <div style="border: 1px solid #ddd; padding: 20px; border-radius: 4px; background: white;">
        <h3>Daftar Mahasiswa</h3>

        <?php if ($flash): ?>
            <div class="alert alert-<?= $flash['type'] === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show">
                <?= htmlspecialchars($flash['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <a href="<?= BASE_URL ?>/mahasiswa/create" class="btn btn-primary mb-3">Tambah Mahasiswa</a>

        <table class="table table-bordered table-striped table-hover">
            <thead>
                <tr>
                    <th style="background:#000;color:#fff;text-align:center;">NIM</th>
                    <th style="background:#000;color:#fff;text-align:center;">Nama</th>
                    <th style="background:#000;color:#fff;text-align:center;">Email</th>
                    <th style="background:#000;color:#fff;text-align:center;">Jurusan</th>
                    <th style="background:#000;color:#fff;text-align:center;">Prodi ID</th>
                    <th style="background:#000;color:#fff;text-align:center;">Angkatan</th>
                    <th style="background:#000;color:#fff;text-align:center;">Status</th>
                    <th style="background:#000;color:#fff;text-align:center;">Aksi</th>
                </tr>
            </thead>
                        <tbody>
                <?php foreach ($mahasiswa as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item->getNim() ?? '-') ?></td>
                    <td><?= htmlspecialchars($item->getNama() ?? '-') ?></td>
                    <td><?= htmlspecialchars($item->getEmail() ?? '-') ?></td>
                    <td><?= htmlspecialchars($item->getJurusan() ?? '-') ?></td>
                    <td><?= htmlspecialchars($item->getProdiId() ?? '-') ?></td>
                    <td><?= htmlspecialchars($item->getAngkatan() ?? '-') ?></td>
                    <td><?= htmlspecialchars($item->getStatus() ?? '-') ?></td>
                    <td style="text-align:center;">
                        <div class="d-flex gap-1 justify-content-center">
                            <a href="<?= BASE_URL ?>/mahasiswa/detail?nim=<?= $item->getNim() ?>" class="btn btn-info btn-sm">Detail</a>
                            <a href="<?= BASE_URL ?>/mahasiswa/edit?nim=<?= $item->getNim() ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="<?= BASE_URL ?>/mahasiswa/delete?nim=<?= $item->getNim() ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin hapus data ini?')">Hapus</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <a href="<?= BASE_URL ?>/dashboard" class="btn btn-secondary" style="background:red;border-color:red;">Kembali</a>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>