<?php
session_start();
include 'koneksi.php';

$id_karyawan = $_SESSION['id_karyawan'];

if (!isset($id_karyawan)) {
    header("Location: login.php");
    exit;
}

/* AMBIL DATA USER */
$user = mysqli_fetch_assoc(mysqli_query($koneksi,"
SELECT * FROM robotv80_karyawan WHERE id_karyawan='$id_karyawan'
"));

/* AMBIL BARANG */
$barang = mysqli_query($koneksi,"SELECT * FROM robotv80_barang");

if (isset($_POST['beli'])) {

    $id_barang = $_POST['id_barang'];
    $jumlah = $_POST['jumlah'];

    $b = mysqli_fetch_assoc(mysqli_query($koneksi,"
        SELECT * FROM robotv80_barang WHERE id_barang='$id_barang'
    "));

    $total = $b['harga'] * $jumlah;

    /* CEK SALDO */
    if ($user['saldo'] < $total) {
        echo "Saldo tidak cukup";
        exit;
    }

    /* CEK STOK */
    if ($b['stok'] < $jumlah) {
        echo "Stok tidak cukup";
        exit;
    }

    mysqli_begin_transaction($koneksi);

    try {

        /* potong saldo */
        mysqli_query($koneksi,"
            UPDATE robotv80_karyawan
            SET saldo = saldo - $total
            WHERE id_karyawan='$id_karyawan'
        ");

        /* kurangi stok */
        mysqli_query($koneksi,"
            UPDATE robotv80_barang
            SET stok = stok - $jumlah
            WHERE id_barang='$id_barang'
        ");

        /* simpan transaksi */
        mysqli_query($koneksi,"
            INSERT INTO robotv80_pembelian
            (id_karyawan,id_barang,jumlah,total)
            VALUES
            ('$id_karyawan','$id_barang','$jumlah','$total')
        ");

        mysqli_commit($koneksi);

        header("Location: dashboard_karyawan.php");
        exit;

    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        echo "Gagal transaksi";
    }
}
?>

<h2>Beli Barang</h2>

<p>Saldo kamu: Rp <?= number_format($user['saldo']) ?></p>

<form method="POST">

<select name="id_barang">
<?php while($b = mysqli_fetch_assoc($barang)) { ?>
<option value="<?= $b['id_barang'] ?>">
<?= $b['nama_barang'] ?> - Rp <?= $b['harga'] ?>
</option>
<?php } ?>
</select>

<input type="number" name="jumlah" placeholder="Jumlah" required>

<button name="beli">Beli</button>

</form>
