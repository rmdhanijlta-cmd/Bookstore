<?php
include 'koneksi.php';

$nama     = $_POST['nama'];
$email    = $_POST['email'];
$alamat   = $_POST['alamat'];
$telepon  = $_POST['telepon'];
$password = $_POST['password'];

$query = "INSERT INTO users (nama, email, alamat, telepon, password, role)
          VALUES ('$nama','$email','$alamat','$telepon','$password','user')";

if(mysqli_query($conn, $query)){
    echo "Registrasi berhasil";
    header("Location: login.php");
}else{
    echo "Gagal registrasi: " . mysqli_error($conn);
}
?>