<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}

/* =========================
   PAGINATION
========================= */

$batas = 5;

$halaman = isset($_GET['halaman'])
? (int)$_GET['halaman']
: 1;

if($halaman < 1){
    $halaman = 1;
}

$mulai = ($halaman - 1) * $batas;

/* =========================
   SEARCH
========================= */

$cari = isset($_GET['cari'])
? mysqli_real_escape_string($koneksi,$_GET['cari'])
: '';

/* =========================
   SIMPAN ADMIN
========================= */

if(isset($_POST['simpan'])){

    $username = mysqli_real_escape_string(
        $koneksi,
        $_POST['username']
    );

    $password = ($_POST['password']);

    $nama = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama']
    );

    $level = mysqli_real_escape_string(
        $koneksi,
        $_POST['level']
    );

    $cek = mysqli_query(

        $koneksi,

        "SELECT * FROM robotv80_admin
        WHERE username='$username'"

    );

    if(mysqli_num_rows($cek) > 0){

        echo "
        <script>
        alert('Username sudah digunakan');
        </script>
        ";

    } else {

        mysqli_query(

            $koneksi,

            "INSERT INTO robotv80_admin
            (
                username,
                password,
                nama,
                level
            )

            VALUES
            (
                '$username',
                '$password',
                '$nama',
                '$level'
            )"

        );

        echo "
        <script>
        alert('Admin berhasil ditambahkan');
        window.location='data_admin.php';
        </script>
        ";
    }
}

/* =========================
   HAPUS ADMIN
========================= */

if(isset($_GET['hapus'])){

    $id = (int)$_GET['hapus'];

    mysqli_query(

        $koneksi,

        "DELETE FROM robotv80_admin
        WHERE id_admin='$id'"

    );

    echo "
    <script>
    alert('Admin berhasil dihapus');
    window.location='data_admin.php';
    </script>
    ";

    exit;
}

/* =========================
   EDIT ADMIN
========================= */

$edit = false;

if(isset($_GET['edit'])){

    $edit = true;

    $id_edit = (int)$_GET['edit'];

    $ambil = mysqli_query(

        $koneksi,

        "SELECT * FROM robotv80_admin
        WHERE id_admin='$id_edit'"

    );

    $e = mysqli_fetch_assoc($ambil);
}

/* =========================
   UPDATE ADMIN
========================= */

if(isset($_POST['update'])){

    $id = (int)$_POST['id_admin'];

    $username = mysqli_real_escape_string(
        $koneksi,
        $_POST['username']
    );

    $nama = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama']
    );

    $level = mysqli_real_escape_string(
        $koneksi,
        $_POST['level']
    );

    if($_POST['password'] != ''){

        $password = ($_POST['password']);

        mysqli_query(

            $koneksi,

            "UPDATE robotv80_admin SET

            username='$username',
            password='$password',
            nama='$nama',
            level='$level'

            WHERE id_admin='$id'"

        );

    } else {

        mysqli_query(

            $koneksi,

            "UPDATE robotv80_admin SET

            username='$username',
            nama='$nama',
            level='$level'

            WHERE id_admin='$id'"

        );
    }

    echo "
    <script>
    alert('Admin berhasil diupdate');
    window.location='data_admin.php';
    </script>
    ";

    exit;
}

/* =========================
   TOTAL DATA
========================= */

$query_total = mysqli_query(

    $koneksi,

    "SELECT COUNT(*) as total

    FROM robotv80_admin

    WHERE

    username LIKE '%$cari%'

    OR nama LIKE '%$cari%'

    OR level LIKE '%$cari%'"

);

$total_data = mysqli_fetch_assoc(
    $query_total
)['total'];

$total_halaman = ceil(
    $total_data / $batas
);

/* =========================
   DATA ADMIN
========================= */

$data = mysqli_query(

    $koneksi,

    "SELECT * FROM robotv80_admin

    WHERE

    username LIKE '%$cari%'

    OR nama LIKE '%$cari%'

    OR level LIKE '%$cari%'

    ORDER BY id_admin DESC

    LIMIT $mulai,$batas"

);

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<meta name="viewport"
<meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>myROBOT-V80</title>
  <link href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png" rel="icon" type="image/png" />

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI';
}

body{

    background:
    linear-gradient(
    135deg,
    #667eea,
    #764ba2
    );

    padding:30px;
}

.container{

    max-width:1200px;

    margin:auto;

    background:white;

    padding:30px;

    border-radius:20px;

    box-shadow:
    0 10px 30px rgba(0,0,0,0.2);
}

h2{

    text-align:center;

    margin-bottom:25px;

    color:#333;
}

.grid{

    display:grid;

    grid-template-columns:
    repeat(auto-fit,minmax(300px,1fr));

    gap:20px;
}

label{

    font-weight:bold;
}

input,
select{

    width:100%;

    padding:12px;

    border:1px solid #ccc;

    border-radius:10px;

    margin-top:5px;

    margin-bottom:15px;
}

button{

    background:
    linear-gradient(
    135deg,
    #667eea,
    #764ba2
    );

    color:white;

    border:none;

    padding:12px 20px;

    border-radius:10px;

    cursor:pointer;

    font-weight:bold;
}

button:hover{

    opacity:0.9;
}

.btn-back{

    background:
    linear-gradient(
    135deg,
    #28a745,
    #20c997
    );

    color:white;

    text-decoration:none;

    padding:12px 20px;

    border-radius:10px;

    font-weight:bold;
}

.search{

    display:flex;

    gap:10px;

    margin-top:30px;
}

table{

    width:100%;

    border-collapse:collapse;

    margin-top:25px;
}

table th,
table td{

    border:1px solid #ddd;

    padding:12px;

    text-align:center;
}

table th{

    background:#667eea;

    color:white;
}

tr:nth-child(even){

    background:#f8f9fa;
}

tr:hover{

    background:#eef4ff;
}

.btn-edit{

    background:orange;

    color:white;

    padding:8px 12px;

    border-radius:6px;

    text-decoration:none;
}

.btn-hapus{

    background:red;

    color:white;

    padding:8px 12px;

    border-radius:6px;

    text-decoration:none;
}

.pagination{

    margin-top:25px;

    text-align:center;
}

.pagination a{

    display:inline-block;

    padding:10px 15px;

    margin:3px;

    background:#667eea;

    color:white;

    text-decoration:none;

    border-radius:8px;
}

.pagination a.active{

    background:#28a745;
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

.action{

    display:flex;

    justify-content:center;

    gap:8px;
}

@media(max-width:768px){

    .search{

        flex-direction:column;
    }

    .action{

        flex-direction:column;
    }

    table{

        font-size:12px;
    }
}

</style>

</head>

<body>

<div class="container">

<h2>

<?= $edit
? 'Edit Admin'
: 'Tambah Admin'; ?>

</h2>

<form method="POST">

<?php if($edit){ ?>

<input
type="hidden"
name="id_admin"

value="<?= $e['id_admin']; ?>">

<?php } ?>

<div class="grid">

<div>

<label>Username</label>

<input
type="text"
name="username"
required

value="<?= $edit ? $e['username'] : ''; ?>">

</div>

<div>

<label>Password</label>

<input
type="text"
name="password"

placeholder="<?= $edit
? 'Kosongkan jika tidak diubah'
: 'Masukkan password'; ?>"

<?= $edit ? '' : 'required'; ?>>

</div>

<div>

<label>Nama Lengkap</label>

<input
type="text"
name="nama"
required

value="<?= $edit ? $e['nama'] : ''; ?>">

</div>

<div>

<label>Level</label>

<select name="level">

<option
value="admin"

<?= $edit && $e['level']=='admin'
? 'selected'
: ''; ?>>

Admin

</option>

<option
value="operator"

<?= $edit && $e['level']=='operator'
? 'selected'
: ''; ?>>

Operator

</option>

</select>

</div>

</div>

<div style="display:flex; gap:10px; margin-top:20px;">

<button
type="submit"
name="<?= $edit ? 'update' : 'simpan'; ?>">

<?= $edit
? 'Update Admin'
: 'Simpan Admin'; ?>

</button>

<a href="dashboard_admin.php"
class="btn-back">

Kembali

</a>

</div>

</form>

<form method="GET"
class="search">

<input
type="text"
name="cari"

placeholder="Cari username / nama / level"

value="<?= $cari; ?>">

<button type="submit">

Cari

</button>

</form>

<table>

<tr>

<th>No</th>
<th>Username</th>
<th>Nama</th>
<th>Level</th>
<th>Aksi</th>

</tr>

<?php

$no = $mulai + 1;

while($d = mysqli_fetch_assoc($data)) {

?>

<tr>

<td><?= $no++; ?></td>

<td><?= $d['username']; ?></td>

<td><?= $d['nama']; ?></td>

<td><?= strtoupper($d['level']); ?></td>

<td>

<div class="action">

<a
href="?edit=<?= $d['id_admin']; ?>"
class="btn-edit">

Edit

</a>

<a
href="?hapus=<?= $d['id_admin']; ?>"

class="btn-hapus"

onclick="return confirm('Yakin hapus admin?')">

Hapus

</a>

</div>

</td>

</tr>

<?php } ?>

</table>

<div class="pagination">

<?php for($i=1; $i <= $total_halaman; $i++) { ?>

<a

href="?halaman=<?= $i; ?>&cari=<?= $cari; ?>"

class="<?= ($i == $halaman)
? 'active'
: ''; ?>"

>

<?= $i; ?>

</a>

<?php } ?>

</div>

</div>

<p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>
   <p>&nbsp;</p>

<!-- BOTTOM NAV -->
<div class="bottom-nav">

    <a href="dashboard_admin.php">🏠</a>
    <a href="data_transaksi.php">📊</a>
    <a href="data_nasabah.php">👥</a>
    <a href="logout.php">🚪</a>

</div>


</body>
</html>
