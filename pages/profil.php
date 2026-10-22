<?php
$conn = mysqli_connect($host, $username, $password, $dbname);
$query = "SELECT * FROM profil";
$result = mysqli_query($conn, $query);
$profil = [];

if ($result && mysqli_num_rows($result) > 0) {
    $profil = mysqli_fetch_assoc($result);
} else {    
    echo "<p style='color:red;'>Gagal memuat data dari database: " . mysqli_error($conn) . "</p>";
}
?>
<h2 style="color:#2c3e50; margin-top:0;"><?php echo htmlspecialchars($profil['judul'] ?? '', ENT_QUOTES, 'UTF-8');?></h2>
<p><?php echo nl2br(htmlspecialchars($profil['keterangan'] ?? '', ENT_QUOTES, 'UTF-8')); ?></p>
<ul>
  <li><strong>Visi:</strong> <?php echo nl2br(htmlspecialchars($profil['visi'] ?? '', ENT_QUOTES, 'UTF-8'));?></li>
  <li><strong>Misi:</strong> <?php echo nl2br(htmlspecialchars($profil['misi'] ?? '', ENT_QUOTES, 'UTF-8'));?></li>
</ul>
