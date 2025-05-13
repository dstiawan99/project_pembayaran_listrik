<?php
define('BASE_PATH', dirname(__DIR__));
define('BASE_URL', 'http://localhost/project_pembayaran_listrik/');
$host = "localhost";
$user = "dwise";
$pass = "@BukanSainSaya99.,";
$db = "pembayaran_listrik";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>