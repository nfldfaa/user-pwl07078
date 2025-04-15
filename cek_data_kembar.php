<?php
require "koneksi.php";
require "head.html";

$limitOptions = [5, 10, 25, 50, 100];
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cek Data User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/styleku.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
</head>
<body>
<?php require "head.html"; ?>

<div class="container mt-4">
    <h2 class="text-center mb-4">CEK DATA USER</h2>

    <!-- Search and Limit Filter -->
    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari ID user atau Username...">
        </div>
        <div class="col-md-3">
            <select id="limitSelect" class="form-select">
                <?php foreach ($limitOptions as $opt): ?>
                    <option value="<?= $opt ?>" <?= ($opt === $limit) ? 'selected' : '' ?>><?= $opt ?> per halaman</option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Tabel dan Pagination -->
    <div id="userTableContainer">
        <!-- Data akan dimuat via AJAX -->
    </div>
</div>

<script>
function loadUserData(keyword = '', limit = 5, page = 1) {
    $.get('search_CekDataKembar_Ajax.php', {
        keyword: keyword,
        limit: limit,
        page: page
    }, function(data) {
        $('#userTableContainer').html(data);
    });
}

$(document).ready(function() {
    let limit = $('#limitSelect').val();
    let page = 1;
    let keyword = '';

    loadUserData(keyword, limit, page);

    $('#searchInput, #limitSelect').on('input change', function() {
        keyword = $('#searchInput').val();
        limit = $('#limitSelect').val();
        page = 1;
        loadUserData(keyword, limit, page);
    });

    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const newPage = $(this).data('page');
        if (newPage) {
            keyword = $('#searchInput').val();
            limit = $('#limitSelect').val();
            loadUserData(keyword, limit, newPage);
        }
    });
});
</script>
</body>
</html>
