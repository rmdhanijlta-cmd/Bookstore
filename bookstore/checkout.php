
<?php
session_name('MILLE_USER');
session_start();
include 'koneksi.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

/* CEK LOGIN */
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION['id'];

/* CEK METODE PEMBAYARAN */
$metode = $_POST['metode'] ?? '';

if (!in_array($metode, ['Transfer Bank', 'Tunai'], true)) {
    $_SESSION['pesan_checkout'] = "Metode pembayaran tidak valid.";
    header("Location: keranjang.php");
    exit;
}

try {
    $conn->begin_transaction();

    /* AMBIL DATA KERANJANG */
    $stmt = $conn->prepare("
        SELECT
            k.buku_id,
            k.jumlah,
            b.harga,
            b.stok
        FROM keranjang k
        JOIN buku b ON k.buku_id = b.id
        WHERE k.user_id = ?
        FOR UPDATE
    ");

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    $total = 0;

    while ($row = $result->fetch_assoc()) {
        $jumlah = (int) $row['jumlah'];
        $stok = (int) $row['stok'];
        $harga = (int) $row['harga'];

        if ($jumlah <= 0 || $stok <= 0 || $jumlah > $stok) {
            throw new Exception(
                "Stok buku tidak mencukupi. Silakan periksa kembali keranjang."
            );
        }

        $subtotal = $harga * $jumlah;
        $total += $subtotal;

        $data[] = [
            'buku_id' => (int) $row['buku_id'],
            'jumlah' => $jumlah,
            'harga' => $harga
        ];
    }

    if (count($data) === 0) {
        throw new Exception("Keranjang masih kosong.");
    }

    // Ongkir tetap Rp10.000 per transaksi.
    $ongkir = 10000;
    $total += $ongkir;

    /* TENTUKAN STATUS AWAL PESANAN */
    $status = 'Menunggu Pembayaran';
    $status_pembayaran = 'Belum Bayar';

    /* SIMPAN PESANAN */
    $stmt = $conn->prepare("
        INSERT INTO pesanan (
            user_id,
            total,
            ongkir,
            metode_pembayaran,
            metode_bayar,
            status,
            status_pembayaran
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "iiissss",
        $user_id,
        $total,
        $ongkir,
        $metode,
        $metode,
        $status,
        $status_pembayaran
    );

    $stmt->execute();

    $pesanan_id = $conn->insert_id;

    /* SIMPAN DETAIL PESANAN */
    $stmt_detail = $conn->prepare("
        INSERT INTO detail_pesanan (
            pesanan_id,
            buku_id,
            jumlah,
            harga
        )
        VALUES (?, ?, ?, ?)
    ");

    foreach ($data as $row) {
        $buku_id = $row['buku_id'];
        $jumlah = $row['jumlah'];
        $harga = $row['harga'];

        $stmt_detail->bind_param(
            "iiii",
            $pesanan_id,
            $buku_id,
            $jumlah,
            $harga
        );

        $stmt_detail->execute();
    }

    /* KOSONGKAN KERANJANG */
    $stmt = $conn->prepare("
        DELETE FROM keranjang
        WHERE user_id = ?
    ");

    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $conn->commit();

    header("Location: invoice.php?id=" . $pesanan_id);
    exit;

} catch (Exception $e) {
    $conn->rollback();

    $_SESSION['pesan_checkout'] = $e->getMessage();

    header("Location: keranjang.php");
    exit;
}
?>
