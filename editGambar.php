<!DOCTYPE html>
<html>
<head>
	<title>Sistem Informasi::Edit Data User</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/styleku.css">
	<script src="bootstrap/js/bootstrap.js"></script>
</head>
<body>
<?php
include 'koneksi.php';
$id = $_GET['id'];
$result = $conn->query("SELECT * FROM galeri_gambar WHERE id = $id");
$row = $result->fetch_assoc();
?>

<div class="container mt-5">
    <h2>Edit Nama Gambar</h2>
    <form action="sv_editGambar.php" method="post">
        <input type="hidden" name="id" value="<?= $row['id'] ?>">
        <div class="form-group">
            <label for="filename">Nama Gambar</label>
            <input type="text" class="form-control" id="filename" name="filename" value="<?= htmlspecialchars($row['filename']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="galeriGambar.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>
