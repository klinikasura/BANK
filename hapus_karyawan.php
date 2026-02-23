<?php
session_start();
if (!isset($_SESSION['id_admin'])) {
  header('Location: login.php');
}

require 'koneksi.php';

$id_karyawan = $_GET['id_karyawan'];
$sql = "DELETE FROM robotv80_karyawan WHERE id_karyawan = '$id_karyawan'";
mysqli_query($koneksi, $sql);

header('Location: data_karyawan.php');
?>

