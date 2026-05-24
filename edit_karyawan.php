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

    /* =========================
       SIMPAN KE NASABAH
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
       MASUK KE KARYAWAN
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

    header("Location: data_nasabah.php");

    exit;
}

/* =========================
   HAPUS DATA
========================= */

if (isset($_GET['hapus'])) {

    $id = $_GET['hapus'];

    /* =========================
       AMBIL DATA NASABAH
    ========================= */

    $ambil = mysqli_query(

        $koneksi,

        "SELECT * FROM robot80_tb_siswa
        WHERE id='$id'"

    );

    $data_hapus = mysqli_fetch_assoc($ambil);

    $nama_hapus = $data_hapus['nama'];

    /* =========================
       HAPUS DARI NASABAH
    ========================= */

    mysqli_query(

        $koneksi,

        "DELETE FROM robot80_tb_siswa
        WHERE id='$id'"

    );

    /* =========================
       HAPUS DARI KARYAWAN
    ========================= */

    mysqli_query(

        $koneksi,

        "DELETE FROM robotv80_karyawan
        WHERE nama='$nama_hapus'"

    );

    header("Location: data_nasabah.php");

    exit;
}

/* =========================
   EDIT DATA
========================= */

$edit = false;

if (isset($_GET['edit'])) {

    $edit = true;

    $id_edit = $_GET['edit'];

    $ambil = mysqli_query(

        $koneksi,

        "SELECT * FROM robot80_tb_siswa
        WHERE id='$id_edit'"

    );

    $e = mysqli_fetch_array($ambil);
}

/* =========================
   UPDATE DATA
========================= */

if (isset($_POST['update'])) {

    $id     = $_POST['id'];

    $nama_lama = $_POST['nama_lama'];

    $nama   = mysqli_real_escape_string(
        $koneksi,
        $_POST['nama']
    );

    $kelas  = mysqli_real_escape_string(
        $koneksi,
        $_POST['kelas']
    );

    $alamat = mysqli_real_escape_string(
        $koneksi,
        $_POST['alamat']
    );

    $notlp  = mysqli_real_escape_string(
        $koneksi,
        $_POST['notlp']
    );

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

    header("Location: data_nasabah.php");

    exit;
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
