<?php
session_start();
if (!isset($_SESSION['id_admin'])) {
  header('Location: login.php');
  exit;
}

require 'koneksi.php';

$notif = ''; // untuk menyimpan pesan notifikasi

if (isset($_POST['submit'])) {
  $id_tabungan = $_POST['id_tabungan'];
  $jumlah = $_POST['jumlah'];

  $sql = "SELECT * FROM robotv80_tabungan WHERE id_tabungan = '$id_tabungan'";
  $result = mysqli_query($koneksi, $sql);
  $row = mysqli_fetch_assoc($result);

  if ($row['saldo'] >= $jumlah) {
    $saldo_baru = $row['saldo'] - $jumlah;
    $sql = "UPDATE robotv80_tabungan SET saldo = '$saldo_baru' WHERE id_tabungan = '$id_tabungan'";
    mysqli_query($koneksi, $sql);

    $sql = "INSERT INTO robotv80_transaksi (id_tabungan, jenis_transaksi, jumlah, tanggal) 
            VALUES ('$id_tabungan', 'Tarik Uang', '$jumlah', NOW())";
    mysqli_query($koneksi, $sql);

    header('Location: data_transaksi.php');
    exit;
  } else {
    $notif = "⚠ Saldo tidak cukup untuk melakukan penarikan!";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
<link href="http://10.10.20.250/dashboard/download.jpeg" rel="icon" type="image/png" />
<title>Aplikasi RS. Asura</title>
<link rel="stylesheet" href="style-tarik.css">
</head>
<body>
<div class="container">
  <h2>Tarik Uang</h2>

  <!-- Notifikasi saldo -->
  <?php if($notif != ''): ?>
    <div class="alert"><?php echo $notif; ?></div>
  <?php endif; ?>

  <form action="" method="post">
    <label for="id_tabungan">ID Tabungan:</label>
    <select id="id_tabungan" name="id_tabungan">
      <?php
      $sql = "SELECT t.id_tabungan, k.nama 
              FROM robotv80_tabungan t 
              JOIN robotv80_karyawan k ON t.id_karyawan = k.id_karyawan";
      $result = mysqli_query($koneksi, $sql);
      while ($row = mysqli_fetch_assoc($result)) {
        echo "<option value='".$row['id_tabungan']."'>".$row['id_tabungan']." - ".$row['nama']."</option>";
      }
      ?>
    </select><br><br>

    <label for="jumlah">Jumlah:</label>
    <input type="number" id="jumlah" name="jumlah" required><br><br>

    <input type="submit" name="submit" value="Tarik Uang" class="btn-danger">
    <a href="data_transaksi.php" class="btn-secondary kembali">Kembali</a>
  </form>
</div>
</body>
</html>

