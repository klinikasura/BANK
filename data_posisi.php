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
   TAMBAH DATA
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
   HAPUS DATA
========================= */
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];

    mysqli_query($koneksi, "DELETE FROM robot80_tb_kelas WHERE id='$id'");

    header("Location: data_posisi.php?cari=$cari&halaman=$halaman");
    exit;
}

/* =========================
   UPDATE DATA
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
   EDIT DATA
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

if (!$query_total) {
    die("SQL ERROR TOTAL: " . mysqli_error($koneksi));
}

$data_total = mysqli_fetch_assoc($query_total);
$total_data = $data_total['total'] ?? 0;

$total_halaman = ($total_data > 0) ? ceil($total_data / $batas) : 1;

/* =========================
   DATA
========================= */
$data = mysqli_query($koneksi, "
    SELECT * FROM robot80_tb_kelas
    WHERE nama_kelas LIKE '%$cari_safe%'
    ORDER BY id DESC
    LIMIT $mulai, $batas
");

if (!$data) {
    die("SQL ERROR DATA: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>myROBOT-V80</title>
  <link href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png" rel="icon" type="image/png" />

<style>
body {
    font-family: 'Segoe UI', sans-serif;
    background: #f4f6f9;
    margin: 0;
    padding: 20px;
}

.container {
    background: white;
    padding: 20px;
    border-radius: 14px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

/* FORM */
form {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

input {
    flex: 1;
    padding: 11px;
    border: 1px solid #ddd;
    border-radius: 10px;
    outline: none;
}

input:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79,70,229,0.2);
}

/* BUTTON */
button {
    padding: 11px 16px;
    border: none;
    background: linear-gradient(135deg,#4f46e5,#6366f1);
    color: white;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 600;
}

button:hover {
    transform: translateY(-2px);
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    overflow: hidden;
    border-radius: 10px;
}

th {
    background: #4f46e5;
    color: white;
    padding: 12px;
    text-align: left;
}

td {
    padding: 12px;
    border-bottom: 1px solid #eee;
}

tr:hover {
    background: #f1f5ff;
}

/* ACTION */
a.btn {
    padding: 6px 10px;
    border-radius: 7px;
    color: white;
    text-decoration: none;
    font-size: 12px;
    margin-right: 5px;
}

.edit { background: #f59e0b; }
.hapus { background: #ef4444; }

/* PAGINATION */
.pagination a {
    padding: 8px 12px;
    margin-right: 5px;
    background: #e5e7eb;
    border-radius: 8px;
    text-decoration: none;
}

.active {
    background: #4f46e5 !important;
    color: white !important;
}
.btn-back {
    display: inline-block;
    margin-bottom: 15px;
    padding: 10px 14px;
    background: #111827;
    color: white;
    text-decoration: none;
    border-radius: 10px;
    font-weight: 600;
    transition: 0.3s;
}

.btn-back:hover {
    background: #374151;
    transform: translateY(-2px);
}
</style>
</head>

<body>

<div class="container">
<a href="dashboard_admin.php" class="btn-back">← Kembali</a>

<h2>Data Kelas</h2>



<!-- SEARCH -->
<form method="GET">
    <input type="text" name="cari" placeholder="Cari kelas..." value="<?php echo htmlspecialchars($cari); ?>">
    <button type="submit">Cari</button>
</form>

<!-- TAMBAH DATA -->
<form method="POST">
    <input type="text" name="nama_kelas" placeholder="Tambah kelas..." required>
    <button type="submit" name="simpan">+ Tambah</button>
</form>

<!-- EDIT FORM -->
<?php if ($edit): ?>
<form method="POST">
    <input type="hidden" name="id" value="<?php echo $e['id']; ?>">
    <input type="text" name="nama_kelas" value="<?php echo htmlspecialchars($e['nama_kelas']); ?>" required>
    <button type="submit" name="update">Update</button>
</form>

<?php endif; ?>

<!-- TABLE -->
<table>
<tr>
    <th>No</th>
    <th>Nama Kelas</th>
    <th>Aksi</th>
</tr>

<?php
$no = $mulai + 1;

if (mysqli_num_rows($data) > 0) {
    while ($row = mysqli_fetch_assoc($data)) {
?>
<tr>
    <td><?php echo $no++; ?></td>
    <td><?php echo htmlspecialchars($row['nama_kelas']); ?></td>
    <td>
        <a class="btn edit"
           href="?edit=<?php echo $row['id']; ?>&cari=<?php echo urlencode($cari); ?>&halaman=<?php echo $halaman; ?>">
           Edit
        </a>

        <a class="btn hapus"
           href="?hapus=<?php echo $row['id']; ?>&cari=<?php echo urlencode($cari); ?>&halaman=<?php echo $halaman; ?>"
           onclick="return confirm('Yakin hapus data ini?')">
           Hapus
        </a>
    </td>
</tr>
<?php
    }
} else {
    echo "<tr><td colspan='3' style='text-align:center;'>Data tidak ditemukan</td></tr>";
}
?>

</table>

<!-- PAGINATION -->
<div style="margin-top:15px;">
<?php for ($i = 1; $i <= $total_halaman; $i++) { ?>
    <a class="<?php if ($i == $halaman) echo 'active'; ?>"
       href="?halaman=<?php echo $i; ?>&cari=<?php echo urlencode($cari); ?>">
       <?php echo $i; ?>
    </a>
<?php } ?>
</div>

</div>

</body>
</html>
