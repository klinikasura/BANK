<?php
session_start();
include "koneksi.php";

/* =========================
   CEK LOGIN
========================= */

if (!isset($_SESSION['id_karyawan'])) {
    header("Location: login.php");
    exit;
}

$id_karyawan = $_SESSION['id_karyawan'];

/* =========================
   SEARCH
========================= */

$search = $_GET['search'] ?? "";

/* =========================
   PAGINATION
========================= */

$limit = 5;

$page = $_GET['page'] ?? 1;

if($page < 1){
    $page = 1;
}

$offset = ($page - 1) * $limit;

/* =========================
   QUERY DATA LOGIN SAJA
========================= */

$sql = "

SELECT 
    p.*, 
    b.nama_barang,
    k.nama AS nama_karyawan,
    k.jabatan

FROM robotv80_pembelian p

JOIN robotv80_barang b 
ON p.id_barang = b.id_barang

JOIN robotv80_karyawan k 
ON p.id_karyawan = k.id_karyawan

WHERE 
p.id_karyawan = '$id_karyawan'

AND
(
    b.nama_barang LIKE '%$search%'
    OR k.nama LIKE '%$search%'
)

ORDER BY p.tanggal DESC

LIMIT $limit OFFSET $offset

";

$data = mysqli_query($koneksi, $sql);

if(!$data){
    die("Query Error : " . mysqli_error($koneksi));
}

/* =========================
   TOTAL DATA
========================= */

$total_query = mysqli_query($koneksi, "

SELECT COUNT(*) as total

FROM robotv80_pembelian p

JOIN robotv80_barang b 
ON p.id_barang = b.id_barang

JOIN robotv80_karyawan k 
ON p.id_karyawan = k.id_karyawan

WHERE 
p.id_karyawan = '$id_karyawan'

AND
(
    b.nama_barang LIKE '%$search%'
    OR k.nama LIKE '%$search%'
)

");

$total_data = mysqli_fetch_assoc($total_query);

$total = $total_data['total'];

$totalPage = ceil($total / $limit);

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>myROBOT-V80</title>

<link rel="icon" type="image/png"
href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING%20APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:Arial;
    background:#f3f4f6;
    padding:10px;
    padding-bottom:100px;
}

/* =========================
   CONTAINER
========================= */

.container{
    width:100%;
    background:white;
    padding:15px;
    border-radius:15px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);
}

/* =========================
   TITLE
========================= */

h2{
    text-align:center;
    margin-bottom:15px;
    color:#111827;
}

/* =========================
   BUTTON ATAS
========================= */

.top-btn{
    display:flex;
    gap:10px;
    margin-bottom:15px;
    flex-wrap:wrap;
}

.top-btn a{
    flex:1;
    text-align:center;
    padding:12px;
    border-radius:10px;
    text-decoration:none;
    color:white;
    font-weight:bold;
}

.btn-back{
    background:#ef4444;
}

.btn-reset{
    background:#22c55e;
}

/* =========================
   SEARCH
========================= */

.search-box{
    display:flex;
    gap:10px;
    margin-bottom:15px;
}

.search-box input{
    flex:1;
    padding:12px;
    border-radius:10px;
    border:1px solid #ddd;
}

.search-box button{
    padding:12px 15px;
    border:none;
    border-radius:10px;
    background:#4f46e5;
    color:white;
    cursor:pointer;
}

/* =========================
   PRINT BUTTON
========================= */

.print-btn{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background:#10b981;
    color:white;
    font-weight:bold;
    margin-bottom:15px;
    cursor:pointer;
}

/* =========================
   TABLE RESPONSIVE
========================= */

.table-wrapper{
    overflow-x:auto;
    width:100%;
}

table{
    width:100%;
    border-collapse:collapse;
    min-width:750px;
}

th{
    background:#4f46e5;
    color:white;
    padding:12px;
    font-size:13px;
}

td{
    padding:12px;
    border-bottom:1px solid #eee;
    text-align:center;
    font-size:13px;
}

tr:hover td{
    background:#f9fafb;
}

/* =========================
   PAGINATION
========================= */

.pagination{
    margin-top:20px;
    display:flex;
    justify-content:center;
    gap:8px;
    flex-wrap:wrap;
}

.pagination a{
    padding:10px 14px;
    background:#4f46e5;
    color:white;
    border-radius:8px;
    text-decoration:none;
    font-size:13px;
}

.pagination .active{
    background:#22c55e;
}

/* =========================
   EMPTY DATA
========================= */

.kosong{
    text-align:center;
    color:red;
    padding:20px;
}

/* =========================
   BOTTOM NAV
========================= */

.bottom-nav{
    position:fixed;
    bottom:0;
    left:0;
    right:0;
    background:white;
    display:flex;
    justify-content:space-around;
    padding:12px 0;
    border-top:1px solid #e5e7eb;
    box-shadow:0 -5px 20px rgba(0,0,0,0.08);
    z-index:999;
}

.bottom-nav a{
    text-decoration:none;
    font-size:24px;
    padding:10px 16px;
    border-radius:14px;
}

.bottom-nav a:active{
    background:#e0e7ff;
    transform:scale(0.95);
}

/* =========================
   MOBILE
========================= */

@media(max-width:768px){

    body{
        padding:8px;
        padding-bottom:100px;
    }

    .container{
        padding:12px;
    }

    th,
    td{
        font-size:12px;
        padding:10px;
    }

}

</style>

</head>
<body>

<div class="container">

<h2>RIWAYAT PEMBELIAN SAYA</h2>

<!-- BUTTON -->
<div class="top-btn">

<a href="karyawan.php" class="btn-back">
⬅ Kembali
</a>

<a href="pembelian.php" class="btn-reset">
🔁 Beli Lagi
</a>

</div>

<!-- SEARCH -->
<form class="search-box" method="GET">

<input type="text"
name="search"
placeholder="Cari barang..."
value="<?= $search; ?>">

<button type="submit">
Cari
</button>

</form>

<!-- PRINT -->
<button class="print-btn" onclick="window.print()">
🖨 Cetak Data
</button>

<!-- TABLE -->
<div class="table-wrapper">

<table>

<tr>

<th>ID</th>
<th>Nama</th>
<th>Jabatan</th>
<th>Barang</th>
<th>Jumlah</th>
<th>Total</th>
<th>Tanggal</th>

</tr>

<?php

if(mysqli_num_rows($data) > 0){

    while($row = mysqli_fetch_assoc($data)){

?>

<tr>

<td><?= $row['id_beli']; ?></td>

<td><?= $row['nama_karyawan']; ?></td>

<td><?= $row['jabatan']; ?></td>

<td><?= $row['nama_barang']; ?></td>

<td><?= $row['jumlah']; ?></td>

<td>
Rp <?= number_format($row['total'],0,',','.'); ?>
</td>

<td><?= $row['tanggal']; ?></td>

</tr>

<?php

    }

}else{

?>

<tr>

<td colspan="7" class="kosong">
    Data pembelian tidak ditemukan
</td>

</tr>

<?php } ?>

</table>

</div>

<!-- PAGINATION -->

<div class="pagination">

<?php if($page > 1){ ?>

<a href="?page=<?= $page-1; ?>&search=<?= $search; ?>">
Prev
</a>

<?php } ?>

<?php

for($i=1; $i <= $totalPage; $i++){

    $active = ($i == $page) ? 'active' : '';

    echo "
    <a class='$active'
    href='?page=$i&search=$search'>
        $i
    </a>
    ";

}

?>

<?php if($page < $totalPage){ ?>

<a href="?page=<?= $page+1; ?>&search=<?= $search; ?>">
Next
</a>

<?php } ?>

</div>

</div>

<!-- BOTTOM NAV -->

<div class="bottom-nav">

<a href="karyawan.php">🏠</a>

<a href="transfer.php">💸</a>

<a href="data_transaksi_karyawan.php">📊</a>

<a href="edit_profile_karyawan.php">👤</a>

</div>

</body>
</html>
