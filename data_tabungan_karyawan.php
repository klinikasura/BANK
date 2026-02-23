<?php
session_start();
if (!isset($_SESSION['id_karyawan']) || $_SESSION['level'] != 'karyawan') {
  header('Location: login.php');
  exit;
}

require 'koneksi.php';
$id_karyawan = $_SESSION['id_karyawan'];

$sql = "SELECT * FROM robotv80_tabungan WHERE id_karyawan = '$id_karyawan'";
$result = mysqli_query($koneksi, $sql);
?>
<!DOCTYPE html>
<html>
<head>
<link href="http://10.10.20.250/dashboard/download.jpeg" rel="icon" type="image/png" />
<title>Aplikasi RS. Asura</title>
<link rel="stylesheet" href="style-tabungan-karyawan.css">
</head>
<body>
<div class="container">
<h2>Data Tabungan <?php echo $_SESSION['nama']; ?></h2>
<table>
<tr>
  <th>ID Tabungan</th>
  <th>Saldo</th>
  <th>Tanggal</th>
</tr>
<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
  <td><?php echo $row['id_tabungan']; ?></td>
  <td>Rp <?php echo number_format($row['saldo'],0,',','.'); ?></td>
  <td><?php echo date('d-m-Y', strtotime($row['tanggal'])); ?></td>
</tr>
<?php } ?>
</table>
<a href="karyawan.php" class="btn-kembali">Kembali</a>
</div>
</body>
</html>

