<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Dosen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">POLITEKNIK NEGERI JEMBER</h1>
        <h1 class="text-center mb-4">Sistem Informasi Akademik</h1>

        <a href="<?= BASE_URL ?>/dashboard" class="btn btn-sm btn-outline-secondary mb-3">Dashboard</a>

        <div style="border: 1px solid #ddd; padding: 20px; border-radius: 4px;">
            <h4>Daftar Dosen</h4>

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Prodi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($dosen ?? []) as $d): ?>
                    <tr>
                        <td><?= $d['nip']; ?></td>
                        <td><?= $d['nama']; ?></td>
                        <td><?= $d['prodi']; ?></td>
                        <td>
                            <a href="<?= BASE_URL ?>/dosen/detail?nip=<?= $d['nip']; ?>" class="btn btn-sm btn-info">Detail</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <a href="<?= BASE_URL ?>/dashboard" class="btn btn-danger">Kembali</a>
        </div>
    </div>
</body>
</html>
