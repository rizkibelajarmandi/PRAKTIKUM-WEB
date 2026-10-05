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
        </tr>
    </thead>
    <tbody>
        <?php
        $no = 1; 

        foreach (($daftarMahasiswa ?? []) as $mhs):
        ?>
        <tr>
            <td><?= $no++; ?></td>
            
            <td><?= $mhs->getNim(); ?></td>

            <td><?= $mhs->getNama(); ?></td>
            <td><?= $mhs->getProdi(); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
