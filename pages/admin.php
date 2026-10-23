<?php
$conn = mysqli_connect($host, $username, $password, $dbname);

$pesan = '';
$jenisPesan = 'error';
$editMhs = null;
$aksi = $_POST['aksi'] ?? '';

// Informasi kontak disimpan terpisah karena halaman kontak sebelumnya memakai konten statis.
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS site_contact_content (
    id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
    judul VARCHAR(255) NOT NULL,
    pengantar TEXT NOT NULL,
    alamat TEXT NOT NULL,
    email VARCHAR(255) NOT NULL,
    telepon VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
mysqli_query($conn, "INSERT IGNORE INTO site_contact_content (id, judul, pengantar, alamat, email, telepon) VALUES (1, 'Hubungi Kami', 'Silakan hubungi sekretariat kampus melalui informasi berikut:', 'Jl. Prof. Dr. Soepomo No. 10, Jakarta', 'info@kampusnusantara.ac.id', '(021) 555-0199')");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($aksi === 'simpan_beranda') {
        $judul = trim($_POST['judul'] ?? '');
        $gambar = trim($_POST['gambar_lama'] ?? '');
        $konten = trim($_POST['konten'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
            if ($_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
                $pesan = 'Gambar gagal diunggah.';
            } else {
                $infoGambar = @getimagesize($_FILES['gambar']['tmp_name']);
                $ekstensiGambar = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
                $tipeGambar = $infoGambar['mime'] ?? '';
                if (!$infoGambar || !isset($ekstensiGambar[$tipeGambar])) {
                    $pesan = 'Pilih gambar JPG, PNG, GIF, atau WebP yang valid.';
                } else {
                    $folderGambar = __DIR__ . '/../uploads';
                    if (!is_dir($folderGambar)) mkdir($folderGambar, 0755, true);
                    $namaGambar = bin2hex(random_bytes(12)) . '.' . $ekstensiGambar[$tipeGambar];
                    if (move_uploaded_file($_FILES['gambar']['tmp_name'], $folderGambar . '/' . $namaGambar)) {
                        $gambar = 'uploads/' . $namaGambar;
                    } else {
                        $pesan = 'Gambar gagal disimpan ke folder uploads.';
                    }
                }
            }
        }
        if ($pesan !== '') {
            // Tampilkan kembali formulir dengan pesan validasi di bawah.
        } else {
        $jumlah = mysqli_query($conn, 'SELECT COUNT(*) AS total FROM artikel');
        $adaBaris = $jumlah && (int)mysqli_fetch_assoc($jumlah)['total'] > 0;
        $stmt = mysqli_prepare($conn, $adaBaris ? 'UPDATE artikel SET judul = ?, gambar = ?, konten = ?, slug = ? LIMIT 1' : 'INSERT INTO artikel (judul, gambar, konten, slug) VALUES (?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'ssss', $judul, $gambar, $konten, $slug);
        if (mysqli_stmt_execute($stmt)) { header('Location: index.php?modul=admin&pesan=beranda'); exit; }
        $pesan = 'Konten Beranda gagal disimpan.';
        }
    } elseif ($aksi === 'simpan_profil') {
        $judul = trim($_POST['judul'] ?? '');
        $keterangan = trim($_POST['keterangan'] ?? '');
        $visi = trim($_POST['visi'] ?? '');
        $misi = trim($_POST['misi'] ?? '');
        $jumlah = mysqli_query($conn, 'SELECT COUNT(*) AS total FROM profil');
        $adaBaris = $jumlah && (int)mysqli_fetch_assoc($jumlah)['total'] > 0;
        $stmt = mysqli_prepare($conn, $adaBaris ? 'UPDATE profil SET judul = ?, keterangan = ?, visi = ?, misi = ? LIMIT 1' : 'INSERT INTO profil (judul, keterangan, visi, misi) VALUES (?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'ssss', $judul, $keterangan, $visi, $misi);
        if (mysqli_stmt_execute($stmt)) { header('Location: index.php?modul=admin&pesan=profil'); exit; }
        $pesan = 'Konten Profil Kampus gagal disimpan.';
    } elseif ($aksi === 'simpan_kontak') {
        $judul = trim($_POST['judul'] ?? '');
        $pengantar = trim($_POST['pengantar'] ?? '');
        $alamat = trim($_POST['alamat'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telepon = trim($_POST['telepon'] ?? '');
        $stmt = mysqli_prepare($conn, 'UPDATE site_contact_content SET judul = ?, pengantar = ?, alamat = ?, email = ?, telepon = ? WHERE id = 1');
        mysqli_stmt_bind_param($stmt, 'sssss', $judul, $pengantar, $alamat, $email, $telepon);
        if (mysqli_stmt_execute($stmt)) { header('Location: index.php?modul=admin&pesan=kontak'); exit; }
        $pesan = 'Konten Kontak gagal disimpan.';
    } elseif ($aksi === 'hapus') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $stmt = mysqli_prepare($conn, 'DELETE FROM biodata_mhs WHERE id = ?');
            mysqli_stmt_bind_param($stmt, 'i', $id);
            if (mysqli_stmt_execute($stmt)) {
                header('Location: index.php?modul=admin&pesan=hapus');
                exit;
            }
        }
        $pesan = 'Data gagal dihapus.';
    } elseif ($aksi === 'simpan' || $aksi === 'ubah') {
        $nim = trim($_POST['nim'] ?? '');
        $nama = trim($_POST['nama'] ?? '');
        $prodi = trim($_POST['prodi'] ?? '');
        $ipk = $_POST['ipk'] ?? '';

        if ($nim === '' || $nama === '' || $prodi === '' || !is_numeric($ipk) || (float)$ipk < 0 || (float)$ipk > 4) {
            $pesan = 'Lengkapi semua kolom. IPK harus bernilai 0 sampai 4.';
            if ($aksi === 'ubah') {
                $editMhs = ['id' => (int)($_POST['id'] ?? 0), 'nim' => $nim, 'nama' => $nama, 'prodi' => $prodi, 'ipk' => $ipk];
            }
        } elseif ($aksi === 'simpan') {
            $stmt = mysqli_prepare($conn, 'INSERT INTO biodata_mhs (nim, nama, prodi, ipk) VALUES (?, ?, ?, ?)');
            $nilaiIpk = (float)$ipk;
            mysqli_stmt_bind_param($stmt, 'sssd', $nim, $nama, $prodi, $nilaiIpk);
            if (mysqli_stmt_execute($stmt)) {
                header('Location: index.php?modul=admin&pesan=tambah');
                exit;
            }
            $pesan = 'Data gagal ditambahkan. Pastikan NIM belum digunakan.';
        } else {
            $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
            if ($id) {
                $stmt = mysqli_prepare($conn, 'UPDATE biodata_mhs SET nim = ?, nama = ?, prodi = ?, ipk = ? WHERE id = ?');
                $nilaiIpk = (float)$ipk;
                mysqli_stmt_bind_param($stmt, 'sssdi', $nim, $nama, $prodi, $nilaiIpk, $id);
                if (mysqli_stmt_execute($stmt)) {
                    header('Location: index.php?modul=admin&pesan=ubah');
                    exit;
                }
            }
            $pesan = 'Data gagal diperbarui. Pastikan NIM belum digunakan.';
            $editMhs = ['id' => (int)$id, 'nim' => $nim, 'nama' => $nama, 'prodi' => $prodi, 'ipk' => $ipk];
        }
    }
}

if (isset($_GET['pesan'])) {
    $pesan = ['tambah' => 'Data mahasiswa berhasil ditambahkan.', 'ubah' => 'Data mahasiswa berhasil diperbarui.', 'hapus' => 'Data mahasiswa berhasil dihapus.', 'beranda' => 'Konten Beranda berhasil disimpan.', 'profil' => 'Konten Profil Kampus berhasil disimpan.', 'kontak' => 'Konten Kontak berhasil disimpan.'][$_GET['pesan']] ?? '';
    if ($pesan !== '') $jenisPesan = 'sukses';
}

if (!$editMhs && ($_GET['aksi'] ?? '') === 'edit') {
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if ($id) {
        $stmt = mysqli_prepare($conn, 'SELECT id, nim, nama, prodi, ipk FROM biodata_mhs WHERE id = ?');
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        $hasilEdit = mysqli_stmt_get_result($stmt);
        $editMhs = mysqli_fetch_assoc($hasilEdit) ?: null;
    }
}

$hasilMhs = mysqli_query($conn, 'SELECT id, nim, nama, prodi, ipk FROM biodata_mhs ORDER BY id DESC');
$listMhs = $hasilMhs ? mysqli_fetch_all($hasilMhs, MYSQLI_ASSOC) : [];
$totalMahasiswa = count($listMhs);
$hasilBeranda = mysqli_query($conn, 'SELECT judul, gambar, konten, slug FROM artikel LIMIT 1');
$dataBeranda = $hasilBeranda ? (mysqli_fetch_assoc($hasilBeranda) ?: []) : [];
$hasilProfil = mysqli_query($conn, 'SELECT judul, keterangan, visi, misi FROM profil LIMIT 1');
$dataProfil = $hasilProfil ? (mysqli_fetch_assoc($hasilProfil) ?: []) : [];
$hasilKontak = mysqli_query($conn, 'SELECT judul, pengantar, alamat, email, telepon FROM site_contact_content WHERE id = 1');
$dataKontak = $hasilKontak ? (mysqli_fetch_assoc($hasilKontak) ?: []) : [];
$esc = static function ($nilai) { return htmlspecialchars((string)$nilai, ENT_QUOTES, 'UTF-8'); };
?>

<h2 style="color:#2c3e50; margin-top:0;">Halaman Admin</h2>
<p>Kelola data mahasiswa dari halaman ini. Akses pengelolaan hanya tersedia setelah login sebagai admin.</p>

<h3>Konten Halaman Website</h3>
<p>Edit konten Beranda, Profil Kampus, dan Kontak dari bagian ini.</p>

<details open>
  <summary><strong>Edit Beranda</strong></summary>
  <form method="post" enctype="multipart/form-data" action="index.php?modul=admin">
    <input type="hidden" name="aksi" value="simpan_beranda">
    <input type="hidden" name="gambar_lama" value="<?php echo $esc($dataBeranda['gambar'] ?? ''); ?>">
    <p><label>Judul<br><input name="judul" required value="<?php echo $esc($dataBeranda['judul'] ?? ''); ?>" style="width:100%; padding:8px; box-sizing:border-box;"></label></p>
    <p><label>Gambar<br><input type="file" name="gambar" accept="image/jpeg,image/png,image/gif,image/webp" style="width:100%; padding:8px; box-sizing:border-box;"></label></p>
    <?php if (!empty($dataBeranda['gambar'])): ?><p>Gambar saat ini: <img src="<?php echo $esc($dataBeranda['gambar']); ?>" alt="Gambar beranda" style="max-width:180px; max-height:120px; display:block; margin-top:8px;"></p><?php endif; ?>
    <p><label>Konten<br><textarea name="konten" rows="5" required style="width:100%; padding:8px; box-sizing:border-box;"><?php echo $esc($dataBeranda['konten'] ?? ''); ?></textarea></label></p>
    <p><label>Slug<br><input name="slug" required value="<?php echo $esc($dataBeranda['slug'] ?? ''); ?>" style="width:100%; padding:8px; box-sizing:border-box;"></label></p>
    <button type="submit">Simpan Beranda</button>
  </form>
</details>

<details>
  <summary><strong>Edit Profil Kampus</strong></summary>
  <form method="post" action="index.php?modul=admin">
    <input type="hidden" name="aksi" value="simpan_profil">
    <p><label>Judul<br><input name="judul" required value="<?php echo $esc($dataProfil['judul'] ?? ''); ?>" style="width:100%; padding:8px; box-sizing:border-box;"></label></p>
    <p><label>Keterangan<br><textarea name="keterangan" rows="4" required style="width:100%; padding:8px; box-sizing:border-box;"><?php echo $esc($dataProfil['keterangan'] ?? ''); ?></textarea></label></p>
    <p><label>Visi<br><textarea name="visi" rows="3" required style="width:100%; padding:8px; box-sizing:border-box;"><?php echo $esc($dataProfil['visi'] ?? ''); ?></textarea></label></p>
    <p><label>Misi<br><textarea name="misi" rows="3" required style="width:100%; padding:8px; box-sizing:border-box;"><?php echo $esc($dataProfil['misi'] ?? ''); ?></textarea></label></p>
    <button type="submit">Simpan Profil Kampus</button>
  </form>
</details>

<details>
  <summary><strong>Edit Kontak</strong></summary>
  <form method="post" action="index.php?modul=admin">
    <input type="hidden" name="aksi" value="simpan_kontak">
    <p><label>Judul<br><input name="judul" required value="<?php echo $esc($dataKontak['judul'] ?? ''); ?>" style="width:100%; padding:8px; box-sizing:border-box;"></label></p>
    <p><label>Pengantar<br><textarea name="pengantar" rows="3" required style="width:100%; padding:8px; box-sizing:border-box;"><?php echo $esc($dataKontak['pengantar'] ?? ''); ?></textarea></label></p>
    <p><label>Alamat<br><textarea name="alamat" rows="3" required style="width:100%; padding:8px; box-sizing:border-box;"><?php echo $esc($dataKontak['alamat'] ?? ''); ?></textarea></label></p>
    <p><label>Email<br><input type="email" name="email" required value="<?php echo $esc($dataKontak['email'] ?? ''); ?>" style="width:100%; padding:8px; box-sizing:border-box;"></label></p>
    <p><label>Telepon<br><input name="telepon" required value="<?php echo $esc($dataKontak['telepon'] ?? ''); ?>" style="width:100%; padding:8px; box-sizing:border-box;"></label></p>
    <button type="submit">Simpan Kontak</button>
  </form>
</details>

<?php if ($pesan !== ''): ?>
  <p role="status" style="padding:10px; border-radius:4px; color:<?php echo $jenisPesan === 'sukses' ? '#1e6b37' : '#900c3f'; ?>; background:<?php echo $jenisPesan === 'sukses' ? '#d5f5e3' : '#fadbd8'; ?>;"><?php echo $esc($pesan); ?></p>
<?php endif; ?>

<h3 id="mahasiswa"><?php echo $editMhs ? 'Edit Data Mahasiswa' : 'Tambah Mahasiswa'; ?></h3>
<form method="post" action="index.php?modul=admin">
  <input type="hidden" name="aksi" value="<?php echo $editMhs ? 'ubah' : 'simpan'; ?>">
  <?php if ($editMhs): ?><input type="hidden" name="id" value="<?php echo (int)$editMhs['id']; ?>"><?php endif; ?>
  <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:12px;">
    <label>NIM<br><input type="text" name="nim" required value="<?php echo $esc($editMhs['nim'] ?? ''); ?>" style="width:100%; box-sizing:border-box; padding:8px;"></label>
    <label>Nama lengkap<br><input type="text" name="nama" required value="<?php echo $esc($editMhs['nama'] ?? ''); ?>" style="width:100%; box-sizing:border-box; padding:8px;"></label>
    <label>Program studi<br><input type="text" name="prodi" required value="<?php echo $esc($editMhs['prodi'] ?? ''); ?>" style="width:100%; box-sizing:border-box; padding:8px;"></label>
    <label>IPK<br><input type="number" name="ipk" min="0" max="4" step="0.01" required value="<?php echo $esc($editMhs['ipk'] ?? ''); ?>" style="width:100%; box-sizing:border-box; padding:8px;"></label>
  </div>
  <p><button type="submit" style="background:#27ae60; color:white; padding:10px 15px; border:0; border-radius:4px; cursor:pointer;"><?php echo $editMhs ? 'Simpan Perubahan' : 'Tambah Mahasiswa'; ?></button>
  <?php if ($editMhs): ?> <a href="index.php?modul=admin">Batal edit</a><?php endif; ?></p>
</form>

<h3>Daftar Mahasiswa (<?php echo $totalMahasiswa; ?>)</h3>
<table>
  <thead><tr><th>No</th><th>NIM</th><th>Nama Lengkap</th><th>Program Studi</th><th>IPK</th><th>Aksi</th></tr></thead>
  <tbody>
  <?php if ($listMhs): foreach ($listMhs as $no => $mhs): ?>
    <tr>
      <td><?php echo $no + 1; ?></td><td><?php echo $esc($mhs['nim']); ?></td><td><?php echo $esc($mhs['nama']); ?></td><td><?php echo $esc($mhs['prodi']); ?></td><td><?php echo $esc($mhs['ipk']); ?></td>
      <td><a href="index.php?modul=admin&amp;aksi=edit&amp;id=<?php echo (int)$mhs['id']; ?>">Edit</a>
        <form method="post" action="index.php?modul=admin" style="display:inline;" onsubmit="return confirm('Hapus data mahasiswa ini?');">
          <input type="hidden" name="aksi" value="hapus"><input type="hidden" name="id" value="<?php echo (int)$mhs['id']; ?>">
          <button type="submit" style="color:#c0392b; background:none; border:0; cursor:pointer; text-decoration:underline;">Hapus</button>
        </form>
      </td>
    </tr>
  <?php endforeach; else: ?>
    <tr><td colspan="6" style="text-align:center;">Belum ada data mahasiswa.</td></tr>
  <?php endif; ?>
  </tbody>
</table>
