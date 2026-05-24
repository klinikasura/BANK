<?php
session_start();
if (!isset($_SESSION['id_karyawan']) || $_SESSION['level'] != 'karyawan') {
    header('Location: login.php');
    exit;
}

require 'koneksi.php';
/* =========================
   PAGINATION
========================= */

$batas = 10;

$halaman = isset($_GET['halaman'])
? (int)$_GET['halaman']
: 1;

if($halaman < 1){
    $halaman = 1;
}

$mulai = ($halaman - 1) * $batas;

/* =========================
   PENCARIAN
========================= */

$cari = isset($_GET['cari'])
? mysqli_real_escape_string($koneksi, $_GET['cari'])
: '';

/* =========================
   TOTAL DATA
========================= */

$query_total = "

SELECT COUNT(*) as total

FROM robotv80_transaksi tr

JOIN robotv80_tabungan tb
ON tr.id_tabungan = tb.id_tabungan

JOIN robotv80_karyawan k
ON tb.id_karyawan = k.id_karyawan

WHERE

k.nama LIKE '%$cari%'
OR k.jabatan LIKE '%$cari%'
OR tr.jenis_transaksi LIKE '%$cari%'
OR tr.id_tabungan LIKE '%$cari%'
OR tr.transfer_ke LIKE '%$cari%'

";

$total_result = mysqli_query($koneksi, $query_total);

$total_data = mysqli_fetch_assoc($total_result)['total'];

$total_halaman = ceil($total_data / $batas);

/* =========================
   QUERY DATA
========================= */

$sql = "

SELECT 

tr.*,
tr.transfer_ke,

k.nama,
k.jabatan

FROM robotv80_transaksi tr

JOIN robotv80_tabungan tb
ON tr.id_tabungan = tb.id_tabungan

JOIN robotv80_karyawan k
ON tb.id_karyawan = k.id_karyawan

WHERE

k.nama LIKE '%$cari%'
OR k.jabatan LIKE '%$cari%'
OR tr.jenis_transaksi LIKE '%$cari%'
OR tr.id_tabungan LIKE '%$cari%'
OR tr.transfer_ke LIKE '%$cari%'

ORDER BY tr.id_transaksi DESC

LIMIT $mulai, $batas

";

$result = mysqli_query($koneksi, $sql);

if(!$result){
    die(mysqli_error($koneksi));
}

/* =========================
   NOMOR URUT
========================= */

$no = $mulai + 1;

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>myROBOT-V80</title>

<link href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png"
rel="icon"
type="image/png" />

<style>

body{
    font-family:Arial,sans-serif;
    background:#f0f2f5;
    margin:0;
    padding:0;
}

.container{

    width:95%;

    margin:30px auto;

    background:#fff;

    padding:20px;

    border-radius:10px;

    box-shadow:0 0 15px rgba(0,0,0,0.2);
}

h2{

    text-align:center;

    margin-bottom:20px;

    color:#333;
}

/* =========================
   BUTTON
========================= */

.btn-kembali{

    display:inline-block;

    text-decoration:none;

    background:#007bff;

    color:white;

    padding:10px 15px;

    border-radius:5px;

    margin-bottom:15px;
}

.btn-kembali:hover{

    opacity:0.8;
}

.btn-cetak{

    display:inline-block;

    text-decoration:none;

    background:#28a745;

    color:white;

    padding:10px 15px;

    border-radius:5px;

    margin-bottom:15px;

    margin-left:5px;
}

.btn-cetak:hover{

    opacity:0.8;
}

/* =========================
   SEARCH
========================= */

.search-box{

    display:flex;

    gap:10px;

    margin-bottom:20px;
}

.search-box input{

    flex:1;

    padding:10px;

    border:1px solid #ccc;

    border-radius:5px;
}

.search-box button{

    padding:10px 20px;

    border:none;

    background:#28a745;

    color:white;

    border-radius:5px;

    cursor:pointer;
}

/* =========================
   TABLE
========================= */

table{

    width:100%;

    border-collapse:collapse;
}

table th,
table td{

    border:1px solid #ccc;

    padding:10px;

    text-align:center;
}

table th{

    background:#007bff;

    color:white;
}

/* =========================
   PAGINATION
========================= */

.pagination{

    margin-top:20px;

    text-align:center;
}

.pagination a{

    text-decoration:none;

    padding:8px 12px;

    margin:3px;

    background:#007bff;

    color:white;

    border-radius:5px;

    display:inline-block;
}

.pagination a.active{

    background:#28a745;
}

.pagination a:hover{

    opacity:0.8;
}

/* =========================
   PRINT
========================= */

@media print {

    .btn-kembali,
    .btn-cetak,
    .search-box,
    .pagination {

        display:none;
    }

    body{

        background:white;
    }

    .container{

        box-shadow:none;

        width:100%;
    }

}

</style>

</head>

<body>

<div class="container">

<h2>Riwayat Transaksi</h2>

<a href="http://10.10.20.250/dashboard/ROBOT-BANK/karyawan.php"
class="btn-kembali">

Kembali

</a>

<a href="#"
onclick="window.print()"
class="btn-cetak">

Cetak

</a>

<!-- =========================
     SEARCH
========================= -->

<form method="GET"
class="search-box">

<input
type="text"
name="cari"

placeholder="Cari nama, jabatan, jenis transaksi..."

value="<?= $cari; ?>"
>

<button type="submit">

Cari

</button>

</form>

<table>

<tr>

<th>No</th>
<th>ID Transaksi</th>
<th>Tabungan</th>
<th>Nama</th>
<th>Jabatan</th>
<th>Jenis</th>
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

$keterangan =
$row['nama'] .
' melakukan transfer kepada ' .
$row['transfer_ke'];

} elseif($jenis == 'tarik') {

$keterangan =
$row['nama'] .
' menarik uang';

} elseif($jenis == 'setor') {

$keterangan =
$row['nama'] .
' menyetor uang';

} else {

$keterangan =
$row['nama'] .
' melakukan ' .
$row['jenis_transaksi'];

}

?>

<tr>

<td>

<?= $no++; ?>

</td>

<td>

<?= $row['id_transaksi']; ?>

</td>

<td>

<?= $row['id_tabungan']; ?>

</td>

<td>

<?= $row['nama']; ?>

</td>

<td>

<?= $row['jabatan']; ?>

</td>

<td>

<?= ucfirst($row['jenis_transaksi']); ?>

</td>

<td>

Rp
<?= number_format(
$row['jumlah'],
0,
',',
'.'
); ?>

</td>

<td>

<?= date(
'd-m-Y H:i:s',
strtotime($row['tanggal'])
); ?>

</td>

<td>

<?= $keterangan; ?>

</td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="9">

Belum ada data transaksi

</td>

</tr>

<?php endif; ?>

</table>

<!-- =========================
     PAGINATION
========================= -->

<div class="pagination">

<?php for($i = 1; $i <= $total_halaman; $i++) { ?>

<a
href="?halaman=<?= $i; ?>&cari=<?= $cari; ?>"

class="<?=
($i == $halaman)
? 'active'
: '';
?>"

>

<?= $i; ?>

</a>

<?php } ?>

</div>

</div>

</body>
</html>
