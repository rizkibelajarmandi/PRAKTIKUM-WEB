<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Mahasiswa - Sistem Informasi Akademik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    
    <h1 class="text-center">POLITEKNIK NEGERI JEMBER</h1>
    <h1 class="text-center mb-4">Sistem Informasi Akademik</h1>
    
    <a href="<?= BASE_URL ?>/dashboard" class="btn btn-sm btn-outline-secondary mb-3">&laquo; Dashboard</a>

    <div style="border: 1px solid #ddd; padding: 20px; border-radius: 4px; background-color: white;">
        <h4 class="mb-3">Daftar Mahasiswa</h4>
        
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Prodi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mahasiswa ?? [] as $mhs): ?>
                <tr>
                    <td><?= htmlspecialchars($mhs['nim']); ?></td>
                    <td><?= htmlspecialchars($mhs['nama']); ?></td>
                    <td><?= htmlspecialchars($mhs['prodi'] ?? '-'); ?></td>
                    <td><?= htmlspecialchars($mhs['status'] ?? '-'); ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>/mahasiswa/detail?nim=<?= $mhs['nim']; ?>" class="btn btn-sm btn-info">Detail</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <a href="<?= BASE_URL ?>/dosen" class="btn btn-primary mt-3">Lihat Dosen</a>
    </div>
    
</div>
</body>
</html>