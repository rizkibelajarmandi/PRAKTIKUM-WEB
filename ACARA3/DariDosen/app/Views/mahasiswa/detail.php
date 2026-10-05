<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1">

    <title>Detail Mahasiswa</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h3>Politeknik Negeri Jember</h3>
        <h1 class="mb-4">Detail Mahasiswa</h1>
        <?php if (!empty($data['mahasiswa'])) : ?>
            <?php $mahasiswa = $data['mahasiswa']; ?>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        Data Mahasiswa
                    </h5>
                    <table class="table">
                        <tr>
                            <th width="150">NIM</th>
                            <td>
                                <?= htmlspecialchars($mahasiswa['nim']) ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Nama</th>
                            <td>
                                <?= htmlspecialchars($mahasiswa['nama']) ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Program Studi</th>
                            <td>
                                <?= htmlspecialchars($mahasiswa['prodi']) ?>
                            </td>
                        </tr>
                    </table>
                    <a
                        href="?url=mahasiswa"
                        class="btn btn-secondary">
                        Kembali
                    </a>
                </div>
            </div>
        <?php else : ?>
            <div class="alert alert-danger">
                Data mahasiswa tidak ditemukan.
            </div>
            <a
                href="?url=mahasiswa"
                class="btn btn-secondary">
                Kembali
            </a>
        <?php endif; ?>
    </div>
</body>
</html>