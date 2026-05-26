<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}

/* =========================
   SIMPAN DATA
========================= */
if (isset($_POST['simpan'])) {

    $nama   = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kelas  = mysqli_real_escape_string($koneksi, $_POST['kelas']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $notlp  = mysqli_real_escape_string($koneksi, $_POST['notlp']);

    mysqli_query($koneksi, "
        INSERT INTO robot80_tb_siswa
        (nama, kelas, alamat, notlp)
        VALUES
        ('$nama','$kelas','$alamat','$notlp')
    ");

    header("Location: data_nasabah.php");
    exit;
}

/* =========================
   HAPUS
========================= */
if (isset($_GET['hapus'])) {

    $id = (int) $_GET['hapus'];

    mysqli_query($koneksi,"
        DELETE FROM robot80_tb_siswa
        WHERE id='$id'
    ");

    header("Location: data_nasabah.php");
    exit;
}

/* =========================
   EDIT
========================= */
$edit = false;

if (isset($_GET['edit'])) {

    $edit = true;

    $id_edit = (int) $_GET['edit'];

    $e = mysqli_fetch_assoc(
        mysqli_query($koneksi,"
            SELECT * FROM robot80_tb_siswa
            WHERE id='$id_edit'
        ")
    );
}

/* =========================
   UPDATE
========================= */
if (isset($_POST['update'])) {

    $id = (int) $_POST['id'];

    mysqli_query($koneksi,"
        UPDATE robot80_tb_siswa SET
        nama='{$_POST['nama']}',
        kelas='{$_POST['kelas']}',
        alamat='{$_POST['alamat']}',
        notlp='{$_POST['notlp']}'
        WHERE id='$id'
    ");

    header("Location: data_nasabah.php");
    exit;
}

/* =========================
   PAGINATION + SEARCH
========================= */

$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $_GET['search'] : "";

/* DATA */
$data = mysqli_query($koneksi,"
    SELECT * FROM robot80_tb_siswa
    WHERE nama LIKE '%$search%'
    ORDER BY id DESC
    LIMIT $start, $limit
");

/* TOTAL */
$total = mysqli_fetch_assoc(
    mysqli_query($koneksi,"
        SELECT COUNT(*) as total
        FROM robot80_tb_siswa
        WHERE nama LIKE '%$search%'
    ")
);

$total_page = ceil($total['total'] / $limit);
?>

<!DOCTYPE html>
<html lang="id">
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
    background:linear-gradient(135deg,#74ebd5,#ACB6E5);
    padding:30px;
}

.container{
    max-width:1100px;
    margin:auto;
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 10px 30px rgba(0,0,0,0.2);
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


h2{
    margin-bottom:15px;
}

input,select,textarea{
    width:100%;
    padding:10px;
    margin:5px 0 15px;
    border-radius:8px;
    border:1px solid #ccc;
}

button{
    background:#2575fc;
    color:white;
    border:none;
    padding:10px 15px;
    border-radius:8px;
    cursor:pointer;
}

button:hover{
    background:#1d4ed8;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

table th,table td{
    border:1px solid #ddd;
    padding:10px;
    text-align:center;
}

table th{
    background:#2575fc;
    color:white;
}

.btn-edit{
    background:orange;
    padding:5px 10px;
    color:white;
    text-decoration:none;
    border-radius:5px;
}

.btn-hapus{
    background:red;
    padding:5px 10px;
    color:white;
    text-decoration:none;
    border-radius:5px;
}

/* SEARCH */
.search-box{
    display:flex;
    gap:10px;
    margin-bottom:10px;
}

.search-box input{
    flex:1;
}

/* PAGINATION */
.pagination{
    margin-top:20px;
    text-align:center;
}

.pagination a{
    padding:8px 12px;
    margin:2px;
    border:1px solid #2575fc;
    text-decoration:none;
    border-radius:6px;
    color:#2575fc;
}

.pagination a.active{
    background:#2575fc;
    color:white;
}
.btn-back{
    display:inline-block;
    margin-bottom:15px;
    padding:10px 15px;
    background:linear-gradient(135deg,#6c757d,#495057);
    color:white;
    text-decoration:none;
    border-radius:8px;
    font-weight:600;
    transition:0.3s;
}

.btn-back:hover{
    transform:translateY(-2px);
    background:linear-gradient(135deg,#495057,#343a40);
    box-shadow:0 8px 20px rgba(0,0,0,0.2);
}
</style>
</head>

<body>


<div class="container">

<h2>📋 Data Nasabah Bank</h2>
<a href="dashboard_admin.php" class="btn-back">⬅ Kembali</a>

<!-- SEARCH -->
<form method="GET" class="search-box">
    <input type="text" name="search"
    placeholder="🔍 Cari nama nasabah..."
    value="<?= $search ?>">
    <button type="submit">Cari</button>
</form>

<!-- FORM -->
<form method="POST">

<?php if($edit){ ?>

<input type="hidden" name="id" value="<?= $e['id'] ?>">

<label>Nama</label>
<input type="text" name="nama" value="<?= $e['nama'] ?>">

<label>Posisi</label>
<input type="text" name="kelas" value="<?= $e['kelas'] ?>">

<label>Link Photo</label>
<textarea name="alamat"><?= $e['alamat'] ?></textarea>

<label>No HP</label>
<input type="text" name="notlp" value="<?= $e['notlp'] ?>">

<button name="update">Update</button>

<?php } else { ?>

<label>Nama</label>
<input type="text" name="nama" required>

<label>Posisi</label>
<input type="text" name="kelas" required>

<label>Link Photo</label>
<textarea name="alamat" required></textarea>

<label>No HP</label>
<input type="text" name="notlp" required>

<button name="simpan">Simpan</button>

<?php } ?>

</form>

<!-- TABLE -->
<table>

<tr>
<th>No</th>
<th>Nama</th>
<th>Posisi</th>
<th>Link Photo</th>
<th>No HP</th>
<th>Aksi</th>
</tr>

<?php $no=1; while($d=mysqli_fetch_array($data)) { ?>

<tr>
<td><?= $no++ ?></td>
<td><?= $d['nama'] ?></td>
<td><?= $d['kelas'] ?></td>
<td><?= $d['alamat'] ?></td>
<td><?= $d['notlp'] ?></td>
<td>
<a class="btn-edit" href="?edit=<?= $d['id'] ?>">Edit</a><p>
<a class="btn-hapus" href="?hapus=<?= $d['id'] ?>"
onclick="return confirm('Hapus data?')">Hapus</a>
</td>
</tr>

<?php } ?>

</table>

<!-- PAGINATION -->
<div class="pagination">

<?php for($i=1; $i<=$total_page; $i++) { ?>

<a class="<?= ($i==$page)?'active':'' ?>"
href="?page=<?= $i ?>&search=<?= $search ?>">
<?= $i ?>
</a>

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
