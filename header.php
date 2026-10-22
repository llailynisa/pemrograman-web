<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Portal Akademik Kampus Nusantara</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f9; margin: 0; padding: 0; }
    header { background: #2c3e50; color: white; padding: 20px; text-align: center; }
    .user-info { margin-top: 8px; font-size: 14px; color: #1abc9c; }
    nav { background: #34495e; padding: 10px; text-align: center; }
    nav a { color: white; margin: 0 12px; text-decoration: none; font-weight: bold; }
    nav a:hover { color: #1abc9c; text-decoration: underline; }
    .container { max-width: 900px; margin: 30px auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,.1); min-height: 250px; }
    footer { text-align: center; padding: 15px; background: #2c3e50; color: white; margin-top: 40px; font-size: 13px; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
    th { background: #2c3e50; color: white; }
    input { max-width: 100%; }
  </style>
</head>
<body>
  <header>
    <h1 style="margin:0;">Portal Akademik Kampus Nusantara</h1>
    <?php if (($_SESSION['login_status'] ?? false) === true): ?>
      <div class="user-info">
        Halo, <strong><?php echo htmlspecialchars($_SESSION['nama_lengkap'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>
        (<em><?php echo htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></em>) | Status: Aktif
      </div>
    <?php endif; ?>
  </header>
  <nav>
    <a href="index.php?modul=beranda">Beranda</a>
    <a href="index.php?modul=profil">Profil Kampus</a>
    <a href="index.php?modul=mahasiswa">Data Mahasiswa (MySQLi)</a>
    <a href="index.php?modul=kontak">Kontak</a>
    <?php if (($_SESSION['login_status'] ?? false) === true && ($_SESSION['role'] ?? '') === 'admin'): ?>
      <a href="index.php?modul=admin" style="color:#f39c12;">Halaman Admin</a>
      <a href="index.php?modul=logout" style="color:#e74c3c;" onclick="return confirm('Keluar dari sistem?');">Logout</a>
    <?php else: ?>
      <a href="index.php?modul=login" style="color:#f1c40f;">Login Admin</a>
    <?php endif; ?>
  </nav>
  <div class="container">
