<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['id_karyawan'])) {
    header("Location: login.php");
    exit;
}

$id = $_SESSION['id_karyawan'];
$error = '';
$success = '';

// AMBIL DATA
$data = mysqli_fetch_assoc(mysqli_query($koneksi,"
    SELECT * FROM robotv80_karyawan WHERE id_karyawan='$id'
"));

// UPDATE (HANYA NO_TLP)
if (isset($_POST['update'])) {

    $no_tlp = $_POST['no_tlp'];

    $update = mysqli_query($koneksi,"
        UPDATE robotv80_karyawan SET
        no_tlp='$no_tlp'
        WHERE id_karyawan='$id'
    ");

    if ($update) {
        $success = "Nomor telepon berhasil diupdate";

        $data = mysqli_fetch_assoc(mysqli_query($koneksi,"
            SELECT * FROM robotv80_karyawan WHERE id_karyawan='$id'
        "));
    } else {
        $error = "Gagal update data";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>myROBOT-V80</title>
  <link href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png" rel="icon" type="image/png" />

<style>
body{
    font-family: Arial;
    background: #f3f4f6;
    margin:0;
}

.container{
    max-width:500px;
    margin:40px auto;
    background:#fff;
    padding:20px;
    border-radius:12px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

h2{text-align:center}

label{
    display:block;
    margin-top:10px;
    font-weight:bold;
}

input{
    width:100%;
    padding:10px;
    margin-top:5px;
    border:1px solid #ddd;
    border-radius:8px;
}

input:disabled{
    background:#e5e7eb;
    color:#6b7280;
    cursor:not-allowed;
}

button{
    width:100%;
    margin-top:15px;
    padding:10px;
    background:#2563eb;
    color:#fff;
    border:none;
    border-radius:8px;
    cursor:pointer;
}

button:hover{
    background:#1d4ed8;
}

.alert{
    padding:10px;
    margin-top:10px;
    border-radius:8px;
}

.success{background:#dcfce7}
.error{background:#fee2e2}

.back{
    display:block;
    text-align:center;
    margin-top:10px;
    text-decoration:none;
    color:#555;
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
</style>
</head>

<body>

<div class="container">

<h2>Profile Nasabah</h2>

<?php if($error): ?>
<div class="alert error"><?= $error ?></div>
<?php endif; ?>

<?php if($success): ?>
<div class="alert success"><?= $success ?></div>
<?php endif; ?>

<form method="POST">

<label>Nama</label>
<input type="text" value="<?= htmlspecialchars($data['nama']) ?>" disabled>

<label>Jabatan</label>
<input type="text" value="<?= htmlspecialchars($data['jabatan']) ?>" disabled>

<label>Alamat</label>
<input type="text" value="<?= htmlspecialchars($data['alamat']) ?>" disabled>

<label>No Telepon</label>
<input type="text" name="no_tlp" value="<?= htmlspecialchars($data['no_tlp']) ?>">

<button type="submit" name="update">Simpan</button>

</form>

<a href="karyawan.php" class="back">← Kembali</a>

</div>

</body>
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
