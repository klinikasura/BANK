<?php
session_start();
if (!isset($_SESSION['id_admin'])) {
  header('Location: login.php');
}

require 'koneksi.php';

if (isset($_POST['submit'])) {
  $id_karyawan = $_POST['id_karyawan'];
  $saldo = $_POST['saldo'];
  $tanggal = date('Y-m-d');

  $sql = "INSERT INTO robotv80_tabungan (id_karyawan, saldo, tanggal) VALUES ('$id_karyawan', '$saldo', '$tanggal')";
  mysqli_query($koneksi, $sql);

  header('Location: data_tabungan.php');
}
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>myROBOT-V80</title>
  <link href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png" rel="icon" type="image/png" />
  <link rel="stylesheet" href="style.css">
<style>
.button-group{
  display:flex;
  gap:10px;
  margin-top:20px;
}

.button-group input[type="submit"]{
  flex:1;
}

.btn-kembali{
  flex:1;
  text-align:center;
  background:#28a745;
  color:white;
  text-decoration:none;
  padding:12px;
  border-radius:8px;
  font-weight:bold;
}

.btn-kembali:hover{
  background:#1e7e34;
}
</style>
</head>
<body>
  <div class="container">
    <h2>Tambah Tabungan</h2>
    <form action="" method="post">
      <label for="id_karyawan">ID Karyawan:</label>
      <select id="id_karyawan" name="id_karyawan">
        <?php
        $sql = "SELECT * FROM robotv80_karyawan";
        $result = mysqli_query($koneksi, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
          echo "<option value='".$row['id_karyawan']."'>".$row['nama']."</option>";
        }
        ?>
      </select><br><br>
      <label for="saldo">Saldo:</label>
      <input type="text" id="saldo" name="saldo"><br><br>
 <div class="button-group">

  <input type="submit" name="submit" value="Simpan">

  <a href="data_tabungan.php" class="btn-kembali">
    Kembali
  </a>

</div>
    </form>
  </div>
</body>
</html>
