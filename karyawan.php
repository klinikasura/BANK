<?php
session_start();

if (!isset($_SESSION['id_karyawan']) || $_SESSION['level'] != 'karyawan') {
    header('Location: login.php');
    exit;
}

require 'koneksi.php';

$id_karyawan = $_SESSION['id_karyawan'];

/*
|--------------------------------------------------------------------------
| AMBIL SALDO
|--------------------------------------------------------------------------
*/

$sql = "SELECT saldo 
        FROM robotv80_tabungan 
        WHERE id_karyawan = '$id_karyawan'
        LIMIT 1";

$result = mysqli_query($koneksi, $sql);

$saldo = 0;

if ($result && mysqli_num_rows($result) > 0) {

    $row = mysqli_fetch_assoc($result);

    $saldo = (int)$row['saldo'];
}

/*
|--------------------------------------------------------------------------
| AMBIL TRANSAKSI
|--------------------------------------------------------------------------
*/

$last_trx = mysqli_query($koneksi, "
SELECT 
    tr.id_transaksi,
    tr.jenis_transaksi,
    tr.jumlah,
    tr.tanggal
FROM robotv80_transaksi tr
JOIN robotv80_tabungan tb
    ON tr.id_tabungan = tb.id_tabungan
WHERE tb.id_karyawan = '$id_karyawan'
ORDER BY tr.id_transaksi DESC
LIMIT 10
");

$transaksi = mysqli_fetch_all($last_trx, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>myROBOT-V80</title>

<link rel="icon" type="image/png"
href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING%20APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<style>

/* =========================
   RESET
========================= */

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

html,body{
    width:100%;
    overflow-x:hidden;
    font-family:Arial,sans-serif;
    background:#f1f5f9;
}

/* =========================
   CONTAINER
========================= */

.container{
    width:100%;
    max-width:500px;
    margin:auto;
    padding:15px;
    padding-bottom:110px;
}

/* =========================
   TEXT
========================= */

h1,h2,h3,p{
    word-wrap:break-word;
}

/* =========================
   ATM CARD
========================= */

.atm-card{
    width:100%;
    min-height:190px;
    border-radius:22px;
    background:linear-gradient(135deg,#2563eb,#0f172a);
    color:white;
    padding:18px;
    position:relative;
    overflow:hidden;
    animation:floatCard 3s ease-in-out infinite;
    box-shadow:0 10px 25px rgba(0,0,0,0.15);
}

@keyframes floatCard{

    0%,100%{
        transform:translateY(0);
    }

    50%{
        transform:translateY(-6px);
    }
}

.chip{
    width:48px;
    height:34px;
    background:gold;
    border-radius:8px;
    margin-top:15px;
}

.card-number{
    margin-top:28px;
    letter-spacing:2px;
    font-size:15px;
    word-spacing:4px;
}

/* =========================
   SALDO BOX
========================= */

.saldo-box{
    width:100%;
    background:white;
    margin-top:15px;
    padding:18px;
    border-radius:18px;
    box-shadow:0 5px 20px rgba(0,0,0,0.05);
}

.saldo{
    color:#2563eb;
    font-size:30px;
    margin-top:10px;
    word-break:break-word;
}

/* =========================
   CHART
========================= */

.chart-box{
    width:100%;
    background:white;
    margin-top:15px;
    padding:10px;
    border-radius:18px;
    overflow:hidden;
    box-shadow:0 5px 20px rgba(0,0,0,0.05);
}

canvas{
    width:100% !important;
    max-width:100%;
    height:auto !important;
}

/* =========================
   TRANSAKSI
========================= */

.marquee{
    width:100%;
    overflow:hidden;
    white-space:nowrap;
    margin-top:15px;
}

.marquee-content{
    display:inline-block;
    animation:scrollLeft 25s linear infinite;
}

@keyframes scrollLeft{

    0%{
        transform:translateX(100%);
    }

    100%{
        transform:translateX(-100%);
    }
}

.item{
    display:inline-block;
    margin-right:15px;
    background:white;
    padding:10px 14px;
    border-radius:12px;
    font-size:13px;
    box-shadow:0 3px 10px rgba(0,0,0,0.05);
}

/* =========================
   GRID MENU
========================= */

.menu-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:10px;
    margin-top:20px;
}

.menu-grid a{
    background:#2563eb;
    color:white;
    text-decoration:none;
    text-align:center;
    padding:14px 8px;
    border-radius:14px;
    font-size:12px;
    transition:0.2s;
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:60px;
}

.menu-grid a:active{
    transform:scale(0.95);
}

/* =========================
   BUTTON
========================= */

.export-btn{
    width:100%;
    border:none;
    margin-top:20px;
    padding:15px;
    border-radius:15px;
    background:linear-gradient(135deg,#22c55e,#16a34a);
    color:white;
    font-size:15px;
    font-weight:bold;
    box-shadow:0 8px 20px rgba(34,197,94,0.3);
}

/* =========================
   POPUP
========================= */

.popup{
    position:fixed;
    top:15px;
    right:15px;
    left:15px;
    background:#22c55e;
    color:white;
    padding:14px;
    border-radius:14px;
    z-index:9999;
    text-align:center;
    font-size:14px;
    box-shadow:0 5px 20px rgba(0,0,0,0.2);
}

/* =========================
   BOTTOM NAV
========================= */

.bottom-nav{
    position:fixed;
    bottom:0;
    left:0;
    right:0;
    background:white;
    display:flex;
    justify-content:space-around;
    align-items:center;
    padding:10px 0;
    border-top:1px solid #ddd;
    box-shadow:0 -5px 20px rgba(0,0,0,0.08);
    z-index:999;
}

.bottom-nav a{
    text-decoration:none;
    font-size:24px;
    padding:10px;
    border-radius:12px;
    transition:0.2s;
}

.bottom-nav a:active{
    background:#e0e7ff;
    transform:scale(0.92);
}

/* =========================
   TABLET
========================= */

@media(max-width:768px){

    .container{
        padding:12px;
        padding-bottom:110px;
    }

    .saldo{
        font-size:26px;
    }

    .menu-grid{
        grid-template-columns:repeat(4,1fr);
        gap:8px;
    }

    .menu-grid a{
        font-size:11px;
        padding:12px 5px;
    }
}

/* =========================
   MOBILE
========================= */

@media(max-width:480px){

    .container{
        padding:10px;
        padding-bottom:100px;
    }

    .atm-card{
        min-height:180px;
        padding:15px;
    }

    .card-number{
        font-size:13px;
        letter-spacing:1px;
    }

    .saldo{
        font-size:24px;
    }

    .menu-grid{
        grid-template-columns:repeat(2,1fr);
    }

    .menu-grid a{
        min-height:55px;
        font-size:12px;
    }

    .item{
        font-size:12px;
        padding:8px 10px;
    }

    .bottom-nav a{
        font-size:22px;
    }

    .popup{
        font-size:13px;
    }
}

/* =========================
   HP KECIL
========================= */

@media(max-width:360px){

    .saldo{
        font-size:20px;
    }

    .card-number{
        font-size:12px;
    }

    .menu-grid{
        gap:6px;
    }

    .menu-grid a{
        font-size:11px;
        padding:10px 4px;
    }
}
</style>

</head>

<body>

<div class="container">

<p>&nbsp;</p>
Hello,
<b><?= htmlspecialchars($_SESSION['nama']); ?></b>
</p>

   <p>&nbsp;</p>

<!-- =========================
     CARD
========================= -->

<div class="atm-card">

    <div>myROBOT-V80 BANK</div>

    <div class="chip"></div>

    <div class="card-number">
       No. Rekening **** **** **** ID <?= substr($id_karyawan,-4); ?>
    </div>

    <div style="margin-top:20px;">
        <?= htmlspecialchars($_SESSION['nama']); ?>
    </div>

</div>

<!-- =========================
     SALDO
========================= -->

<div class="saldo-box">

    <h3>Saldo</h3>

    <h1 class="saldo">
        Rp <?= number_format($saldo,0,',','.'); ?>
    </h1>

</div>

<!-- =========================
     CHART
========================= -->

<div class="chart-box">

    <canvas id="saldoChart"></canvas>

</div>

<!-- =========================
     TRANSAKSI
========================= -->

<h3>Transaksi Terakhir</h3>

<div class="marquee">

<div class="marquee-content">

<?php foreach($transaksi as $trx): ?>

<span class="item">

<?= $trx['jenis_transaksi']; ?>

Rp <?= number_format($trx['jumlah'],0,',','.'); ?>

</span>

<?php endforeach; ?>

</div>

</div>

<!-- =========================
     MENU
========================= -->

<div class="menu-grid">

    <a href="data_tabungan_karyawan.php">Tabungan</a>

    <a href="data_transaksi_karyawan.php">Transaksi</a>

    <a href="transfer.php">Transfer</a>

    <a href="kasir.php">E-Beli</a>

    <a href="pembelian.php">Riwayat E-Beli</a>

    <a href="edit_profile_karyawan.php">Profile</a>

    <a href="help.php">Help</a>

    <a href="logout.php">Logout</a>

</div>

<!-- =========================
     EXPORT PDF
========================= -->

<button class="export-btn" onclick="exportPDF()">
📄 Export Rekening Koran
</button>

</div>

<!-- =========================
     BOTTOM NAV
========================= -->

<div class="bottom-nav">

    <a href="karyawan.php">🏠</a>

    <a href="transfer.php">💸</a>

    <a href="data_transaksi_karyawan.php">📊</a>

    <a href="edit_profile_karyawan.php">👤</a>

</div>

<script>

/* =========================
   SPEAK
========================= */

function speak(text){

    text = text.replace(/,/g,'');

    text = text.replace(/\./g,'');

    let msg = new SpeechSynthesisUtterance(text);

    msg.lang = 'id-ID';

    window.speechSynthesis.speak(msg);
}

/* =========================
   POPUP
========================= */

function popup(text){

    let div = document.createElement("div");

    div.className = "popup";

    div.innerHTML = text;

    document.body.appendChild(div);

    setTimeout(()=>{

        div.remove();

    },3000);
}

/* =========================
   NOTIFY
========================= */

function notify(text){

    popup(text);

    speak(text);

    if(navigator.vibrate){

        navigator.vibrate(200);
    }
}

/* =========================
   CHART
========================= */

let ctx = document.getElementById("saldoChart");

let chart = new Chart(ctx, {

    type:'line',

    data:{
        labels:[],
        datasets:[{
            label:'Grafik Saldo',
            data:[],
            borderColor:'#2563eb',
            backgroundColor:'rgba(37,99,235,0.1)',
            fill:true,
            tension:0.4,
            borderWidth:3,
            pointRadius:4
        }]
    },

    options:{
        responsive:true,

        plugins:{
            legend:{
                display:true
            }
        },

        scales:{
            y:{
                beginAtZero:true
            }
        }
    }
});

/* =========================
   LOAD CHART
========================= */

function loadChart(){

fetch("api_saldo_chart.php")

.then(res => res.json())

.then(data => {

    let labels = [];

    let values = [];

    data.forEach(item => {

        labels.push(item.tanggal);

        values.push(item.saldo);

    });

    chart.data.labels = labels;

    chart.data.datasets[0].data = values;

    chart.update();

});

}

loadChart();

setInterval(loadChart,5000);

/* =========================
   REFRESH DASHBOARD
========================= */

let lastSaldo = <?= (int)$saldo ?>;

let lastID = 0;

function refresh(){

fetch("api_dashboard_karyawan.php")

.then(r => r.json())

.then(d => {

    if(d.status == "ok"){

        if(d.saldo != lastSaldo){

            let selisih = d.saldo - lastSaldo;

            if(selisih > 0){

                notify(
                    "Transaksi berhasil saldo masuk "
                    +
                    selisih
                    .toLocaleString("id-ID")
                    .replace(/,/g,'')
                    .replace(/\./g,'')
                );
            }

            lastSaldo = d.saldo;

            document.querySelector(".saldo").innerHTML =
            "Rp " + d.saldo.toLocaleString("id-ID");
        }

        if(d.trx && d.trx.id_transaksi > lastID){

            lastID = d.trx.id_transaksi;

            notify(
                d.trx.jenis_transaksi
                +
                " Rp "
                +
                parseInt(d.trx.jumlah)
                .toLocaleString("id-ID")
                .replace(/,/g,'')
                .replace(/\./g,'')
            );
        }
    }

});

}

setInterval(refresh,2000);

/* =========================
   EXPORT PDF
========================= */

function exportPDF(){

    const { jsPDF } = window.jspdf;

    let doc = new jsPDF();

    doc.text("Rekening Koran",10,10);

    doc.text(
        "Nama: <?= htmlspecialchars($_SESSION['nama']); ?>",
        10,
        20
    );

    doc.text(
        "Saldo: Rp " +
        lastSaldo
        .toLocaleString("id-ID")
        .replace(/,/g,'')
        .replace(/\./g,''),
        10,
        30
    );

    doc.save("rekening.pdf");
}

/* =========================
   WELCOME
========================= */

window.onload = function(){

    speak(
        "Hello <?= htmlspecialchars($_SESSION['nama']); ?> transaksi berhasil"
    );
}

</script>

</body>
</html>echo json_encode($data);
