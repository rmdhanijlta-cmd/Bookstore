<?php
session_start();
include 'koneksi.php';

$id = $_GET['id'];

$user = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM users WHERE id='$id'")
);

if(!$user){
    header("Location: admin user.php");
    exit;
}

$jumlah_pesanan = mysqli_fetch_assoc(
    mysqli_query($conn,
    "SELECT COUNT(*) as total FROM pesanan WHERE user_id='$id'")
)['total'];

$total_belanja = mysqli_fetch_assoc(
    mysqli_query($conn,
    "SELECT SUM(total) as total FROM pesanan WHERE user_id='$id'")
)['total'] ?? 0;

$pesanan_selesai = mysqli_fetch_assoc(
    mysqli_query($conn,
    "SELECT COUNT(*) as total 
     FROM pesanan 
     WHERE user_id='$id' AND status='Selesai'")
)['total'];

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}
?>

<?php ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Detail Profil - Mille Pagine</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="wrapper">

    <?php $halaman_aktif = 'user'; include 'admin_sidebar.php'; ?>

    <div class="content">


        <div class="page-head anim">
            <i class="fa-solid fa-user deco"></i>
            <div class="page-head-main">
                <a href="admin user.php" class="back" title="Kembali"><i class="fa-solid fa-arrow-left"></i></a>
                <div>
                    <h1><?= htmlspecialchars($user['nama']); ?></h1>
                    <p>Detail profil dan ringkasan belanja user.</p>
                </div>
            </div>
        </div>

        <div class="stats">
            <div class="stat-card anim" style="--i:1">
                <div class="stat-icon emas"><i class="fa-solid fa-wallet"></i></div>
                <div class="stat-text">
                    <div class="label">Total Belanja</div>
                    <div class="value">Rp <?= number_format($total_belanja,0,',','.'); ?></div>
                </div>
            </div>
            <div class="stat-card anim" style="--i:2">
                <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
                <div class="stat-text">
                    <div class="label">Pesanan Selesai</div>
                    <div class="value"><?= (int)$pesanan_selesai; ?></div>
                </div>
            </div>
            <a href="detail_pesanan.php?user_id=<?= (int)$user['id']; ?>" class="stat-card anim" style="--i:3">
                <div class="stat-icon emas"><i class="fa-solid fa-receipt"></i></div>
                <div class="stat-text">
                    <div class="label">Jumlah Pesanan</div>
                    <div class="value"><?= (int)$jumlah_pesanan; ?> <small>lihat &rsaquo;</small></div>
                </div>
            </a>
        </div>

        <div class="profile-card anim" style="--i:2">

            <div class="profile-top">
                <div class="avatar besar"><i class="fa-regular fa-user"></i></div>
                <div>
                    <h2><?= htmlspecialchars($user['nama']); ?></h2>
                    <a class="badge badge-link" href="detail_pesanan.php?user_id=<?= (int)$user['id']; ?>">
                        <i class="fa-solid fa-receipt"></i> <?= (int)$jumlah_pesanan; ?> Pesanan
                    </a>
                </div>
            </div>

            <div class="profile-grid">
                <div class="form-group">
                    <label>Nama</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['nama']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Alamat</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['alamat'] ?? ''); ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['telepon'] ?? ''); ?>" readonly>
                </div>
            </div>

        </div>

    </div>

</div>

<script src="admin.js"></script>
</body>
</html>
