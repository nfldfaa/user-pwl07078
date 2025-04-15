<?php
include 'koneksi.php';
$id = $_POST['id'];
$filename = $_POST['filename'];

$stmt = $conn->prepare("UPDATE galeri_gambar SET filename = ? WHERE id = ?");
$stmt->bind_param("si", $filename, $id);
$stmt->execute();

header("Location: galeriGambar.php");
exit;
