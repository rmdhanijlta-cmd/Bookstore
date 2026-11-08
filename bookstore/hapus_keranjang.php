<?php
// Menghapus satu item dari keranjang user.
session_name('MILLE_USER');
session_start();
include 'koneksi.php';

$id = $_GET['id'];

mysqli_query($conn,
    "DELETE FROM keranjang WHERE id='$id'"
);

header("Location: keranjang.php");
exit;
?>