<?php
session_start();
if (!isset($_SESSION['id_karyawan']) || $_SESSION['level'] != 'karyawan') {
  header('Location: login.php');
  exit;
}

require 'koneksi.php';

$id_karyawan = $_SESSION['id_karyawan'];

/* =========================
   JOIN TABUNGAN + KARYAWAN
========================= */
$sql = "
SELECT 
    t.id_tabungan,
    t.saldo,
    t.tanggal,
    k.nama,
    k.jabatan,
    k.alamat,
    k.no_tlp

FROM robotv80_tabungan t
JOIN robotv80_karyawan k 
    ON t.id_karyawan = k.id_karyawan

WHERE t.id_karyawan = '$id_karyawan'
";

$result = mysqli_query($koneksi, $sql);
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>myROBOT-V80</title>

<link href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png"
rel="icon" type="image/png" />

<style>
/* RESET */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* BACKGROUND */
body {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(135deg, #dbeafe, #bfdbfe, #93c5fd);
  padding: 20px;
}

/* CONTAINER */
.container {
  width: 100%;
  max-width: 950px;
  background: #ffffffcc;
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 25px;
  box-shadow: 0 10px 30px rgba(59, 130, 246, 0.25);
  color: #1e3a8a;
}

/* TITLE */
h2 {
  text-align: center;
  margin-bottom: 15px;
  font-size: 24px;
  color: #1d4ed8;
}

/* INFO BOX */
.info-box {
  background: #eff6ff;
  padding: 15px;
  border-radius: 12px;
  margin-bottom: 15px;
  line-height: 1.6;
}

/* TABLE */
table {
  width: 100%;
  border-collapse: collapse;
  border-radius: 12px;
  overflow: hidden;
}

th {
  background: #60a5fa;
  color: white;
  padding: 12px;
}

td {
  padding: 12px;
  text-align: center;
}

tr:nth-child(even) {
  background: #eff6ff;
}

/* BUTTON */
.btn-kembali, .btn-cetak {
  display: inline-block;
  margin-top: 20px;
  padding: 10px 18px;
  color: white;
  text-decoration: none;
  border-radius: 10px;
  font-weight: bold;
  transition: 0.3s ease;
}

.btn-kembali {
  background: #3b82f6;
}

.btn-kembali:hover {
  background: #2563eb;
  transform: translateY(-2px);
}

.btn-cetak {
  background: #10b981;
  margin-left: 10px;
}

.btn-cetak:hover {
  background: #059669;
  transform: translateY(-2px);
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

.container {
  max-width:1100px;
  margin:40px auto;
  background:rgba(255,255,255,0.12);
  backdrop-filter:blur(14px);
  padding:25px;
  border-radius:18px;
}

nav a {
  margin:5px;
  padding:10px 14px;
  background:rgba(255,255,255,0.15);
  color:#fff;
  text-decoration:none;
  border-radius:10px;
}

/* PRINT */
@media print {
  .btn-kembali, .btn-cetak {
    display: none;
  }

  body {
    background: white;
  }

  .container {
    box-shadow: none;
    background: white;
  }
}
</style>
</head>

<body>

<div class="container">

<h2>Rekening Tabungan</h2>

<?php if ($row = mysqli_fetch_assoc($result)) { ?>

<!-- INFO KARYAWAN -->
<div class="info-box">
  <b>Nama :</b> <?= $row['nama']; ?><br>
  <b>Jabatan :</b> <?= $row['jabatan']; ?><br>
  <b>Alamat :</b> <?= $row['alamat']; ?><br>
  <b>No HP (Password) :</b> <?= $row['no_tlp']; ?><br>
</div>

<table>
<tr>
  <th>ID Tabungan (Username)</th>
  <th>Saldo Sekarang</th>
  <th>Tanggal Pembuatan</th>
</tr>

<?php do { ?>
<tr>
  <td><?= $row['id_tabungan']; ?></td>
  <td>Rp <?= number_format($row['saldo'],0,',','.'); ?></td>
  <td><?= date('d-m-Y', strtotime($row['tanggal'])); ?></td>
</tr>
<?php } while ($row = mysqli_fetch_assoc($result)); ?>

</table>

<?php } else { ?>
<p style="text-align:center;">Tidak ada data</p>
<?php } ?>

<!-- BUTTON -->
<a href="karyawan.php" class="btn-kembali">← Kembali</a>
<a href="#" onclick="window.print()" class="btn-cetak">🖨 Cetak</a>

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
