<?php
session_start();
if (!isset($_SESSION['id_karyawan'])) {
  header('Location: login.php');
  exit;
}

require 'koneksi.php';

$id_karyawan = $_SESSION['id_karyawan'];

// Query ambil transaksi beserta nama karyawan dan jabatan
// Untuk transfer, gabungkan dengan tabungan penerima/pengirim untuk keterangan
$sql = "SELECT 
            tr.id_transaksi,
            tr.id_tabungan,
            tr.jenis_transaksi,
            tr.jumlah,
            tr.tanggal,
            k.nama,
            k.jabatan,
            CASE 
                WHEN tr.jenis_transaksi LIKE 'Transfer%' THEN (
                    SELECT k2.nama 
                    FROM robotv80_transaksi tr2
                    JOIN robotv80_tabungan tb2 ON tr2.id_tabungan = tb2.id_tabungan
                    JOIN robotv80_karyawan k2 ON tb2.id_karyawan = k2.id_karyawan
                    WHERE tr2.tanggal = tr.tanggal 
                      AND tr2.jumlah = tr.jumlah
                      AND tr2.id_transaksi != tr.id_transaksi
                      LIMIT 1
                )
                ELSE NULL
            END AS nama_lawan
        FROM robotv80_transaksi tr
        JOIN robotv80_tabungan tb ON tr.id_tabungan = tb.id_tabungan
        JOIN robotv80_karyawan k ON tb.id_karyawan = k.id_karyawan
        WHERE tb.id_karyawan = '$id_karyawan'
        ORDER BY tr.tanggal DESC";

$result = mysqli_query($koneksi, $sql);

function formatJenisTransaksi($jenis, $nama_lawan) {
    if (strpos($jenis, 'Transfer') !== false && $nama_lawan) {
        if ($jenis == 'Transfer Masuk') return "Transfer dari $nama_lawan";
        if ($jenis == 'Transfer Keluar') return "Transfer ke $nama_lawan";
    }
    return ucfirst(strtolower($jenis));
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link href="http://10.10.20.250/dashboard/download.jpeg" rel="icon" type="image/png" />
<title>Aplikasi RS. Asura</title>
<style>
/* ---- CSS sama seperti sebelumnya ---- */
body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color:#f9fafb; color:#333; padding:20px;}
.container {max-width:1000px;margin:0 auto;background:#fff;padding:30px 40px;border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,0.1);}
h2{text-align:center;margin-bottom:25px;color:#2c3e50;font-weight:700;letter-spacing:1px;}
nav{text-align:center;margin-bottom:20px;}
nav a{display:inline-block;padding:10px 20px;margin:0 10px 20px 10px;background-color:#3498db;color:white;text-decoration:none;border-radius:7px;font-weight:600;box-shadow:0 5px 15px rgba(52,152,219,0.3);transition:background-color 0.3s;}
nav a:hover{background-color:#2980b9;}
table{width:100%;border-collapse:collapse;font-size:15px;}
table thead tr{background-color:#2980b9;color:white;text-align:left;}
table th, table td{padding:12px 15px;border-bottom:1px solid #ddd;}
table tbody tr:hover{background-color:#f1f6fb;transition:0.3s;}
@media (max-width:700px){
  table thead{display:none;}
  table, table tbody, table tr, table td{display:block;width:100%;}
  table tr{margin-bottom:20px;box-shadow:0 5px 15px rgba(0,0,0,0.1);border-radius:8px;padding:15px;background:white;}
  table td{padding-left:50%;position:relative;text-align:right;border-bottom:1px solid #eee;}
  table td::before{position:absolute;left:15px;width:45%;font-weight:700;text-align:left;color:#555;}
  table td[data-label="ID Transaksi"]::before{content:"ID Transaksi";}
  table td[data-label="Tabungan"]::before{content:"ID Tabungan";}
  table td[data-label="Nama"]::before{content:"Nama";}
  table td[data-label="Jabatan"]::before{content:"Jabatan";}
  table td[data-label="Jenis"]::before{content:"Jenis Transaksi";}
  table td[data-label="Jumlah"]::before{content:"Jumlah";}
  table td[data-label="Tanggal"]::before{content:"Tanggal";}
}
</style>
</head>
<body>
<div class="container">
<h2>Data Transaksi Karyawan</h2>
<nav>
  <a href="karyawan.php">Dashboard</a>
  <a href="transfer.php">Transfer Antar Karyawan</a>
 </nav>

<table>
<thead>
<tr>
<th>ID Transaksi</th>
<th>Tabungan</th>
<th>Nama</th>
<th>Jabatan</th>
<th>Jenis Transaksi</th>
<th>Jumlah</th>
<th>Tanggal</th>
</tr>
</thead>
<tbody>
<?php if(mysqli_num_rows($result) > 0): ?>
<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
<td data-label="ID Transaksi"><?php echo $row['id_transaksi']; ?></td>
<td data-label="Tabungan"><?php echo $row['id_tabungan']; ?></td>
<td data-label="Nama"><?php echo htmlspecialchars($row['nama']); ?></td>
<td data-label="Jabatan"><?php echo htmlspecialchars($row['jabatan']); ?></td>
<td data-label="Jenis"><?php echo formatJenisTransaksi($row['jenis_transaksi'], $row['nama_lawan']); ?></td>
<td data-label="Jumlah">Rp <?php echo number_format($row['jumlah'],0,',','.'); ?></td>
<td data-label="Tanggal"><?php echo date('d-m-Y H:i', strtotime($row['tanggal'])); ?></td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="7" style="text-align:center;padding:20px;">Belum ada data transaksi</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</body>
</html>

