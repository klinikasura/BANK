<?php 
session_start(); 
if (!isset($_SESSION['id_admin'])) { 
  header('Location: login.php'); 
  exit;
} 

require 'koneksi.php'; 

$sql = "SELECT t.id_tabungan, t.id_karyawan, k.nama, k.no_tlp, k.jabatan, t.saldo, t.tanggal 
        FROM robotv80_tabungan t 
        JOIN robotv80_karyawan k ON t.id_karyawan = k.id_karyawan";

$result = mysqli_query($koneksi, $sql); 

if (!$result) {
  die("Query Error: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>myROBOT-V80</title>
  <link href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png" rel="icon" type="image/png" />

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Segoe UI', sans-serif;
}

body {
    min-height: 100vh;
    background: linear-gradient(135deg, #e0f2fe, #f8fafc);
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 25px;
    color: #0f172a;
}

.container {
    width: 100%;
    max-width: 1200px;
    background: #fff;
    border-radius: 20px;
    padding: 25px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.1);
}

/* TITLE */
h2 {
    margin-bottom: 15px;
}

/* TOP BAR */
.topbar {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 15px;
}

/* BUTTON */
.btn {
    padding: 10px 14px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 600;
    font-size: 14px;
}

.btn-primary {
    background: #0ea5e9;
    color: #fff;
}

.btn-secondary {
    background: #e2e8f0;
    color: #0f172a;
}

/* SEARCH */
#search {
    padding: 10px;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    width: 250px;
}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
    border: 1px solid #cbd5e1;
}

th {
    background: #0ea5e9;
    color: white;
    padding: 12px;
    border: 1px solid #0284c7;
}

td {
    padding: 12px;
    border: 1px solid #e2e8f0;
    font-size: 14px;
}

tr:nth-child(even) {
    background: #f8fafc;
}

tr:hover {
    background: #e0f2fe;
}

/* ACTION BUTTON */
.action-btn {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    margin: 2px;
    text-decoration: none;
}

/* EDIT */
.edit { background: #22c55e; color: white; }
.edit:hover { background: #16a34a; }

/* DELETE */
.delete { background: #ef4444; color: white; }
.delete:hover { background: #dc2626; }

/* PRINT */
.print { background: #8b5cf6; color: white; }
.print:hover { background: #7c3aed; }

/* PAGINATION */
.pagination {
    margin-top: 15px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 10px;
}

.pagination button {
    padding: 8px 12px;
    border: none;
    border-radius: 8px;
    background: #e2e8f0;
    cursor: pointer;
    font-weight: 600;
}

.pagination button:hover {
    background: #cbd5e1;
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

<h2>💰 Data Tabungan Bank</h2>

<div class="topbar">

    <div>
        <a href="tambah_tabungan.php" class="btn btn-primary">+ Tambah</a>
        <a href="dashboard_admin.php" class="btn btn-secondary">⬅ Kembali</a>
    </div>

    <input type="text" id="search" placeholder="🔍 Cari data...">

</div>

<table id="tabelData">

<tr>
    <th>ID Tabungan</th>
    <th>ID Karyawan</th>
    <th>Nama</th>
    <th>Jabatan</th>
    <th>No HP</th>
    <th>Saldo</th>
    <th>Tanggal Pembuatan Rekening</th>
    <th>Aksi</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?php echo $row['id_tabungan']; ?></td>
    <td><?php echo $row['id_karyawan']; ?></td>
    <td><?php echo $row['nama']; ?></td>
    <td><?php echo $row['jabatan']; ?></td>
    <td><?php echo $row['no_tlp']; ?></td>
    <td>Rp <?php echo number_format($row['saldo'],0,',','.'); ?></td>
    <td><?php echo $row['tanggal']; ?></td>
    <td>
        <a class="action-btn edit" href="edit_tabungan.php?id_tabungan=<?php echo $row['id_tabungan']; ?>">Edit</a>
        <a class="action-btn delete" onclick="return confirm('Hapus data?')" href="hapus_tabungan.php?id_tabungan=<?php echo $row['id_tabungan']; ?>">Hapus</a>
        <a class="action-btn print" target="_blank" href="cetak_rekening.php?id_karyawan=<?php echo $row['id_karyawan']; ?>">Cetak</a>
    </td>
</tr>
<?php } ?>

</table>

<div class="pagination">
    <button onclick="prevPage()">⬅ Prev</button>
    <span id="pageInfo"></span>
    <button onclick="nextPage()">Next ➡</button>
</div>

</div>

<script>
let currentPage = 1;
const rowsPerPage = 5;

const table = document.getElementById("tabelData");
const rows = table.getElementsByTagName("tr");
const search = document.getElementById("search");

function display() {
    let filter = search.value.toLowerCase();
    let visible = [];

    for (let i = 1; i < rows.length; i++) {
        let text = rows[i].innerText.toLowerCase();

        if (text.includes(filter)) {
            visible.push(rows[i]);
        } else {
            rows[i].style.display = "none";
        }
    }

    let totalPages = Math.ceil(visible.length / rowsPerPage);
    if (currentPage > totalPages) currentPage = 1;

    visible.forEach((row, i) => {
        row.style.display =
            (i >= (currentPage - 1) * rowsPerPage && i < currentPage * rowsPerPage)
            ? "" : "none";
    });

    document.getElementById("pageInfo").innerText =
        "Page " + currentPage + " / " + (totalPages || 1);
}

function nextPage() {
    currentPage++;
    display();
}

function prevPage() {
    if (currentPage > 1) currentPage--;
    display();
}

search.addEventListener("keyup", function() {
    currentPage = 1;
    display();
});

window.onload = display;
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
