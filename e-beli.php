<?php
include "koneksi.php";

// ================== SEARCH ==================
$search = isset($_GET['search']) ? $_GET['search'] : "";

// ================== PAGINATION ==================
$limit = 5;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// ================== TAMBAH DATA ==================
if (isset($_POST['simpan'])) {
    $nama = $_POST['nama_barang'];
    $harga = $_POST['harga'];
    $stok  = $_POST['stok'];

    $koneksi->query("INSERT INTO robotv80_barang (nama_barang, harga, stok)
    VALUES ('$nama', '$harga', '$stok')");
    header("Location: e-beli.php");
}

// ================== HAPUS DATA ==================
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $koneksi->query("DELETE FROM robotv80_barang WHERE id_barang=$id");
    header("Location: e-beli.php");
}

// ================== EDIT DATA (AMBIL DATA) ==================
$editData = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $result = $koneksi->query("SELECT * FROM robotv80_barang WHERE id_barang=$id");
    $editData = $result->fetch_assoc();
}

// ================== UPDATE DATA ==================
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama_barang'];
    $harga = $_POST['harga'];
    $stok  = $_POST['stok'];

    $koneksi->query("UPDATE robotv80_barang SET
        nama_barang='$nama',
        harga='$harga',
        stok='$stok'
        WHERE id_barang=$id
    ");

    header("Location: e-beli.php");
}

// ================== QUERY DATA ==================
$data = $koneksi->query("
    SELECT * FROM robotv80_barang 
    WHERE nama_barang LIKE '%$search%'
    LIMIT $limit OFFSET $offset
");

// total data
$totalData = $koneksi->query("
    SELECT COUNT(*) as total FROM robotv80_barang
    WHERE nama_barang LIKE '%$search%'
")->fetch_assoc()['total'];

$totalPage = ceil($totalData / $limit);
?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, sans-serif;
    background: linear-gradient(135deg, #eef2f7, #f8fafc);
    margin: 0;
    padding: 20px;
    color: #1f2937;
}

/* container (kalau mau pakai nanti) */
.container {
    max-width: 1100px;
    margin: auto;
}

/* judul */
h2 {
    text-align: center;
    font-size: 28px;
    margin-bottom: 10px;
    color: #111827;
}

h3 {
    margin-top: 20px;
    color: #374151;
}

/* form box */
form {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    background: white;
    padding: 15px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    margin-bottom: 15px;
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
/* input */
input {
    flex: 1;
    min-width: 150px;
    padding: 12px;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    outline: none;
    transition: 0.2s;
    background: #f9fafb;
}

input:focus {
    border-color: #6366f1;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.2);
}

/* tombol utama */
button {
    padding: 12px 16px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    color: white;
    font-weight: 600;
    transition: 0.2s;
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(79,70,229,0.3);
}

/* tombol cetak */
button[onclick] {
    background: linear-gradient(135deg, #10b981, #34d399);
    margin-bottom: 10px;
}

/* table */
table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
}

/* header table */
th {
    background: #4f46e5;
    color: white;
    padding: 14px;
    font-size: 14px;
}

/* isi table */
td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #f1f5f9;
}

/* hover row */
tr:hover td {
    background: #f8fafc;
}

/* tombol aksi */
a {
    text-decoration: none;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    margin: 0 2px;
}

/* edit */
a[href*="edit"] {
    background: #f59e0b;
    color: white;
}

/* hapus */
a[href*="hapus"] {
    background: #ef4444;
    color: white;
}

/* pagination */
div a {
    display: inline-block;
    margin: 5px;
    padding: 8px 12px;
    background: #e5e7eb;
    color: #111827;
    border-radius: 8px;
    text-decoration: none;
    transition: 0.2s;
}

div a:hover {
    background: #4f46e5;
    color: white;
}

/* responsive */
@media (max-width: 768px) {
    form {
        flex-direction: column;
    }

    table {
        font-size: 13px;
    }
}
body {
    font-family: Arial, sans-serif;
    background: #f4f6f9;
    margin: 0;
    padding: 20px;
}

/* container */
.container {
    max-width: 1000px;
    margin: auto;
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}

/* judul */
h2 {
    text-align: center;
    color: #333;
}

/* form */
form {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 15px;
}

input {
    flex: 1;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 8px;
    outline: none;
    transition: 0.2s;
}

input:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 5px rgba(79,70,229,0.3);
}

/* tombol */
button {
    padding: 10px 15px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    background: #4f46e5;
    color: white;
    transition: 0.2s;
}

button:hover {
    background: #3730a3;
}

/* tombol print */
.print-btn {
    background: #10b981;
    margin-bottom: 10px;
}

.print-btn:hover {
    background: #059669;
}

/* table */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    overflow: hidden;
    border-radius: 10px;
}

table thead {
    background: #4f46e5;
    color: white;
}

table th, table td {
    padding: 12px;
    text-align: center;
    border-bottom: 1px solid #eee;
}

table tr:hover {
    background: #f1f5f9;
}

/* link tombol aksi */
a {
    text-decoration: none;
    padding: 6px 10px;
    border-radius: 6px;
    color: white;
    font-size: 12px;
}

a[href*="edit"] {
    background: #f59e0b;
}

a[href*="hapus"] {
    background: #ef4444;
}

/* pagination */
.pagination {
    margin-top: 15px;
    text-align: center;
}

.pagination a {
    background: #e5e7eb;
    color: #333;
    margin: 0 5px;
}

.pagination a:hover {
    background: #4f46e5;
    color: white;
}
.btn-back {
    display: inline-block;
    padding: 10px 15px;
    background: #6b7280;
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    transition: 0.2s;
}

.btn-back:hover {
    background: #374151;
    transform: translateY(-2px);
}

/* responsive */
@media (max-width: 600px) {
    form {
        flex-direction: column;
    }
}
</style>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>myROBOT-V80</title>
  <link href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png" rel="icon" type="image/png" />
</head>
<body>

<h2>Data Barang</h2>

<!-- ================== SEARCH ================== -->
<form method="GET">
    <input type="text" name="search" placeholder="Cari barang..." value="<?= $search ?>">
    <button type="submit">Cari</button>
</form>

<!-- ================== FORM INPUT ================== -->
<h3><?= $editData ? "Edit Barang" : "Tambah Barang" ?></h3>

<form method="POST">
    <?php if ($editData) { ?>
        <input type="hidden" name="id" value="<?= $editData['id_barang'] ?>">
    <?php } ?>

    <input type="text" name="nama_barang" placeholder="Nama Barang"
        value="<?= $editData['nama_barang'] ?? '' ?>" required>

    <input type="number" name="harga" placeholder="Harga"
        value="<?= $editData['harga'] ?? '' ?>" required>

    <input type="number" name="stok" placeholder="Stok"
        value="<?= $editData['stok'] ?? '' ?>" required>

    <button type="submit" name="<?= $editData ? 'update' : 'simpan' ?>">
        <?= $editData ? 'Update' : 'Simpan' ?>
    </button>
</form>

<!-- ================== CETAK ================== -->
<button onclick="window.print()">Cetak</button>
<a href="dashboard_admin.php" class="btn-back">Kembali</a>
<a href="riwayat_pembelian.php"
   style="
        display:inline-block;
        padding:10px 14px;
        background:#2563eb;
        color:white;
        text-decoration:none;
        border-radius:10px;
        margin-left:10px;
   ">
📦 Riwayat Pembelian
</a>


<!-- ================== TABLE ================== -->
<table border="1" cellpadding="10">
    <tr>
        <th>ID</th>
        <th>Nama Barang</th>
        <th>Harga</th>
        <th>Stok</th>
        <th>Aksi</th>
    </tr>

    <?php while ($row = $data->fetch_assoc()) { ?>
    <tr>
        <td><?= $row['id_barang'] ?></td>
        <td><?= $row['nama_barang'] ?></td>
        <td><?= $row['harga'] ?></td>
        <td><?= $row['stok'] ?></td>
        <td>
            <a href="?edit=<?= $row['id_barang'] ?>">Edit</a> <p>
            <a href="?hapus=<?= $row['id_barang'] ?>" onclick="return confirm('Hapus data?')">Hapus</a>
        </td>
    </tr>
    <?php } ?>
</table>

<!-- ================== PAGINATION ================== -->
<div style="margin-top:10px;">
    <?php if ($page > 1) { ?>
        <a href="?page=<?= $page - 1 ?>&search=<?= $search ?>">Prev</a>
    <?php } ?>

    <span> Page <?= $page ?> </span>

    <?php if ($page < $totalPage) { ?>
        <a href="?page=<?= $page + 1 ?>&search=<?= $search ?>">Next</a>
    <?php } ?>
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
