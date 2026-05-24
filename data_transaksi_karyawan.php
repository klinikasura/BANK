<?php
session_start();
if (!isset($_SESSION['id_karyawan'])) {
  header('Location: login.php');
  exit;
}

require 'koneksi.php';

$id_karyawan = $_SESSION['id_karyawan'];

/* FILTER + PAGINATION */
$from = $_GET['from'] ?? '';
$to   = $_GET['to'] ?? '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) $page = 1;

$limit = 10;
$offset = ($page - 1) * $limit;

/* WHERE */
$where = "WHERE tb.id_karyawan = '$id_karyawan'";

if (!empty($from) && !empty($to)) {
    $where .= " AND DATE(tr.tanggal) BETWEEN '$from' AND '$to'";
}

/* COUNT */
$count_sql = "SELECT COUNT(*) as total
              FROM robotv80_transaksi tr
              JOIN robotv80_tabungan tb ON tr.id_tabungan = tb.id_tabungan
              $where";

$count_result = mysqli_query($koneksi, $count_sql);
$total_row = mysqli_fetch_assoc($count_result)['total'];
$total_page = ceil($total_row / $limit);

/* =========================
   QUERY UTAMA (FIX PENERIMA)
========================= */
$sql = "SELECT 
            tr.id_transaksi,
            tr.id_tabungan,
            tr.jenis_transaksi,
            tr.jumlah,
            tr.tanggal,
            tr.transfer_ke,
            k.nama,
            k.jabatan,
            tb.saldo,

            /* ✅ INI YANG BENAR UNTUK NAMA PENERIMA */
            kp.nama AS nama_penerima

        FROM robotv80_transaksi tr
        JOIN robotv80_tabungan tb ON tr.id_tabungan = tb.id_tabungan
        JOIN robotv80_karyawan k ON tb.id_karyawan = k.id_karyawan

        /* PENERIMA HARUS DARI transfer_ke -> tabungan -> karyawan */
        LEFT JOIN robotv80_tabungan tbp ON tr.transfer_ke = tbp.id_tabungan
        LEFT JOIN robotv80_karyawan kp ON tbp.id_karyawan = kp.id_karyawan

        $where
        ORDER BY tr.tanggal DESC
        LIMIT $limit OFFSET $offset";

$result = mysqli_query($koneksi, $sql);

/* FORMAT */
function formatJenisTransaksi($row) {

    if ($row['jenis_transaksi'] == 'Transfer Keluar') {
        return "Transfer ke " . ($row['nama_penerima'] ?? '-');
    }

    if ($row['jenis_transaksi'] == 'Transfer Masuk') {
        return "Transfer dari " . ($row['nama_penerima'] ?? '-');
    }

    return $row['jenis_transaksi'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>myROBOT-V80</title>
  <link href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png" rel="icon" type="image/png" />


<style>
body {
  margin:0;
  font-family:'Segoe UI',sans-serif;
  background: linear-gradient(135deg,#0f172a,#1e3a8a,#2563eb);
  color:#fff;
}



.container {
  max-width:1100px;
  margin:40px auto;
  background:rgba(255,255,255,0.12);
  backdrop-filter:blur(14px);
  padding:25px;
  border-radius:18px;
}

nav a {
  margin:5px;
  padding:10px 14px;
  background:rgba(255,255,255,0.15);
  color:#fff;
  text-decoration:none;
  border-radius:10px;
}

form {
  text-align:center;
  margin-bottom:15px;
}

input, button {
  padding:8px;
  border-radius:8px;
  border:none;
}

button {
  background:#22c55e;
  color:#fff;
}

table {
  width:100%;
  border-collapse:collapse;
  background:#fff;
  color:#000;
  border-radius:10px;
  overflow:hidden;
}

th {
  background:#2563eb;
  color:#fff;
  padding:12px;
}

td {
  padding:10px;
  text-align:center;
  border-bottom:1px solid #eee;
}

.pagination {
  text-align:center;
  margin-top:15px;
}

.pagination a {
  padding:8px 12px;
  margin:0 5px;
  background:rgba(255,255,255,0.2);
  color:#fff;
  text-decoration:none;
  border-radius:8px;
}

/* =========================
   RESPONSIVE HP
========================= */
@media (max-width: 768px) {

  .container {
    margin: 15px;
    padding: 15px;
  }

  h2 {
    font-size: 18px;
  }

  /* TABLE SCROLL */
  table {
    display: block;
    overflow-x: auto;
    white-space: nowrap;
  }

  th, td {
    font-size: 12px;
    padding: 8px;
  }

  nav {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
  }

  nav a {
    font-size: 12px;
    padding: 8px 10px;
  }

  input, button {
    width: 100%;
    margin-top: 5px;
  }

  form {
    display: flex;
    flex-direction: column;
    gap: 5px;
  }
}
</style>
</head>

<body>

<div class="container">

<h2 style="text-align:center;">Data Transaksi Karyawan</h2>

<nav>
  <a href="karyawan.php">Dashboard</a>
  <a href="transfer.php">Transfer</a>
  <a href="data_transaksi_karyawan.php">Refresh</a>
</nav>

<form method="GET">
  Dari:
  <input type="date" name="from" value="<?= $from ?>">

  Sampai:
  <input type="date" name="to" value="<?= $to ?>">

  <button type="submit">Cari</button>
</form>

<table>
<thead>
<tr>
  <th>ID</th>
  <th>Tabungan</th>
  <th>Nama</th>
  <th>Jabatan</th>
  <th>Jenis</th>
  <th>Jumlah</th>
  <th>Sisa Saldo</th>
  <th>Penerima</th>
  <th>Tanggal</th>
  <th>Aksi</th>
</tr>
</thead>

<tbody>

<?php if(mysqli_num_rows($result) > 0): ?>
<?php while($row = mysqli_fetch_assoc($result)): ?>
<tr>
  <td><?= $row['id_transaksi']; ?></td>
  <td><?= $row['id_tabungan']; ?></td>
  <td><?= htmlspecialchars($row['nama']); ?></td>
  <td><?= htmlspecialchars($row['jabatan']); ?></td>

  <td><?= formatJenisTransaksi($row); ?></td>

  <td>Rp <?= number_format($row['jumlah'],0,',','.'); ?></td>
  <td>Rp <?= number_format($row['saldo'],0,',','.'); ?></td>

  <!-- ✅ INI FIX PENERIMA -->
  <td><?= htmlspecialchars($row['nama_penerima'] ?? '-') ?></td>

  <td><?= date('d-m-Y H:i', strtotime($row['tanggal'])); ?></td>

  <td>
    <a href="struk.php?id=<?= $row['id_transaksi']; ?>" target="_blank"
       style="padding:6px 10px;background:#f59e0b;color:#000;border-radius:6px;text-decoration:none;">
       Cetak Struk
    </a>
  </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
  <td colspan="10">Tidak ada data</td>
</tr>
<?php endif; ?>

</tbody>
</table>

<div class="pagination">
<?php if($page > 1): ?>
  <a href="?from=<?= $from ?>&to=<?= $to ?>&page=<?= $page-1 ?>">⬅ Prev</a>
<?php endif; ?>

Page <?= $page ?> / <?= $total_page ?>

<?php if($page < $total_page): ?>
  <a href="?from=<?= $from ?>&to=<?= $to ?>&page=<?= $page+1 ?>">Next ➡</a>
<?php endif; ?>
</div>

</div>

</body>
</html>
