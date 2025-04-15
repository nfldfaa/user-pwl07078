<?php
require "koneksi.php";
require "head.html";

// Pengecekan koneksi database
if (!isset($conn) || $conn->connect_error) {
    die("Koneksi database gagal: " . (isset($conn) ? $conn->connect_error : "Variabel koneksi tidak terdefinisi"));
}

// Pastikan variabel ini tersedia
$cari = isset($_GET['cari']) ? $_GET['cari'] : '';
$halAktif = isset($_GET['hal']) ? (int)$_GET['hal'] : 1;
$dataPerHalaman = isset($_GET['dataPerHalaman']) ? (int)$_GET['dataPerHalaman'] : 5;

// Query dasar dengan proteksi SQL injection
$sql = "SELECT * FROM user";
if (!empty($cari)) {
    $sql .= " WHERE iduser LIKE '%$cari%' OR username LIKE '%$cari%'";
}

// Hitung total data
$qry = $conn->query($sql);
if ($qry === false) {
    die("Error dalam query: " . $conn->error);
}

$jmlData = $qry->num_rows;
$jmlHal = ceil($jmlData / $dataPerHalaman);
$halAktif = isset($_GET['hal']) ? (int)$_GET['hal'] : 1;
$awalData = ($dataPerHalaman * $halAktif) - $dataPerHalaman;
$kosong = ($jmlData == 0);

// Query dengan pagination
$sql .= " LIMIT $awalData, $dataPerHalaman";
$hasil = $conn->query($sql);
if ($hasil === false) {
    die("Error dalam query pagination: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Daftar User</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap lokal -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styleku.css">
    <script src="bootstrap/js/bootstrap.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <style>
        .error {
            color: red;
            font-size: 0.9em;
            display: none;
        }
        #ajaxResponse {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">DAFTAR USER</h2>
        <div class="text-center mb-3">
            <a class="btn btn-success" href="addUser.php">Tambah Data</a>
        </div>

        <form action="" method="get" class="d-flex mb-3">
            <input class="form-control me-2" type="text" name="cari" placeholder="Cari user..." value="<?php echo htmlspecialchars($cari); ?>">
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
                    while ($row = $hasil->fetch_assoc()) { ?>
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
        <!-- Tombol Previous -->
        <li class="page-item <?= ($halAktif <= 1) ? 'disabled' : '' ?>">
            <a class="page-link" href="?hal=<?= $halAktif - 1 ?>&cari=<?= urlencode($cari) ?>&dataPerHalaman=<?= $dataPerHalaman ?>">Previous</a>
        </li>

        <!-- Nomor halaman -->
        <?php for ($i = 1; $i <= $jmlHal; $i++): ?>
            <li class="page-item <?= ($i == $halAktif) ? 'active' : '' ?>">
                <a class="page-link" href="?hal=<?= $i ?>&cari=<?= urlencode($cari) ?>&dataPerHalaman=<?= $dataPerHalaman ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>

        <!-- Tombol Next -->
        <li class="page-item <?= ($halAktif >= $jmlHal) ? 'disabled' : '' ?>">
            <a class="page-link" href="?hal=<?= $halAktif + 1 ?>&cari=<?= urlencode($cari) ?>&dataPerHalaman=<?= $dataPerHalaman ?>">Next</a>
        </li>
    </ul>
</nav>
    </div>

    <script>
        $(document).ready(function() {
            $('.delete-btn').on('click', function(e) {
                e.preventDefault();
                var userId = $(this).data('id');
                var row = $(this).closest('tr');

                if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
                    $.ajax({
                        url: 'hpsUser.php',
                        type: 'POST',
                        data: { iduser: userId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                alert('Data berhasil dihapus!');
                                row.fadeOut(500, function() { $(this).remove(); });
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