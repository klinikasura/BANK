<?php
session_start();
require 'koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_karyawan'])) {
    echo json_encode(['status' => 'error']);
    exit;
}

$id_karyawan = $_SESSION['id_karyawan'];

$sql = "SELECT saldo FROM robotv80_tabungan WHERE id_karyawan='$id_karyawan' LIMIT 1";
$result = mysqli_query($koneksi, $sql);

$saldo = 0;
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $saldo = (int)$row['saldo'];
}

echo json_encode([
    'status' => 'ok',
    'saldo' => $saldo
]);
