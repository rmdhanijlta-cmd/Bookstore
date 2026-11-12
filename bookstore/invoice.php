
<?php
// Menampilkan ringkasan pesanan dan instruksi pembayaran.
session_name('MILLE_USER');
session_start();
include 'koneksi.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION['id'];
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id <= 0) {
    echo "Pesanan tidak valid.";
    exit;
}

/* AMBIL PESANAN MILIK USER YANG LOGIN */
$stmt = mysqli_prepare($conn, "
    SELECT *
    FROM pesanan
    WHERE id = ? AND user_id = ?
");

mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
mysqli_stmt_execute($stmt);

$pesanan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$pesanan) {
    echo "Pesanan tidak ditemukan.";
    exit;
}

/* AMBIL DETAIL PESANAN */
$stmt = mysqli_prepare($conn, "
    SELECT dp.*, b.judul, b.gambar
    FROM detail_pesanan dp
    JOIN buku b ON dp.buku_id = b.id
    WHERE dp.pesanan_id = ?
");

mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

$detail = mysqli_stmt_get_result($stmt);

$subtotal_pesanan = 0;
while ($baris_detail = mysqli_fetch_assoc($detail)) {
    $subtotal_pesanan += (int)$baris_detail['harga'] * (int)$baris_detail['jumlah'];
}
mysqli_data_seek($detail, 0);
$ongkir_pesanan = (int)($pesanan['ongkir'] ?? 0);

/* METODE PEMBAYARAN */
$metode = $pesanan['metode_bayar'] ?? '';
$status = $pesanan['status'] ?? '';

function e($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function rupiah($angka) {
    return number_format((float) $angka, 0, ',', '.');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice Mille Pagine</title>

<style>
body {
    background: #F4F1ED;
    font-family: Arial,sans-serif;
    padding: 40px;
}
.card {
    max-width: 700px;
    margin: auto;
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 6px 20px rgba(0,0,0,.1);
}
.header {
    background: #5B2E0F;
    color: white;
    text-align: center;
    padding: 24px;
}
.content {
    padding: 28px;
}
.row {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    margin: 10px 0;
}
.item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 0;
    border-bottom: 1px solid #eee;
}
.item img {
    width: 56px;
    height: 72px;
    object-fit: cover;
    border-radius: 8px;
}
.total {
    font-size: 22px;
    font-weight: bold;
    color: #5B2E0F;
}
.info-pembayaran {
    background: #FFF3E8;
    border: 1px solid #E8D3C0;
    border-radius: 12px;
    padding: 18px;
    margin-top: 24px;
    line-height: 1.7;
}
.info-pembayaran h3 {
    color: #5B2E0F;
    margin-top: 0;
}
.status {
    display: inline-block;
    background: #FFF3E8;
    color: #5B2E0F;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 13px;
}
.btn {
    display: block;
    text-align: center;
    margin: 28px auto 0;
    width: 220px;
    max-width: 100%;
    box-sizing: border-box;
    background: #5B2E0F;
    color: white;
    padding: 14px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: bold;
}
.btn:hover {
    background: #75421F;
}
@media(max-width:600px) {
    body {
        padding: 16px;
    }
    .content {
        padding: 20px;
    }
    .row {
        font-size: 14px;
    }
    .total {
        font-size: 18px;
    }
}
</style>
<link rel="stylesheet" href="simple.css">
</head>

<body>

<div class="card">

    <div class="header">
        <h1>MILLE PAGINE</h1>
        <p>Invoice Pesanan</p>
    </div>

    <div class="content">

        <div class="row">
            <span>No. Pesanan</span>
            <strong>#INV-<?= e($pesanan['id']); ?></strong>
        </div>

        <div class="row">
            <span>Tanggal</span>
            <strong>
                <?= !empty($pesanan['tanggal'])
                    ? date('d M Y', strtotime($pesanan['tanggal']))
                    : '-'; ?>
            </strong>
        </div>

        <div class="row">
            <span>Pembayaran</span>
            <strong><?= e($metode); ?></strong>
        </div>

        <div class="row">
            <span>Status Pesanan</span>
            <strong><?= e($status); ?></strong>
        </div>

        <hr>

        <?php while ($row = mysqli_fetch_assoc($detail)): ?>

        <div class="item">

            <img
                src="<?= e($row['gambar']); ?>"
                alt="<?= e($row['judul']); ?>"
            >

            <div style="flex:1;">
                <div style="font-weight:bold;">
                    <?= e($row['judul']); ?> × <?= (int) $row['jumlah']; ?>
                </div>
            </div>

            <div>
                Rp <?= rupiah($row['harga'] * $row['jumlah']); ?>
            </div>

        </div>

        <?php endwhile; ?>

        <div class="row" style="margin-top:22px;">
            <span>Subtotal</span>
            <strong>Rp <?= rupiah($subtotal_pesanan); ?></strong>
        </div>

        <div class="row">
            <span>Ongkir</span>
            <strong>Rp <?= rupiah($ongkir_pesanan); ?></strong>
        </div>

        <hr>

        <div class="row total">
            <span>TOTAL</span>
            <span>Rp <?= rupiah($pesanan['total']); ?></span>
        </div>

        <?php if ($metode === 'Transfer Bank'): ?>

            <div class="info-pembayaran">
                <h3>Informasi Pembayaran</h3>

                <p>
                    Silakan lakukan transfer ke rekening Mille Pagine,
                    kemudian unggah bukti transfer untuk diverifikasi admin.
                </p>

                <?php if (
                    $status === 'Menunggu Pembayaran' &&
                    ($pesanan['status_pembayaran'] ?? '') === 'Belum Bayar'
                ): ?>

                    <a
                        href="pembayaran.php?id=<?= (int) $pesanan['id']; ?>"
                        class="btn"
                        style="width:100%;margin-top:16px;"
                    >
                        Bayar Sekarang
                    </a>

                <?php elseif ($status === 'Menunggu Verifikasi'): ?>

                    <p><strong>Status:</strong> Menunggu verifikasi pembayaran oleh admin.</p>

                <?php elseif (($pesanan['status_pembayaran'] ?? '') === 'Lunas'): ?>

                    <p><strong>Status:</strong> Pembayaran sudah dikonfirmasi.</p>

                <?php elseif ($status === 'Dibatalkan'): ?>

                    <p><strong>Status:</strong> Pesanan dibatalkan.</p>

                <?php endif; ?>
            </div>

        <?php elseif ($metode === 'Tunai'): ?>

            <div class="info-pembayaran">
                <h3>Informasi Pembayaran Tunai</h3>

                <p>
                    Pembayaran dilakukan secara tunai saat mengambil
                    pesanan di Mille Pagine.
                </p>

                <?php if ($status === 'Dibatalkan'): ?>

                    <p><strong>Status:</strong> Pesanan dibatalkan.</p>

                <?php elseif (($pesanan['status_pembayaran'] ?? '') === 'Lunas'): ?>

                    <p><strong>Status:</strong> Pembayaran sudah dikonfirmasi.</p>

                <?php else: ?>

                    <p><strong>Status:</strong> Menunggu pembayaran saat pengambilan.</p>

                <?php endif; ?>
            </div>

        <?php endif; ?>

        <a href="index.php" class="btn">Kembali ke Beranda</a>

    </div>

</div>

</body>
</html>
