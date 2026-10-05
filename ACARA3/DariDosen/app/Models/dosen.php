<?php

class Dosen
{
    private $data = [
        [
            'nidn' => '001',
            'nama' => 'Bapak Ahmad'
        ],
        [
            'nidn' => '002',
            'nama' => 'Ibu Siti'
        ]
    ];

    public function getAll()
    {
        return $this->data;
    }
}   