<?php
session_start();
if (!isset($_SESSION['id_admin'])) {
  header('Location: login.php');
}

require 'koneksi.php';

if (isset($_POST['submit'])) {
  $id_karyawan = $_POST['id_karyawan'];
  $saldo = $_POST['saldo'];
  $tanggal = date('Y-m-d');

  $sql = "INSERT INTO robotv80_tabungan (id_karyawan, saldo, tanggal) VALUES ('$id_karyawan', '$saldo', '$tanggal')";
  mysqli_query($koneksi, $sql);

  header('Location: data_tabungan.php');
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Tambah Tabungan</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <h2>Tambah Tabungan</h2>
    <form action="" method="post">
      <label for="id_karyawan">ID Karyawan:</label>
      <select id="id_karyawan" name="id_karyawan">
        <?php
        $sql = "SELECT * FROM robotv80_karyawan";
        $result = mysqli_query($koneksi, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
          echo "<option value='".$row['id_karyawan']."'>".$row['nama']."</option>";
        }
        ?>
      </select><br><br>
      <label for="saldo">Saldo:</label>
      <input type="text" id="saldo" name="saldo"><br><br>
      <input type="submit" name="submit" value="Simpan">
    </form>
  </div>
</body>
</html>
