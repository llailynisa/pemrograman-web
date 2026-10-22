<?php
if (!defined('ADMIN_SINGLE_PAGE')) { header('Location: ../index.php?modul=admin'); exit; }

if (isset($_POST['submit'])) {
    $nim = mysqli_real_escape_string($conn, $_POST['nim']);$nama = mysqli_real_escape_string($conn,$_POST['nama']);
    $prodi = mysqli_real_escape_string($conn, $_POST['prodi']);$ipk = mysqli_real_escape_string($conn,$_POST['ipk']);

    $query = "INSERT INTO biodata_mhs (nim, nama, prodi, ipk) VALUES ('$nim', '$nama', '$prodi', '$ipk')";
    $insert = mysqli_query($conn,$query);

    if ($insert) {
        echo "<script>alert('Data mahasiswa berhasil ditambahkan!'); window.location='index.php?modul=mahasiswa';</script>";
    } else {
        echo "<p style='color:red;'>Gagal menambah data: " . mysqli_error($conn) . "</p>";
    }
}
?>

<h2 style="color:#2c3e50; margin-top:0;">Tambah Data Mahasiswa Baru</h2>
<form action="" method="POST">
  <div style="margin-bottom: 12px;"><label style="display: block; font-weight: bold; margin-bottom: 5px;">NIM:</label><input type="text" name="nim" required style="width: 100%; padding: 8px; box-sizing: border-box;"></div>
  <div style="margin-bottom: 12px;"><label style="display: block; font-weight: bold; margin-bottom: 5px;">Nama Lengkap:</label><input type="text" name="nama" required style="width: 100%; padding: 8px; box-sizing: border-box;"></div>
  <div style="margin-bottom: 12px;"><label style="display: block; font-weight: bold; margin-bottom: 5px;">Program Studi:</label><input type="text" name="prodi" required style="width: 100%; padding: 8px; box-sizing: border-box;"></div>
  <div style="margin-bottom: 15px;"><label style="display: block; font-weight: bold; margin-bottom: 5px;">IPK:</label><input type="number" step="0.01" max="4.00" min="0.00" name="ipk" required style="width: 100%; padding: 8px; box-sizing: border-box;"></div>
  <button type="submit" name="submit" style="background: #2980b9; color: white; padding: 10px 15px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">Simpan Data</button>
  <a href="index.php?modul=mahasiswa" style="margin-left: 10px; color: #7f8c8d; text-decoration: none;">Batal</a>
</form>
