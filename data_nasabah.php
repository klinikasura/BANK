<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_admin'])) {
    header("Location: login.php");
    exit;
}

/* =========================
   SIMPAN DATA
========================= */

if (isset($_POST['simpan'])) {

    $nama   = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $kelas  = mysqli_real_escape_string($koneksi, $_POST['kelas']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $notlp  = mysqli_real_escape_string($koneksi, $_POST['notlp']);

    mysqli_begin_transaction($koneksi);

    try {

        /* =========================
           SIMPAN NASABAH
        ========================= */

        mysqli_query(

            $koneksi,

            "INSERT INTO robot80_tb_siswa
            (
                nama,
                kelas,
                alamat,
                notlp
            )

            VALUES
            (
                '$nama',
                '$kelas',
                '$alamat',
                '$notlp'
            )"

        );

        /* =========================
           CEK KARYAWAN
        ========================= */

        $cek = mysqli_query(

            $koneksi,

            "SELECT * FROM robotv80_karyawan
            WHERE nama = '$nama'"

        );

        /* =========================
           JIKA BELUM ADA
        ========================= */

        if(mysqli_num_rows($cek) == 0){

            mysqli_query(

                $koneksi,

                "INSERT INTO robotv80_karyawan
                (
                    nama,
                    jabatan,
                    alamat,
                    no_tlp
                )

                VALUES
                (
                    '$nama',
                    '$kelas',
                    '$alamat',
                    '$notlp'
                )"

            );

        }

        mysqli_commit($koneksi);

        header("Location: data_nasabah.php");

        exit;

    } catch (Exception $e){

        mysqli_rollback($koneksi);

        echo "Gagal simpan data.";
    }
}

/* =========================
   HAPUS DATA
========================= */

if (isset($_GET['hapus'])) {

    $id = (int) $_GET['hapus'];

    /* =========================
       AMBIL DATA NASABAH
    ========================= */

    $ambil = mysqli_query(

        $koneksi,

        "SELECT * FROM robot80_tb_siswa
        WHERE id = '$id'"

    );

    $data_hapus = mysqli_fetch_assoc($ambil);

    if($data_hapus){

        $nama = mysqli_real_escape_string(
            $koneksi,
            $data_hapus['nama']
        );

        mysqli_begin_transaction($koneksi);

        try {

            /* =========================
               HAPUS KARYAWAN
            ========================= */

            mysqli_query(

                $koneksi,

                "DELETE FROM robotv80_karyawan
                WHERE nama = '$nama'"

            );

            /* =========================
               HAPUS NASABAH
            ========================= */

            mysqli_query(

                $koneksi,

                "DELETE FROM robot80_tb_siswa
                WHERE id = '$id'"

            );

            mysqli_commit($koneksi);

        } catch (Exception $e){

            mysqli_rollback($koneksi);

            echo "Gagal hapus data.";
        }

    }

    header("Location: data_nasabah.php");

    exit;
}

/* =========================
   EDIT DATA
========================= */

$edit = false;

if (isset($_GET['edit'])) {

    $edit = true;

    $id_edit = (int) $_GET['edit'];

    $ambil = mysqli_query(

        $koneksi,

        "SELECT * FROM robot80_tb_siswa
        WHERE id='$id_edit'"

    );

    $e = mysqli_fetch_assoc($ambil);
}

/* =========================
   UPDATE DATA
========================= */

if (isset($_POST['update'])) {

    $id = (int) $_POST['id'];

    $nama_lama = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama_lama']
    );

    $nama = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama']
    );

    $kelas = mysqli_real_escape_string(
        $koneksi,
        $_POST['kelas']
    );

    $alamat = mysqli_real_escape_string(
        $koneksi,
        $_POST['alamat']
    );

    $notlp = mysqli_real_escape_string(
        $koneksi,
        $_POST['notlp']
    );

    mysqli_begin_transaction($koneksi);

    try {

        /* =========================
           UPDATE NASABAH
        ========================= */

        mysqli_query(

            $koneksi,

            "UPDATE robot80_tb_siswa SET

            nama='$nama',
            kelas='$kelas',
            alamat='$alamat',
            notlp='$notlp'

            WHERE id='$id'"

        );

        /* =========================
           UPDATE KARYAWAN
        ========================= */

        mysqli_query(

            $koneksi,

            "UPDATE robotv80_karyawan SET

            nama='$nama',
            jabatan='$kelas',
            alamat='$alamat',
            no_tlp='$notlp'

            WHERE nama='$nama_lama'"

        );

        mysqli_commit($koneksi);

        header("Location: data_nasabah.php");

        exit;

    } catch (Exception $e){

        mysqli_rollback($koneksi);

        echo "Gagal update data.";
    }
}

/* =========================
   DATA ANGGOTA
========================= */

$anggota = mysqli_query(

    $koneksi,

    "SELECT * FROM robot80_data_anggota
    ORDER BY nama ASC"

);

?>

<!DOCTYPE html>
<html lang="id">
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
    font-family:'Segoe UI',sans-serif;
}

body{

    background:
    linear-gradient(
    135deg,
    #74ebd5,
    #ACB6E5
    );

    min-height:100vh;

    padding:30px;
}

.container{

    max-width:1100px;

    margin:auto;

    background:white;

    padding:30px;

    border-radius:20px;

    box-shadow:
    0 10px 30px rgba(0,0,0,0.2);
}

h2{

    margin-bottom:20px;

    color:#333;
}

label{

    font-weight:600;

    color:#444;
}

input,
select,
textarea{

    width:100%;

    padding:12px;

    margin-top:6px;

    margin-bottom:18px;

    border:1px solid #ccc;

    border-radius:10px;

    font-size:15px;

    transition:0.3s;
}

input:focus,
select:focus,
textarea:focus{

    border-color:#2575fc;

    outline:none;

    box-shadow:
    0 0 10px rgba(37,117,252,0.3);
}

button{

    background:
    linear-gradient(
    135deg,
    #2575fc,
    #6a11cb
    );

    color:white;

    border:none;

    padding:12px 20px;

    border-radius:10px;

    cursor:pointer;

    font-weight:bold;

    transition:0.3s;
}

button:hover{

    transform:translateY(-2px);
}

.btn-dashboard{

    background:
    linear-gradient(
    135deg,
    #28a745,
    #20c997
    );

    color:white;

    padding:12px 20px;

    border-radius:10px;

    text-decoration:none;

    display:inline-block;

    font-weight:bold;
}

.btn-edit{

    background:orange;

    color:white;

    padding:8px 12px;

    text-decoration:none;

    border-radius:6px;
}

.btn-hapus{

    background:red;

    color:white;

    padding:8px 12px;

    text-decoration:none;

    border-radius:6px;
}

table{

    width:100%;

    border-collapse:collapse;

    margin-top:30px;
}

table th,
table td{

    border:1px solid #ddd;

    padding:12px;

    text-align:center;
}

table th{

    background:#2575fc;

    color:white;
}

tr:nth-child(even){

    background:#f8f9fa;
}

tr:hover{

    background:#eef4ff;
}

.action{

    display:flex;

    gap:6px;

    justify-content:center;
}

@media(max-width:768px){

    .container{

        padding:20px;
    }

    table{

        font-size:13px;
    }

    .action{

        flex-direction:column;
    }
}

</style>

</head>

<body>

<div class="container">

<h2>

<?= $edit
? 'Edit Data Nasabah'
: 'Input Data Nasabah'; ?>

</h2>

<form method="POST">

<?php if($edit){ ?>

<input
type="hidden"
name="id"
value="<?= $e['id']; ?>">

<input
type="hidden"
name="nama_lama"
value="<?= $e['nama']; ?>">

<label>Nama</label>

<input
type="text"
name="nama"
value="<?= $e['nama']; ?>"
required>

<label>Kelas / Jabatan</label>

<input
type="text"
name="kelas"
value="<?= $e['kelas']; ?>"
required>

<label>Alamat</label>

<textarea
name="alamat"
required><?= $e['alamat']; ?></textarea>

<label>No Telepon</label>

<input
type="text"
name="notlp"
value="<?= $e['notlp']; ?>"
required>

<div style="display:flex; gap:10px;">

<button
type="submit"
name="update">

Update

</button>

<a href="data_nasabah.php"
class="btn-dashboard">

Kembali

</a>

</div>

<?php } else { ?>

<label>Pilih Nama</label>

<select
name="nama"
id="namaSelect"
required>

<option value="">

-- Pilih Nama --

</option>

<?php while($a = mysqli_fetch_assoc($anggota)) { ?>

<option
value="<?= $a['nama']; ?>"

data-kelas="<?= $a['posisi']; ?>"

data-alamat="<?= $a['alamat']; ?>"

data-hp="<?= $a['hp']; ?>"

>

<?= $a['nama']; ?>

</option>

<?php } ?>

</select>

<label>Kelas / Jabatan</label>

<input
type="text"
name="kelas"
id="kelas"
readonly>

<label>Alamat</label>

<textarea
name="alamat"
id="alamat"
readonly></textarea>

<label>No Telepon</label>

<input
type="text"
name="notlp"
id="notlp"
readonly>

<div style="display:flex; gap:10px;">

<button
type="submit"
name="simpan">

Simpan

</button>

<a href="dashboard_admin.php"
class="btn-dashboard">

Dashboard

</a>

</div>

<?php } ?>

</form>

<h2 style="margin-top:40px;">

Data Nasabah

</h2>

<table>

<tr>

<th>No</th>
<th>Nama</th>
<th>Kelas</th>
<th>Alamat</th>
<th>No Telepon</th>
<th>Aksi</th>

</tr>

<?php

$no = 1;

$data = mysqli_query(

    $koneksi,

    "SELECT * FROM robot80_tb_siswa
    ORDER BY id DESC"

);

while($d = mysqli_fetch_array($data)) {

?>

<tr>

<td><?= $no++; ?></td>

<td><?= $d['nama']; ?></td>

<td><?= $d['kelas']; ?></td>

<td><?= $d['alamat']; ?></td>

<td><?= $d['notlp']; ?></td>

<td>

<div class="action">

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

</div>

</td>

</tr>

<?php } ?>

</table>

</div>

<script>

const namaSelect =
document.getElementById('namaSelect');

if(namaSelect){

namaSelect.addEventListener('change', function(){

    const selected =
    this.options[this.selectedIndex];

    document.getElementById('kelas').value =
    selected.getAttribute('data-kelas');

    document.getElementById('alamat').value =
    selected.getAttribute('data-alamat');

    document.getElementById('notlp').value =
    selected.getAttribute('data-hp');

});

}

</script>

</body>
</html>
