<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['id_admin']) && !isset($_SESSION['id_karyawan'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';
$struk = null;

if (isset($_POST['submit'])) {
    $id_tabungan_pengirim = $_POST['id_tabungan_pengirim'];
    $id_tabungan_penerima = $_POST['id_tabungan_penerima'];

    $jumlah = str_replace(['.', ','], '', $_POST['jumlah']);
    $jumlah = (int)$jumlah;

    if ($id_tabungan_pengirim == $id_tabungan_penerima) {
        $error = "Tabungan tidak boleh sama.";
    } elseif ($jumlah <= 0) {
        $error = "Jumlah harus lebih dari 0.";
    } else {

        // CEK PENGIRIM
        $q = mysqli_query($koneksi, "SELECT saldo FROM robotv80_tabungan WHERE id_tabungan='$id_tabungan_pengirim'");
        $pengirim = mysqli_fetch_assoc($q);

        if (!$pengirim) {
            $error = "Pengirim tidak ditemukan.";
        } elseif ($pengirim['saldo'] < $jumlah) {
            $error = "Saldo tidak cukup.";
        } else {

            // CEK PENERIMA
            $q = mysqli_query($koneksi, "SELECT saldo FROM robotv80_tabungan WHERE id_tabungan='$id_tabungan_penerima'");
            $penerima = mysqli_fetch_assoc($q);

            if (!$penerima) {
                $error = "Penerima tidak ditemukan.";
            } else {

                $saldo_pengirim_baru = $pengirim['saldo'] - $jumlah;
                $saldo_penerima_baru = $penerima['saldo'] + $jumlah;

                mysqli_query($koneksi, "UPDATE robotv80_tabungan SET saldo='$saldo_pengirim_baru' WHERE id_tabungan='$id_tabungan_pengirim'");
                mysqli_query($koneksi, "UPDATE robotv80_tabungan SET saldo='$saldo_penerima_baru' WHERE id_tabungan='$id_tabungan_penerima'");

                $tanggal = date('Y-m-d H:i:s');

                mysqli_query($koneksi, "INSERT INTO robotv80_transaksi (id_tabungan, jenis_transaksi, jumlah, tanggal)
                VALUES ('$id_tabungan_pengirim','Transfer Keluar','$jumlah','$tanggal')");

                mysqli_query($koneksi, "INSERT INTO robotv80_transaksi (id_tabungan, jenis_transaksi, jumlah, tanggal)
                VALUES ('$id_tabungan_penerima','Transfer Masuk','$jumlah','$tanggal')");

                // AMBIL NAMA PENGIRIM
                $q1 = mysqli_query($koneksi, "
                    SELECT k.nama 
                    FROM robotv80_tabungan t 
                    JOIN robotv80_karyawan k ON t.id_karyawan = k.id_karyawan
                    WHERE t.id_tabungan='$id_tabungan_pengirim'
                ");
                $nama_pengirim = mysqli_fetch_assoc($q1)['nama'] ?? '-';

                // AMBIL NAMA PENERIMA
                $q2 = mysqli_query($koneksi, "
                    SELECT k.nama 
                    FROM robotv80_tabungan t 
                    JOIN robotv80_karyawan k ON t.id_karyawan = k.id_karyawan
                    WHERE t.id_tabungan='$id_tabungan_penerima'
                ");
                $nama_penerima = mysqli_fetch_assoc($q2)['nama'] ?? '-';

                $success = "Transfer berhasil.";

                $struk = [
                    'pengirim' => $nama_pengirim,
                    'penerima' => $nama_penerima,
                    'jumlah'   => $jumlah,
                    'tanggal'  => $tanggal,
                    'id_pengirim' => $id_tabungan_pengirim,
                    'id_penerima' => $id_tabungan_penerima
                ];
            }
        }
    }
}

// AMBIL TABUNGAN
function getTabunganOptions($koneksi, $filter = null) {
    $sql = "SELECT t.id_tabungan, k.nama
            FROM robotv80_tabungan t
            JOIN robotv80_karyawan k ON t.id_karyawan=k.id_karyawan";

    if ($filter) {
        $sql .= " WHERE t.id_karyawan='$filter'";
    }

    $sql .= " ORDER BY k.nama ASC";

    $res = mysqli_query($koneksi, $sql);

    $data = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $data[] = $row;
    }
    return $data;
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
    background: linear-gradient(135deg,#0f172a,#2563eb);
    margin:0;
}

.container{
    max-width:500px;
    margin:40px auto;
    background:#fff;
    padding:25px;
    border-radius:15px;
}

h2{text-align:center}

label{font-weight:bold; display:block; margin-top:10px}

select,input{
    width:100%;
    padding:10px;
    margin-top:5px;
    border-radius:8px;
    border:1px solid #ccc;
}

button,input[type=submit]{
    width:100%;
    margin-top:15px;
    padding:10px;
    background:#2563eb;
    color:#fff;
    border:none;
    border-radius:8px;
    cursor:pointer;
}

button:hover,input[type=submit]:hover{
    background:#1e40af;
}

.alert{
    padding:10px;
    margin-top:10px;
    border-radius:8px;
}

.error{background:#fee2e2}
.success{background:#dcfce7}

#struk{
    margin-top:15px;
    padding:15px;
    border:1px dashed #000;
    background:#f9f9f9;
}
.btn-back{
    display:block;
    text-align:center;
    margin-top:10px;
    padding:10px;
    background:#6b7280;
    color:white;
    text-decoration:none;
    border-radius:8px;
    transition:0.3s;
}

.btn-back:hover{
    background:#4b5563;
    transform: translateY(-2px);
}
</style>
</head>

<body>

<div class="container">
<h2>Transfer Antar Karyawan</h2>

<?php if($error): ?>
<div class="alert error"><?= $error ?></div>
<?php endif; ?>

<?php if($success): ?>
<div class="alert success"><?= $success ?></div>
<?php endif; ?>

<form method="POST">

<label>Pengirim</label>
<select name="id_tabungan_pengirim" required>
<?php
$opt = isset($_SESSION['id_admin'])
    ? getTabunganOptions($koneksi)
    : getTabunganOptions($koneksi, $_SESSION['id_karyawan']);

foreach($opt as $o){
    echo "<option value='{$o['id_tabungan']}'>{$o['id_tabungan']} - {$o['nama']}</option>";
}
?>
</select>

<label>Penerima</label>
<select name="id_tabungan_penerima" required>
<?php
$opt2 = getTabunganOptions($koneksi);
foreach($opt2 as $o){
    echo "<option value='{$o['id_tabungan']}'>{$o['id_tabungan']} - {$o['nama']}</option>";
}
?>
</select>

<label>Jumlah</label>
<input type="number" name="jumlah" required>

<input type="submit" name="submit" value="Transfer">
<a href="data_transaksi_karyawan.php" class="btn-back">← Kembali</a>
</form>

<?php if($struk): ?>
<div id="struk">
<h3>STRUK TRANSFER</h3>
<hr>
<p>Tanggal: <?= $struk['tanggal'] ?></p>
<p>Pengirim: <?= $struk['pengirim'] ?> (<?= $struk['id_pengirim'] ?>)</p>
<p>Penerima: <?= $struk['penerima'] ?> (<?= $struk['id_penerima'] ?>)</p>
<p>Jumlah: Rp <?= number_format($struk['jumlah'],0,',','.') ?></p>
<hr>
<p style="text-align:center">✔ BERHASIL</p>

<button onclick="printStruk()">PRINT</button>
</div>
<?php endif; ?>

</div>

<script>
function printStruk(){
    let printContent = document.getElementById('struk').innerHTML;
    let original = document.body.innerHTML;

    document.body.innerHTML = printContent;
    window.print();
    document.body.innerHTML = original;
    location.reload();
}
</script>

</body>
</html>
