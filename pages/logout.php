<?php
// Menghapus semua variabel session
$_SESSION = [];

// Menghancurkan session server
session_destroy();

// Alihkan kembali ke beranda
echo "<script>alert('Anda telah berhasil logout.'); window.location='index.php?modul=beranda';</script>";
exit;
?>