<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}
?>

<?php
$buku = mysqli_query($conn, "
    SELECT buku.*, kategori.nama_kategori
    FROM buku
    LEFT JOIN kategori ON buku.kategori_id = kategori.id
    ORDER BY buku.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Buku - Mille Pagine</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="wrapper">

    <?php $halaman_aktif = 'buku'; include 'admin_sidebar.php'; ?>

    <div class="content">

        <div class="page-head anim" style="--i:0">
            <i class="fa-solid fa-book-open deco"></i>
            <i class="fa-solid fa-feather-pointed deco2"></i>
            <div class="page-head-main">
                <a href="admin-dashboard.php" class="back" title="Kembali"><i class="fa-solid fa-arrow-left"></i></a>
                <div>
                    <h1>Kelola Buku</h1>
                    <p>Tambah, ubah, atau hapus koleksi buku di toko.</p>
                </div>
            </div>
            <div class="page-head-actions">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Cari judul, penulis, kategori..." data-filter="#daftar">
                </div>
                <a href="tambah_buku.php" class="btn btn-emas"><i class="fa-solid fa-plus"></i> Tambah Buku</a>
            </div>
        </div>

        <div class="list" id="daftar">

        <?php $no = 0; while($row = mysqli_fetch_assoc($buku)): $no++; ?>

            <div class="item anim" style="--i:<?= min($no, 8); ?>"
                 data-cari="<?= htmlspecialchars($row['judul'] . ' ' . $row['penulis'] . ' ' . $row['nama_kategori']); ?>">

                <div class="item-left">
                    <div class="cover-mini">
                        <img src="<?= htmlspecialchars($row['gambar']); ?>" alt="<?= htmlspecialchars($row['judul']); ?>">
                    </div>
                    <div>
                        <h3><?= htmlspecialchars($row['judul']); ?></h3>
                        <p><?= htmlspecialchars($row['penulis']); ?></p>
                        <span class="badge"><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($row['nama_kategori'] ?? 'Tanpa kategori'); ?></span>
                    </div>
                </div>

                <div class="item-right">
                    <div class="price-besar">Rp <?= number_format($row['harga'],0,',','.'); ?></div>

                    <a href="edit_buku.php?id=<?= (int)$row['id']; ?>" class="btn btn-edit">
                        <i class="fa-solid fa-pen"></i> Ubah
                    </a>

                    <a href="hapus_buku.php?id=<?= (int)$row['id']; ?>" class="btn btn-delete"
                       onclick="return confirm('Hapus buku ini?')">
                        <i class="fa-solid fa-trash"></i> Hapus
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
