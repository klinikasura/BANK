<?php
require 'koneksi.php';

$id_karyawan = $_GET['id_karyawan'];

$sql = "SELECT t.*, k.nama, k.no_tlp, k.jabatan
        FROM robotv80_tabungan t
        JOIN robotv80_karyawan k ON t.id_karyawan = k.id_karyawan
        WHERE t.id_karyawan = '$id_karyawan'";

$result = mysqli_query($koneksi, $sql);

$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>myROBOT-V80</title>
  <link href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png" rel="icon" type="image/png" />
<style>
body {
    font-family: Arial;
    padding: 20px;
}

.card {
    border: 1px solid #ddd;
    padding: 20px;
    border-radius: 10px;
}

h2 {
    text-align: center;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table, th, td {
    border: 1px solid #000;
}

th, td {
    padding: 10px;
    text-align: left;
}

.btn {
    margin-top: 20px;
}

@media print {
    .btn {
        display: none;
    }
}
</style>
</head>

<body>

<div class="card">

<h2>🏦 REKENING PRIBADI KARYAWAN</h2>

<p><b>Nama:</b> <?php echo $data['nama']; ?></p>
<p><b>Jabatan:</b> <?php echo $data['jabatan']; ?></p>
<p><b>No HP:</b> <?php echo $data['no_tlp']; ?></p>

<table>
<tr>
    <th>ID Tabungan</th>
    <th>Saldo</th>
    <th>Tanggal Pembuatan Rekening</th>
</tr>

<?php
mysqli_data_seek($result, 0);
while ($row = mysqli_fetch_assoc($result)) {
?>
<tr>
    <td><?php echo $row['id_tabungan']; ?></td>
    <td>Rp <?php echo number_format($row['saldo'],0,',','.'); ?></td>
    <td><?php echo $row['tanggal']; ?></td>
</tr>
<?php } ?>

</table>

<button class="btn" onclick="window.print()">🖨 Cetak / Print</button>

</div>

</body>
</html>
