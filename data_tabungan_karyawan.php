<?php
session_start();
if (!isset($_SESSION['id_karyawan']) || $_SESSION['level'] != 'karyawan') {
  header('Location: login.php');
  exit;
}

require 'koneksi.php';
$id_karyawan = $_SESSION['id_karyawan'];

$sql = "SELECT * FROM robotv80_tabungan WHERE id_karyawan = '$id_karyawan'";
$result = mysqli_query($koneksi, $sql);
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>myROBOT-V80</title>
  <link href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png" rel="icon" type="image/png" />
<link rel="stylesheet" href="style-tabungan-karyawan.css">
<style>
/* RESET */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* BACKGROUND TERANG BIRU MUDA */
body {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(135deg, #dbeafe, #bfdbfe, #93c5fd);
  padding: 20px;
}

/* CONTAINER */
.container {
  width: 100%;
  max-width: 900px;
  background: #ffffffcc;
  backdrop-filter: blur(10px);
  border-radius: 16px;
  padding: 25px;
  box-shadow: 0 10px 30px rgba(59, 130, 246, 0.25);
  border: 1px solid #e0f2fe;
  color: #1e3a8a;
}

/* TITLE */
h2 {
  text-align: center;
  margin-bottom: 20px;
  font-size: 24px;
  color: #1d4ed8;
}

/* TABLE */
table {
  width: 100%;
  border-collapse: collapse;
  border-radius: 12px;
  overflow: hidden;
}

/* HEADER */
th {
  background: #60a5fa;
  color: white;
  padding: 12px;
  font-size: 13px;
  letter-spacing: 0.5px;
}

/* ROW */
td {
  padding: 12px;
  text-align: center;
  color: #1e3a8a;
}

/* STRIPED */
tr:nth-child(even) {
  background: #eff6ff;
}

/* HOVER */
tr:hover {
  background: #dbeafe;
  transition: 0.2s ease;
}

/* BUTTON */
.btn-kembali {
  display: inline-block;
  margin-top: 20px;
  padding: 10px 18px;
  background: #3b82f6;
  color: white;
  text-decoration: none;
  border-radius: 10px;
  font-weight: bold;
  transition: 0.3s ease;
}

.btn-kembali:hover {
  background: #2563eb;
  transform: translateY(-2px);
}

/* RESPONSIVE */
@media (max-width: 600px) {
  th, td {
    font-size: 12px;
    padding: 8px;
  }

  h2 {
    font-size: 18px;
  }
}
</style>
</head>
<body>
<div class="container">
<h2>Data Tabungan <?php echo $_SESSION['nama']; ?></h2>
<table>
<tr>
  <th>ID Tabungan</th>
  <th>Saldo</th>
  <th>Tanggal</th>
</tr>
<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
  <td><?php echo $row['id_tabungan']; ?></td>
  <td>Rp <?php echo number_format($row['saldo'],0,',','.'); ?></td>
  <td><?php echo date('d-m-Y', strtotime($row['tanggal'])); ?></td>
</tr>
<?php } ?>
</table>
<a href="karyawan.php" class="btn-kembali">Kembali</a>
</div>
</body>
</html>

