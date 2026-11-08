<?php
// Memperbarui status pesanan setelah diperiksa oleh admin.
session_name('MILLE_ADMIN');
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: pesanan_user.php");
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$status_baru = $_POST['status'] ?? '';
if (!$id || $id < 1 || !in_array($status_baru, ['Dikirim', 'Selesai', 'Dibatalkan'], true)) {
    header("Location: pesanan_user.php");
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    mysqli_begin_transaction($conn);
    $stmt = mysqli_prepare($conn, "SELECT status, status_pembayaran FROM pesanan WHERE id = ? FOR UPDATE");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $pesanan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if (!$pesanan) throw new Exception('Pesanan tidak ditemukan.');

    $sekarang = $pesanan['status'];
    $pembayaran = $pesanan['status_pembayaran'];

    if ($status_baru === 'Dibatalkan') {
        // Admin hanya dapat membatalkan pesanan yang belum lunas dan masih menunggu.
        if ($pembayaran === 'Lunas' || !in_array($sekarang, ['Menunggu Pembayaran', 'Menunggu Verifikasi'], true)) {
            throw new Exception('Pesanan ini tidak dapat dibatalkan.');
        }
        $alasan = 'Pesanan dibatalkan oleh admin.';
        $up = mysqli_prepare($conn, "UPDATE pesanan SET status = 'Dibatalkan', status_pembayaran = CASE WHEN status_pembayaran = 'Menunggu Verifikasi' THEN 'Ditolak' ELSE status_pembayaran END, alasan_pembatalan = ? WHERE id = ?");
        mysqli_stmt_bind_param($up, "si", $alasan, $id);
    } else {
        if ($pembayaran !== 'Lunas') throw new Exception('Pesanan belum dibayar.');
        if (($status_baru === 'Dikirim' && $sekarang !== 'Diproses') || ($status_baru === 'Selesai' && $sekarang !== 'Dikirim')) {
            throw new Exception('Perubahan status tidak sesuai urutan.');
        }
        $up = mysqli_prepare($conn, "UPDATE pesanan SET status = ? WHERE id = ?");
        mysqli_stmt_bind_param($up, "si", $status_baru, $id);
    }
    mysqli_stmt_execute($up);
    mysqli_commit($conn);
} catch (Throwable $e) {
    mysqli_rollback($conn);
    error_log($e->getMessage());
}
header("Location: pesanan_user.php");
exit;
?>
