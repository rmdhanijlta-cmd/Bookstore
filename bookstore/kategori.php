<?php
session_name('MILLE_USER');
session_start();
include 'koneksi.php';
$kategori_id = filter_input(INPUT_GET, 'kategori', FILTER_VALIDATE_INT);
if (!$kategori_id || $kategori_id < 1) {
    header('Location: index.php');
    exit;
}
$stmt = mysqli_prepare($conn, 'SELECT id, nama_kategori FROM kategori WHERE id = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 'i', $kategori_id);
mysqli_stmt_execute($stmt);
$kategori = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$kategori) {
    header('Location: index.php');
    exit;
}
$stmt_buku = mysqli_prepare($conn, 'SELECT buku.* FROM buku WHERE kategori_id = ? ORDER BY id DESC');
mysqli_stmt_bind_param($stmt_buku, 'i', $kategori_id);
mysqli_stmt_execute($stmt_buku);
$buku = mysqli_stmt_get_result($stmt_buku);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($kategori['nama_kategori']); ?> - Mille Pagine</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<style>
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: Arial,sans-serif;
}
body {
    background: #F4F1ED;
    color: #4A2410;
}
.layout {
    display: flex;
    min-height: 100vh;
}
.sidebar {
    width: 210px;
    flex-shrink: 0;
    background: linear-gradient(180deg,#4A2410,#5B2E0F);
    padding: 28px 16px;
    display: flex;
    flex-direction: column;
}
.sidebar a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 14px;
    margin-bottom: 6px;
    border-radius: 10px;
    color: #fff;
    text-decoration: none;
    transition: all .25s;
}
.sidebar a:hover {
    background: rgba(255,255,255,.1);
    transform: translateX(4px);
}
.sidebar a.active {
    background: #FFF3E8;
    color: #4A2410;
    font-weight: bold;
}
.main {
    flex: 1;
    padding: 34px 42px;
}
.heading {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 28px;
}
.back {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #fff;
    color: #4A2410;
    display: grid;
    place-items: center;
    text-decoration: none;
}
.heading h1 {
    font: bold 36px Georgia,serif;
}
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill,minmax(180px,1fr));
    gap: 22px;
}
.book {
    background: #fff;
    border-radius: 16px;
    padding: 16px;
    text-decoration: none;
    color: #4A2410;
    box-shadow: 0 5px 18px #0000000c;
    transition: .2s;
}
.book:hover {
    transform: translateY(-4px);
}
.book img {
    width: 100%;
    height: 220px;
    object-fit: contain;
    margin-bottom: 12px;
}
.book h3 {
    font-size: 16px;
    margin-bottom: 6px;
}
.book p {
    color: #8B6749;
    font-size: 14px;
}
.empty {
    padding: 28px;
    background: #fff;
    border-radius: 14px;
    color: #76563B;
}
@media(max-width:700px) {
    .sidebar {
        width: 70px;
        padding: 18px 8px;
    }
    .sidebar a {
        justify-content: center;
        font-size: 0;
    }
    .sidebar a i {
        font-size: 18px;
    }
    .main {
        padding: 22px 16px;
    }
    .heading h1 {
        font-size: 27px;
    }
}
</style><link rel="stylesheet" href="simple.css">
</head><body><div class="layout">
<nav class="sidebar"><a href="index.php"><i class="fa-solid fa-house"></i> Beranda</a><a href="keranjang.php"><i class="fa-solid fa-cart-shopping"></i> Keranjang</a><a href="tentang.php"><i class="fa-solid fa-circle-info"></i> Tentang Kita</a><a href="profil.php"><i class="fa-solid fa-user"></i> Profil</a></nav>
<main class="main"><div class="heading"><a class="back" href="index.php"><i class="fa-solid fa-arrow-left"></i></a><h1><?= htmlspecialchars($kategori['nama_kategori']); ?></h1></div>
<?php if ($buku && mysqli_num_rows($buku)>0): ?><div class="grid"><?php while($item=mysqli_fetch_assoc($buku)): ?><a class="book" href="detail_buku.php?id=<?= (int)$item['id']; ?>"><img src="<?= htmlspecialchars($item['gambar'] ?? 'buku.png'); ?>" alt=""><h3><?= htmlspecialchars($item['judul']); ?></h3><p>Rp <?= number_format((float)$item['harga'],0,',','.'); ?></p></a><?php endwhile; ?></div><?php else: ?><div class="empty">Belum ada buku dalam kategori ini.</div><?php endif; ?>
</main></div></body></html>
