<?php
session_start();

if (!isset($_SESSION['id_admin'])) {
    header('Location: login.php');
    exit;
}

require 'koneksi.php';

$error = '';

if (isset($_POST['submit'])) {

    $id_tabungan_pengirim = $_POST['id_tabungan_pengirim'];
    $id_tabungan_penerima = $_POST['id_tabungan_penerima'];

    $jumlah = str_replace('.', '', $_POST['jumlah']);
    $jumlah = (int)$jumlah;

    if ($jumlah <= 0) {
        $error = "⚠ Jumlah transfer harus lebih dari 0.";
    } elseif ($id_tabungan_pengirim == $id_tabungan_penerima) {
        $error = "⚠ Pengirim dan penerima tidak boleh sama.";
    } else {

        $q1 = mysqli_query($koneksi,"
            SELECT t.*, k.nama
            FROM robotv80_tabungan t
            JOIN robotv80_karyawan k ON t.id_karyawan = k.id_karyawan
            WHERE t.id_tabungan = '$id_tabungan_pengirim'
        ");

        $pengirim = mysqli_fetch_assoc($q1);

        $q2 = mysqli_query($koneksi,"
            SELECT t.*, k.nama
            FROM robotv80_tabungan t
            JOIN robotv80_karyawan k ON t.id_karyawan = k.id_karyawan
            WHERE t.id_tabungan = '$id_tabungan_penerima'
        ");

        $penerima = mysqli_fetch_assoc($q2);

        if (!$pengirim || !$penerima) {
            $error = "⚠ Data tidak ditemukan.";
        } else {

            if ($pengirim['saldo'] < $jumlah) {
                $error = "⚠ Saldo tidak cukup.";
            } else {

                mysqli_begin_transaction($koneksi);

                try {

                    $saldo_pengirim = $pengirim['saldo'] - $jumlah;
                    $saldo_penerima = $penerima['saldo'] + $jumlah;

                    mysqli_query($koneksi,"
                        UPDATE robotv80_tabungan 
                        SET saldo='$saldo_pengirim'
                        WHERE id_tabungan='$id_tabungan_pengirim'
                    ");

                    mysqli_query($koneksi,"
                        UPDATE robotv80_tabungan 
                        SET saldo='$saldo_penerima'
                        WHERE id_tabungan='$id_tabungan_penerima'
                    ");

                    mysqli_query($koneksi,"
                        INSERT INTO robotv80_transaksi
                        (id_tabungan, jenis_transaksi, jumlah, transfer_ke, tanggal)
                        VALUES
                        ('$id_tabungan_pengirim','Transfer','$jumlah','".$penerima['nama']."',NOW())
                    ");

                    mysqli_query($koneksi,"
                        INSERT INTO robotv80_transaksi
                        (id_tabungan, jenis_transaksi, jumlah, transfer_ke, tanggal)
                        VALUES
                        ('$id_tabungan_penerima','Transfer Masuk','$jumlah','".$pengirim['nama']."',NOW())
                    ");

                    mysqli_commit($koneksi);

                    header("Location: data_transaksi.php");
                    exit;

                } catch(Exception $e) {
                    mysqli_rollback($koneksi);
                    $error = "⚠ Transfer gagal.";
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
<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>myROBOT-V80</title>

<link href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png"
rel="icon"
type="image/png" />


<style>
body{
    font-family:'Segoe UI';
    background:linear-gradient(135deg,#e0f2fe,#f8fafc);
    display:flex;
    justify-content:center;
    padding:20px;
}

.container{
    background:#fff;
    padding:25px;
    border-radius:20px;
    width:100%;
    max-width:550px;
    box-shadow:0 15px 40px rgba(0,0,0,0.1);
}

h2{text-align:center;}

label{font-weight:600;margin-top:10px;display:block;}

select,input{
    width:100%;
    padding:10px;
    margin-top:5px;
    border-radius:10px;
    border:1px solid #ccc;
}
/* ================= BOTTOM NAV ================= */
.bottom-nav{
    position:fixed;
    bottom:0;
    left:0;
    right:0;
    background:white;
    display:flex;
    justify-content:space-around;
    padding:14px 0;
    box-shadow:0 -5px 20px rgba(0,0,0,0.08);
    border-top:1px solid #e2e8f0;
}

.bottom-nav a{
    text-decoration:none;
    font-size:24px;
    color:#0284c7;
    padding:10px 18px;
    border-radius:14px;
    transition:0.2s;
}

.bottom-nav a:active{
    background:#e0f2fe;
    transform:scale(0.95);
}

/* SALDO BOX */
.saldo-box{
    display:none;
    background:#e0f2fe;
    color:#0369a1;
    padding:10px;
    margin-top:10px;
    border-radius:10px;
    font-weight:600;
}

/* BUTTON */
button{
    width:100%;
    margin-top:15px;
    padding:12px;
    border:none;
    border-radius:10px;
    background:#0ea5e9;
    color:#fff;
    font-weight:bold;
    cursor:pointer;
}

button:hover{
    background:#0284c7;
}

/* BACK */
a{
    display:block;
    text-align:center;
    margin-top:10px;
    padding:10px;
    background:#e2e8f0;
    border-radius:10px;
    text-decoration:none;
    color:#0f172a;
}
.alert{
    background:#dc2626;
    color:white;
    padding:10px;
    border-radius:10px;
    margin-bottom:10px;
}
</style>
</head>

<body>

<div class="container">

<h2>💸 Transfer Antar Nasabah</h2>

<?php if($error != ''): ?>
<div class="alert"><?= $error ?></div>
<?php endif; ?>

<form method="POST">

<label>Pengirim</label>
<select name="id_tabungan_pengirim"
onchange="getSaldo(this.value,'saldo1')" required>
<option value="">-- pilih --</option>
<?php
$q = mysqli_query($koneksi,"
SELECT t.id_tabungan,k.nama
FROM robotv80_tabungan t
JOIN robotv80_karyawan k ON t.id_karyawan=k.id_karyawan
");
while($r=mysqli_fetch_assoc($q)){
echo "<option value='{$r['id_tabungan']}'>{$r['nama']}</option>";
}
?>
</select>

<div id="saldo1" class="saldo-box"></div>

<label>Penerima</label>
<select name="id_tabungan_penerima"
onchange="getSaldo(this.value,'saldo2')" required>
<option value="">-- pilih --</option>
<?php
$q = mysqli_query($koneksi,"
SELECT t.id_tabungan,k.nama
FROM robotv80_tabungan t
JOIN robotv80_karyawan k ON t.id_karyawan=k.id_karyawan
");
while($r=mysqli_fetch_assoc($q)){
echo "<option value='{$r['id_tabungan']}'>{$r['nama']}</option>";
}
?>
</select>

<div id="saldo2" class="saldo-box"></div>

<label>Jumlah</label>
<input type="text" name="jumlah" id="jumlah" required>

<button type="submit" name="submit">Transfer</button>

<a href="data_transaksi.php">Kembali</a>

</form>

</div>

<script>
function getSaldo(id,target){
    if(id==""){
        document.getElementById(target).style.display="none";
        return;
    }

    fetch("get_saldo2.php?id_tabungan="+id)
    .then(res=>res.json())
    .then(data=>{
        let box=document.getElementById(target);
        box.style.display="block";
        box.innerHTML="💰 Saldo: Rp "+Number(data.saldo).toLocaleString("id-ID");
    });
}

/* format rupiah */
document.getElementById('jumlah').addEventListener('input',function(){
    let angka=this.value.replace(/\D/g,'');
    this.value=angka.replace(/\B(?=(\d{3})+(?!\d))/g,'.');
});
</script>

<!-- BOTTOM NAV -->
<div class="bottom-nav">

    <a href="dashboard_admin.php">🏠</a>
    <a href="data_transaksi.php">📊</a>
    <a href="data_nasabah.php">👥</a>
    <a href="logout.php">🚪</a>

</div>

</body>
</html>
