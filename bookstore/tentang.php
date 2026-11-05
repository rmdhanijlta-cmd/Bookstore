<?php
session_start();
include 'koneksi.php';

$total_judul    = 0;
$total_kategori = 0;
$total_terjual  = 0;

$q1 = mysqli_query($conn, "SELECT COUNT(*) AS n FROM buku");
if($q1){ $total_judul = (int)mysqli_fetch_assoc($q1)['n']; }

$q2 = mysqli_query($conn, "SELECT COUNT(*) AS n FROM kategori");
if($q2){ $total_kategori = (int)mysqli_fetch_assoc($q2)['n']; }

$q3 = mysqli_query($conn, "SELECT COALESCE(SUM(jumlah),0) AS n FROM detail_pesanan");
if($q3){ $total_terjual = (int)mysqli_fetch_assoc($q3)['n']; }
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tentang Kita - Mille Pagine</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
:root{
    --coklat:#5B2E0F;
    --coklat-tua:#4A2410;
    --coklat-muda:#7B5A3D;
    --krem:#F5E6D3;
    --krem-terang:#FFF3E8;
    --latar:#F4F1ED;
    --emas:#B98A55;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

body{
    background:var(--latar);
    height:100vh;
    overflow:hidden;
}

.wrapper{
    width:100%;
    height:100vh;
    display:flex;
}

/* ===== SIDEBAR (tidak diubah) ===== */
.sidebar{
    width:220px;
    background:linear-gradient(180deg, #5B2E0F 0%, #3F1E08 100%);
    padding:22px 14px;
    flex-shrink:0;
    display:flex;
    flex-direction:column;
}

.sidebar-brand{
    display:flex;
    align-items:center;
    gap:10px;
    padding:4px 8px 20px;
    margin-bottom:16px;
    border-bottom:1px solid rgba(255,243,232,.15);
    color:#FFF3E8;
    font-family:Georgia, serif;
    font-size:20px;
    letter-spacing:.5px;
}

.sidebar-brand .logo-ikon{
    width:40px;
    height:40px;
    border-radius:12px;
    background:#F5E6D3;
    color:#5B2E0F;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
    box-shadow:0 6px 14px rgba(0,0,0,.3);
    flex-shrink:0;
}

.sidebar-menu{
    display:flex;
    flex-direction:column;
    gap:6px;
}

.nav-item{
    display:flex;
    align-items:center;
    gap:12px;
    color:rgba(255,243,232,.8);
    text-decoration:none;
    font-size:14px;
    padding:9px 10px;
    border-radius:14px;
    transition:background .2s ease, transform .2s ease, color .2s ease;
}

.nav-item .ikon{
    width:34px;
    height:34px;
    border-radius:10px;
    background:rgba(255,243,232,.08);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:14px;
    flex-shrink:0;
    transition:background .2s ease, color .2s ease;
}

.nav-item:hover{
    background:rgba(255,243,232,.08);
    color:white;
    transform:translateX(4px);
}

.nav-item:hover .ikon{
    background:rgba(255,243,232,.18);
}

.nav-item.active{
    background:#FFF3E8;
    color:#5B2E0F;
    font-weight:bold;
    box-shadow:0 8px 18px rgba(0,0,0,.25);
}

.nav-item.active .ikon{
    background:#5B2E0F;
    color:white;
}

.sidebar-bawah{
    margin-top:auto;
}

.bantuan{
    display:block;
    text-align:center;
    text-decoration:none;
    color:#FFF3E8;
    background:rgba(255,243,232,.08);
    border:1px solid rgba(255,243,232,.18);
    border-radius:18px;
    padding:18px 14px;
    font-size:13px;
    line-height:1.4;
    transition:background .2s ease, transform .2s ease;
}

.bantuan:hover{
    background:rgba(255,243,232,.16);
    transform:translateY(-3px);
}

.bantuan .ikon-besar{
    width:44px;
    height:44px;
    margin:0 auto 10px;
    border-radius:50%;
    background:#B98A55;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
}

.bantuan strong{
    display:block;
    font-size:14px;
    margin-bottom:2px;
}

.bantuan span{
    color:rgba(255,243,232,.7);
}

@media(max-width:700px){
    .sidebar{
        width:72px;
        padding:16px 8px;
    }

    .sidebar-brand{
        justify-content:center;
        padding:4px 0 16px;
        font-size:0;
    }

    .nav-item{
        justify-content:center;
        padding:8px 0;
        font-size:0;
        gap:0;
    }

    .nav-item:hover{
        transform:none;
    }

    .bantuan{
        padding:12px 4px;
    }

    .bantuan strong,
    .bantuan span{
        display:none;
    }

    .bantuan .ikon-besar{
        margin:0 auto;
    }
}

/* ===== CONTENT ===== */
.content{
    flex:1;
    min-width:0;
    height:100vh;
    overflow-y:auto;
    padding:24px 32px 50px;
    scroll-behavior:smooth;
}

/* animasi muncul saat load */
@keyframes naik{
    from{ opacity:0; transform:translateY(18px); }
    to{ opacity:1; transform:translateY(0); }
}
.anim{
    animation:naik .55s ease both;
    animation-delay:calc(var(--i, 0) * 90ms);
}

/* muncul saat di-scroll */
.reveal{
    opacity:0;
    transform:translateY(26px);
    transition:opacity .6s ease, transform .6s ease;
    transition-delay:calc(var(--d, 0) * 90ms);
}
.reveal.tampil{
    opacity:1;
    transform:translateY(0);
}

/* ===== HERO ===== */
.hero{
    position:relative;
    overflow:hidden;
    background:linear-gradient(120deg, var(--coklat) 0%, #7A3E13 100%);
    border-radius:22px;
    padding:36px 40px;
    margin-bottom:28px;
    color:var(--krem-terang);
    box-shadow:0 14px 30px rgba(91,46,15,.25);
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:32px;
}
.hero .deco{
    position:absolute;
    left:44%;
    bottom:-30px;
    font-size:150px;
    color:rgba(255,243,232,.06);
    transform:rotate(-12deg);
    pointer-events:none;
}

.hero-text{ position:relative; flex:1; min-width:0; }
.hero-tag{
    display:inline-flex;
    align-items:center;
    gap:8px;
    background:rgba(255,243,232,.14);
    border:1px solid rgba(255,243,232,.25);
    padding:6px 14px;
    border-radius:999px;
    font-size:13px;
    margin-bottom:16px;
}
.hero-text h1{
    font-family:Georgia, serif;
    font-size:38px;
    margin-bottom:14px;
}
.hero-text p{
    font-size:15px;
    line-height:1.7;
    color:rgba(255,243,232,.85);
    margin-bottom:12px;
    max-width:520px;
}

.hero-btns{
    display:flex;
    flex-wrap:wrap;
    gap:12px;
    margin-top:22px;
}
.btn{
    display:inline-flex;
    align-items:center;
    gap:8px;
    padding:12px 22px;
    border-radius:999px;
    font-size:14px;
    font-weight:bold;
    text-decoration:none;
    border:none;
    cursor:pointer;
    transition:transform .2s ease, background .2s ease, box-shadow .2s ease;
}
.btn:hover{ transform:translateY(-3px); }
.btn-krem{
    background:var(--krem);
    color:var(--coklat-tua);
    box-shadow:0 8px 18px rgba(0,0,0,.22);
}
.btn-krem:hover{ background:white; }
.btn-garis{
    background:transparent;
    color:var(--krem-terang);
    border:1.5px solid rgba(255,243,232,.5);
}
.btn-garis:hover{ background:rgba(255,243,232,.15); }

.hero-img{
    position:relative;
    flex-shrink:0;
    width:340px;
}
.hero-img img{
    display:block;
    width:100%;
    height:250px;
    object-fit:cover;
    border-radius:20px;
    border:4px solid rgba(255,243,232,.3);
    box-shadow:0 18px 36px rgba(0,0,0,.35);
    transform:rotate(3deg);
    transition:transform .4s ease;
}
.hero-img:hover img{ transform:rotate(0deg) scale(1.03); }

/* ===== STATISTIK ===== */
.stats{
    display:grid;
    grid-template-columns:repeat(3, minmax(0, 1fr));
    gap:18px;
    margin-bottom:34px;
}
.stat{
    background:white;
    border-radius:18px;
    padding:24px 18px;
    text-align:center;
    box-shadow:0 6px 18px rgba(91,46,15,.08);
    transition:transform .25s ease, box-shadow .25s ease;
}
.stat:hover{
    transform:translateY(-6px);
    box-shadow:0 16px 30px rgba(91,46,15,.18);
}
.stat-icon{
    width:58px;
    height:58px;
    border-radius:50%;
    background:linear-gradient(160deg, var(--krem) 0%, #EAD2B5 100%);
    color:var(--coklat);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:22px;
    margin:0 auto 12px;
    transition:transform .3s ease;
}
.stat:hover .stat-icon{ transform:rotate(-10deg) scale(1.1); }
.stat .angka{
    font-size:38px;
    font-weight:bold;
    color:var(--coklat-tua);
    line-height:1;
}
.stat .label{
    margin-top:8px;
    font-size:14px;
    color:var(--coklat-muda);
}

/* ===== JUDUL SECTION ===== */
.section-head{
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:16px;
}
.section-head .icon{
    width:36px;
    height:36px;
    border-radius:50%;
    background:var(--coklat);
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:15px;
}
.section-head h2{
    font-size:22px;
    color:var(--coklat-tua);
    font-family:Georgia, serif;
}

/* ===== KONTAK ===== */
.contact-grid{
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:22px;
    margin-bottom:34px;
}

.panel{
    background:white;
    border-radius:20px;
    padding:24px;
    box-shadow:0 6px 18px rgba(91,46,15,.08);
}
.panel-title{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:10px;
    margin-bottom:18px;
}
.panel-title h3{
    font-family:Georgia, serif;
    font-size:20px;
    color:var(--coklat-tua);
}

.status-buka{
    display:inline-flex;
    align-items:center;
    gap:8px;
    font-size:12px;
    font-weight:bold;
    padding:6px 12px;
    border-radius:999px;
    background:#EEE;
    color:#666;
}
.status-buka .titik{
    width:9px;
    height:9px;
    border-radius:50%;
    background:#999;
}
.status-buka.buka{ background:#E7F5E9; color:#216B35; }
.status-buka.buka .titik{
    background:#2E9E4F;
    animation:denyut 1.6s ease infinite;
}
.status-buka.tutup{ background:#FDE8E8; color:#A61B25; }
.status-buka.tutup .titik{ background:#C1121F; }

@keyframes denyut{
    0%{ box-shadow:0 0 0 0 rgba(46,158,79,.55); }
    70%{ box-shadow:0 0 0 8px rgba(46,158,79,0); }
    100%{ box-shadow:0 0 0 0 rgba(46,158,79,0); }
}

.contact-item{
    display:flex;
    align-items:center;
    gap:14px;
    padding:12px;
    margin:0 -12px 6px;
    border-radius:14px;
    transition:background .2s ease;
}
.contact-item:hover{ background:var(--krem-terang); }

.contact-icon{
    width:44px;
    height:44px;
    border-radius:12px;
    background:var(--coklat);
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:17px;
    flex-shrink:0;
    transition:transform .25s ease;
}
.contact-item:hover .contact-icon{ transform:rotate(-8deg) scale(1.08); }

.contact-info{ flex:1; min-width:0; }
.contact-info strong{
    display:block;
    color:var(--coklat-tua);
    font-size:14px;
    margin-bottom:3px;
}
.contact-info a,
.contact-info p{
    color:var(--coklat-muda);
    font-size:14px;
    line-height:1.5;
    text-decoration:none;
    overflow-wrap:anywhere;
}
.contact-info a:hover{ color:var(--coklat); text-decoration:underline; }

.salin{
    border:none;
    background:var(--krem);
    color:var(--coklat);
    width:36px;
    height:36px;
    border-radius:50%;
    cursor:pointer;
    flex-shrink:0;
    transition:background .2s ease, transform .2s ease;
}
.salin:hover{ background:var(--coklat); color:white; transform:scale(1.1); }
.salin.ok{ background:#2E9E4F; color:white; }

.social-card{
    display:flex;
    align-items:center;
    gap:16px;
    padding:16px 18px;
    margin-bottom:14px;
    border:1.5px solid #E2CBB4;
    border-radius:16px;
    background:#FFFCF8;
    text-decoration:none;
    transition:all .25s ease;
}
.social-card:hover{
    transform:translateY(-4px);
    border-color:var(--coklat);
    background:var(--coklat);
    box-shadow:0 12px 24px rgba(91,46,15,.25);
}
.social-card i.merek{
    font-size:30px;
    color:var(--coklat);
    width:38px;
    text-align:center;
    transition:color .25s ease;
}
.social-card div{ flex:1; }
.social-card strong{
    display:block;
    color:var(--coklat-tua);
    font-size:15px;
    margin-bottom:2px;
    transition:color .25s ease;
}
.social-card span{
    color:var(--coklat-muda);
    font-size:14px;
    transition:color .25s ease;
}
.social-card i.panah{
    color:var(--emas);
    transition:transform .25s ease, color .25s ease;
}
.social-card:hover i.merek,
.social-card:hover strong,
.social-card:hover span,
.social-card:hover i.panah{ color:var(--krem-terang); }
.social-card:hover i.panah{ transform:translateX(5px); }

/* ===== FAQ ===== */
.faq{ margin-bottom:34px; }
.faq-item{
    background:white;
    border-radius:16px;
    margin-bottom:12px;
    box-shadow:0 6px 18px rgba(91,46,15,.08);
    overflow:hidden;
    transition:box-shadow .25s ease;
}
.faq-item:hover{ box-shadow:0 12px 24px rgba(91,46,15,.14); }

.faq-q{
    width:100%;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:16px;
    padding:18px 22px;
    border:none;
    background:transparent;
    cursor:pointer;
    text-align:left;
    font-size:15px;
    font-weight:bold;
    color:var(--coklat-tua);
}
.faq-q .plus{
    width:30px;
    height:30px;
    border-radius:50%;
    background:var(--krem);
    color:var(--coklat);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:12px;
    flex-shrink:0;
    transition:transform .3s ease, background .3s ease, color .3s ease;
}
.faq-item.buka .plus{
    transform:rotate(45deg);
    background:var(--coklat);
    color:white;
}
.faq-a{
    max-height:0;
    overflow:hidden;
    transition:max-height .35s ease;
}
.faq-a p{
    padding:0 22px 20px;
    color:var(--coklat-muda);
    font-size:14px;
    line-height:1.7;
}

/* ===== QUOTE ===== */
.quote-box{
    position:relative;
    overflow:hidden;
    background:linear-gradient(120deg, var(--coklat) 0%, #7A3E13 100%);
    border-radius:22px;
    padding:34px 40px;
    display:flex;
    align-items:center;
    gap:24px;
    color:var(--krem-terang);
    box-shadow:0 14px 30px rgba(91,46,15,.25);
}
.quote-box .kutip{
    font-size:46px;
    color:rgba(255,243,232,.35);
    flex-shrink:0;
}
.quote-box .kutip-bg{
    position:absolute;
    right:30px;
    top:50%;
    transform:translateY(-50%) rotate(-10deg);
    font-size:120px;
    color:rgba(255,243,232,.07);
    pointer-events:none;
}
.quote-content{ position:relative; }
.quote-content p{
    font-size:24px;
    font-family:Georgia, serif;
    font-style:italic;
    margin-bottom:10px;
    line-height:1.4;
}
.quote-content span{
    font-size:15px;
    color:#E6CFB4;
}

/* ===== TOAST ===== */
.toast{
    position:fixed;
    bottom:28px;
    left:50%;
    transform:translate(-50%, 30px);
    background:var(--coklat-tua);
    color:var(--krem-terang);
    padding:12px 22px;
    border-radius:999px;
    font-size:14px;
    font-weight:bold;
    box-shadow:0 12px 26px rgba(0,0,0,.3);
    opacity:0;
    pointer-events:none;
    transition:opacity .3s ease, transform .3s ease;
    z-index:50;
}
.toast.tampil{
    opacity:1;
    transform:translate(-50%, 0);
}

/* ===== RESPONSIF ===== */
@media(max-width:1000px){
    .hero{ flex-direction:column; align-items:flex-start; }
    .hero-img{ width:100%; }
    .hero-img img{ height:220px; transform:none; }
    .hero .deco{ display:none; }
    .contact-grid{ grid-template-columns:1fr; }
}
@media(max-width:700px){
    .content{ padding:18px; }
    .hero{ padding:26px; }
    .hero-text h1{ font-size:28px; }
    .stats{ grid-template-columns:1fr; }
    .quote-box{ padding:26px; flex-direction:column; align-items:flex-start; }
    .quote-content p{ font-size:19px; }
}
</style>
</head>

<body>

<div class="wrapper">

    <nav class="sidebar">

        <div class="sidebar-brand">
            <div class="logo-ikon">
                <i class="fa-solid fa-book-open-reader"></i>
            </div>
            Mille Pagine
        </div>

        <div class="sidebar-menu">
            <a class="nav-item" href="index.php">
                <span class="ikon">
                    <i class="fa-solid fa-house"></i>
                </span>
                Beranda
            </a>

            <a class="nav-item" href="keranjang.php">
                <span class="ikon">
                    <i class="fa-solid fa-cart-shopping"></i>
                </span>
                Keranjang
            </a>

            <a class="nav-item active" href="tentang.php">
                <span class="ikon">
                    <i class="fa-solid fa-circle-info"></i>
                </span>
                Tentang Kita
            </a>

            <a class="nav-item" href="profil.php">
                <span class="ikon">
                    <i class="fa-solid fa-user"></i>
                </span>
                Profil
            </a>
        </div>

        <div class="sidebar-bawah">
            <a href="chat.php" class="bantuan">
                <div class="ikon-besar">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <strong>Butuh bantuan?</strong>
                <span>Hubungi Admin</span>
            </a>
        </div>

    </nav>

    <!-- CONTENT -->
    <div class="content">

        <!-- HERO -->
        <div class="hero anim">
            <i class="fa-solid fa-book-open deco"></i>

            <div class="hero-text">
                <span class="hero-tag"><i class="fa-solid fa-heart"></i> Toko buku online</span>
                <h1>Tentang Kita</h1>
                <p>
                    Bookstore adalah toko buku online yang menyediakan berbagai pilihan buku berkualitas untuk semua kalangan.
                    Kami percaya bahwa setiap buku memiliki kekuatan untuk membuka wawasan, menginspirasi, dan menemani
                    setiap perjalanan belajar maupun hiburan.
                </p>
                <p>
                    Kami berkomitmen memberikan pengalaman berbelanja buku yang mudah, aman, dan nyaman melalui layanan
                    yang sederhana serta koleksi buku yang terus diperbarui.
                </p>

                <div class="hero-btns">
                    <a href="index.php" class="btn btn-krem">
                        <i class="fa-solid fa-book-open"></i> Lihat Koleksi
                    </a>
                    <a href="chat.php" class="btn btn-garis">
                        <i class="fa-solid fa-headset"></i> Hubungi Admin
                    </a>
                </div>
            </div>

            <div class="hero-img">
                <img src="library.jpg" alt="Tentang Kita">
            </div>
        </div>

        <!-- STATISTIK -->
        <div class="stats">

            <div class="stat reveal" style="--d:0">
                <div class="stat-icon"><i class="fa-solid fa-book"></i></div>
                <div class="angka" data-target="<?= $total_judul; ?>">0</div>
                <div class="label">Judul Buku</div>
            </div>

            <div class="stat reveal" style="--d:1">
                <div class="stat-icon"><i class="fa-solid fa-layer-group"></i></div>
                <div class="angka" data-target="<?= $total_kategori; ?>">0</div>
                <div class="label">Kategori</div>
            </div>

            <div class="stat reveal" style="--d:2">
                <div class="stat-icon"><i class="fa-solid fa-bag-shopping"></i></div>
                <div class="angka" data-target="<?= $total_terjual; ?>">0</div>
                <div class="label">Buku Terjual</div>
            </div>

        </div>

        <!-- KONTAK -->
        <div class="section-head reveal">
            <div class="icon"><i class="fa-solid fa-address-book"></i></div>
            <h2>Hubungi Kami</h2>
        </div>

        <div class="contact-grid">

            <div class="panel reveal" style="--d:0">
                <div class="panel-title">
                    <h3>Kontak</h3>
                    <span class="status-buka" id="statusBuka">
                        <span class="titik"></span> <span id="statusTeks">Memeriksa...</span>
                    </span>
                </div>

                <div class="contact-item">
                    <div class="contact-icon"><i class="fa-brands fa-whatsapp"></i></div>
                    <div class="contact-info">
                        <strong>Telpon/Whatsapp</strong>
                        <a href="https://wa.me/6281234567890" target="_blank" rel="noopener">0812-3456-7890</a>
                    </div>
                    <button type="button" class="salin" data-salin="0812-3456-7890" title="Salin nomor">
                        <i class="fa-regular fa-copy"></i>
                    </button>
                </div>

                <div class="contact-item">
                    <div class="contact-icon"><i class="fa-solid fa-envelope"></i></div>
                    <div class="contact-info">
                        <strong>Email</strong>
                        <a href="mailto:bookstore@gmail.com">bookstore@gmail.com</a>
                    </div>
                    <button type="button" class="salin" data-salin="bookstore@gmail.com" title="Salin email">
                        <i class="fa-regular fa-copy"></i>
                    </button>
                </div>

                <div class="contact-item">
                    <div class="contact-icon"><i class="fa-solid fa-clock"></i></div>
                    <div class="contact-info">
                        <strong>Jam Operasional</strong>
                        <p>Senin – Minggu<br>08.00 – 21.00 WIB</p>
                    </div>
                </div>
            </div>

            <div class="panel reveal" style="--d:1">
                <div class="panel-title">
                    <h3>Ikuti Kami</h3>
                </div>

                <a href="https://instagram.com/bookstore.id" target="_blank" rel="noopener" class="social-card">
                    <i class="fa-brands fa-instagram merek"></i>
                    <div>
                        <strong>Instagram</strong>
                        <span>@bookstore.id</span>
                    </div>
                    <i class="fa-solid fa-arrow-right panah"></i>
                </a>

                <a href="https://tiktok.com/@bookstore.id" target="_blank" rel="noopener" class="social-card">
                    <i class="fa-brands fa-tiktok merek"></i>
                    <div>
                        <strong>Tiktok</strong>
                        <span>@bookstore.id</span>
                    </div>
                    <i class="fa-solid fa-arrow-right panah"></i>
                </a>
            </div>

        </div>

        <!-- FAQ -->
        <div class="section-head reveal">
            <div class="icon"><i class="fa-solid fa-circle-question"></i></div>
            <h2>Pertanyaan Umum</h2>
        </div>

        <div class="faq">

            <div class="faq-item reveal" style="--d:0">
                <button type="button" class="faq-q">
                    Apakah harus punya akun untuk melihat-lihat buku?
                    <span class="plus"><i class="fa-solid fa-plus"></i></span>
                </button>
                <div class="faq-a">
                    <p>Tidak. Kamu bisa menjelajahi semua buku dan kategori tanpa akun. Akun baru dibutuhkan saat memasukkan buku ke keranjang dan memesan.</p>
                </div>
            </div>

            <div class="faq-item reveal" style="--d:1">
                <button type="button" class="faq-q">
                    Metode pembayaran apa saja yang tersedia?
                    <span class="plus"><i class="fa-solid fa-plus"></i></span>
                </button>
                <div class="faq-a">
                    <p>Saat ini tersedia pembayaran lewat QRIS dan Tunai. Pilih metodenya di halaman keranjang sebelum menekan tombol pesan.</p>
                </div>
            </div>

            <div class="faq-item reveal" style="--d:2">
                <button type="button" class="faq-q">
                    Apakah ada biaya ongkir?
                    <span class="plus"><i class="fa-solid fa-plus"></i></span>
                </button>
                <div class="faq-a">
                    <p>Ongkir gratis. Total yang kamu bayar sama dengan total harga buku di ringkasan belanja.</p>
                </div>
            </div>

            <div class="faq-item reveal" style="--d:3">
                <button type="button" class="faq-q">
                    Bagaimana cara melihat status pesanan?
                    <span class="plus"><i class="fa-solid fa-plus"></i></span>
                </button>
                <div class="faq-a">
                    <p>Buka menu Profil. Di sana ada jumlah pesanan yang sedang diproses, dikirim, dan selesai, lengkap dengan detail tiap bukunya.</p>
                </div>
            </div>

            <div class="faq-item reveal" style="--d:4">
                <button type="button" class="faq-q">
                    Ada kendala atau pertanyaan lain?
                    <span class="plus"><i class="fa-solid fa-plus"></i></span>
                </button>
                <div class="faq-a">
                    <p>Hubungi admin lewat menu <strong>Hubungi Admin</strong> di sidebar, atau lewat WhatsApp dan email di atas pada jam operasional.</p>
                </div>
            </div>

        </div>

        <!-- QUOTE -->
        <div class="quote-box reveal">
            <i class="fa-solid fa-quote-left kutip-bg"></i>
            <i class="fa-solid fa-quote-left kutip"></i>

            <div class="quote-content">
                <p>“A book is a dream that you hold in your hands.”</p>
                <span>— Neil Gaiman</span>
            </div>
        </div>

    </div>

</div>

<div class="toast" id="toast"></div>

<script>
/* ===== muncul saat di-scroll ===== */
var wadah = document.querySelector('.content');
var elReveal = document.querySelectorAll('.reveal');

if('IntersectionObserver' in window){
    var pengamat = new IntersectionObserver(function(daftar){
        daftar.forEach(function(e){
            if(e.isIntersecting){
                e.target.classList.add('tampil');
                var angka = e.target.querySelector('.angka');
                if(angka && !angka.dataset.selesai){ hitungNaik(angka); }
                pengamat.unobserve(e.target);
            }
        });
    }, { root: wadah, threshold: 0.15 });

    elReveal.forEach(function(el){ pengamat.observe(el); });
}else{
    elReveal.forEach(function(el){
        el.classList.add('tampil');
        var a = el.querySelector('.angka');
        if(a){ a.textContent = a.dataset.target; }
    });
}

/* ===== angka hitung naik ===== */
function hitungNaik(el){
    el.dataset.selesai = '1';
    var target = parseInt(el.dataset.target, 10) || 0;
    var durasi = 1200;
    var mulai  = null;

    function langkah(t){
        if(mulai === null){ mulai = t; }
        var p = Math.min((t - mulai) / durasi, 1);
        var halus = 1 - Math.pow(1 - p, 3);
        el.textContent = Math.round(target * halus).toLocaleString('id-ID');
        if(p < 1){ requestAnimationFrame(langkah); }
    }
    requestAnimationFrame(langkah);
}

/* ===== status buka / tutup (WIB) ===== */
function cekBuka(){
    var wib = new Date(new Date().toLocaleString('en-US', { timeZone: 'Asia/Jakarta' }));
    var menit = wib.getHours() * 60 + wib.getMinutes();
    var buka  = menit >= 8 * 60 && menit < 21 * 60;

    var kotak = document.getElementById('statusBuka');
    var teks  = document.getElementById('statusTeks');

    kotak.classList.remove('buka', 'tutup');
    kotak.classList.add(buka ? 'buka' : 'tutup');
    teks.textContent = buka ? 'Sedang buka' : 'Sedang tutup';
}
cekBuka();
setInterval(cekBuka, 60000);

/* ===== salin ke clipboard ===== */
var toast = document.getElementById('toast');
var timerToast;

function tampilToast(pesan){
    toast.textContent = pesan;
    toast.classList.add('tampil');
    clearTimeout(timerToast);
    timerToast = setTimeout(function(){ toast.classList.remove('tampil'); }, 2000);
}

function salinTeks(teks){
    if(navigator.clipboard && window.isSecureContext){
        return navigator.clipboard.writeText(teks);
    }
    return new Promise(function(ok, gagal){
        var t = document.createElement('textarea');
        t.value = teks;
        t.style.position = 'fixed';
        t.style.opacity = '0';
        document.body.appendChild(t);
        t.select();
        try{ document.execCommand('copy') ? ok() : gagal(); }
        catch(e){ gagal(e); }
        document.body.removeChild(t);
    });
}

document.querySelectorAll('.salin').forEach(function(tombol){
    tombol.addEventListener('click', function(){
        salinTeks(tombol.dataset.salin).then(function(){
            tombol.classList.add('ok');
            tombol.innerHTML = '<i class="fa-solid fa-check"></i>';
            tampilToast('Berhasil disalin: ' + tombol.dataset.salin);
            setTimeout(function(){
                tombol.classList.remove('ok');
                tombol.innerHTML = '<i class="fa-regular fa-copy"></i>';
            }, 1800);
        }).catch(function(){
            tampilToast('Gagal menyalin, salin manual ya');
        });
    });
});

/* ===== FAQ accordion ===== */
document.querySelectorAll('.faq-q').forEach(function(tombol){
    tombol.addEventListener('click', function(){
        var item = tombol.parentElement;
        var isi  = item.querySelector('.faq-a');
        var sudahBuka = item.classList.contains('buka');

        // tutup yang lain
        document.querySelectorAll('.faq-item.buka').forEach(function(lain){
            lain.classList.remove('buka');
            lain.querySelector('.faq-a').style.maxHeight = null;
        });

        if(!sudahBuka){
            item.classList.add('buka');
            isi.style.maxHeight = isi.scrollHeight + 'px';
        }
    });
});
</script>

</body>
</html>