<?php

session_start();

if (!isset($_SESSION['id_karyawan']) || $_SESSION['level'] != 'karyawan') {
    header("Location: login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| KONEKSI DATABASE
|--------------------------------------------------------------------------
*/

$host = "10.10.20.250";
$user = "root";
$pass = "";
$db   = "sikdraisyah";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal : " . mysqli_connect_error());
}

/*
|--------------------------------------------------------------------------
| SESSION LOGIN
|--------------------------------------------------------------------------
*/

$id_karyawan = $_SESSION['id_karyawan'];

/*
|--------------------------------------------------------------------------
| FILTER TANGGAL
|--------------------------------------------------------------------------
*/

$dari   = isset($_GET['dari']) ? $_GET['dari'] : '';
$sampai = isset($_GET['sampai']) ? $_GET['sampai'] : '';

$where_tanggal = "";

if(!empty($dari) && !empty($sampai)){

    $where_tanggal = "
    AND DATE(t.tanggal)
    BETWEEN '$dari' AND '$sampai'
    ";

}

/*
|--------------------------------------------------------------------------
| PAGINATION
|--------------------------------------------------------------------------
*/

$batas = 5;

$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;

if($halaman < 1){
    $halaman = 1;
}

$mulai = ($halaman - 1) * $batas;

/*
|--------------------------------------------------------------------------
| TOTAL DATA
|--------------------------------------------------------------------------
*/

$total_query = mysqli_query($koneksi, "

SELECT COUNT(*) as total

FROM robotv80_transaksi t

LEFT JOIN robotv80_tabungan tb
ON t.id_tabungan = tb.id_tabungan

WHERE 
tb.id_karyawan = '$id_karyawan'
OR t.transfer_ke = (
    SELECT nama 
    FROM robotv80_karyawan 
    WHERE id_karyawan = '$id_karyawan'
)

$where_tanggal

");

$total_data = mysqli_fetch_assoc($total_query);

$total = $total_data['total'];

$total_halaman = ceil($total / $batas);

/*
|--------------------------------------------------------------------------
| QUERY DATA TRANSAKSI
|--------------------------------------------------------------------------
|
| transfer_ke sekarang berisi NAMA TUJUAN
|
*/

$query = mysqli_query($koneksi, "

SELECT

    t.id_transaksi,
    t.id_tabungan,
    t.jenis_transaksi,
    t.jumlah,
    t.transfer_ke,
    t.tanggal,

    tb.id_karyawan,
    tb.saldo,

    pengirim.nama AS nama_pengirim,
    pengirim.jabatan,
    pengirim.no_tlp,

    tujuan.nama AS nama_tujuan

FROM robotv80_transaksi t

LEFT JOIN robotv80_tabungan tb
ON t.id_tabungan = tb.id_tabungan

/*
|--------------------------------------------------------------------------
| DATA PENGIRIM
|--------------------------------------------------------------------------
*/

LEFT JOIN robotv80_karyawan pengirim
ON tb.id_karyawan = pengirim.id_karyawan

/*
|--------------------------------------------------------------------------
| DATA TUJUAN TRANSFER
|--------------------------------------------------------------------------
|
| transfer_ke = nama karyawan
|
*/

LEFT JOIN robotv80_karyawan tujuan
ON t.transfer_ke = tujuan.nama

WHERE

tb.id_karyawan = '$id_karyawan'

OR t.transfer_ke = (
    SELECT nama 
    FROM robotv80_karyawan 
    WHERE id_karyawan = '$id_karyawan'
)

$where_tanggal

ORDER BY t.id_transaksi DESC

LIMIT $mulai, $batas

");

/*
|--------------------------------------------------------------------------
| NOTIFIKASI TRANSAKSI TERBARU
|--------------------------------------------------------------------------
*/

$cek_notif = mysqli_query($koneksi, "

SELECT *

FROM robotv80_transaksi t

LEFT JOIN robotv80_tabungan tb
ON t.id_tabungan = tb.id_tabungan

WHERE
tb.id_karyawan = '$id_karyawan'

OR t.transfer_ke = (
    SELECT nama 
    FROM robotv80_karyawan 
    WHERE id_karyawan = '$id_karyawan'
)

ORDER BY t.id_transaksi DESC

LIMIT 1

");

$notif = mysqli_fetch_assoc($cek_notif);

/*
|--------------------------------------------------------------------------
| AMBIL NAMA LOGIN
|--------------------------------------------------------------------------
*/

$get_user = mysqli_query($koneksi, "
SELECT nama
FROM robotv80_karyawan
WHERE id_karyawan = '$id_karyawan'
");

$user_login = mysqli_fetch_assoc($get_user);

$nama_login = $user_login['nama'];

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>myROBOT-V80</title>

<link rel="icon" type="image/png"
href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING%20APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png">

<meta http-equiv="refresh" content="30">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#edf1f7;
    font-family:Arial, sans-serif;
    padding:15px;
}

/* =========================
   CONTAINER
========================= */

.container{
    width:100%;
    background:white;
    border-radius:15px;
    padding:15px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
    overflow:hidden;
}

h2{
    margin-bottom:20px;
    color:#333;
    font-size:22px;
}

/* =========================
   FILTER
========================= */

.filter-box{
    background:#f8f9fa;
    padding:15px;
    border-radius:10px;
    margin-bottom:20px;
}

.filter-box form{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    align-items:center;
}

.filter-box label{
    font-size:14px;
    font-weight:bold;
}

input[type=date]{
    padding:10px;
    border:1px solid #ccc;
    border-radius:8px;
    width:180px;
}

/* =========================
   BUTTON
========================= */

button{
    padding:10px 18px;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:bold;
    transition:0.2s;
}

button:hover{
    opacity:0.9;
}

.btn-filter{
    background:#007bff;
    color:white;
}

.btn-print{
    background:#28a745;
    color:white;
}

.btn-reset{
    background:#dc3545;
    color:white;
    text-decoration:none;
    padding:10px 18px;
    border-radius:8px;
    font-size:14px;
}

/* =========================
   TABLE
========================= */

.table-wrapper{
    width:100%;
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
    min-width:800px;
}

table th{
    background:#007bff;
    color:white;
    padding:14px;
    border:1px solid #ddd;
    font-size:14px;
}

table td{
    padding:12px;
    border:1px solid #ddd;
    font-size:14px;
}

table tr:nth-child(even){
    background:#f9f9f9;
}

table tr:hover{
    background:#eef5ff;
}

/* =========================
   STATUS
========================= */

.setor{
    color:green;
    font-weight:bold;
}

.tarik{
    color:red;
    font-weight:bold;
}

.transfer{
    color:orange;
    font-weight:bold;
}

.badge{
    background:#28a745;
    color:white;
    padding:6px 12px;
    border-radius:20px;
    font-size:12px;
    display:inline-block;
}

.transfer-box{
    background:#ffc107;
    color:black;
    padding:6px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:bold;
    display:inline-block;
}

.masuk{
    background:#17a2b8;
    color:white;
    padding:5px 10px;
    border-radius:20px;
    font-size:11px;
    font-weight:bold;
    display:inline-block;
}

.keluar{
    background:#dc3545;
    color:white;
    padding:5px 10px;
    border-radius:20px;
    font-size:11px;
    font-weight:bold;
    display:inline-block;
}

.nominal{
    color:#007bff;
    font-weight:bold;
}

.kosong{
    text-align:center;
    color:red;
    padding:20px;
}

/* =========================
   PAGINATION
========================= */

.pagination{
    margin-top:25px;
    display:flex;
    justify-content:center;
    gap:8px;
    flex-wrap:wrap;
}

.pagination a{
    text-decoration:none;
    padding:10px 15px;
    background:#007bff;
    color:white;
    border-radius:8px;
    font-size:14px;
    transition:0.2s;
}

.pagination a:hover{
    background:#0056b3;
}

.pagination .active{
    background:#28a745;
}

/* =========================
   FOOTER
========================= */

.footer{
    margin-top:20px;
    text-align:right;
}

/* =========================
   BOTTOM NAVIGATION
========================= */

.bottom-nav{
    position:fixed;
    bottom:0;
    left:0;
    right:0;
    background:white;
    display:flex;
    justify-content:space-around;
    align-items:center;
    padding:10px 0;
    border-top:1px solid #ddd;
    box-shadow:0 -5px 20px rgba(0,0,0,0.08);
    z-index:999;
}

.bottom-nav a{
    text-decoration:none;
    font-size:24px;
    padding:10px;
    border-radius:12px;
    transition:0.2s;
}

.bottom-nav a:hover{
    background:#f1f1f1;
}

.bottom-nav a:active{
    background:#e0e7ff;
    transform:scale(0.92);
}
.table-wrapper{
    width:100%;
    overflow-x:auto;
    -webkit-overflow-scrolling:touch;
}

table{
    min-width:900px;
}

/* =========================
   RESPONSIVE MOBILE
========================= */

@media(max-width:768px){

    body{
        padding:10px;
        padding-bottom:90px;
    }

    .container{
        padding:12px;
        border-radius:12px;
    }

    h2{
        font-size:18px;
        text-align:center;
    }

    .filter-box form{
        flex-direction:column;
        align-items:stretch;
    }

    input[type=date]{
        width:100%;
    }

    .btn-filter,
    .btn-reset,
    .btn-print{
        width:100%;
        text-align:center;
    }

    table th,
    table td{
        font-size:12px;
        padding:10px;
    }

    .pagination a{
        padding:8px 12px;
        font-size:12px;
    }

    .bottom-nav a{
        font-size:22px;
    }

    .footer{
        text-align:center;
    }

}

/* =========================
   PRINT
========================= */

@media print{

    .filter-box,
    .footer,
    .pagination,
    .bottom-nav{
        display:none;
    }

    body{
        background:white;
        padding:0;
    }

    .container{
        box-shadow:none;
    }

}


</style>

</head>
<body>

<div class="container">

<h2>RIWAYAT TRANSAKSI NASABAH</h2>

<div class="filter-box">

<form method="GET">

<label>Dari :</label>

<input type="date" name="dari" value="<?= $dari; ?>">

<label>Sampai :</label>

<input type="date" name="sampai" value="<?= $sampai; ?>">

<button type="submit" class="btn-filter">
    Filter
</button>

<a href="data_transaksi_karyawan.php" class="btn-reset">
    Reset
</a>

<a href="karyawan.php" class="btn-reset">
    Kembali
</a>

</form>

</div>
<div class="table-wrapper">
<table>

<tr>

<th>No</th>
<th>ID</th>
<th>Pengirim</th>
<th>Jenis</th>
<th>Jumlah</th>
<th>Transfer Ke</th>
<th>Status</th>
<th>Tanggal</th>

</tr>

<?php

if(mysqli_num_rows($query) > 0){

    $no = $mulai + 1;

    while($data = mysqli_fetch_array($query)){

        $jenis = strtolower($data['jenis_transaksi']);

?>

<tr>

<td><?= $no++; ?></td>

<td><?= $data['id_transaksi']; ?></td>

<td>

<span class="badge">
    <?= $data['nama_pengirim']; ?>
</span>

</td>

<td class="<?= $jenis; ?>">
    <?= $data['jenis_transaksi']; ?>
</td>

<td class="nominal">
    Rp <?= number_format($data['jumlah'],0,',','.'); ?>
</td>

<td>

<?php

if($data['jenis_transaksi'] == 'Transfer'){

    echo "
    <span class='transfer-box'>
        ".$data['transfer_ke']."
    </span>
    ";

}else{

    echo "-";

}

?>

</td>

<td>

<?php

/*
|--------------------------------------------------------------------------
| STATUS TRANSFER
|--------------------------------------------------------------------------
*/

if(
    $data['jenis_transaksi'] == 'Transfer'
    &&
    $data['transfer_ke'] == $nama_login
){

    echo "
    <span class='masuk'>
        Transfer Masuk
    </span>
    ";

}else if($data['jenis_transaksi'] == 'Transfer'){

    echo "
    <span class='keluar'>
        Transfer Keluar
    </span>
    ";

}else{

    echo "-";

}

?>

</td>

<td>
    <?= date('d-m-Y H:i:s', strtotime($data['tanggal'])); ?>
</td>

</tr>

<?php

    }

}else{

?>

<tr>

<td colspan="8" class="kosong">
    Data transaksi tidak ditemukan
</td>

</tr>

<?php } ?>

</table>

<!-- PAGINATION -->

<div class="pagination">

<?php if($halaman > 1){ ?>

<a href="?halaman=<?= $halaman-1; ?>&dari=<?= $dari; ?>&sampai=<?= $sampai; ?>">
    Prev
</a>

<?php } ?>

<?php

for($i=1; $i <= $total_halaman; $i++){

    $active = ($i == $halaman) ? 'active' : '';

    echo "
    <a class='$active'
    href='?halaman=$i&dari=$dari&sampai=$sampai'>
        $i
    </a>
    ";

}

?>

<?php if($halaman < $total_halaman){ ?>

<a href="?halaman=<?= $halaman+1; ?>&dari=<?= $dari; ?>&sampai=<?= $sampai; ?>">
    Next
</a>

<?php } ?>

</div>

<div class="footer">

<button onclick="window.print()" class="btn-print">
    Cetak
</button>

</div>

</div>

<script>

window.onload = function(){

    let transaksiBaru = "<?= $notif['id_transaksi']; ?>";

    let transaksiLama = localStorage.getItem("last_transaksi");

    if(transaksiBaru != transaksiLama){

        let jenis  = "<?= $notif['jenis_transaksi']; ?>";
        let jumlah = "<?= number_format($notif['jumlah'],0,',','.'); ?>";

        alert(
            "🔔 TRANSAKSI BARU\n\n" +
            "Jenis : " + jenis + "\n" +
            "Jumlah : Rp " + jumlah
        );

        localStorage.setItem("last_transaksi", transaksiBaru);

    }

}

</script>

<!-- =========================
     BOTTOM NAV
========================= -->

<div class="bottom-nav">

    <a href="karyawan.php">🏠</a>

    <a href="transfer.php">💸</a>

    <a href="data_transaksi_karyawan.php">📊</a>

    <a href="edit_profile_karyawan.php">👤</a>

</div>

</body>
</html>
