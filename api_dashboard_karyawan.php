<?php
session_start();
require 'koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_karyawan'])) {
    echo json_encode(['status' => 'error']);
    exit;
}

$id = $_SESSION['id_karyawan'];

// SALDO
$s = mysqli_query($koneksi,"
    SELECT saldo FROM robotv80_tabungan 
    WHERE id_karyawan='$id' LIMIT 1
");
$saldo = mysqli_fetch_assoc($s)['saldo'] ?? 0;

// TRANSAKSI TERAKHIR
$t = mysqli_query($koneksi,"
    SELECT tr.id_transaksi, tr.jenis_transaksi, tr.jumlah, tr.tanggal
    FROM robotv80_transaksi tr
    JOIN robotv80_tabungan tb ON tr.id_tabungan = tb.id_tabungan
    WHERE tb.id_karyawan='$id'
    ORDER BY tr.id_transaksi DESC
    LIMIT 1
");

$trx = mysqli_fetch_assoc($t);

echo json_encode([
    'status' => 'ok',
    'saldo' => (int)$saldo,
    'trx' => $trx
]);
