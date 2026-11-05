<?php
session_start();
include 'koneksi.php';

$email    = $_POST['email'];
$password = $_POST['password'];

$query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$password'");

$data = mysqli_fetch_assoc($query);

if($data){

    $_SESSION['id']    = $data['id'];
    $_SESSION['nama']  = $data['nama'];
    $_SESSION['role']  = $data['role'];
    $_SESSION['email'] = $data['email'];

    // CEK ROLE
    if($data['role'] == 'admin'){
        header("Location: admin-dashboard.php");
    }else{
        header("Location: index.php");
    }

}else{
    echo "Email atau password salah";
}
?>