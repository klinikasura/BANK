<?php
session_start();
if (!isset($_SESSION['id_karyawan']) || $_SESSION['level'] != 'karyawan') {
    header('Location: login.php');
    exit;
}

require 'koneksi.php';

$id_karyawan = $_SESSION['id_karyawan'];

// =====================
// SALDO
// =====================
$sql = "SELECT saldo FROM robotv80_tabungan WHERE id_karyawan = '$id_karyawan' LIMIT 1";
$result = mysqli_query($koneksi, $sql);

$saldo = 0;
if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $saldo = (int)$row['saldo'];
}

// =====================
// TRANSAKSI TERAKHIR (LINE INFO)
// =====================
$last_trx = mysqli_query($koneksi, "
    SELECT tr.jenis_transaksi, tr.jumlah, tr.tanggal
    FROM robotv80_transaksi tr
    JOIN robotv80_tabungan tb ON tr.id_tabungan = tb.id_tabungan
    WHERE tb.id_karyawan = '$id_karyawan'
    ORDER BY tr.id_transaksi DESC
    LIMIT 1
");

$trx = mysqli_fetch_assoc($last_trx);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>myROBOT-V80</title> 
 <link href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png" rel="icon" type="image/png" />

<link rel="stylesheet" href="style-karyawan.css">

<style>
/* POPUP */
.popup {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #22c55e;
    color: white;
    padding: 12px 15px;
    border-radius: 10px;
    z-index: 9999;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

/* LINE INFO */
.line-info {
    margin-top: 15px;
    padding: 12px;
    background: #f8fafc;
    border-radius: 10px;
    border-left: 5px solid #2563eb;
    color: #111827;
}

.line-info h4 {
    margin: 0 0 8px 0;
    font-size: 14px;
}
</style>

</head>

<body>

<div class="container dashboard-karyawan">

  <h2>E-TABUNGAN</h2>

  <p>Selamat datang, <strong><?= htmlspecialchars($_SESSION['nama']); ?></strong>!</p>

  <!-- SALDO -->
  <div class="saldo-box">
    <h3>Saldo Tabungan Anda</h3>
    <p class="saldo">Rp <?= number_format($saldo, 0, ',', '.'); ?></p>
  </div>

  <!-- LINE INFO TRANSAKSI -->
  <div class="line-info">
    <h4>Info Transaksi Terakhir</h4>

    <?php if($trx): ?>
        <p>
            <?php if($trx['jenis_transaksi'] == 'Transfer Masuk'): ?>
                💰 Masuk Rp <?= number_format($trx['jumlah'],0,',','.'); ?>
            <?php elseif($trx['jenis_transaksi'] == 'Transfer Keluar'): ?>
                📤 Keluar Rp <?= number_format($trx['jumlah'],0,',','.'); ?>
            <?php else: ?>
                🔔 <?= $trx['jenis_transaksi']; ?>
            <?php endif; ?>
        </p>

        <small><?= date('d-m-Y H:i', strtotime($trx['tanggal'])); ?></small>

    <?php else: ?>
        <p>Tidak ada transaksi</p>
    <?php endif; ?>
  </div>

  <!-- MENU -->
  <ul>
    <li><a href="data_tabungan_karyawan.php">Data Tabungan</a></li>
    <li><a href="data_transaksi_karyawan.php">Data Transaksi</a></li>
    <li><a href="transfer.php">Transfer</a></li>
  <li><a href="rekening_koran.php">Rekening Koran</a></li>
    <li><a href="edit_profile_karyawan.php">Profile</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>

</div>

<script>
let lastID = 0;

// =====================
// GOOGLE VOICE
// =====================
function speak(text) {
    let msg = new SpeechSynthesisUtterance(text);
    msg.lang = 'id-ID';
    msg.rate = 1;
    window.speechSynthesis.speak(msg);
}

// =====================
// POPUP
// =====================
function showPopup(text) {
    let div = document.createElement("div");
    div.className = "popup";
    div.innerHTML = text;
    document.body.appendChild(div);

    setTimeout(() => div.remove(), 4000);
}

// =====================
// REALTIME TRANSAKSI
// =====================
function cekTransaksi() {
    fetch("api_notif_transaksi.php")
    .then(res => res.json())
    .then(data => {

        if (data.status === "ok") {

            let idBaru = parseInt(data.id);

            if (idBaru > lastID) {

                lastID = idBaru;

                if (data.jenis === "Transfer Masuk") {
                    showPopup("💰 Saldo Masuk +Rp " + data.jumlah);
                    speak("Anda menerima saldo sebesar " + data.jumlah + " rupiah");
                }

                if (data.jenis === "Transfer Keluar") {
                    showPopup("📤 Transfer Keluar Rp " + data.jumlah);
                    speak("Anda melakukan transfer sebesar " + data.jumlah + " rupiah");
                }
            }
        }
    });
}

// jalan tiap 2 detik
setInterval(cekTransaksi, 2000);
</script>

<script>
let lastID = 0;
let lastSaldo = <?= (int)$saldo ?>;

// 🔊 suara google
function speak(text) {
    let msg = new SpeechSynthesisUtterance(text);
    msg.lang = 'id-ID';
    msg.rate = 1;
    window.speechSynthesis.speak(msg);
}

// 🔔 popup
function showPopup(text) {
    let div = document.createElement("div");
    div.className = "popup";
    div.innerHTML = text;
    document.body.appendChild(div);

    setTimeout(() => div.remove(), 4000);
}

// 🔄 AUTO REFRESH
function refreshDashboard() {
    fetch("api_dashboard_karyawan.php")
    .then(res => res.json())
    .then(data => {

        if (data.status === "ok") {

            // =====================
            // UPDATE SALDO
            // =====================
            if (data.saldo != lastSaldo) {
                let selisih = data.saldo - lastSaldo;

                if (selisih > 0) {
                    showPopup("💰 Saldo Masuk +Rp " + selisih);
                    speak("Saldo Anda masuk " + selisih + " rupiah");
                }

                document.querySelector(".saldo").innerHTML =
                    "Rp " + data.saldo.toLocaleString("id-ID");

                lastSaldo = data.saldo;
            }

            // =====================
            // UPDATE TRANSAKSI
            // =====================
            if (data.trx && data.trx.id_transaksi) {

                let idBaru = parseInt(data.trx.id_transaksi);

                if (idBaru > lastID) {

                    lastID = idBaru;

                    if (data.trx.jenis_transaksi === "Transfer Masuk") {
                        showPopup("💰 Masuk Rp " + data.trx.jumlah);
                        speak("Anda menerima saldo " + data.trx.jumlah);
                    }

                    if (data.trx.jenis_transaksi === "Transfer Keluar") {
                        showPopup("📤 Keluar Rp " + data.trx.jumlah);
                        speak("Anda transfer " + data.trx.jumlah);
                    }

                    // UPDATE LINE INFO
                    location.reload(); // update line info simpel & aman
                }
            }
        }
    });
}

// jalan tiap 2 detik
setInterval(refreshDashboard, 2000);
</script>

</body>
</html>
