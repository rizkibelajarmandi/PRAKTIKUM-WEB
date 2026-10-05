<?php

require_once __DIR__ . '/../Models/dosen.php';

class DosenController
{
    public function index()
    {
        $model = new Dosen();

        $data['dosen'] = $model->getAll();

        require_once __DIR__ . '/../Views/dosen/index.php';
    }
}