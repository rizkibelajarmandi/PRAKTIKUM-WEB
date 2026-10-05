<?php

class Mahasiswa
{
    private $data = [
        [
            'nim' => '23001',
            'nama' => 'Budi',
            'prodi' => 'Teknik Informatika'
        ],
        [
            'nim' => '23002',
            'nama' => 'Siti',
            'prodi' => 'Teknik Informatika'
        ],
        [
            'nim' => '23003',
            'nama' => 'Agus',
            'prodi' => 'Teknik Informatika'
        ],
        [
            'nim' => '23004',
            'nama' => 'Dinda',
            'prodi' => 'Teknik Informatika'
        ]
    ];

    public function getAll()
    {
        return $this->data;
    }

    public function getByNim($nim)
    {
        foreach ($this->data as $mahasiswa) {

            if ($mahasiswa['nim'] === $nim) {
                return $mahasiswa;
            }
        }

        return null;
    }
}