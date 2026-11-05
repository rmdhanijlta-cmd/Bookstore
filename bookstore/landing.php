<?php
session_start();

// kalau sudah login, tidak perlu lihat landing lagi
if(isset($_SESSION['id'])){
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Selamat Datang - Mille Pagine</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

body{
    min-height:100vh;
    overflow:hidden;
}

/* BACKGROUND */
.bg{
    min-height:100vh;
    width:100%;
    background-image:
        linear-gradient(135deg, rgba(60,20,0,0.88), rgba(30,10,0,0.70)),
        url('library.jpg');
    background-size:cover;
    background-position:center;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:20px;
    position:relative;
}

/* ICON BUKU MELAYANG (dekorasi) */
.float{
    position:absolute;
    color:rgba(255,243,232,0.10);
    animation:melayang 7s ease-in-out infinite;
}
.float.a{ top:12%; left:10%; font-size:64px; }
.float.b{ bottom:14%; left:18%; font-size:42px; animation-delay:-2s; }
.float.c{ top:18%; right:12%; font-size:50px; animation-delay:-4s; }
.float.d{ bottom:12%; right:9%; font-size:72px; animation-delay:-1s; }

@keyframes melayang{
    0%,100%{ transform:translateY(0) rotate(-6deg); }
    50%{ transform:translateY(-18px) rotate(6deg); }
}

/* KARTU TENGAH */
.card{
    position:relative;
    width:100%;
    max-width:400px;
    padding:44px 34px 34px;
    text-align:center;
    border-radius:28px;
    background:rgba(255,243,232,0.10);
    border:1px solid rgba(255,243,232,0.25);
    backdrop-filter:blur(14px);
    -webkit-backdrop-filter:blur(14px);
    box-shadow:0 24px 60px rgba(0,0,0,0.45);
    animation:masuk .7s ease both;
}

@keyframes masuk{
    from{ opacity:0; transform:translateY(24px) scale(.97); }
    to{ opacity:1; transform:translateY(0) scale(1); }
}

/* LOGO */
.logo{
    width:76px;
    height:76px;
    margin:0 auto 18px;
    border-radius:50%;
    background:#F5E6D3;
    color:#5B2E0F;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:32px;
    box-shadow:0 8px 20px rgba(0,0,0,0.3);
}

.card h1{
    font-family:Georgia, serif;
    font-size:34px;
    color:#FFF3E8;
    margin-bottom:8px;
    letter-spacing:.5px;
}

.card p.tagline{
    color:rgba(255,243,232,0.75);
    font-size:15px;
    line-height:1.5;
    margin-bottom:30px;
}

/* TOMBOL */
.btn{
    display:flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    width:100%;
    padding:15px 20px;
    border-radius:16px;
    text-decoration:none;
    font-size:17px;
    font-weight:bold;
    transition:transform .2s ease, box-shadow .2s ease, background .2s ease;
}

.btn-utama{
    background:#F5E6D3;
    color:#4A2410;
    box-shadow:0 8px 20px rgba(0,0,0,0.25);
}
.btn-utama:hover{
    transform:translateY(-3px);
    box-shadow:0 12px 26px rgba(0,0,0,0.35);
}

.btn-outline{
    margin-top:14px;
    background:transparent;
    color:#FFF3E8;
    border:2px solid rgba(255,243,232,0.6);
}
.btn-outline:hover{
    background:rgba(255,243,232,0.15);
    transform:translateY(-3px);
}

/* GARIS PEMISAH */
.pemisah{
    display:flex;
    align-items:center;
    gap:12px;
    margin:24px 0 18px;
    color:rgba(255,243,232,0.55);
    font-size:13px;
}
.pemisah::before,
.pemisah::after{
    content:"";
    flex:1;
    height:1px;
    background:rgba(255,243,232,0.25);
}

/* LINK TAMU */
.tamu{
    display:inline-flex;
    align-items:center;
    gap:8px;
    color:#FFF3E8;
    text-decoration:none;
    font-size:15px;
    padding:8px 14px;
    border-radius:10px;
    transition:background .2s ease;
}
.tamu i{ transition:transform .2s ease; }
.tamu:hover{ background:rgba(255,243,232,0.12); }
.tamu:hover i{ transform:translateX(4px); }

@media(max-width:480px){
    .card{ padding:36px 24px 28px; }
    .card h1{ font-size:28px; }
    .float{ display:none; }
}
</style>
</head>

<body>

<div class="bg">

    <i class="fa-solid fa-book float a"></i>
    <i class="fa-solid fa-feather-pointed float b"></i>
    <i class="fa-solid fa-book-open float c"></i>
    <i class="fa-solid fa-bookmark float d"></i>

    <div class="card">

        <div class="logo">
            <i class="fa-solid fa-book-open-reader"></i>
        </div>

        <h1>Mille Pagine</h1>
        <p class="tagline">Temukan buku favoritmu,<br>dari novel sampai pendidikan.</p>

        <a href="login.php" class="btn btn-utama">
            <i class="fa-solid fa-right-to-bracket"></i> Masuk
        </a>

        <a href="register.php" class="btn btn-outline">
            <i class="fa-solid fa-user-plus"></i> Daftar Akun
        </a>

        <div class="pemisah">atau</div>

        <a href="index.php" class="tamu">
            Lihat-lihat dulu <i class="fa-solid fa-arrow-right"></i>
        </a>

    </div>

</div>

</body>
</html>