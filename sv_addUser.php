<?php
// Memanggil file pustaka fungsi
require "fungsi.php";

// Memindahkan data kiriman dari form ke variabel biasa
$username = $_POST["username"];
$password = $_POST["password"];
$status = $_POST["status"];

// Query untuk menyimpan data tanpa ID User (karena auto-increment)
$sql = "INSERT INTO user (username, password, status) VALUES (?, ?, ?)";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("sss", $username, $password, $status);

if ($stmt->execute()) {
    echo "<div class='alert alert-success'>
            Data berhasil ditambahkan! 
            <script>
                setTimeout(function() {
                    window.location.href = 'ajaxUpdateUser.php';
                }, 2000);
            </script>
          </div>";
} else {
    echo "<div class='alert alert-danger'>
            Data gagal tersimpan: " . $stmt->error . "
          </div>";
}

$stmt->close();
?>