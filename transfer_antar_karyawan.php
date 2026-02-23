<?php
session_start();
if (!isset($_SESSION['id_admin'])) {
    header('Location: login.php');
    exit;
}

require 'koneksi.php';

$error = ''; // untuk menyimpan pesan notifikasi

if (isset($_POST['submit'])) {
    $id_tabungan_pengirim = $_POST['id_tabungan_pengirim'];
    $id_tabungan_penerima = $_POST['id_tabungan_penerima'];
    $jumlah = $_POST['jumlah'];

    if ($id_tabungan_pengirim == $id_tabungan_penerima) {
        $error = "⚠ Tabungan pengirim dan penerima tidak boleh sama.";
    } else {
        // Ambil saldo pengirim
        $sql = "SELECT * FROM robotv80_tabungan WHERE id_tabungan = '$id_tabungan_pengirim'";
        $result = mysqli_query($koneksi, $sql);
        $row_pengirim = mysqli_fetch_assoc($result);

        // Ambil saldo penerima
        $sql = "SELECT * FROM robotv80_tabungan WHERE id_tabungan = '$id_tabungan_penerima'";
        $result = mysqli_query($koneksi, $sql);
        $row_penerima = mysqli_fetch_assoc($result);

        if ($row_pengirim['saldo'] >= $jumlah) {
            // Update saldo pengirim dan penerima
            $saldo_pengirim_baru = $row_pengirim['saldo'] - $jumlah;
            $saldo_penerima_baru = $row_penerima['saldo'] + $jumlah;

            mysqli_query($koneksi, "UPDATE robotv80_tabungan SET saldo = '$saldo_pengirim_baru' WHERE id_tabungan = '$id_tabungan_pengirim'");
            mysqli_query($koneksi, "UPDATE robotv80_tabungan SET saldo = '$saldo_penerima_baru' WHERE id_tabungan = '$id_tabungan_penerima'");

            // Catat transaksi
            mysqli_query($koneksi, "INSERT INTO robotv80_transaksi (id_tabungan, jenis_transaksi, jumlah, tanggal) 
                                     VALUES ('$id_tabungan_pengirim', 'Transfer', '$jumlah', NOW())");
            mysqli_query($koneksi, "INSERT INTO robotv80_transaksi (id_tabungan, jenis_transaksi, jumlah, tanggal) 
                                     VALUES ('$id_tabungan_penerima', 'Transfer', '$jumlah', NOW())");

            header('Location: data_transaksi.php');
            exit;
        } else {
            $error = "⚠ Saldo pengirim tidak cukup untuk melakukan transfer.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link href="http://10.10.20.250/dashboard/download.jpeg" rel="icon" type="image/png" />
    <title>Transfer Antar Karyawan</title>
    <link rel="stylesheet" href="style-transfer.css">
</head>
<body>
<div class="container">
    <h2>Transfer Antar Karyawan</h2>

    <!-- Notifikasi -->
    <?php if($error != ''): ?>
        <div class="alert"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="" method="post">
        <label for="id_tabungan_pengirim">ID Tabungan Pengirim:</label>
        <select id="id_tabungan_pengirim" name="id_tabungan_pengirim" required>
            <?php
            $sql = "SELECT t.id_tabungan, k.nama 
                    FROM robotv80_tabungan t 
                    JOIN robotv80_karyawan k ON t.id_karyawan = k.id_karyawan";
            $result = mysqli_query($koneksi, $sql);
            while($row = mysqli_fetch_assoc($result)){
                echo "<option value='".$row['id_tabungan']."'>".$row['id_tabungan']." - ".$row['nama']."</option>";
            }
            ?>
        </select><br><br>

        <label for="id_tabungan_penerima">ID Tabungan Penerima:</label>
        <select id="id_tabungan_penerima" name="id_tabungan_penerima" required>
            <?php
            $sql = "SELECT t.id_tabungan, k.nama 
                    FROM robotv80_tabungan t 
                    JOIN robotv80_karyawan k ON t.id_karyawan = k.id_karyawan";
            $result = mysqli_query($koneksi, $sql);
            while($row = mysqli_fetch_assoc($result)){
                echo "<option value='".$row['id_tabungan']."'>".$row['id_tabungan']." - ".$row['nama']."</option>";
            }
            ?>
        </select><br><br>

        <label for="jumlah">Jumlah:</label>
        <input type="number" id="jumlah" name="jumlah" required><br><br>

        <input type="submit" name="submit" value="Transfer" class="btn-transfer">
        <a href="data_transaksi.php" class="btn-kembali">Kembali</a>
    </form>
</div>
</body>
</html>

