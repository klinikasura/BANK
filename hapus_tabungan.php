<?php
session_start();
if (!isset($_SESSION['id_admin'])) {
  header('Location: login.php');
}

require 'koneksi.php';

$id_tabungan = $_GET['id_tabungan'];
$sql = "DELETE FROM robotv80_tabungan WHERE id_tabungan = '$id_tabungan'";
mysqli_query($koneksi, $sql);

header('Location: data_tabungan.php');
?>

