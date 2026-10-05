<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Mahasiswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">POLITEKNIK NEGERI JEMBER</h1>
        <h1 class="text-center mb-4">Sistem Informasi Akademik</h1>

        <div class="mx-auto" style="max-width: 500px; border: 1px solid #ddd; padding: 20px; border-radius: 4px;">
            <h4>Detail Mahasiswa</h4>

            <?php $mahasiswa = $mahasiswa ?? []; ?>

            <div class="card">
                <div class="card-body">
                    <p><strong>NIM:</strong> <?= $mahasiswa['nim'] ?? ''; ?></p>
                    <p><strong>Nama:</strong> <?= $mahasiswa['nama'] ?? ''; ?></p>
                    <p><strong>Program Studi:</strong> <?= $mahasiswa['prodi'] ?? ''; ?></p>
                </div>
            </div>

            <a href="<?= BASE_URL ?>/mahasiswa" class="btn btn-danger mt-3">Kembali</a>
        </div>
    </div>
</body>
</html>