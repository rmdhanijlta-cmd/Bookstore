<?php
// Menghubungkan tampilan percakapan user dengan admin.
session_name('MILLE_ADMIN');
session_start();
include 'koneksi.php';

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
}

$user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : 0;

if($user_id <= 0){
    header("Location: chat_admin.php");
    exit;
}

$userData = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT id, nama
        FROM users
        WHERE id='$user_id'
    ")
);

if(!$userData){
    header("Location: chat_admin.php");
    exit;
}

if(isset($_POST['kirim'])){

    $pesan = trim($_POST['pesan']);

    if($pesan != ''){

        mysqli_query($conn, "
            INSERT INTO chat
            (user_id, pengirim, pesan, waktu, status)
            VALUES
            ('$user_id', 'admin', '$pesan', NOW(), 'belum')
        ");

        header("Location: chat_user_admin.php?user_id=$user_id");
        exit;
    }
}

mysqli_query($conn, "
    UPDATE chat
    SET status='terbaca'
    WHERE user_id='$user_id'
    AND pengirim='user'
");

$chat = mysqli_query($conn, "
    SELECT *
    FROM chat
    WHERE user_id='$user_id'
    ORDER BY waktu ASC
");

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Chat <?= htmlspecialchars($userData['nama']); ?> - Mille Pagine</title>
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
                <a href="chat_admin.php" class="back" title="Kembali"><i class="fa-solid fa-arrow-left"></i></a>
                <div>
                    <h1>Hubungi User</h1>
                    <p>Percakapan langsung dengan pelanggan.</p>
                </div>
            </div>
            <div class="page-head-actions">

            </div>
        </div>

        <div class="chat-box anim" style="--i:1">

            <div class="chat-header">
                <div class="avatar"><i class="fa-solid fa-user"></i></div>
                <div>
                    <h2><?= htmlspecialchars($userData['nama']); ?></h2>
                    <p>Percakapan dengan user</p>
                </div>
            </div>

            <div class="messages">
                <?php while($c = mysqli_fetch_assoc($chat)): ?>
                    <div class="message <?= $c['pengirim'] == 'admin' ? 'admin-message' : 'user-message'; ?>">
                        <?= nl2br(htmlspecialchars($c['pesan'])); ?>
                        <div class="time"><?= date('H:i', strtotime($c['waktu'])); ?></div>
                    </div>
                <?php endwhile; ?>
            </div>

            <form method="POST" class="chat-form">
                <input type="text" name="pesan" placeholder="Ketik balasan..." autocomplete="off" required>
                <button type="submit" name="kirim" class="send-btn" title="Kirim">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>

        </div>

    </div>

</div>

<script src="admin.js"></script>
</body>
</html>
