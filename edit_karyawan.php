<?php
session_start();
if (!isset($_SESSION['id_admin'])) {
  header('Location: login.php');
}

require 'koneksi.php';

$id_karyawan = $_GET['id_karyawan'];
$sql = "SELECT * FROM robotv80_karyawan WHERE id_karyawan = '$id_karyawan'";
$result = mysqli_query($koneksi, $sql);
$row = mysqli_fetch_assoc($result);

if (isset($_POST['submit'])) {
  $nama = $_POST['nama'];
  $jabatan = $_POST['jabatan'];
  $alamat = $_POST['alamat'];
  $no_tlp = $_POST['no_tlp'];

  $sql = "UPDATE robotv80_karyawan SET nama = '$nama', jabatan = '$jabatan', alamat = '$alamat', no_tlp = '$no_tlp' WHERE id_karyawan = '$id_karyawan'";
  mysqli_query($koneksi, $sql);

  header('Location: data_karyawan.php');
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Karyawan</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h2>Edit Karyawan</h2>
    <form action="" method="post">
      <label for="nama">Nama:</label>
      <input type="text" id="nama" name="nama" value="<?php echo $row['nama']; ?>"><br><br>
      <label for="jabatan">Jabatan:</label>
      <input type="text" id="jabatan" name="jabatan" value="<?php echo $row['jabatan']; ?>"><br><br>
      <label for="alamat">Alamat:</label>
      <textarea id="alamat" name="alamat"><?php echo $row['alamat']; ?></textarea><br><br>
      <label for="no_tlp">No. Telp:</label>
      <input type="text" id="no_tlp" name="no_tlp" value="<?php echo $row['no_tlp']; ?>"><br><br>
      <input type="submit" name="submit" value="Simpan">
    </form>
  </div>
</body>
</html>

