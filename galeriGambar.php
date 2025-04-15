<?php
include 'koneksi.php';

if (!isset($conn) || $conn->connect_error) {
    die("Koneksi database gagal. Silakan cek file koneksi.php");
}

$limitOptions = [4, 8, 20, 40, 100];
$limit = isset($_GET['limit']) && in_array((int)$_GET['limit'], $limitOptions) ? (int)$_GET['limit'] : 4;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$total = $conn->query("SELECT COUNT(*) as total FROM galeri_gambar")->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);
$result = $conn->query("SELECT * FROM galeri_gambar ORDER BY uploaded_at DESC LIMIT $limit OFFSET $offset");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Galeri Gambar</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styleku.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.js"></script>
    <style>
        .gallery-img:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
    </style>
</head>
<body>
<?php require "head.html"; ?>

<div class="container mt-4">
    <h2 class="text-center mb-4">GALERI GAMBAR</h2>

    <!-- Upload Form -->
    <div class="upload-section mb-4 p-4 bg-light rounded">
        <form id="uploadForm" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="gambar">Upload Gambar (JPEG, PNG, GIF)</label>
                <input type="file" name="gambar" id="gambar" class="form-control" required>
                <small class="form-text text-muted">Ukuran maksimal: 10MB</small>
                <div id="fileError" class="text-danger mt-1"></div>
            </div>
            <button type="submit" class="btn btn-primary">Unggah</button>
            <div id="uploadMessage" class="mt-3"></div>
        </form>
    </div>

    <!-- Filter & Search -->
    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" id="cariInput" class="form-control" placeholder="Cari nama gambar...">
        </div>
        <div class="col-md-3">
            <select id="limitSelect" class="form-control">
                <?php foreach ($limitOptions as $opt): ?>
                    <option value="<?= $opt ?>" <?= ($opt === $limit) ? 'selected' : '' ?>><?= $opt ?> per halaman</option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Galeri -->
    <div class="row" id="galleryContainer">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <a href="<?= $row['filepath'] ?>" target="_blank">
                            <img src="<?= $row['thumbpath'] ?>" class="card-img-top gallery-img" alt="<?= htmlspecialchars($row['filename']) ?>">
                        </a>
                        <div class="card-body">
                            <p class="card-text small text-truncate"><?= htmlspecialchars($row['filename']) ?></p>
                            <a href="editGambar.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="hpsGambar.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus gambar ini?')">Hapus</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">Belum ada gambar yang diupload.</div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page - 1 ?>&limit=<?= $limit ?>">Sebelumnya</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&limit=<?= $limit ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?page=<?= $page + 1 ?>&limit=<?= $limit ?>">Berikutnya</a>
            </li>
        </ul>
    </nav>
</div>

<script>
$(document).ready(function() {
    $('#gambar').on('change', function() {
        const file = this.files[0];
        const maxSize = 10 * 1024 * 1024;

        if (file && file.size > maxSize) {
            $('#fileError').text('Ukuran file terlalu besar (maks 10MB)');
            this.value = '';
        } else {
            $('#fileError').text('');
        }
    });

    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        $('#uploadMessage').removeClass().text('');

        $.ajax({
            url: 'sv_galeriGambar.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    location.reload();
                } else {
                    $('#uploadMessage').addClass('alert alert-danger').text('Error: ' + response.message);
                }
            },
            error: function() {
                $('#uploadMessage').addClass('alert alert-danger').text('Terjadi kesalahan saat mengunggah.');
            }
        });
    });

    // Live Search
    $('#cariInput, #limitSelect').on('input change', function() {
        const keyword = $('#cariInput').val();
        const limit = $('#limitSelect').val();

        $.get('search_GaleriGambar_Ajax.php', { keyword: keyword, limit: limit }, function(data) {
            $('#galleryContainer').html(data);
        });
    });
});
</script>
</body>
</html>
