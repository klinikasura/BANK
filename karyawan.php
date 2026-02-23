<?php
session_start();
if (!isset($_SESSION['id_karyawan']) || $_SESSION['level'] != 'karyawan') {
    header('Location: login.php');
    exit;
}

require 'koneksi.php';

// Ambil saldo tabungan karyawan
$id_karyawan = $_SESSION['id_karyawan'];
$sql = "SELECT saldo FROM robotv80_tabungan WHERE id_karyawan = '$id_karyawan' LIMIT 1";
$result = mysqli_query($koneksi, $sql);
$saldo = 0;
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $saldo = $row['saldo'];
}
?>
<!DOCTYPE html>
<html>
<head>
<link href="http://10.10.20.250/dashboard/download.jpeg" rel="icon" type="image/png" />
<title>Aplikasi RS. Asura</title>
<link rel="stylesheet" href="style-karyawan.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
<div class="container dashboard-karyawan">
  <h2>Dashboard Karyawan</h2>
  <p>Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['nama']); ?></strong>!</p>

  <div class="saldo-box">
    <h3>Saldo Tabungan Anda</h3>
    <p class="saldo">Rp <?php echo number_format($saldo, 0, ',', '.'); ?></p>
  </div>

  <ul>
    <li><a href="data_tabungan_karyawan.php">Data Tabungan</a></li>
    <li><a href="data_transaksi_karyawan.php">Data Transaksi</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
</div>
</body>
</html>

