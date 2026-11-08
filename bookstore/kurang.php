<?php
// Mengurangi jumlah buku di keranjang.
session_name('MILLE_USER');
session_start();
include 'koneksi.php';

$id = $_GET['id'];

$data = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT jumlah FROM keranjang WHERE id='$id'")
);

if($data['jumlah'] > 1){

    mysqli_query($conn,
        "UPDATE keranjang SET jumlah = jumlah - 1 WHERE id='$id'"
    );

}else{

    mysqli_query($conn,
        "DELETE FROM keranjang WHERE id='$id'"
    );
}

header("Location: keranjang.php");
exit;
?>