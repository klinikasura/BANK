<?php 
session_start(); 
if (!isset($_SESSION['id_admin'])) { 
  header('Location: login.php'); 
} 
require 'koneksi.php'; 
$sql = "SELECT * FROM robotv80_karyawan"; 
$result = mysqli_query($koneksi, $sql); 
?>
<!DOCTYPE html>
<html>
<head>
<link href="http://10.10.20.250/dashboard/download.jpeg" rel="icon" type="image/png" />
  <title>Aplikasi RS. Asura</title>
  <link rel="stylesheet" href="style-data-karyawan.css">
</head>
<body>
  <div class="container">
    <h2>Data Karyawan</h2>
    <a href="tambah_karyawan.php" class="btn btn-primary">Tambah Karyawan</a>
    <a href="dashboard_admin.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    <table>
      <tr>
        <th>ID Karyawan</th>
        <th>Nama</th>
        <th>Jabatan</th>
        <th>Alamat</th>
        <th>No. Telp</th>
        <th>Aksi</th>
      </tr>
      <?php while ($row = mysqli_fetch_assoc($result)) { ?>
      <tr>
        <td><?php echo $row['id_karyawan']; ?></td>
        <td><?php echo $row['nama']; ?></td>
        <td><?php echo $row['jabatan']; ?></td>
        <td><?php echo $row['alamat']; ?></td>
        <td><?php echo $row['no_tlp']; ?></td>
        <td>
          <a href="edit_karyawan.php?id_karyawan=<?php echo $row['id_karyawan']; ?>">Edit</a> |
          <a href="hapus_karyawan.php?id_karyawan=<?php echo $row['id_karyawan']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
        </td>
      </tr>
      <?php } ?>
    </table>
  </div>
</body>
</html>

