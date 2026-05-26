<?php 
session_start(); 

if (!isset($_SESSION['id_admin'])) { 
    header('Location: login.php'); 
    exit;
} 

require 'koneksi.php';

/* =========================
   PAGINATION
========================= */

$batas = 10;

$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;

if ($halaman < 1) {
    $halaman = 1;
}

$mulai = ($halaman - 1) * $batas;

/* =========================
   SEARCH + FILTER
========================= */

$cari = isset($_GET['cari']) 
? mysqli_real_escape_string($koneksi, $_GET['cari']) 
: '';

$jenis_filter = isset($_GET['jenis']) 
? mysqli_real_escape_string($koneksi, $_GET['jenis']) 
: '';

/* =========================
   WHERE QUERY (FIXED)
========================= */

$where = "
(
    LOWER(k.nama) LIKE LOWER('%$cari%')
    OR LOWER(tr.jenis_transaksi) LIKE LOWER('%$cari%')
    OR tr.id_tabungan LIKE '%$cari%'
)
AND (
    '$jenis_filter' = ''
    OR LOWER(tr.jenis_transaksi) = LOWER('$jenis_filter')
)
";

/* =========================
   TOTAL DATA
========================= */

$query_total = "
SELECT COUNT(*) as total
FROM robotv80_transaksi tr
JOIN robotv80_tabungan tb ON tr.id_tabungan = tb.id_tabungan
JOIN robotv80_karyawan k ON tb.id_karyawan = k.id_karyawan
WHERE $where
";

$total_result = mysqli_query($koneksi, $query_total);
$total_data = mysqli_fetch_assoc($total_result)['total'];

$total_halaman = ceil($total_data / $batas);

/* =========================
   QUERY DATA
========================= */

$sql = "
SELECT 
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
WHERE $where
ORDER BY tr.id_transaksi DESC
LIMIT $mulai, $batas
";

$result = mysqli_query($koneksi, $sql);

if (!$result) {
    die("Query Error : " . mysqli_error($koneksi));
}

$no = $mulai + 1;
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>myROBOT-V80</title>
  <link href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png" rel="icon" type="image/png" />

<style>
body{
    font-family:Arial;
    background:#f0f2f5;
    margin:0;
}

.container{
    width:95%;
    margin:30px auto;
    background:#fff;
    padding:20px;
    border-radius:10px;
    box-shadow:0 0 15px rgba(0,0,0,0.2);
}

h2{text-align:center;}

.btn{
    padding:8px 15px;
    border-radius:5px;
    color:#fff;
    text-decoration:none;
    display:inline-block;
    margin:3px;
}

.btn-primary{background:#28a745;}
.btn-warning{background:#ffc107;color:#000;}
.btn-info{background:#17a2b8;}
.btn-dark{background:#343a40;}
.btn-secondary{background:#6c757d;}

.search-box{
    display:flex;
    gap:10px;
    margin:20px 0;
}

.search-box input,
.search-box select{
    flex:1;
    padding:10px;
}

.search-box button{
    padding:10px 20px;
    background:#007bff;
    color:white;
    border:none;
    cursor:pointer;
}

table{
    width:100%;
    border-collapse:collapse;
}

table th, table td{
    border:1px solid #ccc;
    padding:10px;
    text-align:center;
}

table th{
    background:#007bff;
    color:white;
}

.badge{
    padding:5px 10px;
    border-radius:5px;
    color:white;
    font-size:12px;
}

.badge-setor{background:#28a745;}
.badge-tarik{background:#dc3545;}
.badge-transfer{background:#17a2b8;}

.pagination{
    margin-top:20px;
    text-align:center;
}

.pagination a{
    padding:8px 12px;
    margin:3px;
    background:#007bff;
    color:white;
    text-decoration:none;
    border-radius:5px;
}

.pagination a.active{
    background:#28a745;
}
/* ================= BOTTOM NAV ================= */
.bottom-nav{
    position:fixed;
    bottom:0;
    left:0;
    right:0;
    background:white;
    display:flex;
    justify-content:space-around;
    padding:14px 0;
    box-shadow:0 -5px 20px rgba(0,0,0,0.08);
    border-top:1px solid #e2e8f0;
}

.bottom-nav a{
    text-decoration:none;
    font-size:24px;
    color:#0284c7;
    padding:10px 18px;
    border-radius:14px;
    transition:0.2s;
}

.bottom-nav a:active{
    background:#e0f2fe;
    transform:scale(0.95);
}
</style>

</head>

<body>

<div class="container">

<h2>Data Transaksi</h2>

<a href="tambah_transaksi.php" class="btn btn-primary">Tambah</a>
<a href="transfer_antar_karyawan.php" class="btn btn-info">Transfer</a>
<a href="riwayat_transaksi.php" class="btn btn-dark">Riwayat Transaksi</a>
<a href="riwayat_help.php" class="btn btn-dark">Riwayat E-Help</a>
<a href="dashboard_admin.php" class="btn btn-secondary">Kembali</a>

<!-- SEARCH + FILTER -->
<form method="GET" class="search-box">

<input type="text" name="cari"
placeholder="Cari nama / tabungan / jenis..."
value="<?= $cari; ?>">

<select name="jenis">
    <option value="">Semua Jenis</option>
    <option value="Setor" <?= ($jenis_filter=='Setor')?'selected':''; ?>>Setor</option>
    <option value="Tarik" <?= ($jenis_filter=='Tarik')?'selected':''; ?>>Tarik</option>
    <option value="Transfer" <?= ($jenis_filter=='Transfer')?'selected':''; ?>>Transfer</option>
</select>

<button type="submit">Cari</button>

</form>

<table>

<tr>
<th>No</th>
<th>ID</th>
<th>Nama Nasabah</th>
<th>Jenis Transaksi</th>
<th>Jumlah Nominal </th>
<th>Saldo Sekarang</th>
<th>Tanggal Transaksi</th>
</tr>

<?php if (mysqli_num_rows($result) > 0): ?>

<?php while ($row = mysqli_fetch_assoc($result)): ?>

<tr>

<td><?= $no++; ?></td>
<td><?= $row['id_transaksi']; ?></td>
<td><?= $row['id_tabungan']." - ".$row['nama']; ?></td>

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

<td>Rp <?= number_format($row['jumlah'],0,',','.'); ?></td>
<td>Rp <?= number_format($row['saldo'],0,',','.'); ?></td>
<td><?= date('d-m-Y H:i:s', strtotime($row['tanggal'])); ?></td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>
<td colspan="7">Belum ada data transaksi</td>
</tr>

<?php endif; ?>

</table>

<!-- PAGINATION -->
<div class="pagination">

<?php for ($i = 1; $i <= $total_halaman; $i++): ?>

<a href="?halaman=<?= $i; ?>&cari=<?= $cari; ?>&jenis=<?= $jenis_filter; ?>"
class="<?= ($i == $halaman) ? 'active' : ''; ?>">

<?= $i; ?>

</a>

<?php endfor; ?>

</div>

</div>

<!-- BOTTOM NAV -->
<div class="bottom-nav">

    <a href="dashboard_admin.php">🏠</a>
    <a href="data_transaksi.php">📊</a>
    <a href="data_nasabah.php">👥</a>
    <a href="logout.php">🚪</a>

</div>

</body>
</html>
