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
? mysqli_real_escape_string($koneksi, $_GET['cari'])
: '';

/* =========================
   AUTO NO INDUK
========================= */

$kode = "";

$queryKode = mysqli_query(
    $koneksi,
    "SELECT MAX(id) as maxid
    FROM robot80_data_anggota"
);

$dataKode = mysqli_fetch_assoc($queryKode);

$maxid = $dataKode['maxid'];

$noUrut = $maxid + 1;

$no_induk = $kode . sprintf("%04s", $noUrut);

/* =========================
   SIMPAN DATA
========================= */

if(isset($_POST['simpan'])){

    $no_induk  = mysqli_real_escape_string($koneksi,$_POST['no_induk']);
    $nama      = mysqli_real_escape_string($koneksi,$_POST['nama']);
    $username  = mysqli_real_escape_string($koneksi,$_POST['username']);
    $password  = md5($_POST['password']);
    $jk        = mysqli_real_escape_string($koneksi,$_POST['jk']);
    $posisi    = mysqli_real_escape_string($koneksi,$_POST['posisi']);
    $ttl       = mysqli_real_escape_string($koneksi,$_POST['ttl']);
    $alamat    = mysqli_real_escape_string($koneksi,$_POST['alamat']);
    $saldo     = mysqli_real_escape_string($koneksi,$_POST['saldo']);
    $gaji      = mysqli_real_escape_string($koneksi,$_POST['gaji']);
    $status    = mysqli_real_escape_string($koneksi,$_POST['status']);
    $sip       = mysqli_real_escape_string($koneksi,$_POST['sip']);
    $ket       = mysqli_real_escape_string($koneksi,$_POST['ket']);
    $str       = mysqli_real_escape_string($koneksi,$_POST['str']);
    $masuk_kerja = $_POST['masuk_kerja'];
    $alamat2   = mysqli_real_escape_string($koneksi,$_POST['alamat2']);
    $cek_status = $_POST['cek_status'];
    $masa_sip  = $_POST['masa_sip'];
    $pendidikan = mysqli_real_escape_string($koneksi,$_POST['pendidikan']);
    $hp        = mysqli_real_escape_string($koneksi,$_POST['hp']);
    $id_siswa  = mysqli_real_escape_string($koneksi,$_POST['id_siswa']);
    $cek_status_mulai = $_POST['cek_status_mulai'];

    /* =========================
       FOTO
    ========================= */

    $foto = '';

    if(isset($_FILES['foto']['name'])){

        $foto = $_FILES['foto']['name'];
        $tmp  = $_FILES['foto']['tmp_name'];

        if($foto != ''){

            $folder = "foto/";

            if(!is_dir($folder)){
                mkdir($folder,0777,true);
            }

            move_uploaded_file(
                $tmp,
                $folder.$foto
            );

        } else {

            $foto = "default.png";
        }
    }

    /* =========================
       QUERY SIMPAN
    ========================= */

    $query = mysqli_query(

        $koneksi,

        "INSERT INTO robot80_data_anggota
        (
            no_induk,
            nama,
            username,
            password,
            jk,
            posisi,
            ttl,
            alamat,
            foto,
            saldo,
            gaji,
            status,
            sip,
            ket,
            str,
            masuk_kerja,
            alamat2,
            cek_status,
            masa_sip,
            pendidikan,
            hp,
            id_siswa,
            cek_status_mulai
        )

        VALUES
        (
            '$no_induk',
            '$nama',
            '$username',
            '$password',
            '$jk',
            '$posisi',
            '$ttl',
            '$alamat',
            '$foto',
            '$saldo',
            '$gaji',
            '$status',
            '$sip',
            '$ket',
            '$str',
            '$masuk_kerja',
            '$alamat2',
            '$cek_status',
            '$masa_sip',
            '$pendidikan',
            '$hp',
            '$id_siswa',
            '$cek_status_mulai'
        )"

    );

    if($query){

        header("Location: input_karyawan_baru.php");
        exit;

    } else {

        echo "Gagal Simpan : " . mysqli_error($koneksi);
    }
}

/* =========================
   HAPUS DATA
========================= */

if(isset($_GET['hapus'])){

    $id = (int)$_GET['hapus'];

    $ambilFoto = mysqli_query(
        $koneksi,
        "SELECT foto
        FROM robot80_data_anggota
        WHERE id='$id'"
    );

    $fotoData = mysqli_fetch_assoc($ambilFoto);

    if($fotoData['foto'] != 'default.png'){

        @unlink(
            "foto/".$fotoData['foto']
        );
    }

    mysqli_query(

        $koneksi,

        "DELETE FROM robot80_data_anggota
        WHERE id='$id'"

    );

    header("Location: input_karyawan_baru.php");

    exit;
}

/* =========================
   EDIT DATA
========================= */

$edit = false;

if(isset($_GET['edit'])){

    $edit = true;

    $id_edit = (int)$_GET['edit'];

    $ambil = mysqli_query(

        $koneksi,

        "SELECT * FROM robot80_data_anggota
        WHERE id='$id_edit'"

    );

    $e = mysqli_fetch_assoc($ambil);
}

/* =========================
   UPDATE DATA
========================= */

if(isset($_POST['update'])){

    $id = (int)$_POST['id'];

    $nama      = mysqli_real_escape_string($koneksi,$_POST['nama']);
    $username  = mysqli_real_escape_string($koneksi,$_POST['username']);
    $jk        = mysqli_real_escape_string($koneksi,$_POST['jk']);
    $posisi    = mysqli_real_escape_string($koneksi,$_POST['posisi']);
    $ttl       = mysqli_real_escape_string($koneksi,$_POST['ttl']);
    $alamat    = mysqli_real_escape_string($koneksi,$_POST['alamat']);
    $saldo     = mysqli_real_escape_string($koneksi,$_POST['saldo']);
    $gaji      = mysqli_real_escape_string($koneksi,$_POST['gaji']);
    $status    = mysqli_real_escape_string($koneksi,$_POST['status']);
    $sip       = mysqli_real_escape_string($koneksi,$_POST['sip']);
    $ket       = mysqli_real_escape_string($koneksi,$_POST['ket']);
    $str       = mysqli_real_escape_string($koneksi,$_POST['str']);
    $masuk_kerja = $_POST['masuk_kerja'];
    $alamat2   = mysqli_real_escape_string($koneksi,$_POST['alamat2']);
    $cek_status = $_POST['cek_status'];
    $masa_sip  = $_POST['masa_sip'];
    $pendidikan = mysqli_real_escape_string($koneksi,$_POST['pendidikan']);
    $hp        = mysqli_real_escape_string($koneksi,$_POST['hp']);
    $id_siswa  = mysqli_real_escape_string($koneksi,$_POST['id_siswa']);
    $cek_status_mulai = $_POST['cek_status_mulai'];

    /* =========================
       UPDATE FOTO
    ========================= */

    $updateFoto = "";

    if($_FILES['foto']['name'] != ''){

        $foto = $_FILES['foto']['name'];
        $tmp  = $_FILES['foto']['tmp_name'];

        move_uploaded_file(
            $tmp,
            "foto/".$foto
        );

        $updateFoto = ", foto='$foto'";
    }

    /* =========================
       UPDATE PASSWORD
    ========================= */

    $updatePassword = "";

    if($_POST['password'] != ''){

        $password = md5($_POST['password']);

        $updatePassword =
        ", password='$password'";
    }

    /* =========================
       QUERY UPDATE
    ========================= */

    $query = mysqli_query(

        $koneksi,

        "UPDATE robot80_data_anggota SET

        nama='$nama',
        username='$username'
        $updatePassword,
        jk='$jk',
        posisi='$posisi',
        ttl='$ttl',
        alamat='$alamat'
        $updateFoto,
        saldo='$saldo',
        gaji='$gaji',
        status='$status',
        sip='$sip',
        ket='$ket',
        str='$str',
        masuk_kerja='$masuk_kerja',
        alamat2='$alamat2',
        cek_status='$cek_status',
        masa_sip='$masa_sip',
        pendidikan='$pendidikan',
        hp='$hp',
        id_siswa='$id_siswa',
        cek_status_mulai='$cek_status_mulai'

        WHERE id='$id'"

    );

    if($query){

        header("Location: input_karyawan_baru.php");
        exit;

    } else {

        echo "Gagal Update : " . mysqli_error($koneksi);
    }
}

/* =========================
   TOTAL DATA
========================= */

$query_total = mysqli_query(

    $koneksi,

    "SELECT COUNT(*) as total

    FROM robot80_data_anggota

    WHERE

    nama LIKE '%$cari%'

    OR posisi LIKE '%$cari%'

    OR username LIKE '%$cari%'"

);

$total_data = mysqli_fetch_assoc(
    $query_total
)['total'];

$total_halaman = ceil(
    $total_data / $batas
);

/* =========================
   DATA
========================= */

$data = mysqli_query(

    $koneksi,

    "SELECT * FROM robot80_data_anggota

    WHERE

    nama LIKE '%$cari%'

    OR posisi LIKE '%$cari%'

    OR username LIKE '%$cari%'

    ORDER BY id DESC

    LIMIT $mulai,$batas"

);

?>

<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>myROBOT-V80</title>

<link href="http://10.10.20.250/dashboard/APPS-ROBOT/BUILDING APLIKASI/@API-GITHUB-V80/ROBOT-GITHUB/ROBOTV80.png"
rel="icon"
type="image/png" />

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

    max-width:1300px;

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
textarea,
select{

    width:100%;

    padding:12px;

    margin-top:5px;

    border:1px solid #ccc;

    border-radius:10px;

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
}

.search{

    display:flex;

    gap:10px;

    margin-top:30px;
}

table{

    width:100%;

    border-collapse:collapse;

    margin-top:20px;
}

table th,
table td{

    border:1px solid #ddd;

    padding:10px;

    text-align:center;
}

table th{

    background:#667eea;

    color:white;
}

img{

    width:60px;

    height:60px;

    object-fit:cover;

    border-radius:10px;
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

    margin-top:20px;

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

</style>

</head>

<body>

<div class="container">

<h2>

<?= $edit
? 'Edit Data Karyawan'
: 'Input Data Karyawan'; ?>

</h2>

<form method="POST"
enctype="multipart/form-data">

<?php if($edit){ ?>

<input
type="hidden"
name="id"
value="<?= $e['id']; ?>">

<?php } ?>

<div class="grid">

<div>

<label>No Induk</label>

<input
type="text"
name="no_induk"

value="<?= $edit ? $e['no_induk'] : $no_induk; ?>"

readonly>

<label>Nama</label>

<input
type="text"
name="nama"
required

value="<?= $edit ? $e['nama'] : ''; ?>">

<label>Username (SIMRS)</label>

<input
type="text"
name="username"
required

value="<?= $edit ? $e['username'] : ''; ?>">

<label>Password (SIMRS)</label>

<input
type="text"
name="password"
<?= $edit ? '' : 'required'; ?>>

<label>Jenis Kelamin</label>

<select name="jk">

<option value="Laki-laki">Laki-laki</option>
<option value="Perempuan">Perempuan</option>

</select>

<label>Posisi</label>

<input
type="text"
name="posisi"

value="<?= $edit ? $e['posisi'] : ''; ?>">

<label>TTL</label>

<input
type="text"
name="ttl"

value="<?= $edit ? $e['ttl'] : ''; ?>">

<label>Link Photo</label>

<textarea
name="alamat"><?= $edit ? $e['alamat'] : ''; ?></textarea>

<label>Foto</label>

<input
type="file"
name="foto">

</div>

<div>

<label>Saldo E-MONEY</label>

<input
type="text"
name="saldo"

value="<?= $edit ? $e['saldo'] : '0'; ?>">

<label>Link Gaji</label>

<input
type="text"
name="gaji"

value="<?= $edit ? $e['gaji'] : ''; ?>">

<label>Status (PKWT ATAU NON PKWT)</label>

<input
type="text"
name="status"

value="<?= $edit ? $e['status'] : ''; ?>">

<label>NO. SIP</label>

<input
type="text"
name="sip"

value="<?= $edit ? $e['sip'] : ''; ?>">

<label>Keterangan</label>

<textarea
name="ket"><?= $edit ? $e['ket'] : ''; ?></textarea>

<label>NO. STR</label>

<input
type="text"
name="str"

value="<?= $edit ? $e['str'] : ''; ?>">

<label>Masuk Kerja</label>

<input
type="date"
name="masuk_kerja"

value="<?= $edit ? $e['masuk_kerja'] : ''; ?>">

<label>Alamat LENGKAP</label>

<input
type="text"
name="alamat2"

value="<?= $edit ? $e['alamat2'] : ''; ?>">

<label>Habis PWKT</label>

<input
type="date"
name="cek_status"

value="<?= $edit ? $e['cek_status'] : ''; ?>">

<label>Habis SIP</label>

<input
type="date"
name="masa_sip"

value="<?= $edit ? $e['masa_sip'] : ''; ?>">

<label>Pendidikan</label>

<input
type="text"
name="pendidikan"

value="<?= $edit ? $e['pendidikan'] : ''; ?>">

<label>No HP</label>

<input
type="text"
name="hp"

value="<?= $edit ? $e['hp'] : ''; ?>">

<label>ID E-Money</label>

<input
type="text"
name="id_siswa"

value="<?= $edit ? $e['id_siswa'] : ''; ?>">

<label>Mulai PKWT</label>

<input
type="date"
name="cek_status_mulai"

value="<?= $edit ? $e['cek_status_mulai'] : ''; ?>">

</div>

</div>

<div style="margin-top:20px; display:flex; gap:10px;">

<button
type="submit"
name="<?= $edit ? 'update' : 'simpan'; ?>">

<?= $edit ? 'Update' : 'Simpan'; ?>

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

placeholder="Cari nama / posisi..."

value="<?= $cari; ?>">

<button type="submit">

Cari

</button>

</form>
<a href="cetak_karyawan.php?cari=<?= $cari; ?>" target="_blank"
style="
    display:inline-block;
    padding:10px 14px;
    background:#ef4444;
    color:white;
    text-decoration:none;
    border-radius:10px;
    margin-top:10px;
">
🧾 Cetak PDF
</a>

<table>

<tr>

<th>No</th>
<th>Foto</th>
<th>No Induk</th>
<th>Nama</th>
<th>Username</th>
<th>Posisi</th>
<th>HP</th>
<th>Status</th>
<th>Aksi</th>

</tr>

<?php

$no = $mulai + 1;

while($d = mysqli_fetch_assoc($data)) {

?>

<tr>

<td><?= $no++; ?></td>

<td>

<img
src="foto/<?= $d['foto']; ?>">

</td>

<td><?= $d['no_induk']; ?></td>

<td><?= $d['nama']; ?></td>

<td><?= $d['username']; ?></td>

<td><?= $d['posisi']; ?></td>

<td><?= $d['hp']; ?></td>

<td><?= $d['status']; ?></td>

<td>

<a
href="?edit=<?= $d['id']; ?>"
class="btn-edit">

Edit

</a>

<a
href="?hapus=<?= $d['id']; ?>"

class="btn-hapus"

onclick="return confirm('Yakin hapus data?')">

Hapus

</a>

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
