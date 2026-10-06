<?php
// app/Core/BaseModel.php
// Parent class semua Model/Repository yang butuh akses database (PDO),
// sehingga tidak perlu menulis ulang property $pdo dan constructor.

class BaseModel
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
}
