<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Mahasiswa</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h3>Politeknik Negeri Jember</h3>
        <h1 class="mb-4">Data Mahasiswa</h1>
        <div class="mb-3">
            <a href="?url=dosen" class="btn btn-secondary">
                Dosen
            </a>
        </div>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama</th>
                    <th>Prodi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['mahasiswa'])) : ?>
                    <?php $no = 1; ?>
                    <?php foreach ($data['mahasiswa'] as $mahasiswa) : ?>
                        <tr>
                            <td>
                                <?= $no++ ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($mahasiswa['nim']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($mahasiswa['nama']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($mahasiswa['prodi']) ?>
                            </td>
                            <td>

                                <a
                                    href="?url=mahasiswa/detail&nim=<?= urlencode($mahasiswa['nim']) ?>"
                                    class="btn btn-info btn-sm">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5" class="text-center">
                            Data mahasiswa tidak tersedia.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>