<?php
/**
 * Billing VA → DB SIKEU modern (al_istiqomah_sikeu).
 * Sebelumnya: 10.99.23.26 / Kuningan_Yayasan_Al_Istiqomah (scct*).
 */
$host = '101.50.2.56';
$port = 3306;
$base = 'al_istiqomah_sikeu';
$user = 'root';
$pawd = 'Bismillah100%';

$dbhandle = mysqli_connect($host, $user, $pawd, $base, $port)
    or die("Couldn't connect to SQL Server on {$host}");

mysqli_set_charset($dbhandle, 'utf8mb4');
