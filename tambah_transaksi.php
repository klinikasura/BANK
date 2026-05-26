<?php
session_start();
if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}

require "koneksi.php";

/* Data rekening + saldo */
$rekening = mysqli_query($koneksi,"
    SELECT t.id_tabungan, t.saldo, k.nama
    FROM robotv80_tabungan t
    JOIN robotv80_karyawan k ON t.id_karyawan = k.id_karyawan
");

/* Untuk transfer ke */
$transferList = mysqli_query($koneksi,"
    SELECT t.id_tabungan, k.nama
    FROM robotv80_tabungan t
    JOIN robotv80_karyawan k ON t.id_karyawan = k.id_karyawan
");
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
body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #e0f2fe, #f8fafc);
    display: flex;
    justify-content: center;
    padding: 30px;
}

.container {
    background: white;
    padding: 25px;
    border-radius: 20px;
    width: 100%;
    max-width: 650px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.1);
}

h2 {
    margin-bottom: 20px;
}

label {
    font-weight: 600;
    display: block;
    margin-top: 10px;
}

select, input {
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 10px;
    border: 1px solid #cbd5e1;
}

/* saldo box */
.saldo-box {
    background: #ecfeff;
    padding: 10px;
    margin-top: 10px;
    border-radius: 10px;
    font-weight: bold;
    color: #0369a1;
    display: none;
}

/* transfer box */
#transferBox {
    display: none;
}

.btn {
    margin-top: 20px;
    padding: 10px 14px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
}

.btn-save {
    background: #0ea5e9;
    color: white;
}

.btn-back {
    background: #e2e8f0;
    color: #0f172a;
    text-decoration: none;
    display: inline-block;
    padding: 10px 14px;
    border-radius: 10px;
    margin-left: 10px;
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
</style>

</head>
<body>

<div class="container">

<h2>💳 Transaksi Bank</h2>

<form action="simpan_transaksi.php" method="POST">

    <!-- REKENING -->
    <label>Pilih Rekening</label>
    <select name="id_tabungan" id="rekening" onchange="showSaldo()" required>
        <option value="">-- Pilih --</option>
        <?php while($r = mysqli_fetch_assoc($rekening)) { ?>
            <option value="<?php echo $r['id_tabungan']; ?>"
                    data-saldo="<?php echo $r['saldo']; ?>">
                <?php echo $r['nama']; ?> (ID <?php echo $r['id_tabungan']; ?>)
            </option>
        <?php } ?>
    </select>

    <div class="saldo-box" id="saldoBox"></div>

    <!-- JENIS TRANSAKSI -->
    <label>Jenis Transaksi</label>
    <select name="jenis_transaksi" id="jenis" onchange="toggleTransfer()" required>
        <option value="">-- Pilih --</option>
        <option value="Setor">Setor</option>
        <option value="Tarik">Tarik</option>
        <option value="Transfer">Transfer</option>
    </select>

    <!-- JUMLAH -->
    <label>Jumlah</label>
    <input type="number" name="jumlah" required>

    <!-- TRANSFER KE -->
    <div id="transferBox">
        <label>Transfer Ke</label>
        <select name="transfer_ke">
            <option value="">-- Pilih Tujuan --</option>
            <?php while($t = mysqli_fetch_assoc($transferList)) { ?>
                <option value="<?php echo $t['id_tabungan']; ?>">
                    <?php echo $t['nama']; ?> (ID <?php echo $t['id_tabungan']; ?>)
                </option>
            <?php } ?>
        </select>
    </div>

    <button type="submit" class="btn btn-save">💾 Simpan</button>
    <a href="data_transaksi.php" class="btn-back">⬅ Kembali</a>

</form>

</div>

<script>
function showSaldo() {
    let select = document.getElementById("rekening");
    let saldo = select.options[select.selectedIndex].getAttribute("data-saldo");

    let box = document.getElementById("saldoBox");

    if (saldo) {
        box.style.display = "block";
        box.innerHTML = "💰 Saldo: Rp " + parseInt(saldo).toLocaleString();
    } else {
        box.style.display = "none";
    }
}

function toggleTransfer() {
    let jenis = document.getElementById("jenis").value;
    let box = document.getElementById("transferBox");

    if (jenis === "Transfer") {
        box.style.display = "block";
    } else {
        box.style.display = "none";
    }
}
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
