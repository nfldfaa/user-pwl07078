<?php
// Memanggil file pustaka fungsi
require "fungsi.php";

// Pastikan data dikirim melalui metode POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Memindahkan data dari form ke variabel biasa
    $iduser = $_POST["iduser"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $status = $_POST["status"];

    // Query update dengan prepared statement
    $sql = "UPDATE user SET username = ?, password = ?, status = ? WHERE iduser = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ssss", $username, $password, $status, $iduser);

    // Eksekusi query
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>
                Data berhasil diperbarui! 
                <script>
                    setTimeout(function() {
                        window.location.href = 'ajaxUpdateUser.php';
                    }, 2000);
                </script>
              </div>";
    } else {
        echo "<div class='alert alert-danger'>
                Data gagal diperbarui: " . $stmt->error . "
              </div>";
    }

    // Menutup statement
    $stmt->close();
}
?>
