<?php
include 'koneksi.php';
$id = $_GET['id'];

// Ambil path file
$result = $conn->query("SELECT filepath, thumbpath FROM galeri_gambar WHERE id = $id");
$row = $result->fetch_assoc();

// Hapus file dari server
if (file_exists($row['filepath'])) {
    unlink($row['filepath']);
}
if (file_exists($row['thumbpath'])) {
    unlink($row['thumbpath']);
}

// Hapus data dari database
$conn->query("DELETE FROM galeri_gambar WHERE id = $id");

header("Location: galeriGambar.php");
exit;
