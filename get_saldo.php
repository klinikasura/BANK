<?php
require 'koneksi.php';

$id = $_GET['id_tabungan'];

$q = mysqli_query($koneksi,"
SELECT saldo 
FROM robotv80_tabungan 
WHERE id_tabungan='$id'
");

$r = mysqli_fetch_assoc($q);

echo number_format($r['saldo'],0,',','.');
?>
