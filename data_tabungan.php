<?php 
session_start(); 
if (!isset($_SESSION['id_admin'])) { 
  header('Location: login.php'); 
} 
require 'koneksi.php'; 
$sql = "SELECT t.id_tabungan, t.id_karyawan, k.nama, k.no_tlp, k.jabatan, t.saldo, t.tanggal 
        FROM robotv80_tabungan t 
        JOIN robotv80_karyawan k ON t.id_karyawan = k.id_karyawan";
$result = mysqli_query($koneksi, $sql); 
if (!$result) {
  die("Query Error: " . mysqli_error($koneksi));
}
?> 
<!DOCTYPE html> 
<html> 
<head> 
<link href="http://10.10.20.250/dashboard/download.jpeg" rel="icon" type="image/png" />
  <title>Aplikasi RS. Asura</title>
  <link rel="stylesheet" href="style-tabungan.css"> 
</head> 
<body> 
  <div class="container"> 
    <h2>Data Tabungan</h2> 
    <a href="tambah_tabungan.php" class="btn btn-primary">Tambah Tabungan</a> 
    <a href="dashboard_admin.php" class="btn btn-secondary">Kembali ke Dashboard</a> 
    <table> 
      <tr> 
        <th>ID Tabungan</th> 
        <th>ID Karyawan</th> 
        <th>Nama Karyawan</th> 
        <th>Jabatan</th> 
        <th>Nomor HP</th> 
        <th>Saldo</th> 
        <th>Tanggal</th> 
        <th>Aksi</th> 
      </tr> 
      <?php while ($row = mysqli_fetch_assoc($result)) { ?> 
      <tr> 
        <td><?php echo $row['id_tabungan']; ?></td> 
        <td><?php echo $row['id_karyawan']; ?></td> 
        <td><?php echo $row['nama']; ?></td> 
        <td><?php echo $row['jabatan']; ?></td> 
        <td><?php echo $row['no_tlp']; ?></td> 
   <td>
  Rp <?php echo number_format($row['saldo'], 0, ',', '.'); ?>
</td>
        <td><?php echo $row['tanggal']; ?></td> 
        <td> 
          <a href="edit_tabungan.php?id_tabungan=<?php echo $row['id_tabungan']; ?>">Edit</a> | 
          <a href="hapus_tabungan.php?id_tabungan=<?php echo $row['id_tabungan']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a> 
        </td> 
      </tr> 
      <?php } ?> 
    </table> 
  </div> 
</body> 
</html>

