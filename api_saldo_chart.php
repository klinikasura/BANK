<?php
session_start();

header('Content-Type: application/json');

require 'koneksi.php';

if (!isset($_SESSION['id_karyawan'])) {

    echo json_encode([]);

    exit;
}

$id_karyawan = $_SESSION['id_karyawan'];

/*
|--------------------------------------------------------------------------
| AMBIL TRANSAKSI SESUAI LOGIN
|--------------------------------------------------------------------------
*/

$sql = "
SELECT
    tr.id_transaksi,
    tr.jumlah,
    tr.jenis_transaksi,
    tr.tanggal
FROM robotv80_transaksi tr
JOIN robotv80_tabungan tb
    ON tr.id_tabungan = tb.id_tabungan
WHERE tb.id_karyawan = '$id_karyawan'
ORDER BY tr.id_transaksi ASC
LIMIT 30
";

$result = mysqli_query($koneksi, $sql);

$data = [];

$saldo = 0;

while($row = mysqli_fetch_assoc($result)){

    $jumlah = (int)$row['jumlah'];

    /*
    |--------------------------------------------------------------------------
    | HITUNG SALDO
    |--------------------------------------------------------------------------
    */

    if(
        strtolower($row['jenis_transaksi']) == 'setor'
        ||
        strtolower($row['jenis_transaksi']) == 'masuk'
        ||
        strtolower($row['jenis_transaksi']) == 'deposit'
    ){

        $saldo += $jumlah;

    }else{

        $saldo -= $jumlah;
    }

    $data[] = [

        'tanggal' =>
        date('d/m H:i', strtotime($row['tanggal'])),

        'saldo' => $saldo
    ];
}

echo json_encode($data);
?>
