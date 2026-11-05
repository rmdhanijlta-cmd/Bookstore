<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
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
</head>
<body>

<div class="wrapper">

    <?php $halaman_aktif = 'tentang'; include 'admin_sidebar.php'; ?>

    <div class="content">


<!-- TOP -->
    <div class="about-top">

        <div class="about-text">

            <h1>Tentang Kita</h1>

            <img src="line.png" alt="ornamen" class="ornament">

            <p>
                Mille Pagine adalah toko buku online yang menyediakan berbagai pilihan buku berkualitas untuk semua kalangan.
                Kami percaya bahwa setiap buku memiliki kekuatan untuk membuka wawasan, menginspirasi, dan menemani
                setiap perjalanan belajar maupun hiburan.
            </p>

            <p>
                Kami berkomitmen memberikan pengalaman berbelanja buku yang mudah, aman, dan nyaman melalui layanan
                yang sederhana serta koleksi buku yang terus diperbarui.
            </p>

        </div>

        <div class="about-image">
            <img src="library.jpg" alt="Tentang Kita">
        </div>

    </div>

    <!-- CONTACT -->
    <div class="contact-box">

        <div class="contact-col">

            <h2>Hubungi Kami</h2>

            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fa-solid fa-phone"></i>
                </div>

                <div>
                    <strong>Telpon/Whatsapp</strong>
                    <p>0812-3456-7890</p>
                </div>
            </div>

            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fa-solid fa-envelope"></i>
                </div>

                <div>
                    <strong>Email</strong>
                    <p>millepagine@gmail.com</p>
                </div>
            </div>

            <div class="contact-item">
                <div class="contact-icon">
                    <i class="fa-solid fa-clock"></i>
                </div>

                <div>
                    <strong>Jam Operasional</strong>
                    <p>Senin – Minggu<br>08.00 – 21.00 WIB</p>
                </div>
            </div>

        </div>

        <!-- GARIS VERTIKAL -->
        <div class="divider-line"></div>

        <div class="contact-col">

            <h2>Ikuti Kami</h2>

            <div class="social-card">
                <i class="fa-brands fa-instagram"></i>

                <div>
                    <strong>Instagram</strong>
                    <span>@millepagine.id</span>
                </div>
            </div>

            <div class="social-card">
                <i class="fa-brands fa-tiktok"></i>

                <div>
                    <strong>Tiktok</strong>
                    <span>@millepagine.id</span>
                </div>
            </div>

        </div>

    </div>

    <!-- QUOTE -->
    <div class="quote-box">

        <div class="quote-img">
            <img src="buku.png" alt="buku">
        </div>

        <div class="quote-divider"></div>

        <div class="quote-content">
            <p>“A book is a dream that you hold in your hands.”</p>
            <span>— Neil Gaiman</span>
        </div>

        <div class="quote-img">
            <img src="bulu.png" alt="bulu">
        </div>

    </div>

    </div>

</div>

<script src="admin.js"></script>
</body>
</html>
