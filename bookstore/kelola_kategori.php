<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

$kategori = mysqli_query($conn, "
    SELECT * FROM kategori
    ORDER BY id ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Kategori - Mille Pagine</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="wrapper">

    <?php $halaman_aktif = 'kategori'; include 'admin_sidebar.php'; ?>

    <div class="content">

        <div class="page-head anim" style="--i:0">
            <i class="fa-solid fa-table-list deco"></i>
            <i class="fa-solid fa-feather-pointed deco2"></i>
            <div class="page-head-main">
                <a href="admin-dashboard.php" class="back" title="Kembali"><i class="fa-solid fa-arrow-left"></i></a>
                <div>
                    <h1>Kelola Kategori</h1>
                    <p>Atur kategori supaya buku mudah ditemukan pembaca.</p>
                </div>
            </div>
            <div class="page-head-actions">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Cari kategori..." data-filter="#daftar">
                </div>
                <a href="tambah kategori.php" class="btn btn-emas"><i class="fa-solid fa-plus"></i> Tambah Kategori</a>
            </div>
        </div>

        <div class="list" id="daftar">

        <?php $no = 0; while($k = mysqli_fetch_assoc($kategori)): $no++; ?>

            <div class="item anim" style="--i:<?= min($no, 8); ?>" data-cari="<?= htmlspecialchars($k['nama_kategori']); ?>">

                <div class="item-left">
                    <div class="kategori-ikon"><i class="fa-solid fa-layer-group"></i></div>
                    <h2 class="judul-kategori"><?= htmlspecialchars($k['nama_kategori']); ?></h2>
                </div>

                <div class="item-right">
                    <a href="edit kategori.php?id=<?= (int)$k['id']; ?>" class="btn btn-edit">
                        <i class="fa-solid fa-pen"></i> Ubah
                    </a>

                    <a href="hapus_kategori.php?id=<?= (int)$k['id']; ?>" class="btn btn-delete"
                       onclick="return confirm('Hapus kategori ini?')">
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
