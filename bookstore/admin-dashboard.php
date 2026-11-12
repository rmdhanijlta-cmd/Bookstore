<?php
// Menampilkan ringkasan data dan aktivitas toko untuk admin.
session_name('MILLE_ADMIN');
session_start();
include 'koneksi.php';
$total_terjual = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT SUM(jumlah) as total
        FROM detail_pesanan
    ")
)['total'] ?? 0;

$total_penjualan = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT SUM(total) as total
        FROM pesanan
    ")
)['total'] ?? 0;

$popular = mysqli_query($conn, "
    SELECT 
        buku.id,
        buku.judul,
        buku.penulis,
        buku.gambar,
        buku.rating,
        SUM(detail_pesanan.jumlah) as terjual
    FROM detail_pesanan
    JOIN buku ON detail_pesanan.buku_id = buku.id
    GROUP BY buku.id
    ORDER BY terjual DESC
    LIMIT 3
");

$cari = trim($_GET['cari'] ?? '');
$kategori_aktif = isset($_GET['kategori']) ? (int) $_GET['kategori'] : 0;

if ($cari !== '' && $kategori_aktif > 0) {
    $stmt = mysqli_prepare($conn, "
        SELECT buku.* FROM buku
        WHERE buku.kategori_id = ?
          AND (buku.judul LIKE ? OR buku.penulis LIKE ?)
        ORDER BY buku.id DESC
    ");
    $keyword = "%" . $cari . "%";
    mysqli_stmt_bind_param($stmt, "iss", $kategori_aktif, $keyword, $keyword);
    mysqli_stmt_execute($stmt);
    $query = mysqli_stmt_get_result($stmt);
} elseif ($cari !== '') {
    $stmt = mysqli_prepare($conn, "
        SELECT * FROM buku
        WHERE judul LIKE ? OR penulis LIKE ?
        ORDER BY id DESC
    ");
    $keyword = "%" . $cari . "%";
    mysqli_stmt_bind_param($stmt, "ss", $keyword, $keyword);
    mysqli_stmt_execute($stmt);
    $query = mysqli_stmt_get_result($stmt);
} elseif ($kategori_aktif > 0) {
    $stmt = mysqli_prepare($conn, "
        SELECT * FROM buku
        WHERE kategori_id = ?
        ORDER BY id DESC
    ");
    mysqli_stmt_bind_param($stmt, "i", $kategori_aktif);
    mysqli_stmt_execute($stmt);
    $query = mysqli_stmt_get_result($stmt);
} else {
    $query = mysqli_query($conn, "SELECT * FROM buku ORDER BY id DESC LIMIT 4");
}


if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

function hitung_admin($conn, $sql){
    $r = @mysqli_query($conn, $sql);
    if(!$r){ return 0; }
    $row = mysqli_fetch_row($r);
    return (int)($row[0] ?? 0);
}
$total_user = hitung_admin($conn, "SELECT COUNT(*) FROM users WHERE role='user'");
$total_pesanan = hitung_admin($conn, "SELECT COUNT(*) FROM pesanan");
$total_menunggu = hitung_admin($conn, "
    SELECT COUNT(*) FROM pesanan p
    WHERE (COALESCE(NULLIF(p.metode_bayar,''), p.metode_pembayaran) = 'Transfer Bank'
           AND p.status_pembayaran = 'Menunggu Verifikasi'
           AND p.bukti_transfer IS NOT NULL AND p.bukti_transfer <> '')
       OR (COALESCE(NULLIF(p.metode_bayar,''), p.metode_pembayaran) = 'Tunai'
           AND p.status_pembayaran = 'Belum Bayar' AND p.status = 'Menunggu Pembayaran')
");
$nama_admin = $_SESSION['nama'] ?? 'Admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Admin - Mille Pagine</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="admin.css">
<link rel="stylesheet" href="admin-fix.css">
</head>
<body>

<div class="wrapper">

    <?php $halaman_aktif = 'beranda'; include 'admin_sidebar.php'; ?>

    <div class="content">


        <div class="page-head anim">
            <i class="fa-solid fa-book-open deco"></i>
            <i class="fa-solid fa-feather-pointed deco2"></i>
            <div class="page-head-main">
                <div>
                    <h1>Halo, <?= htmlspecialchars($nama_admin); ?>!</h1>
                    <p>Pantau penjualan, pesanan, dan koleksi buku Mille Pagine dari satu tempat.</p>
                </div>
            </div>
            <div class="page-head-actions">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <form method="GET">
                        <?php if($kategori_aktif > 0): ?><input type="hidden" name="kategori" value="<?= $kategori_aktif; ?>"><?php endif; ?>
                        <input type="text" name="cari" placeholder="Cari judul atau penulis..." value="<?= htmlspecialchars($cari); ?>">
                        <button type="submit">Cari</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="stats">
            <div class="stat-card anim" style="--i:1">
                <div class="stat-icon"><i class="fa-solid fa-bag-shopping"></i></div>
                <div class="stat-text">
                    <div class="label">Total Terjual</div>
                    <div class="value"><?= (int)$total_terjual; ?> <small>buku</small></div>
                </div>
            </div>
            <div class="stat-card anim" style="--i:2">
                <div class="stat-icon emas">Rp</div>
                <div class="stat-text">
                    <div class="label">Total Pendapatan</div>
                    <div class="value">Rp <?= number_format((float)$total_penjualan, 0, ',', '.'); ?></div>
                </div>
            </div>
            <div class="stat-card anim" style="--i:3">
                <div class="stat-icon"><i class="fa-solid fa-receipt"></i></div>
                <div class="stat-text">
                    <div class="label">Total Pesanan</div>
                    <div class="value"><?= $total_pesanan; ?> <small>pesanan</small></div>
                </div>
            </div>
        </div>

        <?php if(mysqli_num_rows($popular) > 0): ?>
        <div class="section-head anim" style="--i:3">
            <div class="icon"><i class="fa-solid fa-fire"></i></div>
            <h2>Buku Terlaris</h2>
        </div>

        <div class="top-books">
            <?php $rank = 1; while($row = mysqli_fetch_assoc($popular)): ?>
                <a href="detail_buku.php?id=<?= (int)$row['id']; ?>" class="top-book anim" style="--i:<?= $rank + 3; ?>">
                    <div class="rank"><?= $rank++; ?></div>
                    <img src="<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['judul']); ?>">
                    <div class="info">
                        <h3><?= htmlspecialchars($row['judul']); ?></h3>
                        <p class="penulis">by <?= htmlspecialchars($row['penulis']); ?></p>
                        
                        <div class="terjual">Terjual <strong><?= (int)$row['terjual']; ?></strong>x</div>
                    </div>
                </a>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

        <div class="section-head anim" style="--i:4">
            <div class="icon"><i class="fa-solid fa-layer-group"></i></div>
            <h2>Koleksi Buku</h2>
            <?php if($cari !== ''): ?><small>Hasil untuk "<?= htmlspecialchars($cari); ?>"</small><?php endif; ?>
        </div>

        <?php
            $kategori = mysqli_query($conn, "SELECT * FROM kategori ORDER BY id ASC");
            $qs_cari = $cari !== '' ? '&cari=' . urlencode($cari) : '';
        ?>
        <div class="categories">
            <a href="admin-dashboard.php<?= $cari !== '' ? '?cari=' . urlencode($cari) : ''; ?>"
               class="category-btn <?= $kategori_aktif === 0 ? 'selected' : ''; ?>">
                <i class="fa-solid fa-border-all"></i> Semua
            </a>
            <?php while($k = mysqli_fetch_assoc($kategori)): ?>
                <a href="admin-dashboard.php?kategori=<?= (int)$k['id']; ?><?= $qs_cari; ?>"
                   class="category-btn <?= $kategori_aktif === (int)$k['id'] ? 'selected' : ''; ?>">
                    <i class="fa-solid fa-book"></i> <?= htmlspecialchars($k['nama_kategori']); ?>
                </a>
            <?php endwhile; ?>
        </div>

        <div class="books">
            <?php if(mysqli_num_rows($query) == 0): ?>
                <div class="kosong" style="grid-column:1/-1">
                    <i class="fa-solid fa-book-open"></i>
                    <p>Buku yang dicari belum ketemu.</p>
                </div>
            <?php endif; ?>

            <?php $n = 0; while($row = mysqli_fetch_assoc($query)): $n++; ?>
                <div class="book anim" style="--i:<?= min($n, 8); ?>">
                    <a href="detail_buku.php?id=<?= (int)$row['id']; ?>" class="cover">
                        <img src="<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['judul']); ?>">
                    </a>
                    <h3><?= htmlspecialchars($row['judul']); ?></h3>
                    <p class="penulis">by <?= htmlspecialchars($row['penulis']); ?></p>
                    <div class="book-bawah">
                        <span class="price">Rp <?= number_format($row['harga'], 0, ',', '.'); ?></span>
                        <a href="edit_buku.php?id=<?= (int)$row['id']; ?>" class="icon-btn" title="Ubah buku">
                            <i class="fa-solid fa-pen"></i>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

    </div>

</div>

<script src="admin.js"></script>
</body>
</html>
