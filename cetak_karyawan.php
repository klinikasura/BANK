<?php
include 'koneksi.php';

$cari = isset($_GET['cari']) ? mysqli_real_escape_string($koneksi, $_GET['cari']) : '';

$data = mysqli_query($koneksi, "
SELECT * FROM robot80_data_anggota
WHERE 
nama LIKE '%$cari%'
OR posisi LIKE '%$cari%'
OR username LIKE '%$cari%'
OR no_induk LIKE '%$cari%'
ORDER BY id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>myROBOT-V80</title>

<link href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png"
rel="icon"
type="image/png" />

<style>
body{
    font-family:Arial;
    padding:20px;
}

h2{
    text-align:center;
    margin-bottom:10px;
}

.info{
    text-align:center;
    font-size:12px;
    margin-bottom:15px;
}

table{
    width:100%;
    border-collapse:collapse;
    font-size:10px;
}

th,td{
    border:1px solid #000;
    padding:6px;
    text-align:left;
    vertical-align:top;
}

th{
    background:#ddd;
    font-size:11px;
}

.print-btn{
    padding:10px 14px;
    background:#2563eb;
    color:white;
    border:none;
    cursor:pointer;
    margin-bottom:10px;
}

@media print{
    .print-btn{
        display:none;
    }
}
</style>

</head>

<body>

<h2>REKAP DATA KARYAWAN</h2>

<div class="info">
Tanggal Cetak: <?= date('d-m-Y H:i'); ?>
</div>

<button class="print-btn" onclick="window.print()">
🖨️ PRINT / SAVE PDF
</button>

<table>

<tr>
    <th>No</th>
    <th>No Induk</th>
    <th>Nama</th>
    <th>Username</th>
    <th>JK</th>
    <th>Posisi</th>
    <th>TTL</th>
    <th>Alamat</th>
    <th>No HP</th>
    <th>Status</th>
    <th>SIP</th>
    <th>STR</th>
    <th>Pendidikan</th>
    <th>Masuk Kerja</th>
</tr>

<?php
$no = 1;
while($d = mysqli_fetch_assoc($data)){
?>

<tr>
    <td><?= $no++; ?></td>
    <td><?= $d['no_induk']; ?></td>
    <td><?= $d['nama']; ?></td>
    <td><?= $d['username']; ?></td>
    <td><?= $d['jk']; ?></td>
    <td><?= $d['posisi']; ?></td>
    <td><?= $d['ttl']; ?></td>
    <td><?= $d['alamat2']; ?></td>
    <td><?= $d['hp']; ?></td>
    <td><?= $d['status']; ?></td>
    <td><?= $d['sip']; ?></td>
    <td><?= $d['str']; ?></td>
    <td><?= $d['pendidikan']; ?></td>
    <td><?= $d['masuk_kerja']; ?></td>
</tr>

<?php } ?>

</table>

<script>
window.onload = function(){
    window.print();
}
</script>

</body>
</html>
