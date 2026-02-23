<?php
session_start();
if (!isset($_SESSION['id_admin'])) {
    header('Location: login.php');
    exit;
}

require 'koneksi.php';

if (isset($_POST['submit'])) {
    $id_tabungan = $_POST['id_tabungan'];
    $jenis_transaksi = $_POST['jenis_transaksi'];
    $jumlah = str_replace(['.',','], '', $_POST['jumlah']);
    $jumlah = (int)$jumlah;

    // Validasi jumlah
    if($jumlah <= 0){
        $error = "Nominal harus lebih besar dari 0.";
    } else {
        if($jenis_transaksi == 'Setor') {
            // Setor uang
            $sql = "INSERT INTO robotv80_transaksi (id_tabungan, jenis_transaksi, jumlah, tanggal)
                    VALUES ('$id_tabungan', 'Setor', '$jumlah', NOW())";
            mysqli_query($koneksi, $sql);
            mysqli_query($koneksi, "UPDATE robotv80_tabungan SET saldo = saldo + '$jumlah' WHERE id_tabungan = '$id_tabungan'");
            header('Location: data_transaksi.php');
            exit;

        } elseif($jenis_transaksi == 'Tarik') {
            // Tarik uang
            $cek = mysqli_query($koneksi, "SELECT saldo FROM robotv80_tabungan WHERE id_tabungan='$id_tabungan'");
            $row = mysqli_fetch_assoc($cek);
            if($row['saldo'] < $jumlah){
                $error = "Saldo tidak cukup untuk tarik.";
            } else {
                mysqli_query($koneksi, "INSERT INTO robotv80_transaksi (id_tabungan, jenis_transaksi, jumlah, tanggal)
                            VALUES ('$id_tabungan', 'Tarik', '$jumlah', NOW())");
                mysqli_query($koneksi, "UPDATE robotv80_tabungan SET saldo = saldo - '$jumlah' WHERE id_tabungan = '$id_tabungan'");
                header('Location: data_transaksi.php');
                exit;
            }

        } elseif($jenis_transaksi == 'Transfer') {
            $id_tabungan_penerima = $_POST['id_tabungan_penerima'];
            if($id_tabungan == $id_tabungan_penerima){
                $error = "Tabungan pengirim dan penerima tidak boleh sama.";
            } else {
                $cek = mysqli_query($koneksi, "SELECT saldo FROM robotv80_tabungan WHERE id_tabungan='$id_tabungan'");
                $row = mysqli_fetch_assoc($cek);
                if($row['saldo'] < $jumlah){
                    $error = "Saldo tidak cukup untuk transfer.";
                } else {
                    // Kurangi saldo pengirim
                    mysqli_query($koneksi, "UPDATE robotv80_tabungan SET saldo = saldo - '$jumlah' WHERE id_tabungan = '$id_tabungan'");
                    // Tambah saldo penerima
                    mysqli_query($koneksi, "UPDATE robotv80_tabungan SET saldo = saldo + '$jumlah' WHERE id_tabungan = '$id_tabungan_penerima'");
                    // Catat transaksi
                    mysqli_query($koneksi, "INSERT INTO robotv80_transaksi (id_tabungan, jenis_transaksi, jumlah, tanggal)
                                VALUES ('$id_tabungan', 'Transfer', '$jumlah', NOW())");
                    mysqli_query($koneksi, "INSERT INTO robotv80_transaksi (id_tabungan, jenis_transaksi, jumlah, tanggal)
                                VALUES ('$id_tabungan_penerima', 'Transfer', '$jumlah', NOW())");
                    header('Location: data_transaksi.php');
                    exit;
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link href="http://10.10.20.250/dashboard/download.jpeg" rel="icon" type="image/png" />
<title>Aplikasi RS. Asura</title>
<style>
body { font-family: Arial; background: #f0f2f5; margin:0; padding:0; }
.container { width:500px; margin:50px auto; background:#fff; padding:25px 30px; border-radius:10px; box-shadow:0 0 15px rgba(0,0,0,0.2);}
h2 { text-align:center; margin-bottom:20px; color:#333;}
label { display:block; margin-bottom:5px; font-weight:bold; color:#555;}
input[type="text"], input[type="number"], select { width:100%; padding:10px; margin-bottom:15px; border-radius:5px; border:1px solid #ccc; font-size:15px;}
input[type="submit"], .btn-kembali { display:inline-block; padding:10px 20px; border:none; border-radius:5px; text-decoration:none; color:#fff; cursor:pointer; margin-top:5px;}
input[type="submit"] { background-color:#28a745;}
input[type="submit"]:hover { background-color:#218838;}
.btn-kembali { background-color:#6c757d;}
.btn-kembali:hover { background-color:#5a6268;}
.alert { padding:10px; background-color:#f44336; color:white; margin-bottom:15px; border-radius:5px;}
</style>
</head>
<body>
<div class="container">
    <h2>Tambah Transaksi</h2>

    <?php if(isset($error)){ ?>
        <div class="alert"><?php echo $error; ?></div>
    <?php } ?>

    <form action="" method="post">
        <label for="id_tabungan">ID Tabungan (Pengirim / Setor / Tarik):</label>
        <select id="id_tabungan" name="id_tabungan" required>
            <?php
            $sql = "SELECT t.id_tabungan, k.nama FROM robotv80_tabungan t JOIN robotv80_karyawan k ON t.id_karyawan = k.id_karyawan";
            $result = mysqli_query($koneksi, $sql);
            while($row = mysqli_fetch_assoc($result)){
                echo "<option value='".$row['id_tabungan']."'>".$row['id_tabungan']." - ".$row['nama']."</option>";
            }
            ?>
        </select>

        <label for="jenis_transaksi">Jenis Transaksi:</label>
        <select id="jenis_transaksi" name="jenis_transaksi" required>
            <option value="Setor">Setor</option>
            <option value="Tarik">Tarik</option>
            <option value="Transfer">Transfer</option>
        </select>

        <div id="transfer-penerima" style="display:none;">
            <label for="id_tabungan_penerima">ID Tabungan Penerima:</label>
            <select id="id_tabungan_penerima" name="id_tabungan_penerima">
                <?php
                $sql = "SELECT t.id_tabungan, k.nama FROM robotv80_tabungan t JOIN robotv80_karyawan k ON t.id_karyawan = k.id_karyawan";
                $result = mysqli_query($koneksi, $sql);
                while($row = mysqli_fetch_assoc($result)){
                    echo "<option value='".$row['id_tabungan']."'>".$row['id_tabungan']." - ".$row['nama']."</option>";
                }
                ?>
            </select>
        </div>

        <label for="jumlah">Jumlah:</label>
        <input type="text" id="jumlah" name="jumlah" placeholder="Contoh: 1.000.000" required>

        <input type="submit" name="submit" value="Tambah Transaksi">
        <a href="data_transaksi.php" class="btn-kembali">Kembali</a>
    </form>
</div>

<script>
// Format ribuan
const inputJumlah = document.getElementById('jumlah');
inputJumlah.addEventListener('input', function(){
    let value = this.value.replace(/\D/g,'');
    this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
});

// Tampilkan pilihan penerima jika transfer
const jenis = document.getElementById('jenis_transaksi');
const penerimaDiv = document.getElementById('transfer-penerima');

jenis.addEventListener('change', function(){
    if(this.value == 'Transfer'){
        penerimaDiv.style.display = 'block';
    } else {
        penerimaDiv.style.display = 'none';
    }
});
</script>
</body>
</html>

