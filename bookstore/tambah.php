<?php
session_start();
include 'koneksi.php';

$id = $_GET['id'];

mysqli_query($conn,
    "UPDATE keranjang SET jumlah = jumlah + 1 WHERE id='$id'"
);

header("Location: keranjang.php");
exit;
?>