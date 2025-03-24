<?php
require "fungsi.php";
require "head.html";

$dataPerHalaman = isset($_GET['dataPerHalaman']) ? $_GET['dataPerHalaman'] : 10;
$cari = isset($_GET['cari']) ? $_GET['cari'] : '';

$sql = $cari ? "SELECT * FROM user WHERE iduser LIKE '%$cari%' OR username LIKE '%$cari%'" : "SELECT * FROM user";
$qry = mysqli_query($koneksi, $sql);
$jmlData = mysqli_num_rows($qry);
$jmlHal = ceil($jmlData / $dataPerHalaman);
$halAktif = isset($_GET['hal']) ? $_GET['hal'] : 1;
$awalData = ($dataPerHalaman * $halAktif) - $dataPerHalaman;
$kosong = !$jmlData;

$sql .= " LIMIT $awalData, $dataPerHalaman";
$hasil = mysqli_query($koneksi, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- Bootstrap lokal -->
	<link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/styleku.css">
	<script src="bootstrap/js/bootstrap.js"></script>
	<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">DAFTAR USER</h2>
        <div class="text-center mb-3">
            <a class="btn btn-success" href="addUser.php">Tambah Data</a>
        </div>

        <form action="" method="get" class="d-flex mb-3">
            <input class="form-control me-2" type="text" name="cari" placeholder="Cari user..." value="<?php echo $cari; ?>">
            <button class="btn btn-primary me-2" type="submit">Cari</button>
            <select name="dataPerHalaman" class="form-control" onchange="this.form.submit()">
                <?php foreach ([5, 10, 25, 50, 100] as $size) {
                    echo "<option value='$size'" . ($dataPerHalaman == $size ? " selected" : "") . ">$size</option>";
                } ?>
            </select>
        </form>

        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID User</th>
                    <th>Username</th>
                    <th>Password</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($kosong) { ?>
                    <tr><td colspan="5" class="text-center alert alert-info">Data tidak ada</td></tr>
                <?php } else {
                    while ($row = mysqli_fetch_assoc($hasil)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row["iduser"]); ?></td>
                            <td><?php echo htmlspecialchars($row["username"]); ?></td>
                            <td><?php echo htmlspecialchars($row["password"]); ?></td>
                            <td><?php echo htmlspecialchars($row["status"]); ?></td>
                            <td>
                                <a class="btn btn-warning btn-sm" href="editUser.php?kode=<?php echo $row['iduser']; ?>">Edit</a>
                                <a class="btn btn-danger btn-sm delete-btn" data-id="<?php echo $row['iduser']; ?>">Hapus</a>
                            </td>
                        </tr>
                <?php } } ?>
            </tbody>
        </table>

        <nav>
            <ul class="pagination justify-content-center">
                <?php if ($halAktif > 1) { ?>
                    <li class="page-item"><a class="page-link" href="?hal=<?php echo $halAktif - 1; ?>&cari=<?php echo $cari; ?>&dataPerHalaman=<?php echo $dataPerHalaman; ?>">Previous</a></li>
                <?php }
                for ($i = 1; $i <= $jmlHal; $i++) { ?>
                    <li class="page-item <?php echo ($i == $halAktif) ? 'active' : ''; ?>">
                        <a class="page-link" href="?hal=<?php echo $i; ?>&cari=<?php echo $cari; ?>&dataPerHalaman=<?php echo $dataPerHalaman; ?>"> <?php echo $i; ?> </a>
                    </li>
                <?php }
                if ($halAktif < $jmlHal) { ?>
                    <li class="page-item"><a class="page-link" href="?hal=<?php echo $halAktif + 1; ?>&cari=<?php echo $cari; ?>&dataPerHalaman=<?php echo $dataPerHalaman; ?>">Next</a></li>
                <?php } ?>
            </ul>
        </nav>
    </div>

    <script>
        $(document).ready(function() {
        $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        var userId = $(this).data('id'); // Mengambil ID user dari atribut data-id
        var row = $(this).closest('tr'); // Mendapatkan baris tabel terkait

        // Menampilkan konfirmasi penghapusan
        if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
            $.ajax({
                url: 'hpsUser.php', // File PHP untuk menghapus user
                type: 'POST',
                data: { iduser: userId },
                dataType: 'json', // Format data yang diharapkan dari server
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Data berhasil dihapus!');
                        row.fadeOut(500, function() { $(this).remove(); }); // Menghapus baris dengan animasi
                    } else {
                        alert('Gagal menghapus data: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('Terjadi kesalahan: ' + error);
                }
            });
        }
    });
});
    </script>
</body>
</html>
