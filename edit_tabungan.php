<?php
session_start();

if (!isset($_SESSION['id_admin'])) {
    header('Location: login.php');
    exit;
}

require 'koneksi.php';

/* =========================
   AMBIL DATA TABUNGAN
========================= */

$id_tabungan = $_GET['id_tabungan'];

$sql = "SELECT * FROM robotv80_tabungan 
WHERE id_tabungan = '$id_tabungan'";

$result = mysqli_query($koneksi, $sql);

$row = mysqli_fetch_assoc($result);

/* =========================
   UPDATE TABUNGAN
========================= */

if (isset($_POST['submit'])) {

    $id_karyawan = $_POST['id_karyawan'];

    // Hilangkan titik dan koma
    $saldo = str_replace(['.', ','], '', $_POST['saldo']);

    // Ubah jadi integer
    $saldo = (int)$saldo;

    /* =========================
       UPDATE DATA
    ========================= */

    $update = "UPDATE robotv80_tabungan SET

    id_karyawan = '$id_karyawan',
    saldo = '$saldo'

    WHERE id_tabungan = '$id_tabungan'
    ";

    mysqli_query($koneksi, $update);

    echo "
    <script>

    alert('Data tabungan berhasil diupdate');

    window.location='data_tabungan.php';

    </script>
    ";

    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<meta name="viewport"
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>myROBOT-V80</title>
  <link href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png" rel="icon" type="image/png" />
<style>

/* =========================
   RESET
========================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* =========================
   BODY
========================= */

body{
    background:#f0f2f5;
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
    padding:20px;
}

/* =========================
   CONTAINER
========================= */

.container{
    background:#fff;
    padding:35px 40px;
    border-radius:15px;
    box-shadow:0 10px 30px rgba(0,0,0,0.1);
    width:100%;
    max-width:500px;
}

/* =========================
   JUDUL
========================= */

h2{
    text-align:center;
    margin-bottom:25px;
    color:#333;
}

/* =========================
   FORM
========================= */

form label{
    display:block;
    margin-top:15px;
    font-weight:600;
    color:#444;
}

form input[type="text"],
form select{

    width:100%;
    padding:12px;
    margin-top:5px;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:16px;
}

/* =========================
   BUTTON
========================= */

.button-group{
    display:flex;
    gap:10px;
    margin-top:25px;
}

form input[type="submit"]{

    flex:1;
    padding:12px;
    border:none;
    border-radius:8px;

    background:linear-gradient(
    135deg,
    #6a11cb,
    #2575fc
    );

    color:#fff;
    font-size:16px;
    font-weight:600;
    cursor:pointer;

    transition:0.3s;
}

form input[type="submit"]:hover{

    transform:translateY(-2px);

    box-shadow:0 6px 15px rgba(0,0,0,0.2);
}

/* =========================
   BUTTON KEMBALI
========================= */

.btn-kembali{

    flex:1;

    text-align:center;

    padding:12px;

    border-radius:8px;

    background:#28a745;

    color:white;

    text-decoration:none;

    font-weight:600;

    transition:0.3s;
}

.btn-kembali:hover{

    background:#1f7d34;
}

/* =========================
   PLACEHOLDER
========================= */

input#saldo::placeholder{
    color:#aaa;
    font-style:italic;
}

</style>

</head>

<body>

<div class="container">

<h2>Edit Tabungan</h2>

<form action="" method="post">

<label for="id_karyawan">
Nama Karyawan
</label>

<select id="id_karyawan"
name="id_karyawan"
required>

<?php

$sql_karyawan =
"SELECT * FROM robotv80_karyawan";

$result_karyawan =
mysqli_query($koneksi, $sql_karyawan);

while ($row_karyawan =
mysqli_fetch_assoc($result_karyawan)) {

$selected =
($row_karyawan['id_karyawan']
== $row['id_karyawan'])
? "selected"
: "";

echo "

<option value='".$row_karyawan['id_karyawan']."'
$selected>

".$row_karyawan['nama']."

</option>

";

}

?>

</select>

<label for="saldo">
Saldo (Rp)
</label>

<input
type="text"
id="saldo"
name="saldo"

value="<?=
number_format(
$row['saldo'],
0,
',',
'.'
);
?>"

placeholder="0"

required
>

<div class="button-group">

<input
type="submit"
name="submit"
value="Simpan">

<a href="data_tabungan.php"
class="btn-kembali">

Kembali

</a>

</div>

</form>

</div>

<script>

/* =========================
   FORMAT RUPIAH
========================= */

const saldoInput =
document.getElementById('saldo');

saldoInput.addEventListener('input', function(){

    let value =
    this.value.replace(/\D/g,'');

    this.value =
    value.replace(
    /\B(?=(\d{3})+(?!\d))/g,
    "."
    );

});

</script>

</body>
</html>
