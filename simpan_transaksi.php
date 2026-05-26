<?php
session_start();
require "koneksi.php";

$id_tabungan   = $_POST['id_tabungan'];
$jenis         = $_POST['jenis_transaksi'];
$jumlah        = (float) $_POST['jumlah'];
$transfer_ke   = $_POST['transfer_ke'] ?? null;

/* ambil saldo rekening utama */
$q = mysqli_query($koneksi, "SELECT saldo FROM robotv80_tabungan WHERE id_tabungan='$id_tabungan'");
$data = mysqli_fetch_assoc($q);

if (!$data) {
    die("Rekening tidak ditemukan");
}

$saldo_awal = $data['saldo'];

mysqli_begin_transaction($koneksi);

try {

    /* ===================== SETOR ===================== */
    if ($jenis == "Setor") {

        $saldo_baru = $saldo_awal + $jumlah;

        mysqli_query($koneksi, "
            UPDATE robotv80_tabungan 
            SET saldo='$saldo_baru'
            WHERE id_tabungan='$id_tabungan'
        ");

    }

    /* ===================== TARIK ===================== */
    elseif ($jenis == "Tarik") {

        if ($saldo_awal < $jumlah) {
            throw new Exception("Saldo tidak cukup!");
        }

        $saldo_baru = $saldo_awal - $jumlah;

        mysqli_query($koneksi, "
            UPDATE robotv80_tabungan 
            SET saldo='$saldo_baru'
            WHERE id_tabungan='$id_tabungan'
        ");
    }

    /* ===================== TRANSFER ===================== */
    elseif ($jenis == "Transfer") {

        if (!$transfer_ke) {
            throw new Exception("Rekening tujuan belum dipilih!");
        }

        if ($saldo_awal < $jumlah) {
            throw new Exception("Saldo tidak cukup untuk transfer!");
        }

        // kurangi pengirim
        $saldo_pengirim = $saldo_awal - $jumlah;

        mysqli_query($koneksi, "
            UPDATE robotv80_tabungan 
            SET saldo='$saldo_pengirim'
            WHERE id_tabungan='$id_tabungan'
        ");

        // ambil saldo penerima
        $q2 = mysqli_query($koneksi, "SELECT saldo FROM robotv80_tabungan WHERE id_tabungan='$transfer_ke'");
        $penerima = mysqli_fetch_assoc($q2);

        if (!$penerima) {
            throw new Exception("Rekening tujuan tidak ditemukan!");
        }

        $saldo_penerima = $penerima['saldo'] + $jumlah;

        mysqli_query($koneksi, "
            UPDATE robotv80_tabungan 
            SET saldo='$saldo_penerima'
            WHERE id_tabungan='$transfer_ke'
        ");
    }

    /* ===================== SIMPAN TRANSAKSI ===================== */
    mysqli_query($koneksi, "
        INSERT INTO robotv80_transaksi
        (id_tabungan, jenis_transaksi, jumlah, transfer_ke, tanggal)
        VALUES
        ('$id_tabungan', '$jenis', '$jumlah', '$transfer_ke', NOW())
    ");

    mysqli_commit($koneksi);

    header("Location: data_transaksi.php");
    exit;

} catch (Exception $e) {

    mysqli_rollback($koneksi);
    die("Gagal transaksi: " . $e->getMessage());
}
?>
