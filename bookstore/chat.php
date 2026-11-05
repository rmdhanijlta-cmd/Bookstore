<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'user'){
    header("Location: landing.php");
    exit;
}

$user_id = $_SESSION['id'];

/* =========================
   KIRIM PESAN
   ========================= */
if(isset($_POST['kirim'])){
    $pesan = trim($_POST['pesan']);

    if($pesan != ''){
        mysqli_query($conn, "
            INSERT INTO chat (user_id, pengirim, pesan, status)
            VALUES ('$user_id', 'user', '$pesan', 'belum')
        ");

        header("Location: chat.php");
        exit;
    }
}

/* =========================
   AMBIL PESAN
   ========================= */
$chat = mysqli_query($conn, "
    SELECT *
    FROM chat
    WHERE user_id='$user_id'
    ORDER BY waktu ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hubungi Admin - Mille Pagine</title>

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
    overflow:hidden;  
    margin:0; 
}

.wrapper{ 
    display:flex; 
    height:100vh; 
}

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
    background:var(--latar);
    padding:32px 48px;
    display:flex;
    flex-direction:column;
    height:100vh;
    overflow:hidden;
}

.header{
    display:flex;
    align-items:center;
    gap:18px;
    margin-bottom:24px;
}

.back{
    width:42px;
    height:42px;
    background:white;
    border:1.5px solid #E2CBB4;
    display:flex;
    align-items:center;
    justify-content:center;
    color:var(--coklat-tua);
    text-decoration:none;
    font-size:16px;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(91,46,15,.06);
    transition:all .2s ease;
}
.back:hover{
    background:var(--coklat);
    color:white;
    border-color:var(--coklat);
    transform:translateX(-3px);
}

.header h1{
    font-size:32px;
    font-family:Georgia, serif;
    color:var(--coklat-tua);
}

/* =========================
   CHAT BOX
   ========================= */

.chat-box{
    flex:1;
    background:white;
    border:1.5px solid #E2CBB4;
    border-radius:20px;
    display:flex;
    flex-direction:column;
    overflow:hidden;
    box-shadow:0 6px 18px rgba(91,46,15,.06);
    margin-bottom:20px;
}

/* PESAN */

.messages{
    flex:1;
    padding:24px;
    overflow-y:auto;
    display:flex;
    flex-direction:column;
    gap:16px;
}

.message{
    max-width:65%;
    padding:12px 18px;
    border-radius:16px;
    font-size:14px;
    line-height:1.4;
    box-shadow:0 2px 6px rgba(0,0,0,0.04);
}

.user-message{
    align-self:flex-end;
    background:var(--coklat);
    color:white;
    border-bottom-right-radius:4px;
}

.admin-message{
    align-self:flex-start;
    background:var(--krem-terang);
    color:var(--coklat-tua);
    border:1px solid #EFE3D4;
    border-bottom-left-radius:4px;
}

.time{
    font-size:11px;
    margin-top:6px;
    opacity:.7;
    text-align:right;
}

/* INPUT */

.chat-form{
    display:flex;
    gap:12px;
    padding:16px 20px;
    border-top:1.5px solid #E2CBB4;
    background:var(--krem-terang);
}

.chat-form input{
    flex:1;
    height:46px;
    border:1.5px solid #E2CBB4;
    border-radius:12px;
    padding:0 18px;
    font-size:14px;
    outline:none;
    background:white;
    color:var(--coklat-tua);
    transition:border-color .2s ease;
}
.chat-form input:focus{
    border-color:var(--emas);
}

.send-btn{
    width:46px;
    height:46px;
    border:none;
    border-radius:12px;
    background:var(--coklat);
    color:white;
    font-size:16px;
    cursor:pointer;
    display:flex;
    align-items:center;
    justify-content:center;
    transition:background .2s ease, transform .2s ease;
}
.send-btn:hover{
    background:#6D3815;
    transform:scale(1.05);
}
.send-btn:active{ transform:scale(0.95); }

@media(max-width:700px){
    .content{ padding:16px; }
    .sidebar{ width:70px; padding:16px 8px; }
    .sidebar a span{ display:none; }
    .sidebar a{ justify-content:center; padding:12px; }
    .sidebar a.active::before{ display:none; }
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

            <a class="nav-item" href="tentang.php">
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

        <div class="header">
            <a href="index.php" class="back" title="Kembali">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <h1>Hubungi Admin</h1>
        </div>

        <div class="chat-box">

            <!-- PESAN -->
            <div class="messages">

                <?php if(mysqli_num_rows($chat) == 0): ?>
                    <div style="text-align:center; color:var(--coklat-muda); margin:auto;">
                        <i class="fa-solid fa-comments" style="font-size:42px; margin-bottom:12px; color:#D9BFA3;"></i>
                        <p style="font-weight:bold; margin-bottom:4px;">Belum ada percakapan.</p>
                        <p style="font-size:14px;">Silakan kirim pesan kepada admin.</p>
                    </div>
                <?php endif; ?>

                <?php while($c = mysqli_fetch_assoc($chat)): ?>
                    <div class="message <?= $c['pengirim'] == 'user' ? 'user-message' : 'admin-message'; ?>">
                        <div>
                            <?= htmlspecialchars($c['pesan']); ?>
                        </div>
                        <div class="time">
                            <?= date('H:i', strtotime($c['waktu'])); ?>
                        </div>
                    </div>
                <?php endwhile; ?>

            </div>

            <!-- FORM KIRIM -->
            <form method="POST" class="chat-form">
                <input type="text" name="pesan" placeholder="Ketik pesan..." autocomplete="off" required>
                <button type="submit" name="kirim" class="send-btn" title="Kirim Pesan">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>

        </div>

    </div>

</div>

</body>
</html>