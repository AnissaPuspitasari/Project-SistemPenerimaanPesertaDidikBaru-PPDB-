<?php
// Aktifkan error reporting untuk debugging
ini_set("display_errors", 1);
error_reporting(E_ALL);

// Koneksi ke database
include('../dbconnect.php');

// Ambil parameter umur dari URL
$u = $_GET['u'];
$cekdulu = mysqli_query($conn, "SELECT * FROM userdata WHERE umur='$u'");
$ambil = mysqli_fetch_array($cekdulu);

// Mengambil data alamat dari database
$ambilprov = $ambil['provinsi'];
$prov1 = mysqli_query($conn, "SELECT name FROM provinces WHERE id='$ambilprov'");
$prov = mysqli_fetch_array($prov1)['name'];

$ambilkota = $ambil['kabupaten'];
$kab1 = mysqli_query($conn, "SELECT name FROM regencies WHERE id='$ambilkota'");
$kab = mysqli_fetch_array($kab1)['name'];

$ambilkec = $ambil['kecamatan'];
$kec1 = mysqli_query($conn, "SELECT name FROM districts WHERE id='$ambilkec'");
$kec = mysqli_fetch_array($kec1)['name'];

$ambilkel = $ambil['kelurahan'];
$kel1 = mysqli_query($conn, "SELECT name FROM villages WHERE id='$ambilkel'");
$kel = mysqli_fetch_array($kel1)['name'];

// Load library Dompdf
require_once("../dompdf/autoload.inc.php");
use Dompdf\Dompdf;

// Inisialisasi Dompdf
$dompdf = new Dompdf();

// Pastikan gambar ada, jika tidak, gunakan placeholder
$foto_path = "../user/" . $ambil['foto'];


function cek_gambar($path) {
    return (file_exists($path) && !empty($path)) ? 
        "<img src='$path' style='width: 150px; height: 180px; object-fit: cover; border: 1px solid #ccc; margin-right: 10px;'>" : 
        "<p style='color: red;'>Gambar tidak tersedia</p>";
}

$img_html = cek_gambar($foto_path);

// HTML untuk PDF
$html = '
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        h3 { text-align: center; margin-bottom: 5px; }
        hr { border: 1px solid black; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .section-title { font-weight: bold; font-size: 14px; margin-top: 10px; text-align: left; }
        .image-container { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>

    <h3>Formulir Pendaftaran SDN 3 Samirejo (Berbasis Online)</h3>
    <hr/><br/>

    <table>
        <tr><th>Nama Lengkap</th><td>' . $ambil['namalengkap'] . '</td></tr>
        <tr><th>Umur</th><td>' . $u . '</td></tr>
        <tr><th>NIK</th><td>' . $ambil['nik'] . '</td></tr>
        <tr><th>Jenis Kelamin</th><td>' . $ambil['jeniskelamin'] . '</td></tr>
        <tr><th>Tempat Lahir</th><td>' . $ambil['tempatlahir'] . '</td></tr>
        <tr><th>Tanggal Lahir</th><td>' . $ambil['tanggallahir'] . '</td></tr>
        <tr><th>Alamat</th><td>' . $ambil['alamat'] . '</td></tr>
        <tr><th>Provinsi</th><td>' . $prov . '</td></tr>
        <tr><th>Kota/Kabupaten</th><td>' . $kab . '</td></tr>
        <tr><th>Kecamatan</th><td>' . $kec . '</td></tr>
        <tr><th>Kelurahan</th><td>' . $kel . '</td></tr>
        <tr><th>Agama</th><td>' . $ambil['agama'] . '</td></tr>
        <tr><th>No Telepon</th><td>' . $ambil['telepon'] . '</td></tr>
    </table>

    <p class="section-title">Data Orang Tua</p>
    <table>
        <tr><th>Nama Ayah</th><td>' . $ambil['ayahnama'] . '</td></tr>
        <tr><th>Pekerjaan Ayah</th><td>' . $ambil['ayahpekerjaan'] . '</td></tr>
        <tr><th>Nama Ibu</th><td>' . $ambil['ibunama'] . '</td></tr>
        <tr><th>Pekerjaan Ibu</th><td>' . $ambil['ibupekerjaan'] . '</td></tr>
    </table>

    <p class="section-title">Data Sekolah Asal</p>
    <table>
        <tr><th>NPSN Sekolah</th><td>' . $ambil['sekolahnpsn'] . '</td></tr>
        <tr><th>Nama Sekolah</th><td>' . $ambil['sekolahnama'] . '</td></tr>
    </table>

    <p class="section-title" style="page-break-before: always;">Dokumen Pendukung</p>

    <div class="image-container">
    <br> <!-- Tambahkan spasi -->
    <p>Foto Data Diri</p>
    ' . $img_html . '



</body>
</html>';

// Membersihkan output buffer agar tidak ada data yang mengganggu
ob_end_clean();

// Load HTML ke Dompdf
$dompdf->loadHtml($html);

// Atur ukuran dan orientasi kertas
$dompdf->setPaper('A4', 'portrait');

// Render HTML menjadi PDF
$dompdf->render();

// Kirim output PDF ke browser
$dompdf->stream("bukti_pendaftaran.pdf", array("Attachment" => false));
?>
