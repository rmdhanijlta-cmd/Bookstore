<?php
session_start();
include 'koneksi.php';

// Pastikan hanya admin yang bisa mengakses.
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: landing.php");
    exit;
}

$query = mysqli_query($conn, "
    SELECT pesanan.*, users.nama
    FROM pesanan
    JOIN users ON pesanan.user_id = users.id
    ORDER BY pesanan.id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pesanan User - Mille Pagine</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="wrapper">

    <?php $halaman_aktif = 'pesanan'; include 'admin_sidebar.php'; ?>

    <div class="content">

        <div class="page-head anim" style="--i:0">
            <i class="fa-solid fa-cart-shopping deco"></i>
            <i class="fa-solid fa-feather-pointed deco2"></i>
            <div class="page-head-main">
                <a href="admin-dashboard.php" class="back" title="Kembali"><i class="fa-solid fa-arrow-left"></i></a>
                <div>
                    <h1>Pesanan User</h1>
                    <p>Semua pesanan masuk, lengkap dengan status pembayaran dan pengiriman.</p>
                </div>
            </div>
            <div class="page-head-actions">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Cari nama atau ID pesanan..." data-filter="#daftar">
                </div>
                <span class="count-chip"><i class="fa-solid fa-receipt"></i> <?= mysqli_num_rows($query); ?> pesanan</span>
            </div>
        </div>

        <div id="daftar">

        <?php if(mysqli_num_rows($query) == 0): ?>
            <div class="kosong"><i class="fa-regular fa-folder-open"></i><p>Belum ada pesanan.</p></div>
        <?php endif; ?>

        <?php $no = 0; while($p = mysqli_fetch_assoc($query)): $no++; ?>

        <div class="order-card anim" style="--i:<?= min($no, 6); ?>"
             data-cari="<?= htmlspecialchars($p['nama'] . ' BK' . str_pad($p['id'],3,'0',STR_PAD_LEFT) . ' ' . $p['status']); ?>">

            <div class="order-head">
                <div class="user-info">
                    <h2><?= htmlspecialchars($p['nama']); ?></h2>
                    <p>
                        ID Pesanan #BK<?= str_pad($p['id'],3,'0',STR_PAD_LEFT); ?>
                        &bull; <?= date('d M Y H:i', strtotime($p['tanggal'])); ?>
                    </p>
                </div>

                <?php $status_class = strtolower(str_replace(' ', '-', $p['status'])); ?>
                <div class="status <?= htmlspecialchars($status_class); ?>">
                    <?= htmlspecialchars($p['status']); ?>
                </div>
            </div>

            <?php
            $pesanan_id = (int)$p['id'];

            $detail = mysqli_query($conn, "
                SELECT detail_pesanan.*, buku.judul
                FROM detail_pesanan
                JOIN buku ON detail_pesanan.buku_id = buku.id
                WHERE detail_pesanan.pesanan_id = $pesanan_id
            ");

            while($d = mysqli_fetch_assoc($detail)):
                $sub = $d['harga'] * $d['jumlah'];
            ?>

            <div class="book-item">
                <div class="book-name">
                    <?= htmlspecialchars($d['judul']); ?> &times; <?= (int)$d['jumlah']; ?>
                </div>
                <div class="book-price">Rp <?= number_format($sub,0,',','.'); ?></div>
            </div>

            <?php endwhile; ?>

            <div class="summary-box">

                <div class="summary-row">
                    <span>Pembayaran</span>
                    <span class="payment"><?= htmlspecialchars($p['metode_pembayaran']); ?></span>
                </div>

                <div class="summary-row">
                    <span>Status Pembayaran</span>
                    <span class="payment"><?= htmlspecialchars($p['status_pembayaran'] ?? 'Belum Bayar'); ?></span>
                </div>

                <div class="summary-row total">
                    <span>Total</span>
                    <span>Rp <?= number_format($p['total'],0,',','.'); ?></span>
                </div>

            </div>

            <div class="actions">

                <?php if (
                    $p['status_pembayaran'] === 'Lunas' &&
                    $p['status'] !== 'Dibatalkan'
                ): ?>

                    <?php if ($p['status'] === 'Diproses'): ?>

                        <form action="ubah_status.php" method="POST">
                            <input type="hidden" name="id" value="<?= $pesanan_id; ?>">
                            <input type="hidden" name="status" value="Dikirim">
                            <button type="submit" class="btn btn-send"><i class="fa-solid fa-truck-fast"></i> Dikirim</button>
                        </form>

                    <?php elseif ($p['status'] === 'Dikirim'): ?>

                        <form action="ubah_status.php" method="POST">
                            <input type="hidden" name="id" value="<?= $pesanan_id; ?>">
                            <input type="hidden" name="status" value="Selesai">
                            <button type="submit" class="btn btn-done"><i class="fa-solid fa-circle-check"></i> Selesai</button>
                        </form>

                    <?php elseif ($p['status'] === 'Selesai'): ?>

                        <span class="payment"><i class="fa-solid fa-circle-check" style="color:var(--hijau)"></i> Pesanan selesai</span>

                    <?php endif; ?>

                <?php elseif ($p['status'] === 'Dibatalkan'): ?>

                    <span class="payment"><i class="fa-solid fa-ban" style="color:var(--merah)"></i> Pesanan dibatalkan</span>

                <?php else: ?>

                    <span class="payment">Menunggu pembayaran atau verifikasi admin</span>
                    <?php if (in_array($p['status'], ['Menunggu Pembayaran', 'Menunggu Verifikasi'], true)): ?>
                        <a href="verifikasi_pembayaran.php" class="btn btn-info">Lihat Verifikasi</a>
                    <?php endif; ?>
                    <?php if (in_array($p['status'], ['Menunggu Pembayaran', 'Menunggu Verifikasi'], true) && ($p['status_pembayaran'] ?? '') !== 'Lunas'): ?>
                        <form action="ubah_status.php" method="POST" onsubmit="return confirm('Batalkan pesanan ini?');">
                            <input type="hidden" name="id" value="<?= $pesanan_id; ?>">
                            <input type="hidden" name="status" value="Dibatalkan">
                            <button type="submit" class="btn btn-reject">Batalkan Pesanan</button>
                        </form>
                    <?php endif; ?>

                <?php endif; ?>

            </div>

        </div>

        <?php endwhile; ?>

        </div>

    </div>

</div>

<script src="admin.js"></script>
</body>
</html>
