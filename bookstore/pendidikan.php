<?php
session_start();
include 'koneksi.php';

$buku = mysqli_query($conn, "
    SELECT buku.*
    FROM buku
    JOIN kategori ON buku.kategori_id = kategori.id
    WHERE kategori.nama_kategori = 'Pendidikan'
    ORDER BY buku.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pendidikan - Mille Pagine</title>

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
    width:210px;
    background:linear-gradient(180deg, var(--coklat-tua) 0%, var(--coklat) 100%);
    padding:28px 16px;
    flex-shrink:0;
    display:flex;
    flex-direction:column;
    box-shadow:4px 0 20px rgba(0,0,0,0.08);
}

.sidebar-menu{
    display:flex;
    flex-direction:column;
    gap:6px;
    flex:1;
}

.sidebar a{
    position:relative;
    display:flex;
    align-items:center;
    gap:12px;
    color:rgba(255,255,255,0.75);
    text-decoration:none;
    font-size:14px;
    padding:12px 16px;
    border-radius:10px;
    transition:all .25s ease;
}

.sidebar a i{
    font-size:16px;
    width:20px;
    text-align:center;
    transition:transform .25s ease;
}

.sidebar a:hover{
    color:white;
    background:rgba(255,255,255,0.08);
    transform:translateX(4px);
}

.sidebar a:hover i{
    transform:scale(1.15);
    color:var(--emas);
}

.sidebar a.active{
    background:var(--krem-terang);
    color:var(--coklat-tua);
    font-weight:bold;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
}

.sidebar a.active i{
    color:var(--coklat);
}

.sidebar a.active::before{
    content:'';
    position:absolute;
    left:0;
    top:50%;
    transform:translateY(-50%);
    width:4px;
    height:60%;
    background:var(--emas);
    border-radius:0 4px 4px 0;
}

/* ===== CONTENT ===== */
.content{ 
    flex:1; 
    padding:32px 48px; 
    height:100vh; 
    overflow-y:auto; 
}

/* HEADER PENDIDIKAN */
.novel-header{ 
    display:flex; 
    align-items:center; 
    justify-content:center; 
    position:relative; 
    margin-bottom:32px; 
}

.back-btn{
    position:absolute;
    left:0;
    top:50%;
    transform:translateY(-50%);
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
.back-btn:hover{
    background:var(--coklat);
    color:white;
    border-color:var(--coklat);
    transform:translateY(-50%) translateX(-3px);
}

.title-area{
    text-align:center;
}

.title-area h1{ 
    font-size:42px; 
    color:var(--coklat-tua); 
    font-family:Georgia, serif; 
    margin-bottom:6px; 
}

.ornament{
    width:180px;  
    height:auto; 
    display:block; 
    margin:0 auto;
}

/* BOOKS GRID (DIATUR TEPAT 3 KE SAMPING) */
.books{
    display:grid;
    grid-template-columns: repeat(3, minmax(220px, 1fr));
    gap:28px;
    max-width:900px;
    margin:0 auto;
}

.book{
    background:white;
    border-radius:18px;
    padding:14px;
    box-shadow:0 6px 18px rgba(91,46,15,.08);
    transition:transform .25s ease, box-shadow .25s ease;
    display:flex;
    flex-direction:column;
}

.book:hover{ 
    transform: translateY(-8px); 
    box-shadow: 0 18px 32px rgba(91,46,15,.2); 
}

.book .cover{
    display:flex;
    align-items:center;
    justify-content:center;
    background:linear-gradient(160deg, var(--krem) 0%, #EAD2B5 100%);
    border-radius:12px;
    padding:16px;
    min-height:220px;
    text-decoration:none;
}

.book img{
    display:block;
    width:auto;
    height:auto;
    max-width:100%;
    max-height:200px;
    object-fit:contain;
    filter:drop-shadow(0 10px 12px rgba(91,46,15,.35));
    transition:transform .35s ease;
}

.book:hover img{ 
    transform: scale(1.06) rotate(-1.5deg); 
}

.book-header{
    margin-top:12px;
}

.book-header h3{
    font-size:15px;
    color:var(--coklat-tua);
    line-height:1.3;
    margin:0;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

.author{
    font-size:13px;
    color:var(--coklat-muda);
    margin:4px 0 12px;
    white-space:nowrap;
    overflow:hidden;
    text-overflow:ellipsis;
}

.book-bawah{
    display:flex;
    align-items:center;
    justify-content:space-between;
    margin-top:auto;
    padding-top:8px;
}

.price{
    font-size:15px;
    font-weight:bold;
    color:var(--coklat);
    margin:0;
}

.cart{
    width:38px;
    height:38px;
    border-radius:50%;
    background:var(--coklat);
    color:white;
    text-decoration:none;
    display:flex;
    align-items:center;
    justify-content:center;
    transition:transform .2s ease, background .2s ease;
}

.cart:hover{ 
    transform: scale(1.12) rotate(-8deg); 
    background: #6D3815; 
}

.cart:active{ 
    transform: scale(.95); 
}

.cart i{
    font-size:15px;
}

/* RESPONSIF */
@media(max-width:900px){
    .books{ grid-template-columns: repeat(2, 1fr); }
}
@media(max-width:700px){
    .content{ padding:20px; }
    .books{ grid-template-columns: 1fr; }
    .sidebar{ width:70px; padding:16px 8px; }
    .sidebar a span{ display:none; }
    .sidebar a{ justify-content:center; padding:12px; }
    .sidebar a.active::before{ display:none; }
}
</style>
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

        <div class="novel-header">
            <a href="index.php" class="back-btn" title="Kembali">
                <i class="fa-solid fa-arrow-left"></i>
            </a>

            <div class="title-area">
                <h1>Pendidikan</h1>
                <img src="line.png" alt="ornamen" class="ornament">
            </div>
        </div>

        <div class="books">
            <?php while($row = mysqli_fetch_assoc($buku)): ?> 

                <div class="book"> 
                    <a href="detail_buku.php?id=<?= (int)$row['id']; ?>" class="cover">
                         <img src="<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['judul']); ?>">
                    </a> 
                     
                    <div class="book-header"> 
                        <h3><?= htmlspecialchars($row['judul']); ?></h3> 
                    </div> 
                    
                    <p class="author">Oleh <?= htmlspecialchars($row['penulis']); ?></p> 

                    <div class="book-bawah">
                        <p class="price">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></p>

                        <a href="tambah_keranjang.php?id=<?= (int)$row['id']; ?>" class="cart" title="Masukkan keranjang">  
                            <i class="fa-solid fa-cart-shopping"></i>
                        </a> 
                    </div>

                </div> 

            <?php endwhile; ?> 
        </div>

    </div>

</div>

</body>
</html>