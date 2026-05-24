<?php
session_start();
require 'koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_karyawan'])) {
    echo json_encode(['status' => 'error']);
    exit;
}

$id_karyawan = $_SESSION['id_karyawan'];

/*
AMBIL TRANSAKSI TERBARU YANG MASUK KE KARYAWAN INI
*/
$sql = "SELECT tr.jumlah, tr.tanggal, tr.jenis_transaksi
        FROM robotv80_transaksi tr
        JOIN robotv80_tabungan tb ON tr.id_tabungan = tb.id_tabungan
        WHERE tb.id_karyawan = '$id_karyawan'
        ORDER BY tr.id_transaksi DESC
        LIMIT 1";

$result = mysqli_query($koneksi, $sql);

$data = mysqli_fetch_assoc($result);

echo json_encode([
    'status' => 'ok',
    'jumlah' => $data['jumlah'] ?? 0,
    'jenis'  => $data['jenis_transaksi'] ?? '',
    'tanggal'=> $data['tanggal'] ?? ''
]);
