<?php
session_start();
require 'koneksi.php';

// Ambil data dari form
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';
$level    = $_POST['level'] ?? '';

if ($level == 'admin') {
    // Login admin
    $sql = "SELECT * FROM robotv80_admin WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($koneksi, $sql);

    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $_SESSION['id_admin'] = $data['id_admin'];
        $_SESSION['username'] = $data['username'];
        $_SESSION['level'] = 'admin';
        header('Location: dashboard_admin.php');
        exit;
    } else {
        $error = "Username atau password admin salah!";
    }
} elseif ($level == 'karyawan') {
    // Login karyawan
    $sql = "SELECT * FROM robotv80_karyawan WHERE id_karyawan = '$username' AND no_tlp = '$password'";
    $result = mysqli_query($koneksi, $sql);

    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $_SESSION['id_karyawan'] = $data['id_karyawan'];
        $_SESSION['nama'] = $data['nama'];        // nama karyawan
        $_SESSION['no_tlp'] = $data['no_tlp'];    // nomor telepon
        $_SESSION['level'] = 'karyawan';
        header('Location: http://10.10.20.250/dashboard/ROBOT-BANK/karyawan.php');
        exit;
    } else {
        $error = "Username atau password karyawan salah!";
    }
} else {
    $error = "Level tidak valid!";
}

// Jika ada error, simpan di session dan kembali ke login
if (isset($error)) {
    $_SESSION['login_error'] = $error;
    header('Location: login.php');
    exit;
}
?>

