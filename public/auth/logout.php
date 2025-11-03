<?php
session_start();
session_unset();       // Hapus semua variabel session
session_destroy();     // Hancurkan session
session_regenerate_id(true);  // Buat session ID baru untuk keamanan

echo "<script>window.location.reload();</script>";
header('Location: login.php');
exit;
?>