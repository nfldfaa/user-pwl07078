<?php
require "koneksi.php";
require "head.html";

// Pengecekan koneksi database
if (!isset($conn) || $conn->connect_error) {
    die("Koneksi database gagal: " . (isset($conn) ? $conn->connect_error : "Variabel koneksi tidak terdefinisi"));
}

// Handle request POST untuk cek ID user
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['iduser'])) {
    $iduser = trim($_POST['iduser']);

    if (!empty($iduser)) {
        $stmt = $koneksi->prepare("SELECT iduser FROM user WHERE iduser = ?");
        $stmt->bind_param("s", $iduser);
        $stmt->execute();
        $result = $stmt->get_result();
        echo ($result->num_rows > 0) ? "exists" : "not_exists";
        $stmt->close();
    } else {
        echo "invalid_input";
    }
    exit;
}

// Setup pagination dan pencarian
$cari = isset($_GET['cari']) ? $_GET['cari'] : '';
$dataPerHalaman = isset($_GET['dataPerHalaman']) ? (int)$_GET['dataPerHalaman'] : 10;
$halAktif = isset($_GET['hal']) ? (int)$_GET['hal'] : 1;
$offset = ($halAktif - 1) * $dataPerHalaman;

$cariLike = "%$cari%";

// Query data user dengan limit dan pencarian
$stmt = $conn->prepare("SELECT * FROM user WHERE iduser LIKE ? OR username LIKE ? LIMIT ?, ?");
$stmt->bind_param("ssii", $cariLike, $cariLike, $offset, $dataPerHalaman);
$stmt->execute();
$hasil = $stmt->get_result();

// Hitung total data
$stmtTotal = $conn->prepare("SELECT COUNT(*) as total FROM user WHERE iduser LIKE ? OR username LIKE ?");
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
    <title>Cek Data User</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styleku.css">
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
<?php require "head.html"; ?>

<div class="container mt-4">
    <h2 class="text-center mb-4">CEK DATA USER</h2>

    <form action="" method="get" class="row g-2 mb-3">
        <div class="col-md-6">
            <input type="text" name="cari" class="form-control" placeholder="Cari user..." value="<?= htmlspecialchars($cari) ?>">
        </div>
        <div class="col-md-3">
            <select name="dataPerHalaman" class="form-select" onchange="this.form.submit()">
                <?php foreach ([5, 10, 25, 50, 100] as $jumlah) {
                    echo "<option value='$jumlah'" . ($dataPerHalaman == $jumlah ? " selected" : "") . ">$jumlah per halaman</option>";
                } ?>
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100">Cari</button>
        </div>
    </form>

    <table class="table table-striped table-hover">
        <thead class="table-dark">
            <tr>
                <th>ID User</th>
                <th>Username</th>
                <th>Password</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($hasil->num_rows == 0): ?>
            <tr>
                <td colspan="4" class="text-center text-muted">Data tidak ditemukan</td>
            </tr>
        <?php else: 
            while ($row = $hasil->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row["iduser"]) ?></td>
                <td><?= htmlspecialchars($row["username"]) ?></td>
                <td><?= htmlspecialchars($row["password"]) ?></td>
                <td><?= htmlspecialchars($row["status"]) ?></td>
            </tr>
        <?php endwhile; endif; ?>
        </tbody>
    </table>

    <!-- Navigasi halaman -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php if ($halAktif > 1): ?>
                <li class="page-item"><a class="page-link" href="?hal=<?= $halAktif - 1 ?>&cari=<?= $cari ?>&dataPerHalaman=<?= $dataPerHalaman ?>">Sebelumnya</a></li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $jmlHal; $i++): ?>
                <li class="page-item <?= ($i == $halAktif) ? 'active' : '' ?>">
                    <a class="page-link" href="?hal=<?= $i ?>&cari=<?= $cari ?>&dataPerHalaman=<?= $dataPerHalaman ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <?php if ($halAktif < $jmlHal): ?>
                <li class="page-item"><a class="page-link" href="?hal=<?= $halAktif + 1 ?>&cari=<?= $cari ?>&dataPerHalaman=<?= $dataPerHalaman ?>">Berikutnya</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</div>
</body>
</html>
