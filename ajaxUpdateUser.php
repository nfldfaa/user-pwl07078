<?php
require "koneksi.php";
require "head.html";

$limitOptions = [5, 10, 25, 50, 100];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Daftar User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styleku.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
</head>
<body>
<?php require "head.html"; ?>

<div class="container mt-4">
    <h2 class="text-center mb-4">DAFTAR USER</h2>

    <!-- Filter & Tambah Data -->
    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari ID user atau Username...">
        </div>
        <div class="col-md-3">
            <select id="limitSelect" class="form-select">
                <?php foreach ($limitOptions as $opt): ?>
                    <option value="<?= $opt ?>"><?= $opt ?> per halaman</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <a href="addUser.php" class="btn btn-success w-100">Tambah Data</a>
        </div>
    </div>

    <!-- Kontainer tabel & pagination -->
    <div id="userTableContainer">
        <!-- Data akan dimuat melalui AJAX -->
    </div>
</div>

<script>
function loadUserData(keyword = '', limit = 5, page = 1) {
    $.get('search_UpdateUser_Ajax.php', {
        keyword: keyword,
        limit: limit,
        page: page
    }, function(data) {
        $('#userTableContainer').html(data);
    });
}

$(document).ready(function() {
    let keyword = '';
    let limit = $('#limitSelect').val();
    let currentPage = 1;

    loadUserData(keyword, limit, currentPage);

    $('#searchInput, #limitSelect').on('input change', function() {
        keyword = $('#searchInput').val();
        limit = $('#limitSelect').val();
        currentPage = 1;
        loadUserData(keyword, limit, currentPage);
    });

    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page) {
            keyword = $('#searchInput').val();
            limit = $('#limitSelect').val();
            loadUserData(keyword, limit, page);
        }
    });

    // Hapus data
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        let userId = $(this).data('id');
        let row = $(this).closest('tr');
        if (confirm('Yakin ingin menghapus user ini?')) {
            $.post('hpsUser.php', { iduser: userId }, function(response) {
                if (response.status === 'success') {
                    row.fadeOut(300, function() { $(this).remove(); });
                } else {
                    alert('Gagal menghapus data: ' + response.message);
                }
            }, 'json');
        }
    });
});
</script>
</body>
</html>
