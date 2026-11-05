
<?php
session_start();
include 'koneksi.php';

/* CEK ADMIN */
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

function e($text) {
    return htmlspecialchars((string)$text, ENT_QUOTES, 'UTF-8');
}

function rupiah($angka) {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

/* NOTIFIKASI */
$pesan = $_SESSION['pesan_verifikasi'] ?? '';
unset($_SESSION['pesan_verifikasi']);

/* PROSES VERIFIKASI PEMBAYARAN */

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $pesanan_id = filter_input(
        INPUT_POST,
        'pesanan_id',
        FILTER_VALIDATE_INT
    );

    $aksi = $_POST['aksi'] ?? '';

    if (
        !$pesanan_id ||
        !in_array($aksi, ['konfirmasi', 'tolak'], true)
    ) {
        $_SESSION['pesan_verifikasi'] = 'Permintaan tidak valid.';
        header("Location: verifikasi_pembayaran.php");
        exit;
    }

    try {
        mysqli_begin_transaction($conn);

        // Ambil dan kunci pesanan agar tidak diproses dua kali.
        $cek = mysqli_prepare($conn, "
            SELECT id, COALESCE(NULLIF(metode_bayar, ''), metode_pembayaran) AS metode_bayar, status, status_pembayaran,
                   bukti_transfer
            FROM pesanan
            WHERE id = ?
            FOR UPDATE
        ");

        mysqli_stmt_bind_param($cek, "i", $pesanan_id);
        mysqli_stmt_execute($cek);

        $pesanan = mysqli_fetch_assoc(
            mysqli_stmt_get_result($cek)
        );

        if (!$pesanan) {
            throw new Exception('Pesanan tidak ditemukan.');
        }

        $metode = $pesanan['metode_bayar'];

        // Tentukan apakah pesanan memenuhi syarat diproses.
        if ($metode === 'Transfer Bank') {

            if (
                $pesanan['status_pembayaran'] !== 'Menunggu Verifikasi' ||
                empty($pesanan['bukti_transfer'])
            ) {
                throw new Exception(
                    'Pembayaran transfer sudah diproses atau bukti tidak ditemukan.'
                );
            }

        } elseif ($metode === 'Tunai') {

            if (
                $aksi !== 'konfirmasi' ||
                $pesanan['status_pembayaran'] !== 'Belum Bayar' ||
                $pesanan['status'] !== 'Menunggu Pembayaran'
            ) {
                throw new Exception(
                    'Pesanan tunai sudah diproses atau tidak dapat dikonfirmasi.'
                );
            }

        } else {
            throw new Exception('Metode pembayaran tidak dikenali.');
        }

        if ($aksi === 'konfirmasi') {

            // Ambil detail pesanan dan kunci stok buku.
            $detail = mysqli_prepare($conn, "
                SELECT dp.buku_id, SUM(dp.jumlah) AS jumlah, b.stok, b.judul
                FROM detail_pesanan dp
                JOIN buku b ON dp.buku_id = b.id
                WHERE dp.pesanan_id = ?
                GROUP BY dp.buku_id, b.stok, b.judul
                FOR UPDATE
            ");

            mysqli_stmt_bind_param($detail, "i", $pesanan_id);
            mysqli_stmt_execute($detail);

            $hasil_detail = mysqli_stmt_get_result($detail);

            $items = [];

            while ($item = mysqli_fetch_assoc($hasil_detail)) {
                $items[] = $item;
            }

            if (empty($items)) {
                throw new Exception('Detail pesanan tidak ditemukan.');
            }

            // Periksa stok sebelum menguranginya.
            foreach ($items as $item) {

                if ((int)$item['jumlah'] < 1 || (int)$item['stok'] < (int)$item['jumlah']) {
                    throw new Exception(
                        'Stok buku "' . $item['judul'] .
                        '" tidak mencukupi. Pembayaran belum dikonfirmasi.'
                    );
                }
            }

            // Kurangi stok hanya saat pembayaran dikonfirmasi.
            $kurangi = mysqli_prepare($conn, "
                UPDATE buku
                SET stok = stok - ?
                WHERE id = ? AND stok >= ?
            ");

            foreach ($items as $item) {

                $jumlah = (int)$item['jumlah'];
                $buku_id = (int)$item['buku_id'];

                mysqli_stmt_bind_param(
                    $kurangi,
                    "iii",
                    $jumlah,
                    $buku_id,
                    $jumlah
                );

                mysqli_stmt_execute($kurangi);

                if (mysqli_stmt_affected_rows($kurangi) !== 1) {
                    throw new Exception(
                        'Gagal memperbarui stok buku.'
                    );
                }
            }

            // Tandai pembayaran lunas.
            $update = mysqli_prepare($conn, "
                UPDATE pesanan
                SET status_pembayaran = 'Lunas',
                    status = 'Diproses',
                    alasan_pembatalan = NULL
                WHERE id = ?
            ");

            mysqli_stmt_bind_param($update, "i", $pesanan_id);
            mysqli_stmt_execute($update);

            if (mysqli_stmt_affected_rows($update) !== 1) {
                throw new Exception('Gagal memperbarui status pesanan.');
            }

            if ($metode === 'Tunai') {
                $pesan_sukses =
                    'Pembayaran tunai berhasil dikonfirmasi. Stok buku berhasil diperbarui.';
            } else {
                $pesan_sukses =
                    'Pembayaran transfer berhasil dikonfirmasi. Stok buku berhasil diperbarui.';
            }

        } else {

            // Penolakan hanya berlaku untuk Transfer Bank.
            $alasan = trim($_POST['alasan'] ?? '');

            if ($alasan === '') {
                $alasan = 'Bukti pembayaran ditolak oleh admin.';
            }

            $update = mysqli_prepare($conn, "
                UPDATE pesanan
                SET status_pembayaran = 'Ditolak',
                    status = 'Dibatalkan',
                    alasan_pembatalan = ?
                WHERE id = ?
            ");

            mysqli_stmt_bind_param(
                $update,
                "si",
                $alasan,
                $pesanan_id
            );

            mysqli_stmt_execute($update);

            if (mysqli_stmt_affected_rows($update) !== 1) {
                throw new Exception('Gagal menolak pembayaran.');
            }

            $pesan_sukses =
                'Pembayaran ditolak dan pesanan dibatalkan.';
        }

        mysqli_commit($conn);

        $_SESSION['pesan_verifikasi'] = $pesan_sukses;

    } catch (Throwable $err) {

        mysqli_rollback($conn);
        error_log($err->getMessage());

        // Tampilkan alasan kegagalan yang aman untuk admin.
        $_SESSION['pesan_verifikasi'] =
            'Proses gagal: ' . $err->getMessage();
    }

    header("Location: verifikasi_pembayaran.php");
    exit;
}

/* AMBIL DATA PEMBAYARAN */
$sql = "
    SELECT
        p.id,
        p.user_id,
        p.tanggal,
        p.total,
        p.metode_bayar,
        p.bukti_transfer,
        p.status,
        p.status_pembayaran,
        u.nama AS nama_user
    FROM pesanan p
    LEFT JOIN users u ON p.user_id = u.id
    WHERE
        (
            COALESCE(NULLIF(p.metode_bayar, ''), p.metode_pembayaran) = 'Transfer Bank'
            AND p.status_pembayaran = 'Menunggu Verifikasi'
            AND p.bukti_transfer IS NOT NULL
            AND p.bukti_transfer <> ''
        )
        OR
        (
            COALESCE(NULLIF(p.metode_bayar, ''), p.metode_pembayaran) = 'Tunai'
            AND p.status_pembayaran = 'Belum Bayar'
            AND p.status = 'Menunggu Pembayaran'
        )
    ORDER BY p.tanggal ASC
";

$query = mysqli_query($conn, $sql);

if (!$query) {
    die('Gagal mengambil pembayaran: ' . e(mysqli_error($conn)));
}

$total_menunggu = mysqli_num_rows($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verifikasi Pembayaran - Mille Pagine</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="wrapper">

    <?php $halaman_aktif = 'verifikasi'; include 'admin_sidebar.php'; ?>

    <div class="content">

        <div class="page-head anim" style="--i:0">
            <i class="fa-solid fa-money-check-dollar deco"></i>
            <i class="fa-solid fa-feather-pointed deco2"></i>
            <div class="page-head-main">
                <a href="admin-dashboard.php" class="back" title="Kembali"><i class="fa-solid fa-arrow-left"></i></a>
                <div>
                    <h1>Verifikasi Pembayaran</h1>
                    <p>Periksa bukti transfer dan konfirmasi pembayaran pesanan user.</p>
                </div>
            </div>
            <div class="page-head-actions">
                <span class="count-chip"><i class="fa-solid fa-clock"></i> <?= $total_menunggu ?> menunggu</span>
            </div>
        </div>

        <?php if ($pesan !== ''): ?>
            <div class="notif anim">
                <i class="fa-solid fa-circle-info"></i>
                <?= e($pesan) ?>
            </div>
        <?php endif; ?>

        <section class="panel anim" style="--i:1">

            <div class="section-head">
                <div class="icon"><i class="fa-solid fa-receipt"></i></div>
                <h2>Daftar Pembayaran</h2>
            </div>

            <div class="table-scroll">
                <table>
                    <thead>
                        <tr>
                            <th>ID Pesanan</th>
                            <th>Nama User</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Metode</th>
                            <th>Bukti Transfer</th>
                            <th>Status</th>
                            <th>Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php if ($total_menunggu > 0): ?>

                        <?php while ($row = mysqli_fetch_assoc($query)): ?>

                            <tr>
                                <td>
                                    #<?= (int)$row['id'] ?>
                                </td>

                                <td>
                                    <?= e(
                                        $row['nama_user']
                                        ?: 'User #' . $row['user_id']
                                    ) ?>
                                </td>

                                <td>
                                    <?= e($row['tanggal']) ?>
                                </td>

                                <td>
                                    <strong>
                                        <?= rupiah($row['total']) ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= e($row['metode_bayar']) ?>
                                </td>

                                <td>
                                    <?php if ($row['metode_bayar'] === 'Transfer Bank'): ?>

                                        <a class="bukti"
                                        href="<?= e($row['bukti_transfer']) ?>"
                                        target="_blank"
                                        rel="noopener noreferrer">
                                            <i class="fa-solid fa-image"></i>
                                            Lihat Bukti
                                        </a>

                                    <?php else: ?>

                                        <span style="color:var(--coklat-muda);">
                                            Bayar saat pengambilan
                                        </span>

                                    <?php endif; ?>
                                </td>

                                <td>
                                    <span class="status">
                                        <?= $row['metode_bayar'] === 'Tunai'
                                            ? 'Menunggu Pembayaran Tunai'
                                            : 'Menunggu Verifikasi' ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <div class="actions">

                                        <?php if ($row['metode_bayar'] === 'Transfer Bank'): ?>

                                            <form method="POST"
                                                onsubmit="return confirm('Konfirmasi pembayaran pesanan #<?= (int)$row['id'] ?>?')">

                                                <input type="hidden"
                                                    name="pesanan_id"
                                                    value="<?= (int)$row['id'] ?>">

                                                <input type="hidden"
                                                    name="aksi"
                                                    value="konfirmasi">

                                                <button type="submit" class="btn btn-approve">
                                                    <i class="fa-solid fa-check"></i>
                                                    Konfirmasi
                                                </button>

                                            </form>

                                            <form method="POST"
                                                onsubmit="return confirm('Tolak pembayaran dan batalkan pesanan ini?')">

                                                <input type="hidden"
                                                    name="pesanan_id"
                                                    value="<?= (int)$row['id'] ?>">

                                                <input type="hidden"
                                                    name="aksi"
                                                    value="tolak">

                                                <input type="hidden"
                                                    name="alasan"
                                                    value="Bukti pembayaran ditolak oleh admin.">

                                                <button type="submit" class="btn btn-reject">
                                                    <i class="fa-solid fa-xmark"></i>
                                                    Tolak Pembayaran
                                                </button>

                                            </form>

                                        <?php else: ?>

                                            <form method="POST"
                                                onsubmit="return confirm('Pembeli sudah membayar tunai dan mengambil pesanan #<?= (int)$row['id'] ?>?')">

                                                <input type="hidden"
                                                    name="pesanan_id"
                                                    value="<?= (int)$row['id'] ?>">

                                                <input type="hidden"
                                                    name="aksi"
                                                    value="konfirmasi">

                                                <button type="submit" class="btn btn-approve">
                                                    <i class="fa-solid fa-money-bill-wave"></i>
                                                    Konfirmasi Tunai
                                                </button>

                                            </form>

                                        <?php endif; ?>

                                    </div>
                                </td>
                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="8" class="empty">
                                <i class="fa-regular fa-folder-open"></i>
                                <p>
                                    Belum ada pembayaran yang menunggu verifikasi.
                                </p>
                            </td>
                        </tr>

                    <?php endif; ?>

                    </tbody>
                </table>
            </div>

        </section>

    </div>

</div>

<script src="admin.js"></script>
</body>
</html>
