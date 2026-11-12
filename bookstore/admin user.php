<?php
session_name('MILLE_ADMIN');
session_start();
include 'koneksi.php';

$users = mysqli_query($conn, "
    SELECT 
        users.*,
        COUNT(pesanan.id) as jumlah_pesanan
    FROM users
    LEFT JOIN pesanan ON users.id = pesanan.user_id
    WHERE users.role='user'
    GROUP BY users.id
    ORDER BY users.id DESC
");

$total_user = mysqli_num_rows($users);

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
<title>User Terdaftar - Mille Pagine</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="admin.css">
<link rel="stylesheet" href="admin-fix.css">
</head>
<body>

<div class="wrapper">

    <?php $halaman_aktif = 'user'; include 'admin_sidebar.php'; ?>

    <div class="content">

        <div class="page-head anim" style="--i:0">
            <i class="fa-solid fa-users deco"></i>
            <i class="fa-solid fa-feather-pointed deco2"></i>
            <div class="page-head-main">
                <a href="admin-dashboard.php" class="back" title="Kembali"><i class="fa-solid fa-arrow-left"></i></a>
                <div>
                    <h1>Pengguna</h1>
                    <p>Daftar pelanggan yang sudah mendaftar di Mille Pagine.</p>
                </div>
            </div>
            <div class="page-head-actions">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Cari nama atau email..." data-filter="#daftar">
                </div>
                <span class="count-chip"><i class="fa-solid fa-users"></i> <?= $total_user; ?> User</span>
            </div>
        </div>

        <div class="list" id="daftar">

        <?php if($total_user == 0): ?>
            <div class="kosong"><i class="fa-regular fa-user"></i><p>Belum ada user terdaftar.</p></div>
        <?php endif; ?>

        <?php $no = 0; while($u = mysqli_fetch_assoc($users)): $no++; ?>

            <a class="item anim" style="--i:<?= min($no, 8); ?>"
               href="admin detail.php?id=<?= (int)$u['id']; ?>"
               data-cari="<?= htmlspecialchars($u['nama'] . ' ' . $u['email']); ?>">

                <div class="item-left">
                    <div class="avatar"><i class="fa-regular fa-user"></i></div>
                    <div>
                        <h3><?= htmlspecialchars($u['nama']); ?></h3>
                        <p><?= htmlspecialchars($u['email']); ?></p>
                        <span class="badge"><i class="fa-solid fa-receipt"></i> <?= (int)$u['jumlah_pesanan']; ?> Pesanan</span>
                    </div>
                </div>

                <span class="detail-btn">Lihat Detail <i class="fa-solid fa-chevron-right"></i></span>

            </a>

        <?php endwhile; ?>

        </div>

    </div>

</div>

<script src="admin.js"></script>
</body>
</html>
