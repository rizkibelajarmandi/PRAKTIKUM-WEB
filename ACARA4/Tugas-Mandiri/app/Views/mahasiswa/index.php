<?php

?>

<h4 class="mb-3">Daftar Mahasiswa</h4>

<table class="table table-bordered table-striped align-middle">
    <thead class="table-dark">
        <tr>
            <th>No</th>
            <th>NIM</th>
            <th>Nama</th>
            <th>Prodi</th>
            <th>Angkatan</th> 
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1;
        if (isset($daftarMahasiswa) && is_array($daftarMahasiswa) && count($daftarMahasiswa) > 0):
            foreach ($daftarMahasiswa as $mhs):
        ?>
        <tr>
            <td><?= $no++; ?></td>
            <td><?= $mhs->getNim(); ?></td>
            <td><?= $mhs->getNama(); ?></td>
            <td><?= $mhs->getProdi(); ?></td>
            <td><?= $mhs->getAngkatan(); ?></td>
        </tr>
        <?php
            endforeach;
        else:
        ?>
        <tr>
            <td colspan="5" class="text-center">Belum ada data mahasiswa.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>
