<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id'])) {
    header('Location: landing.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profil.php');
    exit;
}

$user_id = (int) $_SESSION['id'];
$pesanan_id = filter_input(INPUT_POST, 'pesanan_id', FILTER_VALIDATE_INT);
if (!$pesanan_id || $pesanan_id < 1) {
    header('Location: profil.php');
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    mysqli_begin_transaction($conn);
    $cek = mysqli_prepare($conn, "SELECT id, status FROM pesanan WHERE id = ? AND user_id = ? FOR UPDATE");
    mysqli_stmt_bind_param($cek, 'ii', $pesanan_id, $user_id);
    mysqli_stmt_execute($cek);
    $pesanan = mysqli_fetch_assoc(mysqli_stmt_get_result($cek));

    if (!$pesanan || !in_array($pesanan['status'], ['Dibatalkan', 'Selesai'], true)) {
        throw new Exception('Hanya pesanan selesai atau dibatalkan yang dapat dihapus dari riwayat.');
    }

    $hapus_detail = mysqli_prepare($conn, 'DELETE FROM detail_pesanan WHERE pesanan_id = ?');
    mysqli_stmt_bind_param($hapus_detail, 'i', $pesanan_id);
    mysqli_stmt_execute($hapus_detail);

    $hapus_pesanan = mysqli_prepare($conn, 'DELETE FROM pesanan WHERE id = ? AND user_id = ?');
    mysqli_stmt_bind_param($hapus_pesanan, 'ii', $pesanan_id, $user_id);
    mysqli_stmt_execute($hapus_pesanan);
    mysqli_commit($conn);
} catch (Throwable $e) {
    mysqli_rollback($conn);
    error_log('Hapus riwayat: ' . $e->getMessage());
}
header('Location: profil.php#detail-pesanan');
exit;
?>
