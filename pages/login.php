<?php
$conn = mysqli_connect($host, $username, $password, $dbname);

// Jika sudah terlanjur login, lempar langsung ke halaman mahasiswa
if (isset($_SESSION['login_status'])) {
    echo "<script>window.location='index.php?modul=admin';</script>";
    exit;
}

$pesanError = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);$password = mysqli_real_escape_string($conn,$_POST['password']);

    // Cek data admin ke database
    $query = "SELECT * FROM admin_users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn,$query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Set Session Login
        session_regenerate_id(true);
        $_SESSION['login_status'] = true;
        $_SESSION['role'] = 'admin';
        $_SESSION['username'] =$user['username'];
        $_SESSION['nama_lengkap'] =$user['nama_lengkap'];

        // Contoh Penggunaan Cookie: Menyimpan username selama 1 hari
        setcookie('last_user', $username, time() + (86400 * 1), "/");

        echo "<script>alert('Login berhasil, Selamat datang!'); window.location='index.php?modul=admin';</script>";
        exit;
    } else {
        $pesanError = "Username atau password salah!";
    }
}

// Ambil nilai dari Cookie jika ada
$savedUser =$_COOKIE['last_user'] ?? '';
?>

<h2 style="color:#2c3e50; margin-top:0;">Form Login Administrator</h2>
<p>Silakan masukkan kredensial akun Anda untuk mengakses fitur pengelolaan data mahasiswa.</p>

<?php if (!empty($pesanError)): ?>
  <div style="background: #fadbd8; color: #900c3f; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
    <?php echo $pesanError; ?>
  </div>
<?php endif; ?>

<form action="" method="POST">
  <div style="margin-bottom: 12px;">
    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Username:</label>
    <input type="text" name="username" value="<?php echo htmlspecialchars($savedUser); ?>" required style="width: 100%; padding: 8px; box-sizing: border-box;">
  </div>
  <div style="margin-bottom: 15px;">
    <label style="display: block; font-weight: bold; margin-bottom: 5px;">Password:</label>
    <input type="password" name="password" required style="width: 100%; padding: 8px; box-sizing: border-box;">
  </div>
  <button type="submit" name="login" style="background: #27ae60; color: white; padding: 10px 15px; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">Masuk Sistem</button>
</form>
