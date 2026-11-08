<?php
// Menampilkan beranda dan daftar buku yang tersedia.
session_name('MILLE_USER');
session_start();
include 'koneksi.php';

$login     = isset($_SESSION['id']);
$nama_user = $login ? $_SESSION['nama'] : 'Pengunjung';

$cari      = trim($_GET['cari'] ?? '');
$cari_aman = mysqli_real_escape_string($conn, $cari);

/* =========================
   AMBIL 4 BUKU DARI SEMUA KATEGORI
   ========================= */
$buku_tampil = [];

$kategori = mysqli_query($conn, "SELECT id, nama_kategori FROM kategori ORDER BY id ASC");

while($kat = mysqli_fetch_assoc($kategori)){
    $id_kategori = (int)$kat['id'];

    $query_buku = mysqli_query($conn, "
        SELECT buku.*, kategori.nama_kategori
        FROM buku
        JOIN kategori ON buku.kategori_id = kategori.id
        WHERE buku.kategori_id = '$id_kategori'
        ORDER BY buku.id ASC
        LIMIT 1
    ");

    if($b = mysqli_fetch_assoc($query_buku)){
        $buku_tampil[] = $b;
    }
}

/* Kalau masih kurang dari 4, ambil buku lain */
if(count($buku_tampil) < 4){
    $query_tambahan = mysqli_query($conn, "
        SELECT buku.*, kategori.nama_kategori
        FROM buku
        JOIN kategori ON buku.kategori_id = kategori.id
        ORDER BY buku.id ASC
    ");

    while($b = mysqli_fetch_assoc($query_tambahan)){
        $sudah_ada = false;
        foreach($buku_tampil as $bt){
            if($bt['id'] == $b['id']){ $sudah_ada = true; break; }
        }
        if(!$sudah_ada){ $buku_tampil[] = $b; }
        if(count($buku_tampil) >= 4){ break; }
    }
}

/* POPULAR */
$popular_q = mysqli_query($conn, "
    SELECT
        buku.id,
        buku.judul,
        buku.penulis,
        buku.gambar,
        SUM(detail_pesanan.jumlah) as terjual
    FROM detail_pesanan
    JOIN buku ON detail_pesanan.buku_id = buku.id
    GROUP BY buku.id
    ORDER BY terjual DESC
    LIMIT 3
");
$popular = [];
while($p = mysqli_fetch_assoc($popular_q)){ $popular[] = $p; }

/* PENCARIAN */
if($cari !== ''){
    $buku_tampil = [];

    $query_cari = mysqli_query($conn, "
        SELECT buku.*, kategori.nama_kategori
        FROM buku
        JOIN kategori ON buku.kategori_id = kategori.id
        WHERE buku.judul LIKE '%$cari_aman%'
           OR buku.penulis LIKE '%$cari_aman%'
        ORDER BY buku.id ASC
    ");

    while($b = mysqli_fetch_assoc($query_cari)){
        $buku_tampil[] = $b;
    }
}

/* KATEGORI untuk chip */
$kategori_list = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mille Pagine</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
:root {
    --coklat: #5B2E0F;
    --coklat-tua: #4A2410;
    --coklat-muda: #7B5A3D;
    --krem: #F5E6D3;
    --krem-terang: #FFF3E8;
    --latar: #F4F1ED;
    --emas: #B98A55;
}
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}
body {
    background: var(--latar);
    height: 100vh;
    overflow: hidden;
}
.wrapper {
    width: 100%;
    height: 100vh;
    display: flex;
}
/* ===== SIDEBAR ===== */
.sidebar {
    width: 220px;
    background: linear-gradient(180deg, #5B2E0F 0%, #3F1E08 100%);
    padding: 22px 14px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
}
.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 4px 8px 20px;
    margin-bottom: 16px;
    border-bottom: 1px solid rgba(255,243,232,.15);
    color: var(--krem-terang);
    font-family: Georgia, serif;
    font-size: 20px;
    letter-spacing: .5px;
}
.sidebar-brand .logo-ikon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: var(--krem);
    color: var(--coklat);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    box-shadow: 0 6px 14px rgba(0,0,0,.3);
    flex-shrink: 0;
}
.sidebar-menu {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    color: rgba(255,243,232,.8);
    text-decoration: none;
    font-size: 14px;
    padding: 9px 10px;
    border-radius: 14px;
    transition: background .2s ease, transform .2s ease, color .2s ease;
}
.nav-item .ikon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: rgba(255,243,232,.08);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
    transition: background .2s ease, color .2s ease;
}
.nav-item:hover {
    background: rgba(255,243,232,.08);
    color: white;
    transform: translateX(4px);
}
.nav-item:hover .ikon {
    background: rgba(255,243,232,.18);
}
.nav-item.active {
    background: var(--krem-terang);
    color: var(--coklat);
    font-weight: bold;
    box-shadow: 0 8px 18px rgba(0,0,0,.25);
}
.nav-item.active .ikon {
    background: var(--coklat);
    color: white;
}
.sidebar-bawah {
    margin-top: auto;
}
.bantuan {
    display: block;
    text-align: center;
    text-decoration: none;
    color: var(--krem-terang);
    background: rgba(255,243,232,.08);
    border: 1px solid rgba(255,243,232,.18);
    border-radius: 18px;
    padding: 18px 14px;
    font-size: 13px;
    line-height: 1.4;
    transition: background .2s ease, transform .2s ease;
}
.bantuan:hover {
    background: rgba(255,243,232,.16);
    transform: translateY(-3px);
}
.bantuan .ikon-besar {
    width: 44px;
    height: 44px;
    margin: 0 auto 10px;
    border-radius: 50%;
    background: var(--emas);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}
.bantuan strong {
    display: block;
    font-size: 14px;
    margin-bottom: 2px;
}
.bantuan span {
    color: rgba(255,243,232,.7);
}
/* ===== CONTENT ===== */
.content {
    flex: 1;
    min-width: 0;
    padding: 24px 32px 40px;
    height: 100vh;
    overflow-y: auto;
}
/* animasi muncul */
@keyframes naik {
    from{ opacity: 0;
    transform: translateY(18px);
    }
    to{ opacity: 1;
    transform: translateY(0);
    }
}
.anim {
    animation: naik .55s ease both;
    animation-delay: calc(var(--i, 0) * 90ms);
}
/* ===== TOPBAR ===== */
.topbar {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 20px;
    margin-bottom: 22px;
}
.topbar-kanan {
    display: flex;
    align-items: center;
    gap: 14px;
    flex: 1;
    justify-content: flex-end;
}
.search-box {
    flex: 1;
    max-width: 420px;
    background: white;
    border: 1.5px solid #E2CBB4;
    border-radius: 999px;
    padding: 4px 6px 4px 18px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 4px 12px rgba(91,46,15,.08);
    transition: border-color .2s ease, box-shadow .2s ease;
}
.search-box:focus-within {
    border-color: var(--emas);
    box-shadow: 0 6px 18px rgba(91,46,15,.18);
}
.search-box i {
    color: var(--coklat-muda);
    font-size: 15px;
}
.search-box form {
    flex: 1;
    display: flex;
}
.search-box input {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    color: var(--coklat-tua);
    font-size: 15px;
    padding: 9px 0;
}
.search-box input::placeholder {
    color: #B59A83;
}
.search-box button {
    border: none;
    background: var(--coklat);
    color: white;
    border-radius: 999px;
    padding: 9px 18px;
    font-weight: bold;
    cursor: pointer;
    transition: background .2s ease;
}
.search-box button:hover {
    background: #6D3815;
}
.btn-masuk {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--coklat);
    color: white;
    text-decoration: none;
    font-size: 14px;
    font-weight: bold;
    padding: 11px 20px;
    border-radius: 999px;
    white-space: nowrap;
    box-shadow: 0 6px 14px rgba(91,46,15,.25);
    transition: transform .2s ease, background .2s ease;
}
.btn-masuk:hover {
    transform: translateY(-2px);
    background: #6D3815;
}
.chip-user {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--krem);
    color: var(--coklat-tua);
    font-size: 14px;
    font-weight: bold;
    padding: 9px 16px;
    border-radius: 999px;
    white-space: nowrap;
}
/* ===== HERO ===== */
.hero {
    position: relative;
    overflow: hidden;
    background: linear-gradient(120deg, var(--coklat) 0%, #7A3E13 100%);
    border-radius: 22px;
    padding: 30px 34px;
    margin-bottom: 30px;
    color: var(--krem-terang);
    box-shadow: 0 14px 30px rgba(91,46,15,.25);
}
.hero h1 {
    font-family: Georgia, serif;
    font-size: 28px;
    margin-bottom: 8px;
}
.hero p {
    font-size: 15px;
    color: rgba(255,243,232,.8);
    max-width: 460px;
    line-height: 1.5;
}
.hero .deco {
    position: absolute;
    right: 30px;
    top: 50%;
    transform: translateY(-50%) rotate(-10deg);
    font-size: 120px;
    color: rgba(255,243,232,.10);
}
.hero .deco2 {
    position: absolute;
    right: 150px;
    bottom: -20px;
    font-size: 70px;
    color: rgba(255,243,232,.07);
    transform: rotate(12deg);
}
/* ===== JUDUL SECTION ===== */
.section-head {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}
.section-head .icon {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: var(--coklat);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
}
.section-head h2 {
    font-size: 22px;
    color: var(--coklat-tua);
    font-family: Georgia, serif;
}
.section-head small {
    color: var(--coklat-muda);
    font-size: 14px;
}
/* ===== POPULAR ===== */
.top-books {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 18px;
    margin-bottom: 34px;
}
.top-book {
    position: relative;
    display: flex;
    gap: 16px;
    background: white;
    border-radius: 18px;
    padding: 16px;
    text-decoration: none;
    box-shadow: 0 6px 18px rgba(91,46,15,.08);
    transition: transform .25s ease, box-shadow .25s ease;
}
.top-book:hover {
    transform: translateY(-6px);
    box-shadow: 0 16px 30px rgba(91,46,15,.18);
}
.rank {
    position: absolute;
    top: -10px;
    left: -10px;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: var(--emas);
    color: white;
    border: 3px solid var(--latar);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}
.top-book img {
    width: auto;
    height: auto;
    max-width: 110px;
    max-height: 160px;
    object-fit: contain;
    border-radius: 8px;
    box-shadow: 0 8px 16px rgba(91,46,15,.3);
    flex-shrink: 0;
    align-self: flex-start;
}
.top-book .info {
    padding-top: 4px;
    min-width: 0;
}
.top-book h3 {
    font-size: 16px;
    color: var(--coklat-tua);
    margin-bottom: 6px;
    line-height: 1.3;
}
.top-book .penulis {
    color: var(--coklat-muda);
    font-size: 13px;
    margin-bottom: 10px;
}

.top-book .terjual {
    font-size: 13px;
    color: var(--coklat-muda);
}
.top-book .terjual strong {
    font-size: 18px;
    color: var(--coklat-tua);
}
/* ===== KATEGORI ===== */
.categories {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 34px;
}
.category-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: white;
    color: var(--coklat-tua);
    border: 1.5px solid #E2CBB4;
    text-decoration: none;
    font-size: 15px;
    font-weight: bold;
    padding: 12px 22px;
    border-radius: 999px;
    transition: all .25s ease;
}
.category-btn i {
    color: var(--emas);
    transition: color .25s ease;
}
.category-btn:hover {
    background: var(--coklat);
    color: white;
    border-color: var(--coklat);
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(91,46,15,.25);
}
.category-btn:hover i {
    color: var(--krem);
}
/* ===== BUKU ===== */
.books {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
    gap: 22px;
}
.book {
    background: white;
    border-radius: 18px;
    padding: 12px;
    box-shadow: 0 6px 18px rgba(91,46,15,.08);
    transition: transform .25s ease, box-shadow .25s ease;
    display: flex;
    flex-direction: column;
}
.book:hover {
    transform: translateY(-8px);
    box-shadow: 0 18px 32px rgba(91,46,15,.2);
}
.book .cover {
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(160deg, var(--krem) 0%, #EAD2B5 100%);
    border-radius: 12px;
    padding: 16px;
    min-height: 240px;
}
.book .cover img {
    display: block;
    width: auto;
    height: auto;
    max-width: 100%;
    max-height: 220px;
    object-fit: contain;
    filter: drop-shadow(0 10px 12px rgba(91,46,15,.35));
    transition: transform .35s ease;
}
.book:hover .cover img {
    transform: scale(1.06) rotate(-1.5deg);
}
.book .tag {
    align-self: flex-start;
    margin-top: 12px;
    background: var(--krem);
    color: var(--coklat);
    font-size: 11px;
    font-weight: bold;
    padding: 4px 10px;
    border-radius: 999px;
}
.book h3 {
    font-size: 15px;
    color: var(--coklat-tua);
    line-height: 1.3;
    margin: 8px 0 4px;
}
.book-bawah {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: auto;
    padding-top: 12px;
}
.price {
    font-size: 15px;
    font-weight: bold;
    color: var(--coklat);
}
.cart {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--coklat);
    color: white;
    text-decoration: none;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform .2s ease, background .2s ease;
}
.cart:hover {
    transform: scale(1.12) rotate(-8deg);
    background: #6D3815;
}
.cart:active {
    transform: scale(.95);
}
.cart i {
    font-size: 16px;
}
/* ===== KOSONG ===== */
.kosong {
    grid-column: 1 / -1;
    text-align: center;
    padding: 50px 20px;
    color: var(--coklat-muda);
}
.kosong i {
    font-size: 48px;
    color: #D9BFA3;
    margin-bottom: 14px;
}
.kosong a {
    color: var(--coklat);
    font-weight: bold;
}
/* ===== RESPONSIF ===== */
@media(max-width:1000px) {
    .top-books{ grid-template-columns: 1fr;
    }
    .hero .deco, .hero .deco2{ display: none;
    }
}
@media(max-width:700px) {
    .topbar {
        flex-direction: column;
        align-items: stretch;
    }
    .topbar-kanan {
        justify-content: space-between;
    }
    .content {
        padding: 18px;
    }
    .sidebar {
        width: 72px;
        padding: 16px 8px;
    }
    .sidebar-brand {
        justify-content: center;
        padding: 4px 0 16px;
        font-size: 0;
    }
    .nav-item {
        justify-content: center;
        padding: 8px 0;
        font-size: 0;
        gap: 0;
    }
    .nav-item:hover {
        transform: none;
    }
    .bantuan {
        padding: 12px 4px;
    }
    .bantuan strong, .bantuan span {
        display: none;
    }
    .bantuan .ikon-besar {
        margin: 0 auto;
    }
}
</style>
<link rel="stylesheet" href="simple.css">
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
            <a class="nav-item active" href="index.php">
                <span class="ikon"><i class="fa-solid fa-house"></i></span> Beranda
            </a>

            <a class="nav-item" href="keranjang.php">
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

    <div class="content">

        <!-- TOPBAR -->
        <div class="topbar anim">
            <div class="topbar-kanan">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <form method="GET">
                        <input type="text" name="cari" placeholder="Cari judul atau penulis..."
                               value="<?= htmlspecialchars($cari); ?>">
                        <button type="submit">Cari</button>
                    </form>
                </div>

                <?php if($login): ?>
                    <span class="chip-user">
                        <i class="fa-regular fa-user"></i> <?= htmlspecialchars($nama_user); ?>
                    </span>
                <?php else: ?>
                    <a href="landing.php" class="btn-masuk">
                        <i class="fa-solid fa-right-to-bracket"></i> Masuk/Daftar
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <?php if($cari === ''): ?>

        <!-- HERO -->
        <div class="hero anim" style="--i:1">
            <i class="fa-solid fa-book-open deco"></i>
            <i class="fa-solid fa-feather-pointed deco2"></i>
            <h1>Halo, <?= htmlspecialchars($nama_user); ?>!</h1>
            <p>Temukan bacaan favoritmu hari ini, dari novel seru sampai buku pelajaran.</p>
        </div>

        <!-- POPULAR -->
        <?php if(count($popular) > 0): ?>

        <div class="section-head anim" style="--i:2">
            <div class="icon"><i class="fa-solid fa-fire"></i></div>
            <h2>Popular Books</h2>
        </div>

        <div class="top-books">
            <?php $rank = 1; foreach($popular as $row): ?>
                <a href="detail_buku.php?id=<?= (int)$row['id']; ?>" class="top-book anim" style="--i:<?= $rank + 2; ?>">
                    <div class="rank"><?= $rank++; ?></div>

                    <img src="<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['judul']); ?>">

                    <div class="info">
                        <h3><?= htmlspecialchars($row['judul']); ?></h3>
                        <p class="penulis">by <?= htmlspecialchars($row['penulis']); ?></p>

                        <div class="terjual">Terjual <strong><?= (int)$row['terjual']; ?></strong>x</div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <?php endif; ?>

        <!-- KATEGORI -->
        <div class="section-head anim" style="--i:3">
            <div class="icon"><i class="fa-solid fa-layer-group"></i></div>
            <h2>Kategori</h2>
        </div>

        <div class="categories">
            <?php while($k = mysqli_fetch_assoc($kategori_list)): ?>

                <?php
                    if($k['nama_kategori'] == 'Novel'){
                        $link = 'novel.php';
                    }elseif($k['nama_kategori'] == 'Pendidikan'){
                        $link = 'pendidikan.php';
                    }elseif($k['nama_kategori'] == 'Anak-Anak'){
                        $link = 'anak-anak.php';
                    }else{
                        $link = 'kategori.php?kategori=' . (int)$k['id'];
                    }
                ?>

                <a href="<?= $link; ?>" class="category-btn">
                    <i class="fa-solid fa-book"></i>
                    <?= htmlspecialchars($k['nama_kategori']); ?>
                </a>

            <?php endwhile; ?>
        </div>

        <div class="section-head anim" style="--i:4">
            <div class="icon"><i class="fa-solid fa-star"></i></div>
            <h2>Pilihan untuk Kamu</h2>
        </div>

        <?php else: ?>

        <div class="section-head anim" style="--i:1">
            <div class="icon"><i class="fa-solid fa-magnifying-glass"></i></div>
            <h2>Hasil untuk "<?= htmlspecialchars($cari); ?>"</h2>
            <small><?= count($buku_tampil); ?> buku</small>
        </div>

        <?php endif; ?>

        <!-- DAFTAR BUKU -->
        <div class="books">

            <?php if(count($buku_tampil) == 0): ?>

                <div class="kosong">
                    <i class="fa-solid fa-book-open"></i>
                    <p>Buku yang kamu cari belum ketemu.<br>
                       Coba kata kunci lain, atau <a href="index.php">lihat semua buku</a>.</p>
                </div>

            <?php endif; ?>

            <?php $n = 0; foreach($buku_tampil as $buku): $n++; ?>

                <div class="book anim" style="--i:<?= $n; ?>">

                    <a href="detail_buku.php?id=<?= (int)$buku['id']; ?>" class="cover">
                        <img src="<?= htmlspecialchars($buku['gambar']); ?>"
                             alt="<?= htmlspecialchars($buku['judul']); ?>">
                    </a>

                    <span class="tag"><?= htmlspecialchars($buku['nama_kategori']); ?></span>

                    <h3><?= htmlspecialchars($buku['judul']); ?></h3>

                    <div class="book-bawah">
                        <span class="price">Rp <?= number_format($buku['harga'], 0, ',', '.'); ?></span>

                        <?php if($login): ?>
                            <a href="tambah_keranjang.php?id=<?= (int)$buku['id']; ?>" class="cart" title="Masukkan keranjang">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </a>
                        <?php else: ?>
                            <a href="detail_buku.php?id=<?= (int)$buku['id']; ?>" class="cart" title="Lihat detail">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </a>
                        <?php endif; ?>
                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </div>

</div>

</body>
</html>