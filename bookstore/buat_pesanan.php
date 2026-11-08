
<?php
// Memproses checkout dan menyimpan pesanan beserta detailnya.
session_name('MILLE_USER');
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id'])) {
    $_SESSION['redirect_after_login'] = 'keranjang.php';
    header("Location: landing.php");
    exit;
}

$user_id = (int) $_SESSION['id'];
$metode = $_POST['metode'] ?? '';

if (!in_array($metode, ['Transfer Bank', 'Tunai'], true)) {
    header("Location: keranjang.php");
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    mysqli_begin_transaction($conn);

    // Ambil isi keranjang sekaligus kunci data buku.
    $stmt = mysqli_prepare($conn, "
        SELECT
            k.buku_id,
            k.jumlah,
            b.harga,
            b.stok,
            b.judul
        FROM keranjang k
        JOIN buku b ON k.buku_id = b.id
        WHERE k.user_id = ?
        FOR UPDATE
    ");

    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);

    $query = mysqli_stmt_get_result($stmt);

    $items = [];
    $total = 0;

    while ($row = mysqli_fetch_assoc($query)) {
        $jumlah = (int) $row['jumlah'];
        $stok = (int) $row['stok'];

        if ($jumlah < 1 || $stok < 1 || $jumlah > $stok) {
            throw new Exception(
                "Stok buku " . $row['judul'] . " tidak mencukupi."
            );
        }

        $harga = (int) $row['harga'];
        $total += $harga * $jumlah;

        $items[] = [
            'buku_id' => (int) $row['buku_id'],
            'jumlah' => $jumlah,
            'harga' => $harga
        ];
    }

    if (empty($items)) {
        throw new Exception("Keranjang masih kosong.");
    }

    // Status awal pesanan.
    $status = 'Menunggu Pembayaran';
    $status_pembayaran = 'Belum Bayar';

    // Simpan pesanan.
    $stmt = mysqli_prepare($conn, "
        INSERT INTO pesanan (
            user_id,
            total,
            metode_pembayaran,
            metode_bayar,
            status,
            status_pembayaran
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    mysqli_stmt_bind_param(
        $stmt,
        "iissss",
        $user_id,
        $total,
        $metode,
        $metode,
        $status,
        $status_pembayaran
    );

    mysqli_stmt_execute($stmt);

    $pesanan_id = mysqli_insert_id($conn);

    // Simpan detail pesanan.
    $detail = mysqli_prepare($conn, "
        INSERT INTO detail_pesanan (
            pesanan_id,
            buku_id,
            jumlah,
            harga
        )
        VALUES (?, ?, ?, ?)
    ");

    foreach ($items as $item) {
        $buku_id = $item['buku_id'];
        $jumlah = $item['jumlah'];
        $harga = $item['harga'];

        mysqli_stmt_bind_param(
            $detail,
            "iiii",
            $pesanan_id,
            $buku_id,
            $jumlah,
            $harga
        );

        mysqli_stmt_execute($detail);
    }

    // Kosongkan keranjang.
    $hapus = mysqli_prepare(
        $conn,
        "DELETE FROM keranjang WHERE user_id = ?"
    );

    mysqli_stmt_bind_param($hapus, "i", $user_id);
    mysqli_stmt_execute($hapus);

    mysqli_commit($conn);

    // KEDUA METODE PEMBAYARAN MENUJU INVOICE.
    header("Location: invoice.php?id=" . $pesanan_id);
    exit;

} catch (Throwable $e) {
    mysqli_rollback($conn);

    error_log($e->getMessage());

    $_SESSION['pesan_checkout'] = $e->getMessage();

    header("Location: keranjang.php");
    exit;
}
?>
