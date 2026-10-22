<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "db_akademik_mhs";

// Membuat koneksi menggunakan MySQLi Prosedural
$conn = mysqli_connect($host, $username, $password, $dbname);

// Periksa apakah koneksi berhasil
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>