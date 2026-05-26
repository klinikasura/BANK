<?php
require 'koneksi.php';

$id = $_GET['id_tabungan'];

$q = mysqli_query($koneksi,"
    SELECT saldo 
    FROM robotv80_tabungan 
    WHERE id_tabungan='$id'
");

$data = mysqli_fetch_assoc($q);

echo json_encode($data);
?>
