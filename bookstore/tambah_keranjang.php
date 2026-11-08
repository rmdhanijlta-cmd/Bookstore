<?php
// Menambahkan buku pilihan ke keranjang user.
session_name('MILLE_USER');
session_start();
include 'koneksi.php';

$buku_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$buku_id || $buku_id < 1) { header("Location: index.php"); exit; }
if (!isset($_SESSION['id'])) {
    header("Location: landing.php?redirect=keranjang&buku_id=" . $buku_id);
    exit;
}
$user_id = (int)$_SESSION['id'];
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    mysqli_begin_transaction($conn);
    $buku = mysqli_prepare($conn, "SELECT stok FROM buku WHERE id = ? FOR UPDATE");
    mysqli_stmt_bind_param($buku, "i", $buku_id);
    mysqli_stmt_execute($buku);
    $dataBuku = mysqli_fetch_assoc(mysqli_stmt_get_result($buku));
    if (!$dataBuku || (int)$dataBuku['stok'] < 1) {
        throw new Exception('Stok buku sedang habis.');
    }

    $cek = mysqli_prepare($conn, "SELECT jumlah FROM keranjang WHERE user_id = ? AND buku_id = ? FOR UPDATE");
    mysqli_stmt_bind_param($cek, "ii", $user_id, $buku_id);
    mysqli_stmt_execute($cek);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($cek));
    if ($row) {
        if ((int)$row['jumlah'] >= (int)$dataBuku['stok']) throw new Exception('Jumlah di keranjang sudah mencapai stok tersedia.');
        $up = mysqli_prepare($conn, "UPDATE keranjang SET jumlah = jumlah + 1 WHERE user_id = ? AND buku_id = ?");
        mysqli_stmt_bind_param($up, "ii", $user_id, $buku_id);
        mysqli_stmt_execute($up);
    } else {
        $ins = mysqli_prepare($conn, "INSERT INTO keranjang (user_id, buku_id, jumlah) VALUES (?, ?, 1)");
        mysqli_stmt_bind_param($ins, "ii", $user_id, $buku_id);
        mysqli_stmt_execute($ins);
    }
    mysqli_commit($conn);
} catch (Throwable $e) {
    mysqli_rollback($conn);
    $_SESSION['pesan_checkout'] = $e->getMessage();
}
header("Location: keranjang.php");
exit;
?>