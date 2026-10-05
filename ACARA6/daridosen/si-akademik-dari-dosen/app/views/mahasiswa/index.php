<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">POLITEKNIK NEGERI JEMBER</h1>
        <h1 class="text-center mb-4">Sistem Informasi Akademik</h1>

        <a href="<?= BASE_URL ?>/dashboard" class="btn btn-sm btn-outline-secondary mb-3">&laquo; Dashboard</a>

        <div style="border: 1px solid #ddd; padding: 20px; border-radius: 4px;">
            <h4>Daftar Mahasiswa</h4>

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Prodi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($mahasiswa ?? []) as $mhs): ?>
                    <tr>
                        <td><?= $mhs['nim']; ?></td>
                        <td><?= $mhs['nama']; ?></td>
                        <td><?= $mhs['prodi']; ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/mahasiswa/detail?nim=<?= $mhs['nim']; ?>" class="btn btn-sm btn-info">Detail</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Link ke halaman Dosen (sudah aktif) -->
            <a href="<?= BASE_URL ?>/dosen" class="btn btn-primary">Lihat Dosen</a>
        </div>
    </div>
</body>
</html>
