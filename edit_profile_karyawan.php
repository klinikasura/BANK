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
</style>
</head>

<body>

<div class="container">

<h2>Profile Karyawan</h2>

<?php if($error): ?>
<div class="alert error"><?= $error ?></div>
<?php endif; ?>

<?php if($success): ?>
<div class="alert success"><?= $success ?></div>
<?php endif; ?>

<form method="POST">

<label>Nama (Tidak bisa diubah)</label>
<input type="text" value="<?= htmlspecialchars($data['nama']) ?>" disabled>

<label>Jabatan (Tidak bisa diubah)</label>
<input type="text" value="<?= htmlspecialchars($data['jabatan']) ?>" disabled>

<label>Alamat (Tidak bisa diubah)</label>
<input type="text" value="<?= htmlspecialchars($data['alamat']) ?>" disabled>

<label>No Telepon (Bisa diedit)</label>
<input type="text" name="no_tlp" value="<?= htmlspecialchars($data['no_tlp']) ?>">

<button type="submit" name="update">Simpan</button>

</form>

<a href="karyawan.php" class="back">← Kembali</a>

</div>

</body>
</html>
