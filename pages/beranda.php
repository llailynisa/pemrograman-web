<?php
$conn = mysqli_connect($host, $username, $password, $dbname);
$query = "SELECT * FROM profil";
$result = mysqli_query($conn, $query);

$beranda = [];
if ($result && mysqli_num_rows($result) > 0) {
    $beranda = mysqli_fetch_assoc($result);
} else {    
    echo "<p style='color:red;'>Gagal memuat data dari database: " . mysqli_error($conn) . "</p>";
}
?>

<h2 style="color:#2c3e50; margin-top:0;"><?php echo htmlspecialchars($beranda['judul'] ?? '', ENT_QUOTES, 'UTF-8');?></h2>
<p><?php echo nl2br(htmlspecialchars($beranda['konten'] ?? '', ENT_QUOTES, 'UTF-8'));?></p>
<p><?php echo htmlspecialchars($beranda['slug'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
