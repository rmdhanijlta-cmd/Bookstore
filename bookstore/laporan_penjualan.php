<?php
// Laporan penjualan admin: harian, mingguan, bulanan, tahunan.
session_name('MILLE_ADMIN');
session_start();
include 'koneksi.php';
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$periode = $_GET['periode'] ?? 'harian';
if (!in_array($periode, ['harian', 'mingguan', 'bulanan', 'tahunan'], true)) {
    $periode = 'harian';
}

// Pesanan yang dihitung: sudah lunas / selesai dan tidak dibatalkan.
$syarat = "(p.status_pembayaran = 'Lunas' OR p.status = 'Selesai') AND p.status <> 'Dibatalkan'";

// Rumus pengelompokan per periode.
switch ($periode) {
    case 'mingguan':
        $kunci = "YEARWEEK(p.tanggal, 1)";
        $label = "CONCAT('Minggu ', WEEK(p.tanggal, 1), ' - ', YEAR(p.tanggal), ' (', DATE_FORMAT(DATE_SUB(DATE(p.tanggal), INTERVAL WEEKDAY(p.tanggal) DAY), '%d/%m'), ')')";
        $batas = "DATE_SUB(CURDATE(), INTERVAL 12 WEEK)";
        break;
    case 'bulanan':
        $kunci = "DATE_FORMAT(p.tanggal, '%Y-%m')";
        $label = "DATE_FORMAT(p.tanggal, '%m/%Y')";
        $batas = "DATE_SUB(CURDATE(), INTERVAL 12 MONTH)";
        break;
    case 'tahunan':
        $kunci = "YEAR(p.tanggal)";
        $label = "YEAR(p.tanggal)";
        $batas = "DATE_SUB(CURDATE(), INTERVAL 10 YEAR)";
        break;
    default:
        $kunci = "DATE(p.tanggal)";
        $label = "DATE_FORMAT(p.tanggal, '%d/%m/%Y')";
        $batas = "DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
}

// Subquery per pesanan: pendapatan buku & modal dari detail_pesanan.
$sql = "
    SELECT
        $kunci AS kunci,
        $label AS label,
        COUNT(*) AS jumlah_pesanan,
        SUM(x.qty) AS buku_terjual,
        SUM(x.pendapatan) AS pendapatan,
        SUM(x.modal) AS modal,
        SUM(p.ongkir) AS ongkir
    FROM pesanan p
    JOIN (
        SELECT pesanan_id,
               SUM(jumlah) AS qty,
               SUM(jumlah * harga) AS pendapatan,
               SUM(jumlah * modal) AS modal
        FROM detail_pesanan
        GROUP BY pesanan_id
    ) x ON x.pesanan_id = p.id
    WHERE $syarat AND p.tanggal >= $batas
    GROUP BY kunci, label
    ORDER BY kunci DESC
";
$hasil = mysqli_query($conn, $sql);
$baris = [];
$tot = ['pesanan' => 0, 'buku' => 0, 'pendapatan' => 0, 'modal' => 0, 'ongkir' => 0];
while ($hasil && $r = mysqli_fetch_assoc($hasil)) {
    $r['untung'] = $r['pendapatan'] - $r['modal'];
    $baris[] = $r;
    $tot['pesanan'] += $r['jumlah_pesanan'];
    $tot['buku'] += $r['buku_terjual'];
    $tot['pendapatan'] += $r['pendapatan'];
    $tot['modal'] += $r['modal'];
    $tot['ongkir'] += $r['ongkir'];
}
$tot['untung'] = $tot['pendapatan'] - $tot['modal'];
$margin = $tot['pendapatan'] > 0 ? round($tot['untung'] / $tot['pendapatan'] * 100, 1) : 0;

// Buku terlaris pada rentang yang sama.
$terlaris = mysqli_query($conn, "
    SELECT b.judul, SUM(d.jumlah) AS qty, SUM(d.jumlah * (d.harga - d.modal)) AS untung
    FROM detail_pesanan d
    JOIN pesanan p ON d.pesanan_id = p.id
    JOIN buku b ON d.buku_id = b.id
    WHERE $syarat AND p.tanggal >= $batas
    GROUP BY b.id, b.judul
    ORDER BY qty DESC
    LIMIT 5
");

function rp($n) { return 'Rp ' . number_format((int)$n, 0, ',', '.'); }

$nama_periode = ['harian' => 'Harian (30 hari terakhir)', 'mingguan' => 'Mingguan (12 minggu terakhir)',
                 'bulanan' => 'Bulanan (12 bulan terakhir)', 'tahunan' => 'Tahunan (10 tahun terakhir)'];

function kartu($i, $ikon, $label, $nilai, $kecil = '') {
    echo '<div class="stat-card anim" style="--i:' . $i . '"><div class="stat-icon">' . $ikon . '</div>'
       . '<div class="stat-text"><div class="label">' . $label . '</div><div class="value">' . $nilai
       . ($kecil ? ' <small>' . $kecil . '</small>' : '') . '</div></div></div>';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan Penjualan - Mille Pagine</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="admin.css">
<link rel="stylesheet" href="admin-fix.css">
</head>
<body>
<div class="wrapper">
    <?php $halaman_aktif = 'laporan'; include 'admin_sidebar.php'; ?>

    <div class="content">
        <div class="page-head anim">
            <i class="fa-solid fa-chart-line deco"></i>
            <div class="page-head-main">
                <div>
                    <h1>Laporan Penjualan</h1>
                    <p><?= htmlspecialchars($nama_periode[$periode]); ?></p>
                </div>
            </div>
        </div>

        <div class="categories">
            <?php foreach (['harian'=>'Harian','mingguan'=>'Mingguan','bulanan'=>'Bulanan','tahunan'=>'Tahunan'] as $k=>$v): ?>
                <a href="?periode=<?= $k; ?>" class="category-button <?= $periode === $k ? 'selected' : ''; ?>"><?= $v; ?></a>
            <?php endforeach; ?>
        </div>

        <div class="stats">
            <?php
            kartu(1, 'Rp', 'Pendapatan (penjualan buku)', rp($tot['pendapatan']));
            kartu(2, 'Rp', 'Modal', rp($tot['modal']));
            kartu(3, 'Rp', 'Keuntungan Bersih', rp($tot['untung']), 'margin ' . $margin . '%');
            kartu(4, '<i class="fa-solid fa-receipt"></i>', 'Pesanan', (int)$tot['pesanan'], 'pesanan');
            kartu(5, '<i class="fa-solid fa-bag-shopping"></i>', 'Buku Terjual', (int)$tot['buku'], 'buku');
            kartu(6, '<i class="fa-solid fa-truck"></i>', 'Ongkir Diterima', rp($tot['ongkir']));
            ?>
        </div>

        <div class="section-head anim">
            <div class="icon"><i class="fa-solid fa-table"></i></div>
            <h2>Rincian <?= htmlspecialchars(ucfirst($periode)); ?></h2>
        </div>

        <table>
            <thead>
                <tr><th>Periode</th><th>Pesanan</th><th>Buku</th><th>Pendapatan</th><th>Modal</th><th>Keuntungan</th><th>Ongkir</th></tr>
            </thead>
            <tbody>
            <?php if (!$baris): ?>
                <tr><td colspan="7">Belum ada penjualan lunas pada periode ini.</td></tr>
            <?php endif; ?>
            <?php foreach ($baris as $r): ?>
                <tr>
                    <td><?= htmlspecialchars($r['label']); ?></td>
                    <td><?= (int)$r['jumlah_pesanan']; ?></td>
                    <td><?= (int)$r['buku_terjual']; ?></td>
                    <td><?= rp($r['pendapatan']); ?></td>
                    <td><?= rp($r['modal']); ?></td>
                    <td><strong><?= rp($r['untung']); ?></strong></td>
                    <td><?= rp($r['ongkir']); ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if ($baris): ?>
                <tr>
                    <td><strong>TOTAL</strong></td><td><strong><?= (int)$tot['pesanan']; ?></strong></td><td><strong><?= (int)$tot['buku']; ?></strong></td>
                    <td><strong><?= rp($tot['pendapatan']); ?></strong></td><td><strong><?= rp($tot['modal']); ?></strong></td>
                    <td><strong><?= rp($tot['untung']); ?></strong></td><td><strong><?= rp($tot['ongkir']); ?></strong></td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <div class="section-head anim" style="margin-top:28px">
            <div class="icon"><i class="fa-solid fa-fire"></i></div>
            <h2>5 Buku Terlaris</h2>
        </div>

        <table>
            <thead><tr><th>Judul</th><th>Terjual</th><th>Keuntungan</th></tr></thead>
            <tbody>
            <?php $ada = false; while ($terlaris && $t = mysqli_fetch_assoc($terlaris)): $ada = true; ?>
                <tr><td><?= htmlspecialchars($t['judul']); ?></td><td><?= (int)$t['qty']; ?></td><td><?= rp($t['untung']); ?></td></tr>
            <?php endwhile; ?>
            <?php if (!$ada): ?><tr><td colspan="3">Belum ada data.</td></tr><?php endif; ?>
            </tbody>
        </table>

        <p style="margin-top:18px">
            Keuntungan = Pendapatan buku &minus; Modal. Ongkir ditampilkan terpisah. Hanya pesanan Lunas/Selesai yang dihitung.
            Modal buku diatur di Kelola Buku (Tambah/Edit).
        </p>
    </div>
</div>
</body>
</html>
