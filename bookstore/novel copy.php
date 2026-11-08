<?php
session_name('MILLE_ADMIN');
session_start();
include 'koneksi.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}
?>

<?php
$buku = mysqli_query($conn, "
    SELECT buku.*
    FROM buku
    JOIN kategori ON buku.kategori_id = kategori.id
    WHERE kategori.nama_kategori = 'Novel'
    ORDER BY buku.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Novel - Mille Pagine</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}
body {
    background: #F4F1ED;
    height: 100vh;
    overflow: hidden;
}
.wrapper {
    width: 100%;
    height: 100vh;
    display: flex;
}
/* SIDEBAR */
.sidebar {
    width: 160px;
    background: #5B2E0F;
    padding: 18px 10px;
}
.sidebar a {
    display: flex;
    align-items: center;
    gap: 8px;
    color: white;
    text-decoration: none;
    font-size: 13px;
    padding: 10px 12px;
    border-radius: 8px;
    margin-bottom: 10px;
}
.sidebar a.active {
    background: #FFF3E8;
    color: #5B2E0F;
    font-weight: bold;
}
/* CONTENT */
.content {
    flex: 1;
    padding: 22px 28px;
    height: 100vh;
    overflow-y: auto;
}
/* HEADER NOVEL */
.novel-header {
    display: flex;
    align-items: flex-start;
    justify-content: center;
    position: relative;
    margin-bottom: 8px;
}
.back-btn {
    position: absolute;
    left: 0;
    top: 0;
    width: 40px;
    height: 40px;
    border: 2px solid #2B160A;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #2B160A;
    text-decoration: none;
    font-size: 20px;
}
.title-area {
    text-align: center;
}
.title-area h1 {
    font-size: 52px;
    color: #4A2410;
    font-family: Georgia, serif;
    margin-bottom: 2px;
}
.ornament {
    width: 220px;
    height: auto;
    display: block;
    margin: 0 auto;
}
/* SEARCH TENGAH */
.search-center {
    display: flex;
    justify-content: center;
    margin-top: 6px;
    margin-bottom: 32px;
}
.search-box {
    width: 420px;
    background: #5B2E0F;
    border-radius: 20px;
    padding: 12px 18px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
}
.search-box i {
    color: white;
    font-size: 16px;
}
.search-box input {
    flex: 1;
    background: transparent;
    border: none;
    outline: none;
    color: white;
    font-size: 16px;
}
.search-box input::placeholder {
    color: #F5E6D3;
}
/* BOOKS */
.books {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 40px 40px;
    justify-items: center;
}
.book {
    width: 185px;
    background: #5B2E0F;
    border-radius: 8px;
    padding: 10px;
    color: white;
    min-height: 300px;
}
.book img {
    width: 100%;
    height: 180px;
    object-fit: contain;
    background: #5B2E0F;
    border-radius: 4px;
    padding: 4px;
}
.book-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 6px;
    margin-top: 10px;
}
.book-header h3 {
    font-size: 16px;
    font-family: Roboto;
    line-height: 1.3;
    margin: 0;
    flex: 1;
}
.rating {
    font-size: 9px;
    color: white;
    font-weight: 600;
    white-space: nowrap;
}
.rating i {
    color: #F7C948;
    margin-right: 2px;
}
.author {
    font-size: 13px;
    font-family: Roboto;
    color: #EAD9C7;
    margin-top: 4px;
}
.price {
    font-size: 13px;
    font-family: Roboto;
    color: #EAD9C7;
    margin-top: 8px;
}
.cart {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #8A5A33;
    border: none;
    color: white;
    float: right;
    margin-top: 8px;
    cursor: pointer;
}
</style>
</head>

<body>

<div class="wrapper">

    <!-- SIDEBAR SUDAH DIBENERIN -->
  <nav class="sidebar">

        <a class="active" href="#">
            <i class="fa-solid fa-house"></i> Beranda
        </a>

        <a href="pesanan_user.php">
            <i class="fa-solid fa-cart-shopping"></i> Pesanan User
        </a>

        <a href="tentang copy.php">
            <i class="fa-solid fa-circle-info"></i> Tentang Kita
        </a>

        <a href="admin user.php">
            <i class="fa-solid fa-user"></i> User Yang Terdaftar
        </a>

        <a href="kelola_buku.php">
            <i class="fa-solid fa-book-open"></i> Kelola Buku
        </a>

        <a href="kelola_kategori.php">
            <i class="fa-solid fa-table-list"></i> Kelola Kategori
        </a>
        <a href="verifikasi_pembayaran.php"><i class="fa-solid fa-money-check-dollar"></i> Verifikasi Pembayaran</a>

    </nav>


    <div class="content">

        <div class="novel-header">

            <a href="admin-dashboard.php" class="back-btn">
                <i class="fa-solid fa-arrow-left"></i>
            </a>

            <div class="title-area">
                <h1>Novel</h1>
                <img src="line.png" alt="ornamen" class="ornament">
            </div>

        </div>

        <div class="search-center">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Search...">
            </div>
        </div>

        <div class="books">
            <?php while($row = mysqli_fetch_assoc($buku)):?> 

                <div class="book"> 
                    <a href="detail_buku.php?id=<?= $row['id']; ?>">
                         <img src="<?= $row['gambar']; ?>"
                          alt="<?= $row['judul']; ?>">
                     </a> 
                     
                     <div class="book-header"> 
                        <h3><?= $row['judul']; ?></h3> 
                    </div> 
                    
                    <p class="author">by <?= $row['penulis']; ?>.</p> 

                        <p class="price"> Rp. <?= number_format($row['harga'],0,',','.'); ?> </p>

                    <a href="tambah_keranjang.php?id=<?= $row['id']; ?>" class="cart">  
                        <i class="fa-solid fa-cart-shopping"></i>
                    </a> 

                </div> 
            <?php endwhile; ?> 
        </div>

    </div>

</div>

</body>
</html>