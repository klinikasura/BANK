<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['id_karyawan'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';
$struk = null;

$id_karyawan = $_SESSION['id_karyawan'];

/* =========================
   AMBIL DATA LOGIN
========================= */

$q_login = mysqli_query($koneksi, "
    SELECT 
        t.id_tabungan,
        t.saldo,
        k.nama
    FROM robotv80_tabungan t
    JOIN robotv80_karyawan k
    ON t.id_karyawan = k.id_karyawan
    WHERE t.id_karyawan = '$id_karyawan'
    LIMIT 1
");

$data_login = mysqli_fetch_assoc($q_login);

$id_tabungan_pengirim = $data_login['id_tabungan'];
$saldo_pengirim       = $data_login['saldo'];
$nama_pengirim        = $data_login['nama'];

/* =========================
   PROSES TRANSFER
========================= */

if(isset($_POST['submit'])){

    $id_tabungan_penerima = $_POST['id_tabungan_penerima'];

    $jumlah = str_replace('.', '', $_POST['jumlah']);
    $jumlah = (int)$jumlah;

    /* =========================
       VALIDASI
    ========================= */

    if($id_tabungan_pengirim == $id_tabungan_penerima){

        $error = "Tidak bisa transfer ke rekening sendiri.";

    }
    elseif($jumlah <= 0){

        $error = "Jumlah transfer tidak valid.";

    }
    elseif($saldo_pengirim < $jumlah){

        $error = "Saldo tidak cukup.";

    }
    else{

        /* =========================
           AMBIL DATA PENERIMA
        ========================= */

        $q_penerima = mysqli_query($koneksi, "
            SELECT 
                t.id_tabungan,
                t.saldo,
                k.nama
            FROM robotv80_tabungan t
            JOIN robotv80_karyawan k
            ON t.id_karyawan = k.id_karyawan
            WHERE t.id_tabungan = '$id_tabungan_penerima'
            LIMIT 1
        ");

        $penerima = mysqli_fetch_assoc($q_penerima);

        if(!$penerima){

            $error = "Penerima tidak ditemukan.";

        }else{

            /* =========================
               HITUNG SALDO BARU
            ========================= */

            $saldo_pengirim_baru = $saldo_pengirim - $jumlah;

            $saldo_penerima_baru = $penerima['saldo'] + $jumlah;

            /* =========================
               UPDATE SALDO PENGIRIM
            ========================= */

            mysqli_query($koneksi, "
                UPDATE robotv80_tabungan
                SET saldo = '$saldo_pengirim_baru'
                WHERE id_tabungan = '$id_tabungan_pengirim'
            ");

            /* =========================
               UPDATE SALDO PENERIMA
            ========================= */

            mysqli_query($koneksi, "
                UPDATE robotv80_tabungan
                SET saldo = '$saldo_penerima_baru'
                WHERE id_tabungan = '$id_tabungan_penerima'
            ");

            $tanggal = date('Y-m-d H:i:s');

            /* =========================
               SIMPAN TRANSAKSI
            ========================= */

            mysqli_query($koneksi, "
                INSERT INTO robotv80_transaksi
                (
                    id_tabungan,
                    jenis_transaksi,
                    jumlah,
                    tanggal,
                    transfer_ke
                )
                VALUES
                (
                    '$id_tabungan_pengirim',
                    'Transfer',
                    '$jumlah',
                    '$tanggal',
                    '{$penerima['nama']}'
                )
            ");

            $success = "Transfer berhasil.";

            /* =========================
               STRUK
            ========================= */

            $struk = [

                'pengirim' => $nama_pengirim,
                'penerima' => $penerima['nama'],
                'jumlah'   => $jumlah,
                'tanggal'  => $tanggal

            ];

        }

    }

}

/* =========================
   LIST PENERIMA
========================= */

$res = mysqli_query($koneksi, "
    SELECT 
        t.id_tabungan,
        t.saldo,
        k.nama
    FROM robotv80_tabungan t
    JOIN robotv80_karyawan k
    ON t.id_karyawan = k.id_karyawan
    WHERE t.id_karyawan != '$id_karyawan'
    ORDER BY k.nama ASC
");

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

body{
    margin:0;
    font-family:Arial;
    background:linear-gradient(135deg,#0f172a,#2563eb);
    padding:20px;
}

.container{
    max-width:500px;
    margin:auto;
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,0.2);
}

h2{
    text-align:center;
    margin-bottom:20px;
}

.info-box{
    background:#f1f5f9;
    padding:15px;
    border-radius:12px;
    margin-bottom:15px;
    line-height:1.8;
}

label{
    display:block;
    margin-top:12px;
    font-weight:bold;
}

select,
input{
    width:100%;
    padding:12px;
    border:1px solid #ccc;
    border-radius:10px;
    margin-top:5px;
    font-size:14px;
}

.btn{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    margin-top:15px;
    font-size:15px;
    cursor:pointer;
    font-weight:bold;
}

.btn-transfer{
    background:#2563eb;
    color:white;
}

.btn-transfer:hover{
    background:#1d4ed8;
}

.btn-back{
    display:block;
    text-align:center;
    text-decoration:none;
    background:#6b7280;
    color:white;
}

.btn-back:hover{
    background:#4b5563;
}

.alert{
    padding:12px;
    border-radius:10px;
    margin-bottom:15px;
}

.error{
    background:#fee2e2;
    color:#991b1b;
}

.success{
    background:#dcfce7;
    color:#166534;
}

.struk{
    margin-top:20px;
    padding:15px;
    border:2px dashed #000;
    border-radius:10px;
    background:#fafafa;
}

.struk h3{
    text-align:center;
    margin-bottom:15px;
}

.struk p{
    margin:8px 0;
}

.print-btn{
    width:100%;
    padding:12px;
    background:#16a34a;
    color:white;
    border:none;
    border-radius:10px;
    margin-top:15px;
    cursor:pointer;
    font-weight:bold;
}

.bottom-nav{
    position:fixed;
    bottom:0;
    left:0;
    right:0;
    background:white;
    display:flex;
    justify-content:space-around;
    padding:12px 0;
    box-shadow:0 -5px 20px rgba(0,0,0,0.1);
}

.bottom-nav a{
    text-decoration:none;
    font-size:24px;
}

@media(max-width:768px){

    body{
        padding:10px;
        padding-bottom:90px;
    }

    .container{
        padding:15px;
    }

}

</style>

</head>
<body>

<div class="container">

<h2>TRANSFER SALDO</h2>

<div class="info-box">

<b>Pengirim :</b> <?= $nama_pengirim; ?> <br>

<b>ID Tabungan :</b> <?= $id_tabungan_pengirim; ?> <br>

<b>Saldo :</b> Rp <?= number_format($saldo_pengirim,0,',','.'); ?>

</div>

<?php if($error){ ?>

<div class="alert error">
    <?= $error; ?>
</div>

<?php } ?>

<?php if($success){ ?>

<div class="alert success">
    <?= $success; ?>
</div>

<?php } ?>

<form method="POST">

<label>Pilih Penerima</label>

<select name="id_tabungan_penerima" required>

<option value="">-- Pilih Penerima --</option>

<?php while($o = mysqli_fetch_assoc($res)){ ?>

<option value="<?= $o['id_tabungan']; ?>">

<?= $o['id_tabungan']; ?>
-
<?= $o['nama']; ?>

| Saldo :
Rp <?= number_format($o['saldo'],0,',','.'); ?>

</option>

<?php } ?>

</select>

<label>Jumlah Transfer</label>

<input type="number" name="jumlah" required>

<button type="submit" name="submit" class="btn btn-transfer">
    TRANSFER SEKARANG
</button>

<a href="karyawan.php" class="btn btn-back">
    ← Kembali
</a>

</form>

<?php if($struk){ ?>

<div class="struk">

<h3>STRUK TRANSFER</h3>

<p>
<b>Pengirim :</b>
<?= $struk['pengirim']; ?>
</p>

<p>
<b>Penerima :</b>
<?= $struk['penerima']; ?>
</p>

<p>
<b>Jumlah :</b>
Rp <?= number_format($struk['jumlah'],0,',','.'); ?>
</p>

<p>
<b>Tanggal :</b>
<?= $struk['tanggal']; ?>
</p>

<button onclick="window.print()" class="print-btn">
    PRINT STRUK
</button>

</div>

<?php } ?>

</div>

<div class="bottom-nav">

<a href="karyawan.php">🏠</a>

<a href="transfer.php">💸</a>

<a href="data_transaksi_karyawan.php">📊</a>

<a href="edit_profile_karyawan.php">👤</a>

</div>

</body>
</html>
