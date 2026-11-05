<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['id'])){
    $buku_id = (int)($_GET['id'] ?? 0);

    header("Location: landing.php?redirect=keranjang&buku_id=" . $buku_id);
    exit;
}

$user_id = (int)$_SESSION['id'];
$buku_id = (int)($_GET['id'] ?? 0);

if($buku_id <= 0){
    header("Location: index.php");
    exit;
}

// Cek apakah buku sudah ada di keranjang user
$cek = mysqli_query($conn, "SELECT * FROM keranjang WHERE user_id='$user_id' AND buku_id='$buku_id'");

if(mysqli_num_rows($cek) > 0){

    // Kalau sudah ada, tambah jumlah
    mysqli_query($conn, "UPDATE keranjang SET jumlah = jumlah + 1 WHERE user_id='$user_id' AND buku_id='$buku_id'");

}else{

    // Kalau belum ada, masukkan ke keranjang
    mysqli_query($conn, "INSERT INTO keranjang (user_id, buku_id, jumlah) VALUES ('$user_id','$buku_id',1)");
}

header("Location: keranjang.php");
exit;
?>