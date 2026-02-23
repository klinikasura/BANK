<?php
session_start();
if (!isset($_SESSION['id_admin'])) {
  header('Location: login.php');
  exit;
}

require 'koneksi.php';

$id_tabungan = $_GET['id_tabungan'];
$sql = "SELECT * FROM robotv80_tabungan WHERE id_tabungan = '$id_tabungan'";
$result = mysqli_query($koneksi, $sql);
$row = mysqli_fetch_assoc($result);

if (isset($_POST['submit'])) {
    $id_karyawan = $_POST['id_karyawan'];
    $saldo = str_replace(['.', ','], '', $_POST['saldo']); // hapus titik/koma agar jadi integer
    $saldo = (int)$saldo;

    $sql = "UPDATE robotv80_tabungan SET id_karyawan = '$id_karyawan', saldo = '$saldo' WHERE id_tabungan = '$id_tabungan'";
    mysqli_query($koneksi, $sql);

    header('Location: data_tabungan.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="http://10.10.20.250/dashboard/download.jpeg" rel="icon" type="image/png" />
<title>Aplikasi RS. Asura</title>
<style>
/* ---- Reset ---- */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* ---- Body ---- */
body {
  background: #f0f2f5;
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100vh;
}

/* ---- Container ---- */
.container {
  background: #fff;
  padding: 35px 40px;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1);
  width: 90%;
  max-width: 450px;
}

/* ---- Heading ---- */
h2 {
  text-align: center;
  margin-bottom: 25px;
  color: #333;
}

/* ---- Form ---- */
form label {
  display: block;
  margin-top: 15px;
  font-weight: 600;
}

form input[type="text"], form select {
  width: 100%;
  padding: 10px;
  margin-top: 5px;
  border-radius: 7px;
  border: 1px solid #ccc;
  font-size: 16px;
}

form input[type="submit"] {
  margin-top: 25px;
  padding: 12px;
  width: 100%;
  border: none;
  border-radius: 8px;
  background: linear-gradient(135deg, #6a11cb, #2575fc);
  color: #fff;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

form input[type="submit"]:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 15px rgba(0,0,0,0.2);
}

/* ---- Format nominal saldo ---- */
input#saldo::placeholder {
  color: #aaa;
  font-style: italic;
}
</style>
</head>
<body>
<div class="container">
  <h2>Edit Tabungan</h2>
  <form action="" method="post">
    <label for="id_karyawan">Nama Karyawan:</label>
    <select id="id_karyawan" name="id_karyawan" required>
      <?php
      $sql = "SELECT * FROM robotv80_karyawan";
      $result_karyawan = mysqli_query($koneksi, $sql);
      while ($row_karyawan = mysqli_fetch_assoc($result_karyawan)) {
        $selected = ($row_karyawan['id_karyawan'] == $row['id_karyawan']) ? "selected" : "";
        echo "<option value='".$row_karyawan['id_karyawan']."' $selected>".$row_karyawan['nama']."</option>";
      }
      ?>
    </select>

    <label for="saldo">Saldo (Rp):</label>
    <input type="text" id="saldo" name="saldo" value="<?php echo number_format($row['saldo'],0,',','.'); ?>" placeholder="0">

    <input type="submit" name="submit" value="Simpan">
  </form>
</div>

<script>
// JS untuk men-format input saldo secara realtime
const saldoInput = document.getElementById('saldo');
saldoInput.addEventListener('input', function(e){
    let value = this.value.replace(/\D/g,''); // hapus semua non-digit
    this.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, "."); // format ribuan
});
</script>

</body>
</html>

