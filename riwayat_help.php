<?php
include "koneksi.php";

/* =========================
   SEARCH + PAGINATION
========================= */
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$offset = ($page - 1) * $limit;

/* =========================
   QUERY DATA (FIX SESUAI TABEL)
========================= */
$query = "
SELECT 
    h.id_help,
    h.id_karyawan,
    h.no_tlp,
    h.pesan,

    k.nama AS nama_karyawan,
    k.jabatan

FROM robotv80_help h

LEFT JOIN robotv80_karyawan k 
ON h.id_karyawan = k.id_karyawan

WHERE 
    k.nama LIKE '%$search%'
    OR h.no_tlp LIKE '%$search%'
    OR h.pesan LIKE '%$search%'

ORDER BY h.id_help DESC

LIMIT $limit OFFSET $offset
";

$result = mysqli_query($koneksi, $query);

if (!$result) {
    die("SQL ERROR DATA: " . mysqli_error($koneksi));
}

/* =========================
   TOTAL DATA
========================= */
$totalQuery = "
SELECT COUNT(*) as total
FROM robotv80_help h
LEFT JOIN robotv80_karyawan k 
ON h.id_karyawan = k.id_karyawan
WHERE 
    k.nama LIKE '%$search%'
    OR h.no_tlp LIKE '%$search%'
    OR h.pesan LIKE '%$search%'
";

$totalResult = mysqli_query($koneksi, $totalQuery);

$totalRow = mysqli_fetch_assoc($totalResult);
$total = $totalRow['total'] ?? 0;

$totalPage = max(1, ceil($total / $limit));
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>myROBOT-V80</title>
  <link href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png" rel="icon" type="image/png" />

<style>
body{
    font-family:Arial;
    background:linear-gradient(135deg,#667eea,#764ba2);
    padding:20px;
}

.container{
    max-width:900px;
    margin:auto;
    background:white;
    padding:20px;
    border-radius:16px;
}

/* search */
form{
    display:flex;
    gap:10px;
    margin-bottom:15px;
}

input{
    flex:1;
    padding:12px;
    border-radius:10px;
    border:1px solid #ddd;
}

button{
    padding:12px 15px;
    border:none;
    border-radius:10px;
    background:#4f46e5;
    color:white;
    cursor:pointer;
}

/* card */
.card{
    background:#f9fafb;
    padding:15px;
    margin-bottom:12px;
    border-radius:12px;
    border-left:5px solid #6366f1;
}

.header{
    font-weight:bold;
    color:#111827;
    display:flex;
    justify-content:space-between;
}

.sub{
    font-size:13px;
    color:#6b7280;
}

.pesan{
    margin-top:10px;
    background:white;
    padding:10px;
    border-radius:8px;
    border:1px solid #eee;
}

/* pagination */
.pagination{
    text-align:center;
    margin-top:15px;
}

.pagination a{
    padding:8px 12px;
    margin:3px;
    background:#e5e7eb;
    border-radius:8px;
    text-decoration:none;
}

.pagination a.active{
    background:#4f46e5;
    color:white;
}

/* back */
.back{
    display:inline-block;
    margin-bottom:15px;
    padding:10px 14px;
    background:#ef4444;
    color:white;
    border-radius:10px;
    text-decoration:none;
}
.btn-print{
    padding:12px 16px;
    background:linear-gradient(135deg,#10b981,#34d399);
    color:white;
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-weight:bold;
    margin-bottom:15px;
    transition:0.2s;
}

.btn-print:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 20px rgba(16,185,129,0.3);
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

/* saat print */
@media print {
    .btn-print,
    form,
    .pagination,
    .back {
        display:none;
    }

    body{
        background:white;
    }

    .container{
        box-shadow:none;
        border:none;
    }
}
</style>

</head>
<body>

<div class="container">

<a href="dashboard_admin.php" class="back">← Kembali</a>
<button class="btn-print" onclick="window.print()">🖨️ Cetak / PDF</button>

<h2 style="text-align:center;">RIWAYAT HELP CENTER</h2>

<!-- SEARCH -->
<form method="GET">
    <input type="text" name="search" placeholder="Cari nama / pesan / no HP..." value="<?= htmlspecialchars($search); ?>">
    <button>Cari</button>
</form>

<!-- DATA -->
<?php if(mysqli_num_rows($result) > 0){ ?>
    <?php while($row = mysqli_fetch_assoc($result)){ ?>

<div class="card">

    <div class="header">
        <div>
            ID Help: <?= $row['id_help']; ?> | ID Karyawan: <?= $row['id_karyawan']; ?>
        </div>
        <div class="sub">
            <?= $row['nama_karyawan'] ?? 'Unknown'; ?> (<?= $row['jabatan'] ?? '-'; ?>)
        </div>
    </div>

    <div class="pesan">
        <?= nl2br($row['pesan']); ?>
    </div>

    <div class="sub" style="margin-top:8px;">
        📞 <?= $row['no_tlp']; ?>
    </div>

</div>

    <?php } ?>
<?php } else { ?>
    <p style="text-align:center;">Belum ada data help</p>
<?php } ?>

<!-- PAGINATION -->
<div class="pagination">

<?php if($page > 1){ ?>
<a href="?page=<?= $page-1 ?>&search=<?= urlencode($search); ?>">Prev</a>
<?php } ?>

<span>Page <?= $page ?> / <?= $totalPage ?></span>

<?php if($page < $totalPage){ ?>
<a href="?page=<?= $page+1 ?>&search=<?= urlencode($search); ?>">Next</a>
<?php } ?>

</div>

</div>

<p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>

<!-- BOTTOM NAV -->
<div class="bottom-nav">

    <a href="dashboard_admin.php">🏠</a>
    <a href="data_transaksi.php">📊</a>
    <a href="data_nasabah.php">👥</a>
    <a href="logout.php">🚪</a>

</div>

</body>
</html>
