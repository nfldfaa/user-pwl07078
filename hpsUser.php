<?php
require "fungsi.php";
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["iduser"])) {
    $id = intval($_POST["iduser"]); // Pastikan ID angka

    $sql = "DELETE FROM user WHERE iduser = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => $stmt->error]);
    }

    $stmt->close();
    $koneksi->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request"]);
}
?>
