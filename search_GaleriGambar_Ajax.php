<?php
include 'koneksi.php';

$keyword = isset($_GET['keyword']) ? $conn->real_escape_string($_GET['keyword']) : '';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 4;

$query = "SELECT * FROM galeri_gambar WHERE filename LIKE '%$keyword%' ORDER BY uploaded_at DESC LIMIT $limit";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="col-md-3 mb-4">';
        echo '<div class="card h-100">';
        echo '<a href="' . $row['filepath'] . '" target="_blank">';
        echo '<img src="' . $row['thumbpath'] . '" class="card-img-top gallery-img" alt="' . htmlspecialchars($row['filename']) . '">';
        echo '</a>';
        echo '<div class="card-body">';
        echo '<p class="card-text small text-truncate">' . htmlspecialchars($row['filename']) . '</p>';
        echo '<a href="editGambar.php?id=' . $row['id'] . '" class="btn btn-sm btn-warning">Edit</a> ';
        echo '<a href="hpsGambar.php?id=' . $row['id'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'Yakin ingin menghapus gambar ini?\')">Hapus</a>';
        echo '</div></div></div>';
    }
} else {
    echo '<div class="col-12"><div class="alert alert-warning">Tidak ada gambar ditemukan.</div></div>';
}
