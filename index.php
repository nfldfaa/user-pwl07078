<?php 
session_start(); // Memulai session
require "koneksi.php"; // Pastikan koneksi ke database benar

// Jika sudah login, redirect ke homeadmin.php
if (isset($_SESSION['iduser'])) { 
    header("location: homeadmin.php"); 
    exit; 
}

$error = ""; // Variabel untuk menyimpan pesan error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $passw = trim($_POST['password']); // Ambil password tanpa di-hash!
    
    if (!empty($username) && !empty($passw)) {
        // Gunakan prepared statement untuk mencegah SQL Injection
        $sql1 = "SELECT * FROM user WHERE username = ?";
        $stmt = $conn->prepare($sql1);
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
            
        if ($result->num_rows == 1) {
            $user1 = $result->fetch_assoc();
            
            // Cek apakah password di database di-hash atau tidak
            if ($user1['password'] === $passw) { // Jika password **tidak di-hash**
                $_SESSION['iduser'] = $user1['iduser']; // Simpan session user ID
                $_SESSION['username'] = $user1['username']; // Simpan session username
                header("Location: homeadmin.php");
                exit();
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "User tidak ditemukan.";
        }

        $stmt->close();
    } else {
        $error = "Harap isi semua kolom.";
    }

    $koneksi->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Sistem</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap lokal -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/styleku.css">
    <script src="bootstrap/js/bootstrap.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="w-25 mx-auto text-center mt-5">
            <div class="card bg-dark text-light">
                <div class="card-body">
                    <h2 class="card-title">LOGIN SISTEM</h2>
                    <?php if (!empty($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
                    <form method="post" action="">
                        <div class="form-group">
                            <label for="username">Nama user</label>
                            <input class="form-control" type="text" name="username" id="username">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input class="form-control" type="password" name="password" id="password">
                        </div>
                        <div>
                            <button class="btn btn-info mt-2" type="submit">Login</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
