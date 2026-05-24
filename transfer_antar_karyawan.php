<?php
session_start();

if (!isset($_SESSION['id_admin'])) {
    header('Location: login.php');
    exit;
}

require 'koneksi.php';

$error = '';

if (isset($_POST['submit'])) {

    $id_tabungan_pengirim = $_POST['id_tabungan_pengirim'];
    $id_tabungan_penerima = $_POST['id_tabungan_penerima'];

    // hapus format titik
    $jumlah = str_replace('.', '', $_POST['jumlah']);
    $jumlah = (int)$jumlah;

    /* =========================
       VALIDASI
    ========================= */

    if ($jumlah <= 0) {

        $error = "⚠ Jumlah transfer harus lebih dari 0.";

    } elseif ($id_tabungan_pengirim == $id_tabungan_penerima) {

        $error = "⚠ Pengirim dan penerima tidak boleh sama.";

    } else {

        /* =========================
           DATA PENGIRIM
        ========================= */

        $sql_pengirim = "

        SELECT 
        tb.*,
        k.nama

        FROM robotv80_tabungan tb

        JOIN robotv80_karyawan k
        ON tb.id_karyawan = k.id_karyawan

        WHERE tb.id_tabungan = '$id_tabungan_pengirim'

        ";

        $result_pengirim = mysqli_query(
            $koneksi,
            $sql_pengirim
        );

        $row_pengirim = mysqli_fetch_assoc(
            $result_pengirim
        );

        /* =========================
           DATA PENERIMA
        ========================= */

        $sql_penerima = "

        SELECT 
        tb.*,
        k.nama

        FROM robotv80_tabungan tb

        JOIN robotv80_karyawan k
        ON tb.id_karyawan = k.id_karyawan

        WHERE tb.id_tabungan = '$id_tabungan_penerima'

        ";

        $result_penerima = mysqli_query(
            $koneksi,
            $sql_penerima
        );

        $row_penerima = mysqli_fetch_assoc(
            $result_penerima
        );

        if (!$row_pengirim || !$row_penerima) {

            $error = "⚠ Data tabungan tidak ditemukan.";

        } else {

            /* =========================
               CEK SALDO
            ========================= */

            if ($row_pengirim['saldo'] < $jumlah) {

                $error = "⚠ Saldo pengirim tidak cukup.";

            } else {

                mysqli_begin_transaction($koneksi);

                try {

                    /* =========================
                       NAMA
                    ========================= */

                    $nama_pengirim =
                    $row_pengirim['nama'];

                    $nama_penerima =
                    $row_penerima['nama'];

                    /* =========================
                       UPDATE SALDO
                    ========================= */

                    $saldo_pengirim_baru =
                    $row_pengirim['saldo'] - $jumlah;

                    $saldo_penerima_baru =
                    $row_penerima['saldo'] + $jumlah;

                    mysqli_query(

                        $koneksi,

                        "UPDATE robotv80_tabungan

                        SET saldo = '$saldo_pengirim_baru'

                        WHERE id_tabungan = '$id_tabungan_pengirim'"

                    );

                    mysqli_query(

                        $koneksi,

                        "UPDATE robotv80_tabungan

                        SET saldo = '$saldo_penerima_baru'

                        WHERE id_tabungan = '$id_tabungan_penerima'"

                    );

                    /* =========================
                       TRANSAKSI PENGIRIM
                    ========================= */

                    mysqli_query(

                        $koneksi,

                        "INSERT INTO robotv80_transaksi
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
                            NOW(),
                            '$nama_penerima'
                        )"

                    );

                    /* =========================
                       TRANSAKSI PENERIMA
                    ========================= */

                    mysqli_query(

                        $koneksi,

                        "INSERT INTO robotv80_transaksi
                        (
                            id_tabungan,
                            jenis_transaksi,
                            jumlah,
                            tanggal,
                            transfer_ke
                        )

                        VALUES
                        (
                            '$id_tabungan_penerima',
                            'Transfer Masuk',
                            '$jumlah',
                            NOW(),
                            '$nama_pengirim'
                        )"

                    );

                    mysqli_commit($koneksi);

                    header('Location: data_transaksi.php');

                    exit;

                } catch (Exception $e) {

                    mysqli_rollback($koneksi);

                    $error = "⚠ Transfer gagal.";
                }
            }
        }
    }
}
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

<link rel="stylesheet"
href="style-transfer.css">



<style>

.alert{

    background:#dc3545;

    color:white;

    padding:12px;

    border-radius:5px;

    margin-bottom:15px;
}
}

input[type="submit"]{

    background:#28a745;

    color:white;

    border:none;

    padding:10px 20px;

    border-radius:5px;

    cursor:pointer;
}

input[type="submit"]:hover{

    background:#218838;
}
/* =========================
   BODY
========================= */

body{
    margin:0;
    padding:0;
    font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;

    background:
    linear-gradient(
    135deg,
    #4facfe 0%,
    #00f2fe 100%
    );

    min-height:100vh;

    display:flex;

    justify-content:center;

    align-items:center;
}

/* =========================
   CONTAINER
========================= */

.container{

    width:90%;
    max-width:550px;

    background:#fff;

    padding:35px;

    border-radius:20px;

    box-shadow:
    0 15px 40px rgba(0,0,0,0.2);

    animation:fadeIn 0.5s ease;
}

/* =========================
   ANIMATION
========================= */

@keyframes fadeIn{

    from{
        opacity:0;
        transform:translateY(20px);
    }

    to{
        opacity:1;
        transform:translateY(0);
    }
}

/* =========================
   TITLE
========================= */

h2{

    text-align:center;

    margin-bottom:25px;

    color:#333;

    font-size:28px;

    font-weight:700;
}

/* =========================
   LABEL
========================= */

label{

    display:block;

    margin-bottom:8px;

    font-weight:600;

    color:#444;
}

/* =========================
   INPUT & SELECT
========================= */

select,
input[type="text"]{

    width:100%;

    padding:12px 15px;

    margin-bottom:18px;

    border:1px solid #ccc;

    border-radius:12px;

    font-size:15px;

    transition:0.3s;
}

select:focus,
input[type="text"]:focus{

    border-color:#4facfe;

    outline:none;

    box-shadow:
    0 0 10px rgba(79,172,254,0.3);
}

/* =========================
   BUTTON TRANSFER
========================= */

input[type="submit"]{

    background:
    linear-gradient(
    135deg,
    #28a745,
    #20c997
    );

    color:white;

    border:none;

    padding:12px 20px;

    border-radius:12px;

    cursor:pointer;

    font-size:16px;

    font-weight:600;

    transition:0.3s;

    box-shadow:
    0 8px 20px rgba(40,167,69,0.3);
}

input[type="submit"]:hover{

    transform:translateY(-2px);

    box-shadow:
    0 12px 25px rgba(40,167,69,0.4);
}

/* =========================
   BUTTON KEMBALI
========================= */

.btn-kembali{

    display:inline-block;

    background:
    linear-gradient(
    135deg,
    #6c757d,
    #495057
    );

    color:white;

    padding:12px 20px;

    border-radius:12px;

    text-decoration:none;

    margin-left:8px;

    font-weight:600;

    transition:0.3s;

    box-shadow:
    0 8px 20px rgba(0,0,0,0.2);
}

.btn-kembali:hover{

    transform:translateY(-2px);

    box-shadow:
    0 12px 25px rgba(0,0,0,0.3);
}

/* =========================
   ALERT
========================= */

.alert{

    background:
    linear-gradient(
    135deg,
    #ff416c,
    #ff4b2b
    );

    color:white;

    padding:14px;

    border-radius:12px;

    margin-bottom:20px;

    font-weight:600;

    box-shadow:
    0 8px 20px rgba(255,65,108,0.3);
}

/* =========================
   RESPONSIVE
========================= */

@media(max-width:600px){

    .container{

        padding:25px;
    }

    h2{

        font-size:24px;
    }

    input[type="submit"],
    .btn-kembali{

        width:100%;

        margin-top:10px;

        margin-left:0;
    }
}

</style>

</head>

<body>

<div class="container">

<h2>Transfer Antar Karyawan</h2>

<?php if($error != ''): ?>

<div class="alert">

<?= $error; ?>

</div>

<?php endif; ?>

<form action="" method="post">

<label>

Tabungan Pengirim

</label>

<select
name="id_tabungan_pengirim"
required>

<?php

$sql = "

SELECT 
t.id_tabungan,
k.nama

FROM robotv80_tabungan t

JOIN robotv80_karyawan k
ON t.id_karyawan = k.id_karyawan

ORDER BY k.nama ASC

";

$result = mysqli_query($koneksi, $sql);

while($row = mysqli_fetch_assoc($result)){

echo "

<option value='".$row['id_tabungan']."'>

".$row['id_tabungan']." - ".$row['nama']."

</option>

";

}

?>

</select>

<br><br>

<label>

Tabungan Penerima

</label>

<select
name="id_tabungan_penerima"
required>

<?php

$sql = "

SELECT 
t.id_tabungan,
k.nama

FROM robotv80_tabungan t

JOIN robotv80_karyawan k
ON t.id_karyawan = k.id_karyawan

ORDER BY k.nama ASC

";

$result = mysqli_query($koneksi, $sql);

while($row = mysqli_fetch_assoc($result)){

echo "

<option value='".$row['id_tabungan']."'>

".$row['id_tabungan']." - ".$row['nama']."

</option>

";

}

?>

</select>

<br><br>

<label>

Jumlah Transfer

</label>

<input
type="text"
id="jumlah"
name="jumlah"
required
placeholder="Masukkan jumlah transfer"
>

<br><br>

<input
type="submit"
name="submit"
value="Transfer"
class="btn-transfer"
>

<a href="data_transaksi.php"
class="btn-kembali">

Kembali

</a>

</form>

</div>

<script>

const jumlahInput =
document.getElementById('jumlah');

jumlahInput.addEventListener('input', function(){

let angka =
this.value.replace(/\D/g,'');

this.value =
angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

});

</script>

</body>
</html>
