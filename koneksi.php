<?php
$servername = "10.10.20.250";
$username = "root";
$password = "";
$database = "sikdraisyah";

$koneksi = mysqli_connect($servername, $username, $password, $database);

if (!$koneksi) {
  die("Koneksi gagal: " . mysqli_connect_error());
}
?>
