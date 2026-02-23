<?php 
session_start(); 
if (!isset($_SESSION['id_admin'])) { 
    header('Location: login.php'); 
    exit;
} 

require 'koneksi.php'; 

// Query gabungkan tabungan + karyawan untuk efisiensi
$sql = "SELECT tr.*, k.nama, k.jabatan
        FROM robotv80_transaksi tr
        JOIN robotv80_tabungan tb ON tr.id_tabungan = tb.id_tabungan
        JOIN robotv80_karyawan k ON tb.id_karyawan = k.id_karyawan
        ORDER BY tr.tanggal DESC";

$result = mysqli_query($koneksi, $sql) or die(mysqli_error($koneksi));
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link href="http://10.10.20.250/dashboard/download.jpeg" rel="icon" type="image/png" />
<title>Aplikasi RS. Asura</title>
<link rel="stylesheet" href="style-riwayat.css">
</head>
<body>
<div class="container">
    <h2>Riwayat Transaksi</h2>
    <a href="data_transaksi.php" class="btn-kembali">Kembali</a>

    <table>
        <tr>
            <th>ID Transaksi</th>
            <th>Tabungan</th>
            <th>Nama</th>
            <th>Jabatan</th>
            <th>Jenis Transaksi</th>
            <th>Jumlah</th>
            <th>Tanggal</th>
            <th>Keterangan</th>
        </tr>

        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <?php
                    $keterangan = '';
                    $jenis = strtolower($row['jenis_transaksi']);
                    if($jenis == 'transfer') {
                        $keterangan = $row['nama'] . ' melakukan transfer';
                    } elseif($jenis == 'tarik') {
                        $keterangan = $row['nama'] . ' menarik uang';
                    } elseif($jenis == 'setor') {
                        $keterangan = $row['nama'] . ' menyetor uang';
                    } else {
                        $keterangan = $row['nama'] . ' melakukan ' . $row['jenis_transaksi'];
                    }
                ?>
                <tr>
                    <td><?php echo $row['id_transaksi']; ?></td>
                    <td><?php echo $row['id_tabungan']; ?></td>
                    <td><?php echo $row['nama']; ?></td>
                    <td><?php echo $row['jabatan']; ?></td>
                    <td><?php echo ucfirst($row['jenis_transaksi']); ?></td>
                    <td>Rp <?php echo number_format($row['jumlah'],0,',','.'); ?></td>
                    <td><?php echo date('d-m-Y H:i', strtotime($row['tanggal'])); ?></td>
                    <td><?php echo $keterangan; ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" style="text-align:center;">Belum ada data transaksi</td>
            </tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>

