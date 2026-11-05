<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

$user_id = $_GET['user_id'];

$pesanan_user = mysqli_fetch_assoc(mysqli_query($conn, "
        SELECT pesanan.*, users.nama 
        FROM pesanan 
        JOIN users ON pesanan.user_id = users.id 
        WHERE pesanan.user_id = '$user_id'
"));

if(!$pesanan_user){
    header("Location: pesanan_user.php");
    exit;
}

$items = mysqli_query($conn, "
    SELECT detail_pesanan.*, buku.judul, buku.penulis, buku.gambar, pesanan.status, pesanan.status_pembayaran, pesanan.alasan_pembatalan, pesanan.id AS pesanan_id
    FROM detail_pesanan 
    JOIN buku ON detail_pesanan.buku_id = buku.id
    JOIN pesanan ON detail_pesanan.pesanan_id = pesanan.id
    WHERE pesanan.user_id = '$user_id'
    ORDER BY pesanan.id DESC
");

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detail Pesanan - Mille Pagine</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="wrapper">

    <?php $halaman_aktif = 'user'; include 'admin_sidebar.php'; ?>

    <div class="content">

        <div class="page-head anim" style="--i:0">
            <i class="fa-solid fa-receipt deco"></i>
            <i class="fa-solid fa-feather-pointed deco2"></i>
            <div class="page-head-main">
                <a href="admin detail.php?id=<?= (int)$user_id; ?>" class="back" title="Kembali"><i class="fa-solid fa-arrow-left"></i></a>
                <div>
                    <h1><?= htmlspecialchars($pesanan_user['nama']); ?></h1>
                    <p>Riwayat pesanan lengkap user ini.</p>
                </div>
            </div>
            <div class="page-head-actions">

            </div>
        </div>

        <?php
        $total_semua = 0;
        $no = 0;
        while($item = mysqli_fetch_assoc($items)):
            $no++;
            $subtotal = $item['harga'] * $item['jumlah'];
            $total_semua += $subtotal;
            $status_class = strtolower(str_replace(' ', '-', $item['status']));
        ?>

        <div class="order-card anim" style="--i:<?= min($no, 6); ?>">

            <div class="order-top">
                <div class="book-left">
                    <div class="cover-mini">
                        <img src="<?= htmlspecialchars($item['gambar']); ?>" alt="<?= htmlspecialchars($item['judul']); ?>">
                    </div>
                    <div class="book-info">
                        <h3><?= htmlspecialchars($item['judul']); ?></h3>
                        <p><?= htmlspecialchars($item['penulis']); ?></p>
                        <span class="badge">Pesanan #BK<?= str_pad($item['pesanan_id'],3,'0',STR_PAD_LEFT); ?></span>
                    </div>
                </div>

                <div class="status-box">
                    <span class="status <?= htmlspecialchars($status_class); ?>"><?= htmlspecialchars($item['status']); ?></span>
                    <small><?= htmlspecialchars($item['status_pembayaran'] ?? 'Belum Bayar'); ?></small>
                    <?php if (!empty($item['alasan_pembatalan'])): ?>
                        <small>Alasan: <?= htmlspecialchars($item['alasan_pembatalan']); ?></small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="line"></div>

            <div class="summary">
                <div>Jumlah</div>
                <div><?= (int)$item['jumlah']; ?></div>

                <div>Harga</div>
                <div>Rp <?= number_format($item['harga'],0,',','.'); ?></div>

                <div class="total">Subtotal</div>
                <div class="total">Rp <?= number_format($subtotal,0,',','.'); ?></div>
            </div>

        </div>

        <?php endwhile; ?>

        <div class="order-card anim">
            <div class="summary">
                <div>Total Belanja</div>
                <div>Rp <?= number_format($total_semua,0,',','.'); ?></div>

                <div>Ongkir</div>
                <div>Rp 15.000</div>

                <div class="line" style="grid-column:1/3;"></div>

                <div class="total">Total Pesanan</div>
                <div class="total">Rp <?= number_format($total_semua + 15000,0,',','.'); ?></div>
            </div>
        </div>

    </div>

</div>

<script src="admin.js"></script>
</body>
</html>
