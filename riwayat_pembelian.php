<?php
include "koneksi.php";

/* =========================
   SEARCH + PAGINATION
========================= */
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$offset = ($page - 1) * $limit;

/* =========================
   QUERY DATA (FIX JOIN)
========================= */
$query = "
SELECT 
    p.id_beli,
    p.jumlah,
    p.total,
    p.tanggal,

    k.nama AS nama_karyawan,
    k.jabatan,
    k.no_tlp,

    b.nama_barang

FROM robotv80_pembelian p

LEFT JOIN robotv80_karyawan k 
ON p.id_karyawan = k.id_karyawan

LEFT JOIN robotv80_barang b 
ON p.id_barang = b.id_barang

WHERE 
    k.nama LIKE '%$search%'
    OR k.jabatan LIKE '%$search%'
    OR b.nama_barang LIKE '%$search%'

ORDER BY p.id_beli DESC

LIMIT $limit OFFSET $offset
";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("SQL ERROR DATA: " . mysqli_error($koneksi));
}

/* =========================
   TOTAL DATA
========================= */
$totalQuery = "
SELECT COUNT(*) as total
FROM robotv80_pembelian p
LEFT JOIN robotv80_karyawan k ON p.id_karyawan = k.id_karyawan
LEFT JOIN robotv80_barang b ON p.id_barang = b.id_barang
WHERE 
    k.nama LIKE '%$search%'
    OR k.jabatan LIKE '%$search%'
    OR b.nama_barang LIKE '%$search%'
";

$totalResult = mysqli_query($koneksi, $totalQuery);

if (!$totalResult) {
    die("SQL ERROR TOTAL: " . mysqli_error($koneksi));
}

$totalRow = mysqli_fetch_assoc($totalResult);
$total = $totalRow['total'] ?? 0;

$totalPage = max(1, ceil($total / $limit));

/* =========================
   REKAP
========================= */
$rekap = mysqli_query($koneksi, "
SELECT 
    SUM(jumlah) AS total_jumlah,
    SUM(total) AS total_uang
FROM robotv80_pembelian
");

$r = mysqli_fetch_assoc($rekap);
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
    background:#f4f6f9;
    padding:20px;
}

.container{
    max-width:1100px;
    margin:auto;
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

h2{
    text-align:center;
}

/* search */
form{
    display:flex;
    gap:10px;
    margin-bottom:15px;
}

input{
    flex:1;
    padding:10px;
    border:1px solid #ddd;
    border-radius:8px;
}

button{
    padding:10px 14px;
    background:#2563eb;
    color:white;
    border:none;
    border-radius:8px;
    cursor:pointer;
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

/* rekap */
.rekap{
    background:#e0f2fe;
    padding:15px;
    border-radius:10px;
    margin-bottom:15px;
}

/* table */
table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#2563eb;
    color:white;
    padding:10px;
}

td{
    padding:10px;
    text-align:center;
    border-bottom:1px solid #eee;
}

/* pagination */
.pagination{
    margin-top:15px;
    text-align:center;
}

.pagination a{
    padding:8px 12px;
    margin:3px;
    background:#e5e7eb;
    text-decoration:none;
    border-radius:8px;
}

.pagination a.active{
    background:#2563eb;
    color:white;
}

/* print */
.print-btn{
    padding:10px 14px;
    background:#10b981;
    color:white;
    border:none;
    border-radius:8px;
    cursor:pointer;
    margin-bottom:10px;
}
body{
    font-family:Arial;
    background:#f4f6f9;
    padding:10px;
}

/* container */
.container{
    max-width:1100px;
    margin:auto;
    background:white;
    padding:15px;
    border-radius:12px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
    overflow-x:auto;
}

h2{
    text-align:center;
    font-size:20px;
}

/* search */
form{
    display:flex;
    gap:8px;
    margin-bottom:10px;
    flex-wrap:wrap;
}

input{
    flex:1;
    min-width:150px;
    padding:10px;
    border:1px solid #ddd;
    border-radius:8px;
}

button{
    padding:10px 14px;
    background:#2563eb;
    color:white;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-size:14px;
}

/* tombol cetak */
.print-btn{
    padding:10px 14px;
    background:#10b981;
    color:white;
    border:none;
    border-radius:8px;
    cursor:pointer;
    margin-bottom:10px;
    width:100%;
}

/* rekap */
.rekap{
    background:#e0f2fe;
    padding:12px;
    border-radius:10px;
    margin-bottom:10px;
    font-size:14px;
}

/* table */
table{
    width:100%;
    border-collapse:collapse;
    min-width:700px;
}

th{
    background:#2563eb;
    color:white;
    padding:10px;
    font-size:13px;
}

td{
    padding:10px;
    text-align:center;
    border-bottom:1px solid #eee;
    font-size:13px;
}

/* pagination */
.pagination{
    margin-top:10px;
    text-align:center;
}

.pagination a{
    display:inline-block;
    padding:8px 10px;
    margin:2px;
    background:#e5e7eb;
    text-decoration:none;
    border-radius:8px;
    font-size:13px;
}

.pagination a.active{
    background:#2563eb;
    color:white;
}

/* BOTTOM NAV (HP FIX) */
.bottom-nav{
    position:fixed;
    bottom:0;
    left:0;
    right:0;
    background:white;
    display:flex;
    justify-content:space-around;
    padding:10px 0;
    box-shadow:0 -5px 20px rgba(0,0,0,0.08);
    border-top:1px solid #e2e8f0;
}

.bottom-nav a{
    text-decoration:none;
    font-size:20px;
    color:#0284c7;
    padding:8px 12px;
    border-radius:12px;
}

/* PRINT */
@media print{
    form, .print-btn, .pagination, .bottom-nav{
        display:none;
    }

    body{
        background:white;
    }

    .container{
        box-shadow:none;
        border:none;
    }
}

/* RESPONSIVE HP */
@media (max-width:768px){

    h2{
        font-size:18px;
    }

    table{
        min-width:600px;
    }

    .rekap{
        font-size:13px;
    }

    button{
        width:100%;
    }
}

@media print{
    form, .print-btn, .pagination{
        display:none;
    }
}
</style>

</head>
<body>

<div class="container">

<h2>RIWAYAT PEMBELIAN</h2>

<!-- SEARCH -->
<form method="GET">
    <input type="text" name="search" placeholder="Cari karyawan / barang..." value="<?= htmlspecialchars($search); ?>">
    <button type="submit">Cari</button>
</form>

<!-- PRINT -->
<button class="print-btn" onclick="window.print()">🖨️ CETAK PDF</button>

<!-- REKAP -->
<div class="rekap">
    <b>Total Barang Dibeli :</b> <?= $r['total_jumlah'] ?? 0; ?> <br>
    <b>Total Uang Keluar :</b> Rp <?= number_format($r['total_uang'] ?? 0,2); ?>
</div>

<!-- TABLE -->
<table>
<tr>
    <th>ID</th>
    <th>Karyawan</th>
    <th>Jabatan</th>
    <th>No HP</th>
    <th>Barang</th>
    <th>Jumlah</th>
    <th>Total</th>
    <th>Tanggal</th>
</tr>

<?php
if(mysqli_num_rows($result) > 0){
    while($row = mysqli_fetch_assoc($result)){
?>

<tr>
    <td><?= $row['id_beli']; ?></td>
    <td><?= $row['nama_karyawan'] ?? '-'; ?></td>
    <td><?= $row['jabatan'] ?? '-'; ?></td>
    <td><?= $row['no_tlp'] ?? '-'; ?></td>
    <td><?= $row['nama_barang'] ?? '-'; ?></td>
    <td><?= $row['jumlah']; ?></td>
    <td>Rp <?= number_format($row['total'],2); ?></td>
    <td><?= $row['tanggal']; ?></td>
</tr>

<?php
    }
}else{
    echo "<tr><td colspan='8'>Data tidak ditemukan</td></tr>";
}
?>

</table>

<!-- PAGINATION -->
<div class="pagination">

<?php if($page > 1){ ?>
<a href="?page=<?= $page-1 ?>&search=<?= urlencode($search); ?>">Prev</a>
<?php } ?>

<span>Page <?= $page ?> / <?= $totalPage ?></span>

<?php if($page < $totalPage){ ?>
<a href="?page=<?= $page+1 ?>&search=<?= urlencode($search); ?>">Next</a>
<?php } ?>

</div>

</div>

<p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>

<!-- BOTTOM NAV -->
<div class="bottom-nav">

    <a href="dashboard_admin.php">🏠</a>
    <a href="data_transaksi.php">📊</a>
    <a href="data_nasabah.php">👥</a>
    <a href="logout.php">🚪</a>

</div

</body>
</html>
