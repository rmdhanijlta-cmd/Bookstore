<?php
session_start();
include 'koneksi.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

$users = mysqli_query($conn, "
    SELECT 
        users.id,
        users.nama,
        MAX(chat.waktu) AS chat_terakhir
    FROM users
    INNER JOIN chat ON users.id = chat.user_id
    GROUP BY users.id, users.nama
    ORDER BY chat_terakhir DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Hubungi User - Mille Pagine</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="wrapper">

    <?php $halaman_aktif = 'chat'; include 'admin_sidebar.php'; ?>

    <div class="content">

        <div class="page-head anim" style="--i:0">
            <i class="fa-solid fa-comments deco"></i>
            <i class="fa-solid fa-feather-pointed deco2"></i>
            <div class="page-head-main">
                <a href="admin-dashboard.php" class="back" title="Kembali"><i class="fa-solid fa-arrow-left"></i></a>
                <div>
                    <h1>Hubungi User</h1>
                    <p>Balas pertanyaan dan bantu pelanggan lewat percakapan.</p>
                </div>
            </div>
            <div class="page-head-actions">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Cari nama user..." data-filter="#daftar">
                </div>
                <span class="count-chip"><i class="fa-solid fa-comments"></i> <?= mysqli_num_rows($users); ?> percakapan</span>
            </div>
        </div>

        <div class="list" id="daftar">

            <?php if(mysqli_num_rows($users) == 0): ?>
                <div class="empty">
                    <i class="fa-regular fa-comments"></i>
                    <p>Belum ada percakapan dengan user.</p>
                </div>
            <?php endif; ?>

            <?php $no = 0; while($u = mysqli_fetch_assoc($users)): $no++; ?>

                <a href="chat_user_admin.php?user_id=<?= (int)$u['id']; ?>" class="chat-item anim"
                   style="--i:<?= min($no, 8); ?>" data-cari="<?= htmlspecialchars($u['nama']); ?>">

                    <div class="user-info">
                        <div class="avatar"><i class="fa-solid fa-user"></i></div>
                        <div>
                            <div class="user-name"><?= htmlspecialchars($u['nama']); ?></div>
                            <div class="chat-time">
                                <i class="fa-regular fa-clock"></i>
                                <?= date('d/m/Y H:i', strtotime($u['chat_terakhir'])); ?>
                            </div>
                        </div>
                    </div>

                    <div class="panah"><i class="fa-solid fa-chevron-right"></i></div>

                </a>

            <?php endwhile; ?>

        </div>

    </div>

</div>

<script src="admin.js"></script>
</body>
</html>
