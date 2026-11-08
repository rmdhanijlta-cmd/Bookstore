<?php
// Menghubungkan halaman PHP ke database MySQL bookstore.
$host = "localhost";
$user = "root";
$pass = "";
$db   = "bookstore";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>