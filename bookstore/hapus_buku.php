<?php
// Menghapus data buku yang dipilih admin.
session_name('MILLE_ADMIN');
session_start();
 include 'koneksi.php'; 
if(!isset($_SESSION['role'])
     || $_SESSION['role'] != 'admin')
    { 
        header("Location: login.php"); exit; 
        } 
        $id = $_GET['id']; 
 $data = mysqli_fetch_assoc
 (mysqli_query($conn, "SELECT gambar FROM buku WHERE 
 id='$id'")); 

 if($data && file_exists($data['gambar']))
    { unlink($data['gambar']); 
     }  
     
     mysqli_query($conn, "DELETE FROM buku WHERE id='$id'"); 
     header("Location: kelola_buku.php"); 
     exit; 
?>