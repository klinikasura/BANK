<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_karyawan'])) {
    header("Location: login.php");
    exit;
}

$id_karyawan = $_SESSION['id_karyawan'];
$id_tabungan = $_SESSION['id_tabungan'];

/* =========================
   SIMPAN PENJUALAN
========================= */
if (isset($_POST['simpan'])) {

    $nama_barang = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $jumlah_barang = $_POST['jumlah_barang'];
    $harga_barang = $_POST['harga_barang'];
    $tgl_kadaluarsa = $_POST['tgl_kadaluarsa'];

    /* =========================
       SIMPAN PENJUALAN
    ========================= */
    mysqli_query($koneksi,"
        INSERT INTO robotv80_penjualan
        (
            id_karyawan,
            id_tabungan,
            id_transaksi,
            nama_barang,
            jumlah_barang,
            harga_barang,
            tgl_kadaluarsa
        )
        VALUES
        (
            '$id_karyawan',
            '$id_tabungan',
            0,
            '$nama_barang',
            '$jumlah_barang',
            '$harga_barang',
            '$tgl_kadaluarsa'
        )
    ");

    header("Location: data_penjualan.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Penjualan Karyawan</title>

<style>
body{
    font-family:Segoe UI;
    background:#f1f5f9;
    padding:20px;
}

.container{
    max-width:500px;
    margin:auto;
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 10px 30px rgba(0,0,0,0.1);
}

input{
    width:100%;
    padding:10px;
    margin:8px 0;
    border:1px solid #ddd;
    border-radius:8px;
}

button{
    width:100%;
    padding:10px;
    background:#2563eb;
    color:white;
    border:none;
    border-radius:8px;
}
</style>

</head>

<body>

<div class="container">

<h2>Input Penjualan</h2>

<p>👤 Login sebagai ID Karyawan: <b><?= $id_karyawan ?></b></p>

<form method="POST">

<input type="text" name="nama_barang" placeholder="Nama Barang" required>

<input type="number" name="jumlah_barang" placeholder="Jumlah Barang" required>

<input type="number" name="harga_barang" placeholder="Harga Barang" required>

<input type="date" name="tgl_kadaluarsa" required>

<button name="simpan">Simpan</button>

</form>

</div>

</body>
</html>
