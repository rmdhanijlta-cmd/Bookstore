<?php
// Menampilkan informasi lengkap buku yang dipilih.
session_name('MILLE_USER');
session_start();
include 'koneksi.php';

$buku_id = $_GET['id'];

$buku = mysqli_fetch_assoc(
     mysqli_query($conn, " SELECT buku.*, kategori.nama_kategori 
     FROM buku 
     JOIN kategori ON buku.kategori_id = kategori.id 
     WHERE buku.id = '$buku_id' "
     ) 
);

if(!$buku){
     header("Location: index.php"); 
     exit; 
}

// Tentukan tujuan tombol back
$back = 'index.php'; // fallback default

$ref = $_SERVER['HTTP_REFERER'] ?? '';
if($ref){
    $p = parse_url($ref);

    // hanya terima kalau dari website sendiri
    if(($p['host'] ?? '') === $_SERVER['HTTP_HOST']){
        $halaman = basename($p['path'] ?? '');

        // jangan balik ke halaman ini sendiri / login / keranjang / dll
        $tolak = [basename($_SERVER['PHP_SELF']), 'login.php', 'register.php', 'landing.php', 'keranjang.php'];

        if($halaman !== '' && !in_array($halaman, $tolak)){
            $back = $halaman . (isset($p['query']) ? '?' . $p['query'] : '');
        }
    }
}

if (isset($_POST['tambah_keranjang'])) {
    if (!isset($_SESSION['id'])) {
        header("Location: landing.php");
        exit;
    }

    $jumlah = filter_input(INPUT_POST, 'jumlah', FILTER_VALIDATE_INT);
    $user_id = (int)$_SESSION['id'];
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        mysqli_begin_transaction($conn);
        $lock = mysqli_prepare($conn, "SELECT stok FROM buku WHERE id = ? FOR UPDATE");
        mysqli_stmt_bind_param($lock, "i", $buku_id);
        mysqli_stmt_execute($lock);
        $stokRow = mysqli_fetch_assoc(mysqli_stmt_get_result($lock));
        if (!$stokRow || !$jumlah || $jumlah < 1 || $stokRow['stok'] < 1 || $jumlah > (int)$stokRow['stok']) {
            throw new Exception('Stok buku tidak mencukupi atau buku sedang habis.');
        }

        $cek = mysqli_prepare($conn, "SELECT jumlah FROM keranjang WHERE user_id = ? AND buku_id = ? FOR UPDATE");
        mysqli_stmt_bind_param($cek, "ii", $user_id, $buku_id);
        mysqli_stmt_execute($cek);
        $existing = mysqli_fetch_assoc(mysqli_stmt_get_result($cek));
        $jumlah_lama = $existing ? (int)$existing['jumlah'] : 0;
        if ($jumlah_lama + $jumlah > (int)$stokRow['stok']) {
            throw new Exception('Jumlah di keranjang melebihi stok yang tersedia.');
        }

        if ($existing) {
            $up = mysqli_prepare($conn, "UPDATE keranjang SET jumlah = jumlah + ? WHERE user_id = ? AND buku_id = ?");
            mysqli_stmt_bind_param($up, "iii", $jumlah, $user_id, $buku_id);
            mysqli_stmt_execute($up);
        } else {
            $ins = mysqli_prepare($conn, "INSERT INTO keranjang (user_id, buku_id, jumlah) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($ins, "iii", $user_id, $buku_id, $jumlah);
            mysqli_stmt_execute($ins);
        }
        mysqli_commit($conn);
        header("Location: keranjang.php");
        exit;
    } catch (Throwable $e) {
        mysqli_rollback($conn);
        $_SESSION['pesan_checkout'] = $e->getMessage();
        header("Location: detail_buku.php?id=" . (int)$buku_id);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($buku['judul']); ?> - Mille Pagine</title>

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
    width: 210px;
    background: linear-gradient(180deg, var(--coklat-tua) 0%, var(--coklat) 100%);
    padding: 28px 16px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    box-shadow: 4px 0 20px rgba(0,0,0,0.08);
}
.sidebar-menu {
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex: 1;
}
.sidebar a {
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
    color: rgba(255,255,255,0.75);
    text-decoration: none;
    font-size: 14px;
    padding: 12px 16px;
    border-radius: 10px;
    transition: all .25s ease;
}
.sidebar a i {
    font-size: 16px;
    width: 20px;
    text-align: center;
    transition: transform .25s ease;
}
.sidebar a:hover {
    color: white;
    background: rgba(255,255,255,0.08);
    transform: translateX(4px);
}
.sidebar a:hover i {
    transform: scale(1.15);
    color: var(--emas);
}
.sidebar a.active {
    background: var(--krem-terang);
    color: var(--coklat-tua);
    font-weight: bold;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.sidebar a.active i {
    color: var(--coklat);
}
/* Garis aksen aktif di pinggir kiri menu */
.sidebar a.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 60%;
    background: var(--emas);
    border-radius: 0 4px 4px 0;
}
/* ===== CONTENT ===== */
.content {
    flex: 1;
    background: var(--latar);
    padding: 32px 48px;
    overflow-y: auto;
}
/* BACK BUTTON */
.back-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 42px;
    height: 42px;
    background: white;
    border: 1.5px solid #E2CBB4;
    border-radius: 12px;
    color: var(--coklat-tua);
    text-decoration: none;
    font-size: 16px;
    margin-bottom: 28px;
    box-shadow: 0 4px 12px rgba(91,46,15,.06);
    transition: all .2s ease;
}
.back-btn:hover {
    background: var(--coklat);
    color: white;
    border-color: var(--coklat);
    transform: translateX(-3px);
}
/* DETAIL LAYOUT CONTAINER */
.detail-container {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 40px;
    background: white;
    padding: 36px;
    border-radius: 24px;
    box-shadow: 0 10px 30px rgba(91,46,15,.08);
}
/* BOOK IMAGE COVER */
.book-cover {
    display: flex;
    align-items: flex-start;
    justify-content: center;
    background: linear-gradient(160deg, var(--krem) 0%, #EAD2B5 100%);
    border-radius: 18px;
    padding: 24px;
}
.book-cover img {
    width: 100%;
    max-width: 220px;
    height: auto;
    border-radius: 10px;
    box-shadow: 0 12px 24px rgba(91,46,15,0.3);
    object-fit: contain;
}
/* BOOK DETAILS INFO */
.book-info {
    display: flex;
    flex-direction: column;
}
.tag-kategori {
    align-self: flex-start;
    background: var(--krem);
    color: var(--coklat);
    font-size: 12px;
    font-weight: bold;
    padding: 5px 12px;
    border-radius: 999px;
    margin-bottom: 12px;
}
.book-title h1 {
    font-size: 24px;
    color: var(--coklat-tua);
    font-family: Georgia, serif;
    line-height: 1.3;
    margin-bottom: 8px;
}
.book-penulis {
    font-size: 15px;
    color: var(--coklat-muda);
    margin-bottom: 20px;
}
/* SYNOPSIS */
.synopsis {
    color: #6A5A4A;
    font-size: 15px;
    line-height: 1.6;
    margin-bottom: 28px;
    padding-bottom: 20px;
    border-bottom: 1px solid #EFE3D4;
}
/* PRICE */
.price {
    font-size: 26px;
    font-weight: bold;
    color: var(--coklat);
    margin-bottom: 24px;
}
/* BOTTOM SECTION */
.bottom {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: auto;
}
.qty-box {
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.qty-label {
    font-size: 13px;
    font-weight: bold;
    color: var(--coklat-tua);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.qty {
    display: flex;
    border: 1.5px solid #E2CBB4;
    border-radius: 10px;
    overflow: hidden;
    background: var(--krem-terang);
}
.qty button {
    width: 38px;
    height: 38px;
    border: none;
    background: transparent;
    color: var(--coklat);
    font-size: 18px;
    font-weight: bold;
    cursor: pointer;
    transition: background .2s ease;
}
.qty button:hover {
    background: #E5D0BA;
}
.qty input {
    width: 42px;
    text-align: center;
    border: none;
    background: transparent;
    color: var(--coklat-tua);
    font-weight: bold;
    font-size: 15px;
    outline: none;
}
/* STOCK */
.stock {
    text-align: right;
}
.stock small {
    display: block;
    font-size: 11px;
    font-weight: bold;
    color: var(--coklat-muda);
    margin-bottom: 2px;
}
.stock strong {
    font-size: 18px;
    color: var(--coklat-tua);
}
/* BUTTON */
.cart-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: var(--coklat);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 14px 28px;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    box-shadow: 0 6px 16px rgba(91,46,15,0.2);
    transition: all .2s ease;
}
.cart-btn:hover {
    background: #6D3815;
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(91,46,15,0.3);
}
/* RESPONSIF */
@media(max-width:900px) {
    .detail-container{
        grid-template-columns: 1fr;
    padding: 24px;
    }
    .book-cover{ max-width: 100%;
    }
    .content{ padding: 20px;
    }
    @media(max-width: 700px){
        .sidebar{ width:70px;
    padding: 16px 8px;
    }
        .sidebar a span{ display: none;
    }
        .sidebar a{ justify-content: center;
    padding: 12px;
    }
        .sidebar a.active: :before{ display:none;
    }
    }
}
</style>
<link rel="stylesheet" href="simple.css">
</head>

<body>

<div class="wrapper">

    <!-- SIDEBAR -->
    <nav class="sidebar">
        <div class="sidebar-menu">
            <a href="index.php">
                <i class="fa-solid fa-house"></i> <span>Beranda</span>
            </a>

            <a href="keranjang.php">
                <i class="fa-solid fa-cart-shopping"></i> <span>Keranjang</span>
            </a>

            <a href="tentang.php">
                <i class="fa-solid fa-circle-info"></i> <span>Tentang Kita</span>
            </a>

            <a href="profil.php">
                <i class="fa-solid fa-user"></i> <span>Profil</span>
            </a>

            <a href="chat.php">
                <i class="fa-solid fa-comments"></i> <span>Hubungi Admin</span>
            </a>
        </div>
    </nav>

    <!-- CONTENT -->
    <div class="content">

        <a href="<?= htmlspecialchars($back); ?>" class="back-btn" title="Kembali">
            <i class="fa-solid fa-arrow-left"></i>
        </a>

        <div class="detail-container">
            
            <div class="book-cover">
                <img src="<?= htmlspecialchars($buku['gambar']); ?>" alt="<?= htmlspecialchars($buku['judul']); ?>">
            </div>

            <div class="book-info">
                <span class="tag-kategori"><?= htmlspecialchars($buku['nama_kategori']); ?></span>

                <div class="book-title">
                    <h1><?= htmlspecialchars($buku['judul']); ?></h1>
                </div>
                
                <div class="book-penulis">
                    Oleh <strong><?= htmlspecialchars($buku['penulis']); ?></strong>
                </div>

                <div class="synopsis">
                    <?= nl2br(htmlspecialchars($buku['deskripsi'])); ?>
                </div>

                <div class="price"> 
                    Rp <?= number_format($buku['harga'], 0, ',', '.'); ?> 
                </div>

                <form method="POST">
                    <div class="bottom">
                        <div class="qty-box">
                            <div class="qty-label">Jumlah</div>
                            <div class="qty">
                                <button type="button" onclick="kurang()">-</button>
                                <input type="text" id="jumlah" name="jumlah" value="1" readonly>
                                <button type="button" onclick="tambah()" <?= (int)$buku['stok'] <= 0 ? 'disabled' : ''; ?>>+</button>
                            </div>
                        </div>

                        <div class="stock">
                            <small>STOK TERSEDIA</small>
                            <strong><?= (int)$buku['stok']; ?></strong>
                        </div>

                        <?php if ((int)$buku['stok'] > 0): ?>
                            <button type="submit" name="tambah_keranjang" class="cart-btn">
                                <i class="fa-solid fa-cart-shopping"></i> Masukkan Keranjang
                            </button>
                        <?php else: ?>
                            <button type="button" class="cart-btn" disabled style="opacity:.55;cursor:not-allowed;">
                                <i class="fa-solid fa-ban"></i> Stok Habis
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

        </div>

    </div>

</div>

<script>
function tambah(){
    let input = document.getElementById('jumlah');
    let max = <?= (int)$buku['stok']; ?>;
    if (parseInt(input.value) < max) input.value = parseInt(input.value) + 1;
}

function kurang(){
    let input = document.getElementById('jumlah');
    if(parseInt(input.value) > 1){
        if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
    }
}
</script>

</body>
</html>