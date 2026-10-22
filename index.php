<?php
// 1. Memulai Session di awal baris sebelum ada output apapun
session_start();
ob_start();

// 2. Sertakan koneksi database terpusat
require_once 'koneksi.php';

// 3. Tangkap parameter modul dari URL (Default: beranda)
$modul =$_GET['modul'] ?? 'beranda';

// 4. Daftar Modul yang diizinkan (Termasuk modul login dan logout)
$allowedModules = [
    'beranda', 
    'profil', 
    'mahasiswa',
    'kontak',
    'admin',
    'login',
    'logout'
];

// 5. Daftar modul khusus CRUD yang wajib dilindungi (Hanya bisa diakses jika sudah login)
$protectedModules = [
    'admin',
];

if (in_array($modul, $protectedModules, true) && (($_SESSION['login_status'] ?? false) !== true || ($_SESSION['role'] ?? '') !== 'admin')) {
    header('Location: index.php?modul=login');
    exit;
}

define('ADMIN_SINGLE_PAGE', true);

// 6. Muat komponen header layout atas
include 'header.php';

if (in_array($modul,$allowedModules)) {
    $fileTujuan = "pages/" . $modul . ".php";
    
    if (file_exists($fileTujuan)) {
        include $fileTujuan;
    } else {
        echo "<p style='color:red;'>Berkas konten <code>{$modul}.php</code> tidak ditemukan.</p>";
    }
} else {
    echo "<h2 style='color:red;'>Error 404: Halaman Tidak Ditemukan!</h2>";
    echo "<p>Modul yang Anda minta tidak terdaftar dalam sistem.</p>";
}

// 7. Muat komponen footer layout bawah
include 'footer.php';
?>
