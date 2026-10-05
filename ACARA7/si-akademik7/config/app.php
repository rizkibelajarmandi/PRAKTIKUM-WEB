<?php

/**
 * BASE_URL dihitung OTOMATIS dari SCRIPT_NAME, supaya tidak perlu
 * diedit manual tiap kali folder project dipindah/diganti nama
 * (mis. dari "si-akademik" jadi "si-akademik6").
 *
 * dirname($_SERVER['SCRIPT_NAME']) akan menghasilkan path folder
 * tempat public/index.php berada, contoh: "/ACARA6/si-akademik6/public"
 */
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$path   = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
define('BASE_URL', $scheme . '://' . $_SERVER['HTTP_HOST'] . $path);
?>