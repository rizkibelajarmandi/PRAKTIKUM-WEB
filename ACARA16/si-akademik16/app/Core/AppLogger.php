<?php

class AppLogger
{
    public static function log(PDOException $exception, string $context): void
    {
        $logDirectory = __DIR__ . '/../../storage/logs';
        if (!is_dir($logDirectory) && !mkdir($logDirectory, 0775, true) && !is_dir($logDirectory)) {
            throw new RuntimeException('Direktori log aplikasi tidak dapat dibuat.');
        }

        $logFile = $logDirectory . '/app.log';
        $entry = sprintf(
            "[%s] %s: %s%s",
            date('Y-m-d H:i:s'),
            $context,
            $exception->getMessage(),
            PHP_EOL
        );

        if (file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX) === false) {
            throw new RuntimeException('Log aplikasi tidak dapat ditulis.');
        }
    }
}
