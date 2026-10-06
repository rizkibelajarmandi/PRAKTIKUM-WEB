<?php 
 /** @var array $dosen */
 
 //untuk ngasih tau vs code bahwa variabel $dosen adalah sebuah array yang akan dikirim dari tempat lain
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Dosen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h1 class="text-center">POLITEKNIK NEGERI JEMBER</h1>
    <h2 class="text-center mb-4">Sistem Informasi Akademik</h2>

    <div style="border: 1px solid #ddd; padding: 20px; border-radius: 4px; background: white;">
        <h3>Edit Dosen</h3>
        <form method="post" action="<?= BASE_URL ?>/dosen/update">
            <input type="hidden" name="id" value="<?= $dosen['id'] ?>">
            <div class="mb-3">
                <label class="form-label">NIDN</label>
                <input type="text" name="nidn" class="form-control" value="<?= htmlspecialchars($dosen['nidn']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Nama</label>
                <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($dosen['nama']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Bidang Keahlian</label>
                <input type="text" name="bidang_keahlian" class="form-control" value="<?= htmlspecialchars($dosen['bidang_keahlian']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="<?= BASE_URL ?>/dosen" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
</body>
</html>