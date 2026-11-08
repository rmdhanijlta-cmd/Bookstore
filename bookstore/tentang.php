<?php
// Menampilkan informasi tentang Mille Pagine untuk user.
/* HALAMAN TENTANG USER
   Memastikan sesi user aktif, lalu menampilkan informasi Mille Pagine
   dengan susunan dan gaya yang sama seperti halaman Tentang milik admin. */
session_name('MILLE_USER');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tentang Kita - Mille Pagine</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="admin.css">
<style>
/* Navigasi user memakai warna dan bentuk yang sama dengan navigasi admin. */
.user-sidebar {
    width: 230px;
    flex-shrink: 0;
    background: #fff;
    border-right: 1px solid #E9E1D8;
    padding: 20px 14px;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}
.user-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 4px 8px 18px;
    text-decoration: none;
    color: #5B2E0F;
    font-family: Georgia,serif;
    font-size: 19px;
    font-weight: bold;
}
.user-brand .logo {
    width: 34px;
    height: 34px;
    border-radius: 9px;
    background: #5B2E0F;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
}
.user-menu {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.user-menu a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 9px 10px;
    border-radius: 8px;
    color: #7B6A5C;
    text-decoration: none;
    font-size: 14px;
}
.user-menu a:hover,.user-menu a.active {
    background: #F5EFE8;
    color: #5B2E0F;
}
.user-menu a.active {
    font-weight: bold;
}
.user-bottom {
    margin-top: auto;
    padding-top: 14px;
    border-top: 1px solid #E9E1D8;
}
.user-bottom a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 9px 10px;
    border-radius: 8px;
    color: #7B6A5C;
    text-decoration: none;
}
.user-bottom a:hover {
    background: #F5EFE8;
    color: #5B2E0F;
}
@media(max-width:700px) {
    .user-sidebar {
        width: 72px;
        padding: 16px 8px;
    }
    .user-brand {
        justify-content: center;
        font-size: 0;
    }
    .user-menu a,.user-bottom a {
        justify-content: center;
        font-size: 0;
        gap: 0;
    }
    .user-menu a i,.user-bottom a i {
        font-size: 15px;
    }
}
</style>
</head>
<body>
<div class="wrapper">
    <nav class="user-sidebar">
        <a class="user-brand" href="index.php"><span class="logo"><i class="fa-solid fa-book-open-reader"></i></span>Mille Pagine</a>
        <div class="user-menu">
            <a href="index.php"><i class="fa-solid fa-house"></i> Beranda</a>
            <a href="keranjang.php"><i class="fa-solid fa-cart-shopping"></i> Keranjang</a>
            <a class="active" href="tentang.php"><i class="fa-solid fa-circle-info"></i> Tentang Kita</a>
            <a href="profil.php"><i class="fa-solid fa-user"></i> Profil</a>
        </div>
        <div class="user-bottom">
            <a href="chat.php"><i class="fa-solid fa-headset"></i> Hubungi Admin</a>
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Keluar</a>
        </div>
    </nav>

    <main class="content">
        <!-- Bagian pengenalan toko: teks dan foto, mengikuti susunan halaman admin. -->
        <section class="about-top">
            <div class="about-text">
                <h1>Tentang Kita</h1>
                <img src="line.png" alt="Ornamen" class="ornament">
                <p>Mille Pagine adalah toko buku online yang menyediakan berbagai pilihan buku berkualitas untuk semua kalangan. Kami percaya bahwa setiap buku memiliki kekuatan untuk membuka wawasan, menginspirasi, dan menemani setiap perjalanan belajar maupun hiburan.</p>
                <p>Kami berkomitmen memberikan pengalaman berbelanja buku yang mudah, aman, dan nyaman melalui layanan yang sederhana serta koleksi buku yang terus diperbarui.</p>
            </div>
            <div class="about-image"><img src="library.jpg" alt="Koleksi perpustakaan"></div>
        </section>

        <!-- Informasi kontak dan akun media sosial toko. -->
        <section class="contact-box">
            <div class="contact-col">
                <h2>Hubungi Kami</h2>
                <div class="contact-item"><div class="contact-icon"><i class="fa-solid fa-phone"></i></div><div><strong>Telpon/Whatsapp</strong><p>0812-3456-7890</p></div></div>
                <div class="contact-item"><div class="contact-icon"><i class="fa-solid fa-envelope"></i></div><div><strong>Email</strong><p>millepagine@gmail.com</p></div></div>
                <div class="contact-item"><div class="contact-icon"><i class="fa-solid fa-clock"></i></div><div><strong>Jam Operasional</strong><p>Senin – Minggu<br>08.00 – 21.00 WIB</p></div></div>
            </div>
            <div class="divider-line"></div>
            <div class="contact-col">
                <h2>Ikuti Kami</h2>
                <div class="social-card"><i class="fa-brands fa-instagram"></i><div><strong>Instagram</strong><span>@millepagine.id</span></div></div>
                <div class="social-card"><i class="fa-brands fa-tiktok"></i><div><strong>Tiktok</strong><span>@millepagine.id</span></div></div>
            </div>
        </section>

        <!-- Kutipan penutup halaman. -->
        <section class="quote-box">
            <div class="quote-img"><img src="buku.png" alt="Ilustrasi buku"></div>
            <div class="quote-divider"></div>
            <div class="quote-content"><p>“A book is a dream that you hold in your hands.”</p><span>— Neil Gaiman</span></div>
            <div class="quote-img"><img src="bulu.png" alt="Ilustrasi pena"></div>
        </section>
    </main>
</div>
</body>
</html>
