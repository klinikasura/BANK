<?php
session_start();
if (!isset($_SESSION['id_admin']) || $_SESSION['level'] != 'admin') {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>myROBOT-V80</title>

<link rel="icon" href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING%20APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    min-height:100vh;
    background:
    linear-gradient(180deg,#38bdf8,#dbeafe);
    padding:20px;
    color:#0f172a;
}

/* ================= CONTAINER ================= */
.container{
    max-width:1100px;
    margin:auto;
}

/* ================= HEADER ================= */
.topbar{
    background:white;
    border-radius:24px;
    padding:20px;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.logo-title h2{
    font-size:24px;
    color:#0284c7;
}

.logo-title small{
    color:#64748b;
}

.profile{
    background:#f0f9ff;
    padding:10px 16px;
    border-radius:14px;
    border:2px solid #bae6fd;
    font-size:14px;
}

/* ================= SALDO CARD ================= */
.wallet-card{
    background:
    linear-gradient(135deg,#0ea5e9,#2563eb);
    border-radius:28px;
    padding:25px;
    color:white;
    position:relative;
    overflow:hidden;
    margin-bottom:25px;
    box-shadow:0 15px 40px rgba(37,99,235,0.3);
}

.wallet-card::before{
    content:'';
    position:absolute;
    width:250px;
    height:250px;
    background:rgba(255,255,255,0.08);
    border-radius:50%;
    top:-120px;
    right:-80px;
}

.wallet-card h3{
    font-size:15px;
    opacity:0.9;
}

.wallet-card h1{
    margin-top:10px;
    font-size:34px;
}

.wallet-card p{
    margin-top:6px;
    opacity:0.9;
}

/* ================= MENU ================= */
.menu{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:16px;
}

/* ================= BUTTON CARD ================= */
.card{
    background:white;
    border-radius:24px;
    padding:22px 15px;
    text-decoration:none;
    color:#0f172a;
    text-align:center;
    transition:0.25s;
    position:relative;
    overflow:hidden;

    /* BORDER KANAN KIRI */
    border-left:5px solid #38bdf8;
    border-right:5px solid #2563eb;

    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

.card:hover{
    transform:translateY(-5px);
    box-shadow:0 15px 30px rgba(0,0,0,0.12);
}

.card .icon{
    font-size:34px;
    margin-bottom:10px;
}

.card h4{
    font-size:15px;
}

.card small{
    display:block;
    margin-top:6px;
    color:#64748b;
    font-size:11px;
}

/* ================= LOGOUT ================= */
.logout{
    background:#fee2e2;
    border-left:5px solid #ef4444;
    border-right:5px solid #dc2626;
}

/* ================= BOTTOM NAV ================= */
.bottom-nav{
    position:fixed;
    bottom:0;
    left:0;
    right:0;
    background:white;
    display:flex;
    justify-content:space-around;
    padding:14px 0;
    box-shadow:0 -5px 20px rgba(0,0,0,0.08);
    border-top:1px solid #e2e8f0;
}

.bottom-nav a{
    text-decoration:none;
    font-size:24px;
    color:#0284c7;
    padding:10px 18px;
    border-radius:14px;
    transition:0.2s;
}

.bottom-nav a:active{
    background:#e0f2fe;
    transform:scale(0.95);
}

/* ================= RESPONSIVE ================= */
@media(max-width:900px){

    .menu{
        grid-template-columns:repeat(2,1fr);
    }

}

@media(max-width:600px){

    .topbar{
        flex-direction:column;
        align-items:flex-start;
        gap:12px;
    }

    .menu{
        grid-template-columns:repeat(2,1fr);
        gap:12px;
    }

    .card{
        padding:18px 10px;
        border-radius:20px;
    }

    .card .icon{
        font-size:28px;
    }

    .wallet-card h1{
        font-size:28px;
    }

}

body{
    padding-bottom:100px;
}

</style>
</head>

<body>

<div class="container">

    <!-- HEADER -->
    <div class="topbar">

        <div class="logo-title">
            <h2>🏦 myROBOT-V80</h2>
            <small>Smart E-Wallet & Banking System</small>
        </div>

        <div class="profile">
            👤 <?= htmlspecialchars($_SESSION['username']); ?>
        </div>

    </div>

    <!-- EWALLET CARD -->
    <div class="wallet-card">
        <h3>MASTER SISTEM</h3>
        <h1>Admin Dashboard</h1>
        <p>BANK Financial Management</p>
    </div>

    <!-- MENU -->
    <div class="menu">

        <a class="card" href="data_tabungan.php">
            <div class="icon">💰</div>
            <h4>Tabungan</h4>
            <small>Kelola saldo nasabah</small>
        </a>

        <a class="card" href="data_transaksi.php">
            <div class="icon">🔄</div>
            <h4>Transaksi</h4>
            <small>Riwayat transaksi</small>
        </a>

        <a class="card" href="data_nasabah.php">
            <div class="icon">👥</div>
            <h4>Nasabah</h4>
            <small>Data pelanggan</small>
        </a>

        <a class="card" href="data_admin.php">
            <div class="icon">🛡️</div>
            <h4>Admin</h4>
            <small>Manajemen admin</small>
        </a>

        <a class="card" href="data_posisi.php">
            <div class="icon">📊</div>
            <h4>Posisi</h4>
            <small>Laporan keuangan</small>
        </a>

        <a class="card" href="input_karyawan_baru.php">
            <div class="icon">🧾</div>
            <h4>Karyawan</h4>
            <small>Tambah karyawan</small>
        </a>

        <a class="card" href="e-beli.php">
            <div class="icon">🛒</div>
            <h4>E-Beli</h4>
            <small>Sistem penjualan</small>
        </a>

        <a class="card logout" href="logout.php">
            <div class="icon">🚪</div>
            <h4>Logout</h4>
            <small>Keluar sistem</small>
        </a>

    </div>

</div>

<!-- BOTTOM NAV -->
<div class="bottom-nav">

    <a href="dashboard_admin.php">🏠</a>
    <a href="data_transaksi.php">📊</a>
    <a href="data_nasabah.php">👥</a>
    <a href="logout.php">🚪</a>

</div>

</body>
</html>
