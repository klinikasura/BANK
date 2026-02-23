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
    background: linear-gradient(135deg, #71b7e6, #9b59b6);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
}

/* ---- Container ---- */
.container {
    background: #fff;
    width: 90%;
    max-width: 600px;
    padding: 40px;
    border-radius: 15px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.2);
    text-align: center;
}

/* ---- Heading ---- */
h2 {
    margin-bottom: 10px;
    font-size: 2rem;
    color: #333;
}

/* ---- Welcome ---- */
p {
    margin-bottom: 30px;
    color: #555;
    font-size: 1.1rem;
}

/* ---- Card Links ---- */
ul {
    list-style: none;
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
}

ul li {
    margin: 15px;
}

ul li a {
    display: block;
    padding: 20px 30px;
    background: linear-gradient(135deg, #6a11cb, #2575fc);
    color: #fff;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    transition: all 0.3s ease;
}

ul li a:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.3);
    background: linear-gradient(135deg, #2575fc, #6a11cb);
}

/* ---- Responsive ---- */
@media (max-width: 500px) {
    ul {
        flex-direction: column;
        align-items: center;
    }
    ul li {
        width: 100%;
    }
    ul li a {
        width: 100%;
    }
}
</style>
</head>
<body>
<div class="container">
    <h2>Dashboard Admin</h2>
    <p>Selamat datang, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!</p>
    <ul>
        <li><a href="data_tabungan.php">Data Tabungan</a></li>
        <li><a href="data_transaksi.php">Data Transaksi</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</div>
</body>
</html>

