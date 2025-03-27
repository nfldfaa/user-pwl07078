<?php
session_start();
session_unset(); 
session_destroy();
session_regenerate_id(true); // Regenerasi ID session untuk keamanan
header("location: index.php"); // Kembali ke halaman login
exit;
?>
