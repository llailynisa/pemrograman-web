<?php
$buatTabelKontak = "CREATE TABLE IF NOT EXISTS site_contact_content (
    id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    pengantar TEXT NOT NULL,
    alamat TEXT NOT NULL,
    email VARCHAR(255) NOT NULL,
    telepon VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($conn, $buatTabelKontak);
mysqli_query($conn, "INSERT IGNORE INTO site_contact_content (id, judul, pengantar, alamat, email, telepon) VALUES (1, 'Hubungi Kami', 'Silakan hubungi sekretariat kampus melalui informasi berikut:', 'Jl. Prof. Dr. Soepomo No. 10, Jakarta', 'info@kampusnusantara.ac.id', '(021) 555-0199')");
$hasilKontak = mysqli_query($conn, 'SELECT judul, pengantar, alamat, email, telepon FROM site_contact_content WHERE id = 1');
$kontak = $hasilKontak ? (mysqli_fetch_assoc($hasilKontak) ?: []) : [];
$esc = static function ($nilai) { return htmlspecialchars((string)$nilai, ENT_QUOTES, 'UTF-8'); };
?>
<h2 style="color:#2c3e50; margin-top:0;"><?php echo $esc($kontak['judul'] ?? 'Hubungi Kami'); ?></h2>
<p><?php echo nl2br($esc($kontak['pengantar'] ?? 'Silakan hubungi sekretariat kampus melalui informasi berikut:')); ?></p>
<p><strong>Alamat:</strong> <?php echo nl2br($esc($kontak['alamat'] ?? 'Jl. Prof. Dr. Soepomo No. 10, Jakarta')); ?><br>
<strong>Email:</strong> <?php echo $esc($kontak['email'] ?? 'info@kampusnusantara.ac.id'); ?><br>
<strong>Telepon:</strong> <?php echo $esc($kontak['telepon'] ?? '(021) 555-0199'); ?></p>
