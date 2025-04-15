<?php
$host = "localhost";
$username = "root";  // Sesuaikan dengan username database Anda
$password = "";      // Sesuaikan dengan password database Anda
$database = "pwl07078"; // Sesuaikan dengan nama database Anda

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");
?>