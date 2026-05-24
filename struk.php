<?php
require 'koneksi.php';

$id = $_GET['id'];

$sql = "SELECT tr.*, tb.id_tabungan, k.nama, k.jabatan
        FROM robotv80_transaksi tr
        JOIN robotv80_tabungan tb ON tr.id_tabungan = tb.id_tabungan
        JOIN robotv80_karyawan k ON tb.id_karyawan = k.id_karyawan
        WHERE tr.id_transaksi = '$id'";

$result = mysqli_query($koneksi, $sql);
$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>myROBOT-V80</title>
  <link href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png" rel="icon" type="image/png" />

<style>
body { font-family: Arial; padding:20px; }
.box { width:300px; margin:auto; border:1px dashed #000; padding:15px; }
button { margin-top:10px; }
</style>
</head>
<body onload="window.print()">

<div class="box">
  <h3>STRUK TRANSAKSI</h3>
  <hr>

  <p>ID: <?= $data['id_transaksi']; ?></p>
  <p>Nama: <?= $data['nama']; ?></p>
  <p>Jabatan: <?= $data['jabatan']; ?></p>
  <p>Jenis: <?= $data['jenis_transaksi']; ?></p>
  <p>Jumlah: Rp <?= number_format($data['jumlah'],0,',','.'); ?></p>
  <p>Tanggal: <?= date('d-m-Y H:i', strtotime($data['tanggal'])); ?></p>

  <hr>
  <small>Terima kasih</small>
</div>

</body>
</html>
