<?php
// Menyimpan data buku baru dari formulir admin.
session_name('MILLE_USER');
session_start();
include 'koneksi.php';
if (!isset($_SESSION['id'])) { header("Location: landing.php"); exit; }
$user_id = (int)$_SESSION['id'];
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { header("Location: keranjang.php"); exit; }
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    mysqli_begin_transaction($conn);
    $stmt = mysqli_prepare($conn, "SELECT k.jumlah, b.stok FROM keranjang k JOIN buku b ON b.id = k.buku_id WHERE k.id = ? AND k.user_id = ? FOR UPDATE");
    mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    if ($row && (int)$row['jumlah'] < (int)$row['stok']) {
        $up = mysqli_prepare($conn, "UPDATE keranjang SET jumlah = jumlah + 1 WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($up, "ii", $id, $user_id);
        mysqli_stmt_execute($up);
    }
    mysqli_commit($conn);
} catch (Throwable $e) { mysqli_rollback($conn); error_log($e->getMessage()); }
header("Location: keranjang.php"); exit;
?>