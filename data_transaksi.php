<?php 
session_start(); 
if (!isset($_SESSION['id_admin'])) { 
  header('Location: login.php'); 
  exit;
} 

require 'koneksi.php'; 

$sql = "SELECT 
            tr.id_transaksi,
            tr.jenis_transaksi,
            tr.jumlah,
            tr.tanggal,
            tr.id_tabungan,
            k.nama,
            tb.saldo
        FROM robotv80_transaksi tr
        JOIN robotv80_tabungan tb ON tr.id_tabungan = tb.id_tabungan
        JOIN robotv80_karyawan k ON tb.id_karyawan = k.id_karyawan
        ORDER BY tr.tanggal DESC";

$result = mysqli_query($koneksi, $sql) or die(mysqli_error($koneksi));
?>

<!DOCTYPE html>
<html>
<head>
<link href="http://10.10.20.250/dashboard/download.jpeg" rel="icon" type="image/png" />
<title>Aplikasi RS. Asura</title>
<style>
body { font-family: Arial, sans-serif; background:#f0f2f5; margin:0; padding:0; }
.container { width:90%; margin:30px auto; background:#fff; padding:20px 25px; border-radius:10px; box-shadow:0 0 15px rgba(0,0,0,0.2);}
h2 { text-align:center; margin-bottom:20px; color:#333;}
table { width:100%; border-collapse: collapse; margin-top:15px;}
table th, table td { border:1px solid #ccc; padding:10px; text-align:center; }
table th { background:#007bff; color:white; }
.btn { text-decoration:none; padding:8px 15px; border-radius:5px; color:white; margin-right:5px; font-size:14px; }
.btn-primary { background:#28a745; } .btn-warning { background:#ffc107; } .btn-info { background:#17a2b8; }
.btn-dark { background:#343a40; } .btn-secondary { background:#6c757d; }
.badge { padding:5px 10px; border-radius:5px; color:white; font-weight:bold; font-size:12px;}
.badge-setor { background:#28a745; }
.badge-tarik { background:#dc3545; }
.badge-transfer { background:#17a2b8; }
</style>
</head>
<body>

<div class="container">
  <h2>Data Transaksi</h2>

  <a href="tambah_transaksi.php" class="btn btn-primary">Tambah Transaksi</a>
  <a href="tarik_uang.php" class="btn btn-warning">Tarik Uang</a>
  <a href="transfer_antar_karyawan.php" class="btn btn-info">Transfer Antar Karyawan</a>
  <a href="riwayat_transaksi.php" class="btn btn-dark">Riwayat Transaksi</a>
  <a href="dashboard_admin.php" class="btn btn-secondary">Kembali ke Dashboard</a>

  <table>
    <tr>
      <th>ID</th>
      <th>Tabungan</th>
      <th>Jenis</th>
      <th>Jumlah</th>
      <th>Saldo Saat Ini</th>
      <th>Tanggal</th>
    </tr>

    <?php if(mysqli_num_rows($result) > 0): ?>
      <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <tr>
        <td><?php echo $row['id_transaksi']; ?></td>

        <td><?php echo $row['id_tabungan']." - ".$row['nama']; ?></td>

        <td>
          <?php 
            $jenis = strtolower($row['jenis_transaksi']);
            if ($jenis == 'setor') {
              echo "<span class='badge badge-setor'>SETOR</span>";
            } elseif ($jenis == 'tarik') {
              echo "<span class='badge badge-tarik'>TARIK</span>";
            } elseif ($jenis == 'transfer') {
              echo "<span class='badge badge-transfer'>TRANSFER</span>";
            } else {
              echo $row['jenis_transaksi'];
            }
          ?>
        </td>

        <td>Rp <?php echo number_format($row['jumlah'], 0, ',', '.'); ?></td>
        <td>Rp <?php echo number_format($row['saldo'], 0, ',', '.'); ?></td>
        <td><?php echo date('d-m-Y H:i', strtotime($row['tanggal'])); ?></td>
      </tr>
      <?php } ?>
    <?php else: ?>
      <tr>
        <td colspan="6" style="text-align:center;">Belum ada data transaksi</td>
      </tr>
    <?php endif; ?>

  </table>
</div>

</body>
</html>

