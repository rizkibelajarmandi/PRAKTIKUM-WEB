<?php

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Workshop SI Web Server - Acara 4 (Studi Kasus)</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <?php include __DIR__ . '/../partials/header.php'; ?>

    <?php include __DIR__ . '/../partials/navbar.php'; ?>

    <main class="container mt-4">
        <?php
        $contentPath = $content ?? null;
        if (!is_string($contentPath) || $contentPath === '') {
            throw new InvalidArgumentException('Variabel $content wajib berisi path view konten.');
        }

        require $contentPath;
        ?>
    </main>

    <?php include __DIR__ . '/../partials/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
