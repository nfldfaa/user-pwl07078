<?php
require "koneksi.php";

$keyword = isset($_GET['keyword']) ? $conn->real_escape_string($_GET['keyword']) : '';
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$like = "%$keyword%";

$totalStmt = $conn->prepare("SELECT COUNT(*) as total FROM user WHERE iduser LIKE ? OR username LIKE ?");
$totalStmt->bind_param("ss", $like, $like);
$totalStmt->execute();
$total = $totalStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($total / $limit);

$stmt = $conn->prepare("SELECT * FROM user WHERE iduser LIKE ? OR username LIKE ? ORDER BY iduser ASC LIMIT ?, ?");
$stmt->bind_param("ssii", $like, $like, $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

// Tabel
echo '<table class="table table-striped table-hover">';
echo '<thead class="table-dark"><tr><th>ID User</th><th>Username</th><th>Password</th><th>Status</th></tr></thead><tbody>';

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . htmlspecialchars($row['iduser']) . "</td>
                <td>" . htmlspecialchars($row['username']) . "</td>
                <td>" . htmlspecialchars($row['password']) . "</td>
                <td>" . htmlspecialchars($row['status']) . "</td>
              </tr>";
    }
} else {
    echo '<tr><td colspan="4" class="text-center text-muted">Data tidak ditemukan</td></tr>';
}
echo '</tbody></table>';

// Pagination
echo '<nav><ul class="pagination justify-content-center">';
$prev = $page - 1;
$next = $page + 1;

echo '<li class="page-item ' . ($page <= 1 ? 'disabled' : '') . '">
        <a class="page-link" href="#" data-page="' . $prev . '">Sebelumnya</a></li>';

for ($i = 1; $i <= $totalPages; $i++) {
    $active = ($i == $page) ? 'active' : '';
    echo '<li class="page-item ' . $active . '">
            <a class="page-link" href="#" data-page="' . $i . '">' . $i . '</a>
          </li>';
}

echo '<li class="page-item ' . ($page >= $totalPages ? 'disabled' : '') . '">
        <a class="page-link" href="#" data-page="' . $next . '">Berikutnya</a></li>';

echo '</ul></nav>';
