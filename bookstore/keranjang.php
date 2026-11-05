<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['id'])){
    $_SESSION['redirect_after_login'] = 'keranjang.php';
    header("Location: landing.php");
    exit;
}

$user_id = (int)$_SESSION['id'];

$query = mysqli_query($conn, "
    SELECT keranjang.id AS keranjang_id,
           buku.judul,
           buku.penulis,
           buku.harga,
           buku.gambar,
           buku.stok,
           keranjang.jumlah
    FROM keranjang
    JOIN buku ON keranjang.buku_id = buku.id
    WHERE keranjang.user_id = '$user_id'
    ORDER BY keranjang.id DESC
");

$items = [];
$total = 0;
$jumlah_item = 0;

while($row = mysqli_fetch_assoc($query)){
    $row['subtotal'] = $row['harga'] * $row['jumlah'];
    $total += $row['subtotal'];
    $jumlah_item += $row['jumlah'];
    $items[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Keranjang - Mille Pagine</title>

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

/* ===== SIDEBAR ===== */
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
    color:var(--krem-terang);
    font-family:Georgia, serif;
    font-size:20px;
    letter-spacing:.5px;
}
.sidebar-brand .logo-ikon{
    width:40px;
    height:40px;
    border-radius:12px;
    background:var(--krem);
    color:var(--coklat);
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
.nav-item:hover .ikon{ background:rgba(255,243,232,.18); }

.nav-item.active{
    background:var(--krem-terang);
    color:var(--coklat);
    font-weight:bold;
    box-shadow:0 8px 18px rgba(0,0,0,.25);
}
.nav-item.active .ikon{
    background:var(--coklat);
    color:white;
}

.sidebar-bawah{ margin-top:auto; }

.bantuan{
    display:block;
    text-align:center;
    text-decoration:none;
    color:var(--krem-terang);
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
    background:var(--emas);
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
.bantuan span{ color:rgba(255,243,232,.7); }

/* ===== CONTENT ===== */
.content{
    flex:1;
    min-width:0;
    height:100vh;
    overflow-y:auto;
    padding:24px 32px 40px;
}

/* animasi muncul */
@keyframes naik{
    from{ opacity:0; transform:translateY(18px); }
    to{ opacity:1; transform:translateY(0); }
}
.anim{
    animation:naik .55s ease both;
    animation-delay:calc(var(--i, 0) * 90ms);
}

/* ===== HERO ===== */
.hero{
    position:relative;
    overflow:hidden;
    background:linear-gradient(120deg, var(--coklat) 0%, #7A3E13 100%);
    border-radius:22px;
    padding:28px 34px;
    margin-bottom:30px;
    color:var(--krem-terang);
    box-shadow:0 14px 30px rgba(91,46,15,.25);
    display:flex;
    align-items:center;
    gap:20px;
}
.hero .deco{
    position:absolute;
    right:30px;
    top:50%;
    transform:translateY(-50%) rotate(-10deg);
    font-size:120px;
    color:rgba(255,243,232,.10);
    pointer-events:none;
}
.hero .deco2{
    position:absolute;
    right:170px;
    bottom:-20px;
    font-size:70px;
    color:rgba(255,243,232,.07);
    transform:rotate(12deg);
    pointer-events:none;
}

.back-btn{
    position:relative;
    width:44px;
    height:44px;
    border-radius:50%;
    background:rgba(255,243,232,.15);
    border:1.5px solid rgba(255,243,232,.4);
    color:var(--krem-terang);
    display:flex;
    align-items:center;
    justify-content:center;
    text-decoration:none;
    font-size:17px;
    flex-shrink:0;
    transition:background .2s ease, transform .2s ease;
}
.back-btn:hover{
    background:rgba(255,243,232,.28);
    transform:translateX(-3px);
}

.hero-text{ position:relative; }
.hero-text h1{
    font-family:Georgia, serif;
    font-size:30px;
    margin-bottom:6px;
}
.hero-text p{
    font-size:15px;
    color:rgba(255,243,232,.8);
}

/* ===== LAYOUT ===== */
.cart-page{
    display:grid;
    grid-template-columns:minmax(0, 1fr) 340px;
    gap:26px;
    align-items:start;
}

/* ===== ITEM KERANJANG ===== */
.cart-item{
    display:flex;
    align-items:center;
    gap:18px;
    background:white;
    border-radius:18px;
    padding:16px 20px;
    margin-bottom:18px;
    box-shadow:0 6px 18px rgba(91,46,15,.08);
    transition:transform .25s ease, box-shadow .25s ease;
}
.cart-item:hover{
    transform:translateY(-4px);
    box-shadow:0 14px 26px rgba(91,46,15,.16);
}

.cover-box{
    width:100px;
    min-height:130px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:linear-gradient(160deg, var(--krem) 0%, #EAD2B5 100%);
    border-radius:12px;
    padding:8px;
    flex-shrink:0;
}
.cover-box img{
    display:block;
    width:auto;
    height:auto;
    max-width:100%;
    max-height:114px;
    object-fit:contain;
    filter:drop-shadow(0 6px 8px rgba(91,46,15,.35));
}

.info{ flex:1; min-width:0; }
.info h3{
    color:var(--coklat-tua);
    font-size:18px;
    line-height:1.3;
    margin-bottom:6px;
    overflow-wrap:anywhere;
}
.info .penulis{
    color:var(--coklat-muda);
    font-size:14px;
    margin-bottom:12px;
}
.stok{
    display:inline-flex;
    align-items:center;
    gap:6px;
    background:#E7F5E9;
    color:#216B35;
    font-size:12px;
    font-weight:bold;
    padding:4px 10px;
    border-radius:999px;
}
.stok i{ font-size:7px; }

.right{
    display:flex;
    flex-direction:column;
    align-items:flex-end;
    gap:12px;
    flex-shrink:0;
}

.price{
    color:var(--coklat-tua);
    font-weight:bold;
    font-size:18px;
}

.qty{
    display:flex;
    align-items:center;
    gap:8px;
    background:var(--krem-terang);
    border:1.5px solid #E2CBB4;
    border-radius:999px;
    padding:4px;
}
.qty a,
.qty .habis{
    width:32px;
    height:32px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    text-decoration:none;
    font-size:14px;
}
.qty a{
    background:var(--coklat);
    color:white;
    transition:background .2s ease, transform .2s ease;
}
.qty a:hover{ background:#6D3815; transform:scale(1.1); }
.qty .habis{
    background:#E2CBB4;
    color:white;
    cursor:not-allowed;
}
.qty span{
    min-width:26px;
    text-align:center;
    color:var(--coklat-tua);
    font-weight:bold;
    font-size:15px;
}

.delete{
    display:inline-flex;
    align-items:center;
    gap:6px;
    color:#C1121F;
    background:#FDE8E8;
    text-decoration:none;
    font-size:13px;
    font-weight:bold;
    padding:7px 14px;
    border-radius:999px;
    transition:background .2s ease, transform .2s ease;
}
.delete:hover{ background:#F9D2D2; transform:translateY(-2px); }

/* ===== RINGKASAN ===== */
.summary{
    position:sticky;
    top:0;
    background:white;
    border-radius:20px;
    padding:24px;
    box-shadow:0 6px 18px rgba(91,46,15,.08);
}

.summary-head{
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:18px;
}
.summary-head .icon{
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
.summary-head h2{
    font-size:20px;
    color:var(--coklat-tua);
    font-family:Georgia, serif;
}

.row{
    display:flex;
    justify-content:space-between;
    align-items:center;
    color:var(--coklat-muda);
    margin:14px 0;
    font-size:15px;
}
.row strong{ color:var(--coklat-tua); }
.free{
    color:#216B35;
    font-weight:bold;
    background:#E7F5E9;
    padding:3px 12px;
    border-radius:999px;
    font-size:13px;
}

.garis{
    border:none;
    border-top:1.5px dashed #E2CBB4;
    margin:16px 0;
}

.total{
    display:flex;
    justify-content:space-between;
    align-items:center;
    color:var(--coklat-tua);
    font-weight:bold;
    font-size:20px;
    margin:6px 0 20px;
}

.payment-title{
    color:var(--coklat-tua);
    font-weight:bold;
    font-size:14px;
    margin-bottom:10px;
}

.payment{
    display:flex;
    flex-direction:column;
    gap:10px;
    margin-bottom:18px;
}

.pay-option{
    position:relative;
    cursor:pointer;
}
.pay-option input{
    position:absolute;
    opacity:0;
    pointer-events:none;
}
.pay-card{
    display:flex;
    align-items:center;
    gap:12px;
    padding:12px 14px;
    border:1.5px solid #E2CBB4;
    border-radius:14px;
    background:#FFFCF8;
    color:var(--coklat-tua);
    font-weight:bold;
    font-size:14px;
    transition:all .2s ease;
}
.pay-card .pay-ikon{
    width:34px;
    height:34px;
    border-radius:10px;
    background:var(--krem);
    color:var(--coklat);
    display:flex;
    align-items:center;
    justify-content:center;
    transition:all .2s ease;
}
.pay-card .centang{
    margin-left:auto;
    width:22px;
    height:22px;
    border-radius:50%;
    border:2px solid #D8C2AB;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:11px;
    color:transparent;
    transition:all .2s ease;
}
.pay-option:hover .pay-card{ border-color:var(--emas); }
.pay-option input:checked + .pay-card{
    border-color:var(--coklat);
    background:var(--krem-terang);
    box-shadow:0 6px 14px rgba(91,46,15,.12);
}
.pay-option input:checked + .pay-card .pay-ikon{
    background:var(--coklat);
    color:white;
}
.pay-option input:checked + .pay-card .centang{
    background:var(--coklat);
    border-color:var(--coklat);
    color:white;
}

.qris-box{
    text-align:center;
    background:var(--krem-terang);
    border:1.5px dashed #D8C2AB;
    border-radius:14px;
    padding:16px;
    margin-bottom:18px;
}
.qris-box img{
    width:170px;
    max-width:100%;
    border-radius:10px;
    background:white;
    padding:6px;
}
.qris-box p{
    color:var(--coklat-muda);
    font-size:13px;
    margin-top:10px;
}

.order-btn{
    width:100%;
    background:var(--coklat);
    color:white;
    border:none;
    border-radius:999px;
    padding:15px;
    font-size:17px;
    font-weight:bold;
    cursor:pointer;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:10px;
    box-shadow:0 8px 18px rgba(91,46,15,.28);
    transition:transform .2s ease, background .2s ease;
}
.order-btn:hover{ transform:translateY(-3px); background:#6D3815; }
.order-btn:disabled{
    opacity:.5;
    cursor:not-allowed;
    transform:none;
    box-shadow:none;
}

/* ===== KOSONG ===== */
.kosong{
    background:white;
    border-radius:18px;
    text-align:center;
    padding:60px 20px;
    color:var(--coklat-muda);
    box-shadow:0 6px 18px rgba(91,46,15,.08);
}
.kosong i{
    font-size:56px;
    color:#D9BFA3;
    margin-bottom:16px;
    display:block;
}
.kosong p{ line-height:1.6; margin-bottom:20px; }
.btn-belanja{
    display:inline-flex;
    align-items:center;
    gap:8px;
    background:var(--coklat);
    color:white;
    text-decoration:none;
    font-weight:bold;
    font-size:14px;
    padding:12px 24px;
    border-radius:999px;
    box-shadow:0 6px 14px rgba(91,46,15,.25);
    transition:transform .2s ease, background .2s ease;
}
.btn-belanja:hover{ transform:translateY(-2px); background:#6D3815; }

/* ===== RESPONSIF ===== */
@media(max-width:1100px){
    .cart-page{ grid-template-columns:1fr; }
    .summary{ position:static; }
    .hero .deco, .hero .deco2{ display:none; }
}
@media(max-width:700px){
    .content{ padding:18px; }
    .sidebar{ width:72px; padding:16px 8px; }
    .sidebar-brand{ justify-content:center; padding:4px 0 16px; font-size:0; }
    .nav-item{ justify-content:center; padding:8px 0; font-size:0; gap:0; }
    .nav-item:hover{ transform:none; }
    .bantuan{ padding:12px 4px; }
    .bantuan strong, .bantuan span{ display:none; }
    .bantuan .ikon-besar{ margin:0 auto; }
    .hero{ padding:22px; }
    .hero-text h1{ font-size:24px; }
    .cart-item{ flex-wrap:wrap; }
    .right{ width:100%; flex-direction:row; justify-content:space-between; align-items:center; }
}
</style>
</head>

<body>

<div class="wrapper">

    <!-- SIDEBAR -->
    <nav class="sidebar">

        <div class="sidebar-brand">
            <div class="logo-ikon"><i class="fa-solid fa-book-open-reader"></i></div>
            Mille Pagine
        </div>

        <div class="sidebar-menu">
            <a class="nav-item" href="index.php">
                <span class="ikon"><i class="fa-solid fa-house"></i></span> Beranda
            </a>

            <a class="nav-item active" href="keranjang.php">
                <span class="ikon"><i class="fa-solid fa-cart-shopping"></i></span> Keranjang
            </a>

            <a class="nav-item" href="tentang.php">
                <span class="ikon"><i class="fa-solid fa-circle-info"></i></span> Tentang Kita
            </a>

            <a class="nav-item" href="profil.php">
                <span class="ikon"><i class="fa-solid fa-user"></i></span> Profil
            </a>
        </div>

        <div class="sidebar-bawah">
            <a href="chat.php" class="bantuan">
                <div class="ikon-besar"><i class="fa-solid fa-headset"></i></div>
                <strong>Butuh bantuan?</strong>
                <span>Hubungi Admin</span>
            </a>
        </div>

    </nav>

    <!-- CONTENT -->
    <div class="content">

        <!-- HERO -->
        <div class="hero anim">
            <i class="fa-solid fa-cart-shopping deco"></i>
            <i class="fa-solid fa-feather-pointed deco2"></i>

            <a href="index.php" class="back-btn" title="Kembali ke beranda">
                <i class="fa-solid fa-arrow-left"></i>
            </a>

            <div class="hero-text">
                <h1>Keranjang</h1>
                <p><?= count($items); ?> judul buku &bull; <?= $jumlah_item; ?> barang</p>
            </div>
        </div>

        <?php if(count($items) == 0): ?>

            <div class="kosong anim" style="--i:1">
                <i class="fa-solid fa-basket-shopping"></i>
                <p>Keranjang kamu masih kosong.<br>Yuk cari buku favoritmu dulu.</p>
                <a href="index.php" class="btn-belanja">
                    <i class="fa-solid fa-book-open"></i> Mulai Belanja
                </a>
            </div>

        <?php else: ?>

        <div class="cart-page">

            <!-- DAFTAR ITEM -->
            <div class="cart-left">

                <?php $n = 0; foreach($items as $row): $n++; ?>

                <div class="cart-item anim" style="--i:<?= min($n, 8); ?>">

                    <div class="cover-box">
                        <img src="<?= htmlspecialchars($row['gambar']); ?>"
                             alt="<?= htmlspecialchars($row['judul']); ?>">
                    </div>

                    <div class="info">
                        <h3><?= htmlspecialchars($row['judul']); ?></h3>
                        <p class="penulis"><?= htmlspecialchars($row['penulis']); ?></p>

                        <span class="stok">
                            <i class="fa-solid fa-circle"></i> Stok <?= (int)$row['stok']; ?>
                        </span>
                    </div>

                    <div class="right">
                        <div class="price">
                            Rp <?= number_format($row['subtotal'], 0, ',', '.'); ?>
                        </div>

                        <div class="qty">
                            <a href="kurang.php?id=<?= (int)$row['keranjang_id']; ?>" title="Kurangi">
                                <i class="fa-solid fa-minus"></i>
                            </a>

                            <span><?= (int)$row['jumlah']; ?></span>

                            <?php if($row['jumlah'] < $row['stok']): ?>
                                <a href="tambah.php?id=<?= (int)$row['keranjang_id']; ?>" title="Tambah">
                                    <i class="fa-solid fa-plus"></i>
                                </a>
                            <?php else: ?>
                                <div class="habis" title="Mencapai batas stok">
                                    <i class="fa-solid fa-plus"></i>
                                </div>
                            <?php endif; ?>
                        </div>

                        <a href="hapus_keranjang.php?id=<?= (int)$row['keranjang_id']; ?>" class="delete"
                           onclick="return confirm('Hapus buku ini dari keranjang?');">
                            <i class="fa-solid fa-trash"></i> Hapus
                        </a>
                    </div>

                </div>

                <?php endforeach; ?>

            </div>

            <!-- RINGKASAN -->
            <div class="summary anim" style="--i:2">

                <div class="summary-head">
                    <div class="icon"><i class="fa-solid fa-receipt"></i></div>
                    <h2>Ringkasan Belanja</h2>
                </div>

                <div class="row">
                    <span>Subtotal (<?= $jumlah_item; ?> barang)</span>
                    <strong>Rp <?= number_format($total, 0, ',', '.'); ?></strong>
                </div>

                <div class="row">
                    <span>Ongkir</span>
                    <span class="free">Gratis</span>
                </div>

                <hr class="garis">

                <div class="total">
                    <span>TOTAL</span>
                    <span>Rp <?= number_format($total, 0, ',', '.'); ?></span>
                </div>

                <form id="pesanForm" action="buat_pesanan.php" method="POST">


                    <div class="payment-title">Metode Pembayaran</div>

                    <div class="payment">
                        <label class="pay-option">
                            <input
                                type="radio"
                                name="metode"
                                value="Transfer Bank"
                                checked
                            >

                            <div class="pay-card">
                                <span class="pay-ikon">
                                    <i class="fa-solid fa-building-columns"></i>
                                </span>
                                Transfer Bank
                                <span class="centang">
                                    <i class="fa-solid fa-check"></i>
                                </span>
                            </div>
                        </label>

                        <label class="pay-option">
                            <input
                                type="radio"
                                name="metode"
                                value="Tunai"
                            >

                            <div class="pay-card">
                                <span class="pay-ikon">
                                    <i class="fa-solid fa-money-bill-wave"></i>
                                </span>
                                Tunai
                                <span class="centang">
                                    <i class="fa-solid fa-check"></i>
                                </span>
                            </div>
                        </label>
                    </div>


                    <button class="order-btn" type="submit">
                        <i class="fa-solid fa-bag-shopping"></i> Pesan Sekarang
                    </button>

                </form>

            </div>

        </div>

        <?php endif; ?>

    </div>

</div>

<script>
function toggleQR(){
    var qris = document.querySelector('input[name="metode"][value="QRIS"]');
    var box  = document.getElementById('qrisBox');
    if(qris && box){
        box.style.display = qris.checked ? 'block' : 'none';
    }
}
toggleQR();
</script>

</body>
</html>