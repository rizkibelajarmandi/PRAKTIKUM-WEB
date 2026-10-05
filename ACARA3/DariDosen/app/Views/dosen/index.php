<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">
    <title>Data Dosen</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h3>Politeknik Negeri Jember</h3>
        <h1 class="mb-4">Data Dosen</h1>
        <div class="mb-3">
            <a
                href="?url=mahasiswa"
                class="btn btn-secondary">
                Mahasiswa
            </a>
        </div>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>NIDN</th>
                    <th>Nama</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['dosen'])) : ?>
                    <?php $no = 1; ?>
                    <?php foreach ($data['dosen'] as $dosen) : ?>
                        <tr>
                            <td>
                                <?= $no++ ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($dosen['nidn']) ?>
                            </td>
                            <td>
                                <?= htmlspecialchars($dosen['nama']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td
                            colspan="3"
                            class="text-center">
                            Data dosen tidak tersedia.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>