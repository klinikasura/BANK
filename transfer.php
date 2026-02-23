<?php
session_start();
require 'koneksi.php';

// Cek user login dan role
if (!isset($_SESSION['id_admin']) && !isset($_SESSION['id_karyawan'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

if (isset($_POST['submit'])) {
    $id_tabungan_pengirim = $_POST['id_tabungan_pengirim'];
    $id_tabungan_penerima = $_POST['id_tabungan_penerima'];
    
    // Hapus tanda titik/koma pada jumlah agar jadi integer
    $jumlah = str_replace(['.', ','], '', $_POST['jumlah']);
    $jumlah = (int)$jumlah;

    if ($id_tabungan_pengirim == $id_tabungan_penerima) {
        $error = "⚠ Tabungan pengirim dan penerima tidak boleh sama.";
    } elseif ($jumlah <= 0) {
        $error = "⚠ Jumlah transfer harus lebih besar dari 0.";
    } else {
        // Ambil saldo pengirim
        $sql = "SELECT saldo FROM robotv80_tabungan WHERE id_tabungan = '$id_tabungan_pengirim'";
        $result = mysqli_query($koneksi, $sql);
        if (!$result || mysqli_num_rows($result) == 0) {
            $error = "⚠ Data tabungan pengirim tidak ditemukan.";
        } else {
            $row_pengirim = mysqli_fetch_assoc($result);

            if ($row_pengirim['saldo'] < $jumlah) {
                $error = "⚠ Saldo pengirim tidak cukup untuk melakukan transfer.";
            } else {
                // Ambil saldo penerima
                $sql = "SELECT saldo FROM robotv80_tabungan WHERE id_tabungan = '$id_tabungan_penerima'";
                $result = mysqli_query($koneksi, $sql);
                if (!$result || mysqli_num_rows($result) == 0) {
                    $error = "⚠ Data tabungan penerima tidak ditemukan.";
                } else {
                    $row_penerima = mysqli_fetch_assoc($result);

                    // Update saldo pengirim dan penerima
                    $saldo_pengirim_baru = $row_pengirim['saldo'] - $jumlah;
                    $saldo_penerima_baru = $row_penerima['saldo'] + $jumlah;

                    mysqli_query($koneksi, "UPDATE robotv80_tabungan SET saldo = '$saldo_pengirim_baru' WHERE id_tabungan = '$id_tabungan_pengirim'");
                    mysqli_query($koneksi, "UPDATE robotv80_tabungan SET saldo = '$saldo_penerima_baru' WHERE id_tabungan = '$id_tabungan_penerima'");

                    // Catat transaksi transfer kedua akun
                    $tanggal = date('Y-m-d H:i:s');
                    mysqli_query($koneksi, "INSERT INTO robotv80_transaksi (id_tabungan, jenis_transaksi, jumlah, tanggal) VALUES ('$id_tabungan_pengirim', 'Transfer Keluar', '$jumlah', '$tanggal')");
                    mysqli_query($koneksi, "INSERT INTO robotv80_transaksi (id_tabungan, jenis_transaksi, jumlah, tanggal) VALUES ('$id_tabungan_penerima', 'Transfer Masuk', '$jumlah', '$tanggal')");

                    $success = "✔ Transfer berhasil dilakukan.";
                }
            }
        }
    }
}

// Fungsi bantu untuk ambil tabungan pengguna
function getTabunganOptions($koneksi, $filter_id_karyawan = null) {
    $sql = "SELECT t.id_tabungan, k.nama FROM robotv80_tabungan t JOIN robotv80_karyawan k ON t.id_karyawan = k.id_karyawan";
    if ($filter_id_karyawan !== null) {
        $sql .= " WHERE t.id_karyawan = '$filter_id_karyawan'";
    }
    $sql .= " ORDER BY k.nama ASC";
    $result = mysqli_query($koneksi, $sql);
    $options = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $options[] = $row;
    }
    return $options;
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
<link href="http://10.10.20.250/dashboard/download.jpeg" rel="icon" type="image/png" />
<title>Aplikasi RS. Asura</title>
    <link href="http://10.10.20.250/dashboard/download.jpeg" rel="icon" type="image/png" />
    <link rel="stylesheet" href="style-transfer.css" />
    <style>
      /* Tambahan styling sederhana */
      .alert {
        padding: 12px;
        margin-bottom: 15px;
        border-radius: 5px;
      }
      .alert.error {
        background-color: #f8d7da;
        color: #842029;
        border: 1px solid #f5c2c7;
      }
      .alert.success {
        background-color: #d1e7dd;
        color: #0f5132;
        border: 1px solid #badbcc;
      }
      label {
        display: block;
        margin-top: 15px;
        font-weight: bold;
      }
      input[type=number], select {
        width: 100%;
        padding: 10px;
        margin-top: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 16px;
      }
      input[type=submit], .btn-kembali {
        margin-top: 20px;
        padding: 12px 25px;
        border: none;
        border-radius: 5px;
        font-size: 16px;
        cursor: pointer;
      }
      input[type=submit] {
        background-color: #007bff;
        color: white;
      }
      input[type=submit]:hover {
        background-color: #0056b3;
      }
      .btn-kembali {
        background-color: #6c757d;
        color: white;
        text-decoration: none;
        display: inline-block;
        text-align: center;
      }
      .btn-kembali:hover {
        background-color: #5a6268;
      }
      .container {
        max-width: 480px;
        margin: 50px auto;
        padding: 30px;
        background: #fff;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
        border-radius: 10px;
      }
      h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #333;
      }
    </style>
</head>
<body>

<div class="container">
    <h2>Transfer Antar Karyawan</h2>

    <?php if ($error): ?>
      <div class="alert error"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
      <div class="alert success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form action="" method="post">

        <label for="id_tabungan_pengirim">ID Tabungan Pengirim:</label>
        <select id="id_tabungan_pengirim" name="id_tabungan_pengirim" required>
            <?php
            if (isset($_SESSION['id_admin'])) {
                // Admin bisa pilih semua tabungan sebagai pengirim
                $options = getTabunganOptions($koneksi);
            } else {
                // Karyawan hanya bisa pilih tabungan miliknya sebagai pengirim
                $options = getTabunganOptions($koneksi, $_SESSION['id_karyawan']);
            }
            foreach ($options as $opt) {
                echo "<option value='" . htmlspecialchars($opt['id_tabungan']) . "'>" . htmlspecialchars($opt['id_tabungan'] . " - " . $opt['nama']) . "</option>";
            }
            ?>
        </select>

        <label for="id_tabungan_penerima">ID Tabungan Penerima:</label>
        <select id="id_tabungan_penerima" name="id_tabungan_penerima" required>
            <?php
            // Penerima bisa pilih semua tabungan kecuali tabungan pengirim
            // Karena form belum submit, tampilkan semua tabungan dulu
            // Jika mau lebih dinamis, bisa pakai javascript untuk filter
            $options_penerima = getTabunganOptions($koneksi);
            foreach ($options_penerima as $opt) {
                echo "<option value='" . htmlspecialchars($opt['id_tabungan']) . "'>" . htmlspecialchars($opt['id_tabungan'] . " - " . $opt['nama']) . "</option>";
            }
            ?>
        </select>

        <label for="jumlah">Jumlah (angka tanpa titik atau koma):</label>
        <input type="number" id="jumlah" name="jumlah" min="1" required>

        <input type="submit" name="submit" value="Transfer" class="btn-transfer">
        <a href="http://10.10.20.250/dashboard/ROBOT-BANK/data_transaksi_karyawan.php" class="btn-kembali">Kembali</a>
    </form>
</div>

<script>
// Optional: javascript agar tabungan penerima tidak bisa sama dengan pengirim
const pengirimSelect = document.getElementById('id_tabungan_pengirim');
const penerimaSelect = document.getElementById('id_tabungan_penerima');

pengirimSelect.addEventListener('change', function() {
    const selectedPengirim = this.value;
    for (let option of penerimaSelect.options) {
        option.disabled = (option.value === selectedPengirim);
    }
});
</script>

</body>
</html>

