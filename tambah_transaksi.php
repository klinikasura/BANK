<?php
session_start();

if (!isset($_SESSION['id_admin'])) {
    header('Location: login.php');
    exit;
}

require 'koneksi.php';

$error = '';

/* =====================================
   SIMPAN TRANSAKSI
===================================== */

if (isset($_POST['submit'])) {

    $id_tabungan = $_POST['id_tabungan'];

    $jenis_transaksi =
    $_POST['jenis_transaksi'];

    // hapus format rupiah
    $jumlah =
    str_replace('.', '', $_POST['jumlah']);

    $jumlah = (int)$jumlah;

    /* =====================================
       VALIDASI JUMLAH
    ===================================== */

    if ($jumlah <= 0) {

        $error =
        "Nominal harus lebih besar dari 0.";

    } else {

        /* =====================================
           SETOR
        ===================================== */

        if ($jenis_transaksi == 'Setor') {

            mysqli_begin_transaction($koneksi);

            try {

                mysqli_query(

                    $koneksi,

                    "INSERT INTO robotv80_transaksi
                    (
                        id_tabungan,
                        jenis_transaksi,
                        jumlah,
                        tanggal
                    )

                    VALUES
                    (
                        '$id_tabungan',
                        'Setor',
                        '$jumlah',
                        NOW()
                    )"

                );

                mysqli_query(

                    $koneksi,

                    "UPDATE robotv80_tabungan

                    SET saldo = saldo + '$jumlah'

                    WHERE id_tabungan = '$id_tabungan'"

                );

                mysqli_commit($koneksi);

                header('Location: data_transaksi.php');

                exit;

            } catch (Exception $e) {

                mysqli_rollback($koneksi);

                $error = "Gagal melakukan setor.";
            }
        }

        /* =====================================
           TARIK
        ===================================== */

        elseif ($jenis_transaksi == 'Tarik') {

            $cek = mysqli_query(

                $koneksi,

                "SELECT saldo

                FROM robotv80_tabungan

                WHERE id_tabungan='$id_tabungan'"

            );

            $row = mysqli_fetch_assoc($cek);

            if ($row['saldo'] < $jumlah) {

                $error =
                "Saldo tidak cukup untuk tarik.";

            } else {

                mysqli_begin_transaction($koneksi);

                try {

                    mysqli_query(

                        $koneksi,

                        "INSERT INTO robotv80_transaksi
                        (
                            id_tabungan,
                            jenis_transaksi,
                            jumlah,
                            tanggal
                        )

                        VALUES
                        (
                            '$id_tabungan',
                            'Tarik',
                            '$jumlah',
                            NOW()
                        )"

                    );

                    mysqli_query(

                        $koneksi,

                        "UPDATE robotv80_tabungan

                        SET saldo = saldo - '$jumlah'

                        WHERE id_tabungan = '$id_tabungan'"

                    );

                    mysqli_commit($koneksi);

                    header('Location: data_transaksi.php');

                    exit;

                } catch (Exception $e) {

                    mysqli_rollback($koneksi);

                    $error = "Gagal tarik uang.";
                }
            }
        }

        /* =====================================
           TRANSFER
        ===================================== */

        elseif ($jenis_transaksi == 'Transfer') {

            $id_tabungan_penerima =
            $_POST['id_tabungan_penerima'];

            if ($id_tabungan ==
                $id_tabungan_penerima) {

                $error =
                "Pengirim dan penerima tidak boleh sama.";

            } else {

                /* =========================
                   DATA PENGIRIM
                ========================= */

                $qp = mysqli_query(

                    $koneksi,

                    "SELECT
                    tb.*,
                    k.nama

                    FROM robotv80_tabungan tb

                    JOIN robotv80_karyawan k
                    ON tb.id_karyawan = k.id_karyawan

                    WHERE tb.id_tabungan =
                    '$id_tabungan'"

                );

                $pengirim =
                mysqli_fetch_assoc($qp);

                /* =========================
                   DATA PENERIMA
                ========================= */

                $qr = mysqli_query(

                    $koneksi,

                    "SELECT
                    tb.*,
                    k.nama

                    FROM robotv80_tabungan tb

                    JOIN robotv80_karyawan k
                    ON tb.id_karyawan = k.id_karyawan

                    WHERE tb.id_tabungan =
                    '$id_tabungan_penerima'"

                );

                $penerima =
                mysqli_fetch_assoc($qr);

                if (!$pengirim || !$penerima) {

                    $error =
                    "Data tabungan tidak ditemukan.";

                } else {

                    if ($pengirim['saldo'] < $jumlah) {

                        $error =
                        "Saldo tidak cukup untuk transfer.";

                    } else {

                        mysqli_begin_transaction($koneksi);

                        try {

                            $nama_pengirim =
                            $pengirim['nama'];

                            $nama_penerima =
                            $penerima['nama'];

                            /* =========================
                               UPDATE SALDO
                            ========================= */

                            mysqli_query(

                                $koneksi,

                                "UPDATE robotv80_tabungan

                                SET saldo = saldo - '$jumlah'

                                WHERE id_tabungan =
                                '$id_tabungan'"

                            );

                            mysqli_query(

                                $koneksi,

                                "UPDATE robotv80_tabungan

                                SET saldo = saldo + '$jumlah'

                                WHERE id_tabungan =
                                '$id_tabungan_penerima'"

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
                                    '$id_tabungan',
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

                            $error =
                            "Transfer gagal.";
                        }
                    }
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

<style>

body{

    font-family:Arial;

    background:#f0f2f5;

    margin:0;

    padding:0;
}

.container{

    width:500px;

    margin:40px auto;

    background:#fff;

    padding:25px 30px;

    border-radius:10px;

    box-shadow:0 0 15px rgba(0,0,0,0.2);
}

h2{

    text-align:center;

    margin-bottom:20px;
}

label{

    display:block;

    margin-bottom:5px;

    font-weight:bold;
}

select,
input[type="text"]{

    width:100%;

    padding:10px;

    margin-bottom:15px;

    border-radius:5px;

    border:1px solid #ccc;
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

.btn-kembali{

    background:#6c757d;

    color:white;

    padding:10px 20px;

    border-radius:5px;

    text-decoration:none;

    margin-left:5px;
}

.alert{

    background:#dc3545;

    color:white;

    padding:10px;

    border-radius:5px;

    margin-bottom:15px;
}

</style>

</head>

<body>

<div class="container">

<h2>Tambah Transaksi</h2>

<?php if(isset($error)){ ?>

<div class="alert">

<?= $error; ?>

</div>

<?php } ?>

<form action="" method="post">

<label>

ID Tabungan

</label>

<select
id="id_tabungan"
name="id_tabungan"
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

<label>

Jenis Transaksi

</label>

<select
id="jenis_transaksi"
name="jenis_transaksi"
required>

<option value="Setor">

Setor

</option>

<option value="Tarik">

Tarik

</option>

<option value="Transfer">

Transfer

</option>

</select>

<div id="transfer-penerima"
style="display:none;">

<label>

Tabungan Penerima

</label>

<select
name="id_tabungan_penerima">

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

</div>

<label>

Jumlah

</label>

<input
type="text"
id="jumlah"
name="jumlah"
placeholder="Contoh: 1.000.000"
required
>

<input
type="submit"
name="submit"
value="Simpan"
>

<a href="data_transaksi.php"
class="btn-kembali">

Kembali

</a>

</form>

</div>

<script>

/* =========================
   FORMAT RUPIAH
========================= */

const jumlah =
document.getElementById('jumlah');

jumlah.addEventListener('input', function(){

let angka =
this.value.replace(/\D/g,'');

this.value =
angka.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

});

/* =========================
   TAMPILKAN PENERIMA
========================= */

const jenis =
document.getElementById('jenis_transaksi');

const penerima =
document.getElementById('transfer-penerima');

jenis.addEventListener('change', function(){

if(this.value == 'Transfer'){

penerima.style.display = 'block';

}else{

penerima.style.display = 'none';

}

});

</script>

</body>
</html>
