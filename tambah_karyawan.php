<?php
session_start();
if (!isset($_SESSION['id_admin'])) {
  header('Location: login.php');
}

require 'koneksi.php';

if (isset($_POST['submit'])) {
  $nama = $_POST['nama'];
  $jabatan = $_POST['jabatan'];
  $alamat = $_POST['alamat'];
  $no_tlp = $_POST['no_tlp'];

  $sql = "INSERT INTO robotv80_karyawan (nama, jabatan, alamat, no_tlp) VALUES ('$nama', '$jabatan', '$alamat', '$no_tlp')";
  mysqli_query($koneksi, $sql);

  header('Location: data_karyawan.php');
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Tambah Karyawan</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h2>Tambah Karyawan</h2>
    <form action="" method="post">
      <label for="nama">Nama:</label>
      <input type="text" id="nama" name="nama"><br><br>
      <label for="jabatan">Jabatan:</label>
      <input type="text" id="jabatan" name="jabatan"><br><br>
      <label for="alamat">Alamat:</label>
      <textarea id="alamat" name="alamat"></textarea><br><br>
      <label for="no_tlp">No. Telp:</label>
      <input type="text" id="no_tlp" name="no_tlp"><br><br>
      <input type="submit" name="submit" value="Simpan">
    </form>
  </div>
</body>
</html>
