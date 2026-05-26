<?php
session_start();
include "koneksi.php";

/* =========================
   CEK LOGIN
========================= */
if(!isset($_SESSION['id_karyawan'])){
    header("Location: login.php");
    exit;
}

/* =========================
   SESSION
========================= */
$id_karyawan = $_SESSION['id_karyawan'];

$success = "";
$error   = "";

/* =========================
   AMBIL USER
========================= */
$getUser = mysqli_query($koneksi, "
SELECT nama, no_tlp, jabatan
FROM robotv80_karyawan
WHERE id_karyawan = '$id_karyawan'
LIMIT 1
");

$user = mysqli_fetch_assoc($getUser);

$nama    = $user['nama'];
$no_tlp  = $user['no_tlp'];
$jabatan = $user['jabatan'];

/* =========================
   SIMPAN PESAN
========================= */
if(isset($_POST['kirim'])){

    $pesan = mysqli_real_escape_string($koneksi, $_POST['pesan']);

    if(empty($pesan)){
        $error = "Pesan tidak boleh kosong.";
    }else{

        $sql = "
        INSERT INTO robotv80_help
        (
            id_karyawan,
            no_tlp,
            pesan
        )
        VALUES
        (
            '$id_karyawan',
            '$no_tlp',
            '$pesan'
        )
        ";

        $simpan = mysqli_query($koneksi, $sql);

        if($simpan){
            $success = "Pesan berhasil dikirim.";
        }else{
            $error = mysqli_error($koneksi);
        }
    }
}

/* =========================
   PAGINATION SETTING
========================= */
$limit = 5;

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

/* =========================
   HISTORY HELP (PAGINATION)
========================= */
$history = mysqli_query($koneksi, "
SELECT 
    h.*,
    k.nama,
    k.jabatan
FROM robotv80_help h
JOIN robotv80_karyawan k
ON h.id_karyawan = k.id_karyawan
WHERE h.id_karyawan = '$id_karyawan'
ORDER BY h.id_help DESC
LIMIT $limit OFFSET $offset
");

/* =========================
   TOTAL PAGE
========================= */
$totalData = mysqli_query($koneksi, "
SELECT COUNT(*) as total 
FROM robotv80_help 
WHERE id_karyawan = '$id_karyawan'
");

$totalRow = mysqli_fetch_assoc($totalData);
$totalPage = ceil($totalRow['total'] / $limit);

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>myROBOT-V80</title>

<link rel="icon" type="image/png"
href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING%20APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png">>

<style>
body{
    font-family:Arial;
    background:#f1f5f9;
    padding:15px;
    padding-bottom:100px;
}

.container{
    max-width:700px;
    margin:auto;
    background:white;
    padding:20px;
    border-radius:18px;
}

h2{text-align:center;}

.profile{
    background:#eef2ff;
    padding:15px;
    border-radius:12px;
    margin-bottom:20px;
}

textarea{
    width:100%;
    height:130px;
    padding:15px;
    border-radius:12px;
    border:1px solid #ddd;
}

.btn{
    width:100%;
    padding:14px;
    border:none;
    border-radius:12px;
    margin-top:10px;
    font-weight:bold;
    cursor:pointer;
}

.btn-kirim{background:#2563eb;color:white;}

.btn-back{
    display:block;
    text-align:center;
    margin-top:10px;
    background:#ef4444;
    color:white;
    padding:14px;
    border-radius:12px;
    text-decoration:none;
}

.alert{
    padding:14px;
    border-radius:12px;
    margin-bottom:10px;
}

.success{background:#dcfce7;color:#166534;}
.error{background:#fee2e2;color:#991b1b;}

.card{
    background:#f9fafb;
    padding:15px;
    border-radius:12px;
    margin-bottom:12px;
}

.nama{font-weight:bold;}
.jabatan{font-size:13px;color:#6b7280;}

.tanggal{
    font-size:12px;
    color:#9ca3af;
    margin-top:10px;
}

.pagination{
    margin-top:20px;
    text-align:center;
}

.pagination a{
    padding:10px 14px;
    background:#2563eb;
    color:white;
    text-decoration:none;
    border-radius:10px;
    margin:5px;
    display:inline-block;
}
/* ===== BOTTOM NAV UPGRADE ===== */
.bottom-nav{
    position:fixed;
    bottom:0;
    left:0;
    right:0;
    background:white;
    display:flex;
    justify-content:space-around;
    padding:14px 0;
    border-top:1px solid #e5e7eb;
    box-shadow:0 -5px 20px rgba(0,0,0,0.08);
}

.bottom-nav a{
    text-decoration:none;
    font-size:22px;
    padding:10px 18px;
    border-radius:14px;
    transition:0.2s;
}

.bottom-nav a:active{
    background:#e0e7ff;
    transform:scale(0.95);
}

.pagination a.prev{
    background:#ef4444;
}
</style>

</head>
<body>

<div class="container">

<h2>HELP CENTER</h2>

<div class="profile">
<b>Nama :</b> <?= $nama; ?><br>
<b>Jabatan :</b> <?= $jabatan; ?><br>
<b>No HP :</b> <?= $no_tlp; ?>
</div>

<?php if($success){ ?>
<div class="alert success"><?= $success; ?></div>
<?php } ?>

<?php if($error){ ?>
<div class="alert error"><?= $error; ?></div>
<?php } ?>

<form method="POST">

<textarea name="pesan" required placeholder="Tulis pesan bantuan..."></textarea>

<button type="submit" name="kirim" class="btn btn-kirim">
KIRIM PESAN
</button>

<a href="karyawan.php" class="btn-back">KEMBALI</a>

</form>

<h3 style="margin-top:20px;">Riwayat Pesan</h3>

<?php while($row = mysqli_fetch_assoc($history)){ ?>

<div class="card">

<div class="nama"><?= $row['nama']; ?></div>
<div class="jabatan"><?= $row['jabatan']; ?></div>

<div><?= nl2br($row['pesan']); ?></div>

<div class="tanggal">
ID HELP: <?= $row['id_help']; ?><br>
No HP: <?= $row['no_tlp']; ?>
</div>

</div>

<?php } ?>

<!-- PAGINATION -->
<div class="pagination">

<?php if($page > 1){ ?>
<a class="prev" href="?page=<?= $page-1 ?>">⬅ Prev</a>
<?php } ?>

<span style="padding:10px;">
Halaman <?= $page ?> / <?= $totalPage ?>
</span>

<?php if($page < $totalPage){ ?>
<a href="?page=<?= $page+1 ?>">Next ➡</a>
<?php } ?>

</div>

</div>

   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>


<!-- BOTTOM NAV (BESAR + PREMIUM) -->
<div class="bottom-nav">
    <a href="karyawan.php">🏠</a>
    <a href="transfer.php">💸</a>
    <a href="data_transaksi_karyawan.php">📊</a>
    <a href="edit_profile_karyawan.php">👤</a>
</div>

</body>
</html>
