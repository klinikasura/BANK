<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['id_karyawan'])) {
    header("Location: login.php");
    exit;
}

$id_karyawan = $_SESSION['id_karyawan'];

/* =========================
   SALDO
========================= */
$saldo = $koneksi->query("
    SELECT saldo FROM robotv80_tabungan 
    WHERE id_karyawan='$id_karyawan'
")->fetch_assoc()['saldo'] ?? 0;

/* =========================
   CHECKOUT
========================= */
$struk = null;

if (isset($_POST['checkout'])) {

    $id_barang = $_POST['id_barang'];
    $jumlah = (int)$_POST['jumlah'];

    $barang = $koneksi->query("
        SELECT * FROM robotv80_barang 
        WHERE id_barang='$id_barang'
    ")->fetch_assoc();

    $total = $barang['harga'] * $jumlah;

    if ($barang['stok'] < $jumlah) {
        die("<script>alert('Stok tidak cukup');window.location='kasir.php';</script>");
    }

    if ($saldo < $total) {
        die("<script>alert('Saldo tidak cukup');window.location='kasir.php';</script>");
    }

    /* UPDATE */
    $koneksi->query("UPDATE robotv80_barang SET stok=stok-$jumlah WHERE id_barang='$id_barang'");
    $koneksi->query("UPDATE robotv80_tabungan SET saldo=saldo-$total WHERE id_karyawan='$id_karyawan'");

    /* INSERT PEMBELIAN */
    $koneksi->query("
        INSERT INTO robotv80_pembelian 
        (id_karyawan,id_barang,jumlah,total)
        VALUES
        ('$id_karyawan','$id_barang','$jumlah','$total')
    ");

    $struk = [
        "barang"=>$barang['nama_barang'],
        "harga"=>$barang['harga'],
        "jumlah"=>$jumlah,
        "total"=>$total,
        "tanggal"=>date("Y-m-d H:i:s")
    ];
}

/* =========================
   FILTER RIWAYAT
========================= */
$from = $_GET['from'] ?? '';
$to   = $_GET['to'] ?? '';
$page = $_GET['page'] ?? 1;

if ($page < 1) $page = 1;

$limit = 5;
$offset = ($page - 1) * $limit;

$where = "WHERE p.id_karyawan='$id_karyawan'";

if ($from && $to) {
    $where .= " AND DATE(p.tanggal) BETWEEN '$from' AND '$to'";
}

$total = $koneksi->query("
    SELECT COUNT(*) as total 
    FROM robotv80_pembelian p
    $where
")->fetch_assoc()['total'];

$total_page = ceil($total / $limit);

$riwayat = $koneksi->query("
SELECT p.*, b.nama_barang
FROM robotv80_pembelian p
JOIN robotv80_barang b ON p.id_barang=b.id_barang
$where
ORDER BY p.id_beli DESC
LIMIT $limit OFFSET $offset
");

/* =========================
   BARANG
========================= */
$barang = $koneksi->query("SELECT * FROM robotv80_barang ORDER BY nama_barang ASC");
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

<script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<style>
body{font-family:Arial;background:#f3f4f6;padding:20px}
.container{max-width:1000px;margin:auto;background:#fff;padding:20px;border-radius:12px}

.saldo{background:#10b981;color:#fff;padding:10px;border-radius:8px;text-align:center}

form{display:flex;gap:10px;flex-wrap:wrap;margin-top:10px}

select,input,button{padding:10px;border-radius:8px;border:1px solid #ddd}

button{background:#4f46e5;color:#fff}

.popup{
position:fixed;top:20px;right:20px;
background:#22c55e;color:#fff;
padding:10px;border-radius:8px;
display:none;
}

#struk{margin-top:20px;padding:15px;border:2px dashed #333}

table{width:100%;margin-top:20px;border-collapse:collapse}

th{background:#4f46e5;color:#fff;padding:10px}
.nav-container{
    display:flex;
    justify-content:center;
    gap:10px;
    flex-wrap:wrap;
    margin-top:15px;
}

/* BUTTON DASAR NAV */
.nav-btn{
    padding:10px 14px;
    border-radius:10px;
    text-decoration:none;
    font-weight:600;
    transition:0.3s;
    display:inline-block;
}

/* NEXT */
.btn-next{
    background:#2563eb;
    color:#fff;
}

.btn-next:hover{
    background:#1d4ed8;
    transform:translateY(-2px);
}

/* PREV */
.btn-prev{
    background:#64748b;
    color:#fff;
}

.btn-prev:hover{
    background:#475569;
    transform:translateY(-2px);
}

/* KEMBALI */
.btn-back{
    background:#ef4444;
    color:#fff;
}

.btn-back:hover{
    background:#dc2626;
    transform:translateY(-2px);
}

/* BELI LAGI */
.btn-buy{
    background:#22c55e;
    color:#fff;
}

.btn-buy:hover{
    background:#16a34a;
    transform:translateY(-2px);
}

/* BOX PAGINATION */
.pagination-box{
    margin-top:20px;
    text-align:center;
    padding:10px;
    background:#f8fafc;
    border-radius:10px;
    border:1px solid #e5e7eb;
    font-weight:600;
}
/* ===== BOTTOM NAV UPGRADE ===== */
.bottom-nav{
    position:fixed;
    bottom:0;
    left:0;
    right:0;
    background:white;
    display:flex;
    justify-content:space-around;
    padding:14px 0;
    border-top:1px solid #e5e7eb;
    box-shadow:0 -5px 20px rgba(0,0,0,0.08);
}

.bottom-nav a{
    text-decoration:none;
    font-size:22px;
    padding:10px 18px;
    border-radius:14px;
    transition:0.2s;
}

.bottom-nav a:active{
    background:#e0e7ff;
    transform:scale(0.95);
}

td{text-align:center;padding:10px}
</style>
</head>

<!-- NAV BUTTON LUAR STRUK -->
<div style="margin-bottom:15px; display:flex; gap:10px; flex-wrap:wrap; justify-content:center;">

    <a href="karyawan.php" 
       style="
        padding:10px 14px;
        background:#ef4444;
        color:#fff;
        text-decoration:none;
        border-radius:10px;
        font-weight:bold;
       ">
       ⬅ Kembali
    </a>

    <a href="kasir.php" 
       style="
        padding:10px 14px;
        background:#22c55e;
        color:#fff;
        text-decoration:none;
        border-radius:10px;
        font-weight:bold;
       ">
       🔁 Reset / Beli Lagi
    </a>

</div>

<body>

<div class="popup" id="popup">✔ Transaksi berhasil</div>

<div class="container">

<h2>🛒 E-Beli </h2>

<div class="saldo">
Saldo: Rp <?= number_format($saldo,0,',','.') ?>
</div>



<!-- FORM -->
<form method="POST">

<select name="id_barang" onchange="showInfo(this)" required>
<option value="">Pilih Barang</option>
<?php while($b=$barang->fetch_assoc()){ ?>
<option value="<?= $b['id_barang'] ?>"
        data-stok="<?= $b['stok'] ?>"
        data-harga="<?= $b['harga'] ?>">
    <?= $b['nama_barang'] ?>
</option>
<?php } ?>
</select>

<input type="number" name="jumlah" min="1" required>


<button name="checkout">Checkout</button>
</form>


<div id="info" style="margin-top:10px;"></div>

<!-- STRUK -->
<?php if($struk): ?>
<div id="struk">
<h3>🧾 STRUK</h3>
<p><?= $struk['barang'] ?></p>
<p><?= $struk['jumlah'] ?> x <?= $struk['harga'] ?></p>
<p>Total: Rp <?= number_format($struk['total'],0,',','.') ?></p>

<div id="qrcode"></div>

<button onclick="pdf()">PDF</button>

<div class="nav-container">

    <a href="kasir.php" class="nav-btn btn-buy">
        🔁 Beli Lagi
    </a>

    <a href="karyawan.php" class="nav-btn btn-back">
        ⬅ Kembali
    </a>

</div>
</div>
<?php endif; ?>

<!-- FILTER -->
<form method="GET">
<input type="date" name="from">
<input type="date" name="to">
<button>Cari</button>
</form>

<!-- RIWAYAT -->
<table>
<tr>
<th>ID</th><th>Barang</th><th>Qty</th><th>Total</th><th>Tanggal</th>
</tr>

<?php while($r=$riwayat->fetch_assoc()){ ?>
<tr>
<td><?= $r['id_beli'] ?></td>
<td><?= $r['nama_barang'] ?></td>
<td><?= $r['jumlah'] ?></td>
<td><?= $r['total'] ?></td>
<td><?= $r['tanggal'] ?></td>
</tr>
<?php } ?>
</table>

<!-- PAGINATION -->
<div class="pagination-box">
    Page <?= $page ?> / <?= $total_page ?>
</div>

<div class="nav-container">

<?php if($page > 1): ?>
    <a class="nav-btn btn-prev"
       href="?page=<?= $page-1 ?>&from=<?= $from ?>&to=<?= $to ?>">
       ⬅ Prev
    </a>
<?php endif; ?>

<?php if($page < $total_page): ?>
    <a class="nav-btn btn-next"
       href="?page=<?= $page+1 ?>&from=<?= $from ?>&to=<?= $to ?>">
       Next ➡
    </a>
<?php endif; ?>

</div>

</div>

<script>

function showInfo(el){
let stok = el.options[el.selectedIndex].dataset.stok;
let harga = el.options[el.selectedIndex].dataset.harga;

document.getElementById("info").innerHTML =
"📦 Stok: "+stok+" | 💰 Rp "+Number(harga).toLocaleString("id-ID");
}

<?php if($struk): ?>
document.getElementById("popup").style.display="block";
setTimeout(()=>document.getElementById("popup").style.display="none",2000);

new QRCode(document.getElementById("qrcode"), "<?= $struk['barang'] ?> <?= $struk['total'] ?>");

function pdf(){
const { jsPDF } = window.jspdf;
let doc = new jsPDF();
doc.text("STRUK",10,10);
doc.text("<?= $struk['barang'] ?>",10,20);
doc.text("Total <?= $struk['total'] ?>",10,30);
doc.save("struk.pdf");
}
<?php endif; ?>

</script>

   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>


<!-- BOTTOM NAV (BESAR + PREMIUM) -->
<div class="bottom-nav">
    <a href="karyawan.php">🏠</a>
    <a href="transfer.php">💸</a>
    <a href="data_transaksi_karyawan.php">📊</a>
    <a href="edit_profile_karyawan.php">👤</a>
</div>

</body>
</html>
