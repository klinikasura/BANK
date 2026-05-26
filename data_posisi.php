<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}

/* =========================
   SEARCH + PAGINATION
========================= */
$batas = 5;
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$halaman = max(1, $halaman);
$mulai = ($halaman - 1) * $batas;

$cari = isset($_GET['cari']) ? trim($_GET['cari']) : "";
$cari_safe = mysqli_real_escape_string($koneksi, $cari);

/* =========================
   TAMBAH
========================= */
if (isset($_POST['simpan'])) {
    $nama_kelas = mysqli_real_escape_string($koneksi, $_POST['nama_kelas']);

    mysqli_query($koneksi, "
        INSERT INTO robot80_tb_kelas (nama_kelas)
        VALUES ('$nama_kelas')
    ");

    header("Location: data_posisi.php?cari=$cari&halaman=$halaman");
    exit;
}

/* =========================
   HAPUS
========================= */
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];

    mysqli_query($koneksi, "DELETE FROM robot80_tb_kelas WHERE id='$id'");

    header("Location: data_posisi.php?cari=$cari&halaman=$halaman");
    exit;
}

/* =========================
   UPDATE
========================= */
if (isset($_POST['update'])) {
    $id = (int)$_POST['id'];
    $nama_kelas = mysqli_real_escape_string($koneksi, $_POST['nama_kelas']);

    mysqli_query($koneksi, "
        UPDATE robot80_tb_kelas 
        SET nama_kelas='$nama_kelas'
        WHERE id='$id'
    ");

    header("Location: data_posisi.php?cari=$cari&halaman=$halaman");
    exit;
}

/* =========================
   EDIT
========================= */
$edit = false;
$e = [];

if (isset($_GET['edit'])) {
    $edit = true;
    $id_edit = (int)$_GET['edit'];

    $ambil = mysqli_query($koneksi, "SELECT * FROM robot80_tb_kelas WHERE id='$id_edit'");
    $e = mysqli_fetch_assoc($ambil);
}

/* =========================
   TOTAL DATA
========================= */
$query_total = mysqli_query($koneksi, "
SELECT COUNT(*) as total 
FROM robot80_tb_kelas 
WHERE nama_kelas LIKE '%$cari_safe%'
");

$data_total = mysqli_fetch_assoc($query_total);
$total_data = $data_total['total'] ?? 0;

$total_halaman = max(1, ceil($total_data / $batas));

/* =========================
   DATA
========================= */
$data = mysqli_query($koneksi, "
SELECT * FROM robot80_tb_kelas
WHERE nama_kelas LIKE '%$cari_safe%'
ORDER BY id DESC
LIMIT $mulai, $batas
");
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Posisi</title>

<style>
body{
    font-family:Segoe UI;
    background:#f4f6f9;
    padding:20px;
}

.container{
    background:#fff;
    padding:20px;
    border-radius:14px;
    max-width:1000px;
    margin:auto;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
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

h2{margin-bottom:15px;}

form{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    margin-bottom:15px;
}

input{
    flex:1;
    padding:10px;
    border:1px solid #ddd;
    border-radius:10px;
}

button{
    padding:10px 14px;
    border:none;
    background:#4f46e5;
    color:white;
    border-radius:10px;
    cursor:pointer;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#4f46e5;
    color:white;
    padding:12px;
}

td{
    padding:12px;
    border-bottom:1px solid #eee;
}

tr:hover{
    background:#f1f5ff;
}

.btn{
    padding:6px 10px;
    border-radius:6px;
    color:white;
    text-decoration:none;
    font-size:12px;
}

.edit{background:#f59e0b;}
.hapus{background:#ef4444;}

.pagination{
    margin-top:20px;
    text-align:center;
}

.pagination a{
    padding:8px 12px;
    margin:3px;
    background:#e5e7eb;
    border-radius:8px;
    text-decoration:none;
    color:black;
    display:inline-block;
}

.pagination a.active{
    background:#4f46e5;
    color:white;
}

.nav-bottom{
    position:fixed;
    bottom:0;
    left:0;
    right:0;
    background:white;
    display:flex;
    justify-content:space-around;
    padding:12px;
    border-top:1px solid #ddd;
}
</style>

</head>
<body>

<div class="container">

<h2>Data Posisi</h2>

<!-- SEARCH -->
<form method="GET">
    <input type="text" name="cari" value="<?= htmlspecialchars($cari); ?>" placeholder="Cari...">
    <button>Cari</button>
</form>

<!-- TAMBAH -->
<form method="POST">
    <input type="text" name="nama_kelas" placeholder="Tambah posisi..." required>
    <button name="simpan">Tambah</button>
</form>

<!-- EDIT -->
<?php if ($edit): ?>
<form method="POST">
    <input type="hidden" name="id" value="<?= $e['id']; ?>">
    <input type="text" name="nama_kelas" value="<?= htmlspecialchars($e['nama_kelas']); ?>" required>
    <button name="update">Update</button>
</form>
<?php endif; ?>

<!-- TABLE -->
<table>
<tr>
    <th>No</th>
    <th>Nama</th>
    <th>Aksi</th>
</tr>

<?php
$no = $mulai + 1;
if(mysqli_num_rows($data) > 0){
while($row = mysqli_fetch_assoc($data)){
?>
<tr>
    <td><?= $no++; ?></td>
    <td><?= htmlspecialchars($row['nama_kelas']); ?></td>
    <td>
        <a class="btn edit" href="?edit=<?= $row['id']; ?>&cari=<?= urlencode($cari); ?>&halaman=<?= $halaman; ?>">Edit</a>
        <a class="btn hapus" href="?hapus=<?= $row['id']; ?>&cari=<?= urlencode($cari); ?>&halaman=<?= $halaman; ?>" onclick="return confirm('Hapus?')">Hapus</a>
    </td>
</tr>
<?php }} else { ?>
<tr><td colspan="3">Data kosong</td></tr>
<?php } ?>

</table>

<!-- PAGINATION -->
<div class="pagination">

<?php if($halaman > 1){ ?>
<a href="?halaman=<?= $halaman-1; ?>&cari=<?= urlencode($cari); ?>">Prev</a>
<?php } ?>

<?php for($i=1;$i<=$total_halaman;$i++){ ?>
<a class="<?= ($i==$halaman)?'active':''; ?>" href="?halaman=<?= $i; ?>&cari=<?= urlencode($cari); ?>">
<?= $i; ?>
</a>
<?php } ?>

<?php if($halaman < $total_halaman){ ?>
<a href="?halaman=<?= $halaman+1; ?>&cari=<?= urlencode($cari); ?>">Next</a>
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
