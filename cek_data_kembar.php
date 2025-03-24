<?php
    require "fungsi.php";

    // Pastikan koneksi database tersedia
    if (!$koneksi) {
        die("Koneksi ke database gagal: " . mysqli_connect_error());
    }

    // Proses pencarian user berdasarkan ID
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['iduser'])) {
        $iduser = trim($_POST['iduser']);
        
        if (!empty($iduser)) {
            // Prepared statement untuk mencegah SQL injection
            $stmt = $koneksi->prepare("SELECT iduser FROM user WHERE iduser = ?");
            $stmt->bind_param("s", $iduser);
            $stmt->execute();
            $result = $stmt->get_result();
            
            echo ($result->num_rows > 0) ? "exists" : "not_exists";
            
            $stmt->close();
        } else {
            echo "invalid_input";
        }
    } else {
        echo "no_data";
    }

    // Pagination setup
    $cari = isset($_GET['cari']) ? $_GET['cari'] : '';
    $dataPerHalaman = isset($_GET['dataPerHalaman']) ? (int)$_GET['dataPerHalaman'] : 10;
    $halAktif = isset($_GET['hal']) ? (int)$_GET['hal'] : 1;
    $offset = ($halAktif - 1) * $dataPerHalaman;

    $query = "SELECT * FROM user WHERE iduser LIKE ? OR username LIKE ? LIMIT ?, ?";
    $cariLike = "%$cari%";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("ssii", $cariLike, $cariLike, $offset, $dataPerHalaman);
    $stmt->execute();
    $hasil = $stmt->get_result();

    $totalQuery = "SELECT COUNT(*) as total FROM user WHERE iduser LIKE ? OR username LIKE ?";
    $stmtTotal = $koneksi->prepare($totalQuery);
    $stmtTotal->bind_param("ss", $cariLike, $cariLike);
    $stmtTotal->execute();
    $resultTotal = $stmtTotal->get_result();
    $totalData = $resultTotal->fetch_assoc()['total'];
    $jmlHal = ceil($totalData / $dataPerHalaman);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styleku.css">
    <script src="bootstrap/js/bootstrap.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
    <?php require "head.html"; ?>
    <div class="container mt-4">
        <h2 class="text-center">CEK DATA USER</h2>

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
                </tr>
            </thead>
            <tbody>
                <?php if ($hasil->num_rows == 0) { ?>
                    <tr><td colspan="5" class="text-center alert alert-info">Data tidak ditemukan</td></tr>
                <?php } else {
                    while ($row = $hasil->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row["iduser"]); ?></td>
                            <td><?php echo htmlspecialchars($row["username"]); ?></td>
                            <td><?php echo htmlspecialchars($row["password"]); ?></td>
                            <td><?php echo htmlspecialchars($row["status"]); ?></td>
                        </tr>
                <?php } } ?>
            </tbody>
        </table>

        <nav>
            <ul class="pagination justify-content-center">
                <?php if ($halAktif > 1) { ?>
                    <li class="page-item"><a class="page-link" href="?hal=<?php echo $halAktif - 1; ?>&cari=<?php echo urlencode($cari); ?>&dataPerHalaman=<?php echo $dataPerHalaman; ?>">Previous</a></li>
                <?php }
                for ($i = 1; $i <= $jmlHal; $i++) { ?>
                    <li class="page-item <?php echo ($i == $halAktif) ? 'active' : ''; ?>">
                        <a class="page-link" href="?hal=<?php echo $i; ?>&cari=<?php echo urlencode($cari); ?>&dataPerHalaman=<?php echo $dataPerHalaman; ?>"> <?php echo $i; ?> </a>
                    </li>
                <?php }
                if ($halAktif < $jmlHal) { ?>
                    <li class="page-item"><a class="page-link" href="?hal=<?php echo $halAktif + 1; ?>&cari=<?php echo urlencode($cari); ?>&dataPerHalaman=<?php echo $dataPerHalaman; ?>">Next</a></li>
                <?php } ?>
            </ul>
        </nav>
    </div>
</body>
</html>
