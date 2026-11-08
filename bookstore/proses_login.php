<?php
// Memeriksa email dan password, lalu membuat sesi sesuai peran akun.
include 'koneksi.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$stmt = mysqli_prepare($conn, "SELECT id, nama, email, password, role FROM users WHERE email = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if ($data && ($password === $data['password'] || password_verify($password, $data['password']))) {
    $is_admin = ($data['role'] === 'admin');
    session_name($is_admin ? 'MILLE_ADMIN' : 'MILLE_USER');
    session_start();
    session_regenerate_id(true);

    $_SESSION['id'] = $data['id'];
    $_SESSION['nama'] = $data['nama'];
    $_SESSION['role'] = $data['role'];
    $_SESSION['email'] = $data['email'];

    header('Location: ' . ($is_admin ? 'admin-dashboard.php' : 'index.php'));
    exit;
}

header('Location: login.php?error=1');
exit;
?>