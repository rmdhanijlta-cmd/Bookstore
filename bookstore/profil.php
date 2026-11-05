<?php
session_start();
include 'koneksi.php';

$login = isset($_SESSION['id']);

$nama    = 'User';
$email   = 'Belum masuk ke akun';
$telepon = '';
$alamat  = '';

$pesan      = '';
$tipe_pesan = '';

$proses  = 0;
$dikirim = 0;
$selesai = 0;
$detail  = null;

/* AMBIL DATA PROFIL */
if ($login) {
    $user_id = (int)$_SESSION['id'];

    // fallback dari session kalau query gagal
    $nama  = $_SESSION['nama']  ?? 'User';
    $email = $_SESSION['email'] ?? '';

    $query_profil = mysqli_query($conn, "
        SELECT id, nama, email, telepon, alamat
        FROM users
        WHERE id = $user_id
        LIMIT 1
    ");

    if ($query_profil && mysqli_num_rows($query_profil) > 0) {
        $profil  = mysqli_fetch_assoc($query_profil);
        $nama    = $profil['nama'];
        $email   = $profil['email'];
        $telepon = $profil['telepon'] ?? '';
        $alamat  = $profil['alamat'] ?? '';
    }
}

/* SIMPAN PERUBAHAN PROFIL */
if (isset($_POST['simpan_profil']) && $login) {

    $nama_baru    = trim($_POST['nama'] ?? '');
    $email_baru   = trim($_POST['email'] ?? '');
    $telepon_baru = trim($_POST['telepon'] ?? '');
    $alamat_baru  = trim($_POST['alamat'] ?? '');

    if ($nama_baru === '' || $email_baru === '') {
        $pesan = 'Nama dan email wajib diisi.';
        $tipe_pesan = 'error';

    } elseif (!filter_var($email_baru, FILTER_VALIDATE_EMAIL)) {
        $pesan = 'Format email tidak valid.';
        $tipe_pesan = 'error';

    } else {

        /* Cek email agar tidak dipakai akun lain */
        $stmt_cek = mysqli_prepare($conn, "
            SELECT id FROM users
            WHERE email = ? AND id != ?
            LIMIT 1
        ");
        mysqli_stmt_bind_param($stmt_cek, "si", $email_baru, $user_id);
        mysqli_stmt_execute($stmt_cek);
        $hasil_cek = mysqli_stmt_get_result($stmt_cek);

        if (mysqli_num_rows($hasil_cek) > 0) {
            $pesan = 'Email sudah digunakan akun lain.';
            $tipe_pesan = 'error';

        } else {

            $stmt = mysqli_prepare($conn, "
                UPDATE users
                SET nama = ?, email = ?, telepon = ?, alamat = ?
                WHERE id = ?
            ");
            mysqli_stmt_bind_param(
                $stmt, "ssssi",
                $nama_baru, $email_baru, $telepon_baru, $alamat_baru, $user_id
            );

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['nama']  = $nama_baru;
                $_SESSION['email'] = $email_baru;

                header("Location: profil.php?berhasil=1#form-edit-profil");
                exit;
            } else {
                $pesan = 'Gagal menyimpan perubahan profil.';
                $tipe_pesan = 'error';
            }
        }
    }

    /* Pertahankan input jika terjadi kesalahan */
    $nama    = $nama_baru;
    $email   = $email_baru;
    $telepon = $telepon_baru;
    $alamat  = $alamat_baru;
}

if (isset($_GET['berhasil'])) {
    $pesan = 'Profil berhasil diperbarui!';
    $tipe_pesan = 'success';
}

/* STATUS DAN RIWAYAT PESANAN */
if ($login) {

    $hasil = mysqli_query($conn, "
        SELECT
            SUM(status = 'Diproses') AS proses,
            SUM(status = 'Dikirim')  AS dikirim,
            SUM(status = 'Selesai')  AS selesai
        FROM pesanan
        WHERE user_id = $user_id
    ");

    if ($hasil) {
        $status  = mysqli_fetch_assoc($hasil);
        $proses  = (int)($status['proses']  ?? 0);
        $dikirim = (int)($status['dikirim'] ?? 0);
        $selesai = (int)($status['selesai'] ?? 0);
    }

    $detail = mysqli_query($conn, "
        SELECT
            buku.judul,
            buku.penulis,
            buku.gambar,
            pesanan.id AS pesanan_id,
            pesanan.status,
            pesanan.status_pembayaran,
            pesanan.alasan_pembatalan,
            detail_pesanan.jumlah,
            detail_pesanan.id AS detail_id
        FROM detail_pesanan
        INNER JOIN buku ON detail_pesanan.buku_id = buku.id
        INNER JOIN pesanan ON detail_pesanan.pesanan_id = pesanan.id
        WHERE pesanan.user_id = $user_id
        ORDER BY pesanan.id DESC, detail_pesanan.id DESC
    ");

} else {
    // setelah login/daftar, balik ke halaman profil
    $_SESSION['redirect_after_login'] = 'profil.php';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Profil - Mille Pagine</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
:root{
    --coklat:#5B2E0F;
    --coklat-tua:#4A2410;
    --coklat-muda:#7B5A3D;
    --krem:#F5E6D3;
    --krem-terang:#FFF3E8;
    --latar:#F4F1ED;
    --emas:#B98A55;
}

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

body{
    background:var(--latar);
    height:100vh;
    overflow:hidden;
}

.wrapper{
    width:100%;
    height:100vh;
    display:flex;
}

/* ===== SIDEBAR ===== */
.sidebar{
    width:220px;
    background:linear-gradient(180deg, #5B2E0F 0%, #3F1E08 100%);
    padding:22px 14px;
    flex-shrink:0;
    display:flex;
    flex-direction:column;
}

.sidebar-brand{
    display:flex;
    align-items:center;
    gap:10px;
    padding:4px 8px 20px;
    margin-bottom:16px;
    border-bottom:1px solid rgba(255,243,232,.15);
    color:var(--krem-terang);
    font-family:Georgia, serif;
    font-size:20px;
    letter-spacing:.5px;
}
.sidebar-brand .logo-ikon{
    width:40px;
    height:40px;
    border-radius:12px;
    background:var(--krem);
    color:var(--coklat);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
    box-shadow:0 6px 14px rgba(0,0,0,.3);
    flex-shrink:0;
}

.sidebar-menu{
    display:flex;
    flex-direction:column;
    gap:6px;
}

.nav-item{
    display:flex;
    align-items:center;
    gap:12px;
    color:rgba(255,243,232,.8);
    text-decoration:none;
    font-size:14px;
    padding:9px 10px;
    border-radius:14px;
    transition:background .2s ease, transform .2s ease, color .2s ease;
}
.nav-item .ikon{
    width:34px;
    height:34px;
    border-radius:10px;
    background:rgba(255,243,232,.08);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:14px;
    flex-shrink:0;
    transition:background .2s ease, color .2s ease;
}
.nav-item:hover{
    background:rgba(255,243,232,.08);
    color:white;
    transform:translateX(4px);
}
.nav-item:hover .ikon{ background:rgba(255,243,232,.18); }

.nav-item.active{
    background:var(--krem-terang);
    color:var(--coklat);
    font-weight:bold;
    box-shadow:0 8px 18px rgba(0,0,0,.25);
}
.nav-item.active .ikon{
    background:var(--coklat);
    color:white;
}

.sidebar-bawah{ margin-top:auto; }

.bantuan{
    display:block;
    text-align:center;
    text-decoration:none;
    color:var(--krem-terang);
    background:rgba(255,243,232,.08);
    border:1px solid rgba(255,243,232,.18);
    border-radius:18px;
    padding:18px 14px;
    font-size:13px;
    line-height:1.4;
    transition:background .2s ease, transform .2s ease;
}
.bantuan:hover{
    background:rgba(255,243,232,.16);
    transform:translateY(-3px);
}
.bantuan .ikon-besar{
    width:44px;
    height:44px;
    margin:0 auto 10px;
    border-radius:50%;
    background:var(--emas);
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
}
.bantuan strong{
    display:block;
    font-size:14px;
    margin-bottom:2px;
}
.bantuan span{ color:rgba(255,243,232,.7); }

/* ===== CONTENT ===== */
.content{
    flex:1;
    min-width:0;
    height:100vh;
    overflow-y:auto;
    padding:24px 32px 40px;
}

/* animasi muncul */
@keyframes naik{
    from{ opacity:0; transform:translateY(18px); }
    to{ opacity:1; transform:translateY(0); }
}
.anim{
    animation:naik .55s ease both;
    animation-delay:calc(var(--i, 0) * 90ms);
}

/* ===== HERO PROFIL ===== */
.hero{
    position:relative;
    overflow:hidden;
    background:linear-gradient(120deg, var(--coklat) 0%, #7A3E13 100%);
    border-radius:22px;
    padding:30px 34px;
    margin-bottom:30px;
    color:var(--krem-terang);
    box-shadow:0 14px 30px rgba(91,46,15,.25);
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:20px;
}
.hero .deco{
    position:absolute;
    right:30px;
    top:50%;
    transform:translateY(-50%) rotate(-10deg);
    font-size:120px;
    color:rgba(255,243,232,.08);
    pointer-events:none;
}
.hero .deco2{
    position:absolute;
    right:170px;
    bottom:-20px;
    font-size:70px;
    color:rgba(255,243,232,.06);
    transform:rotate(12deg);
    pointer-events:none;
}

.hero-info{
    position:relative;
    display:flex;
    align-items:center;
    gap:20px;
    min-width:0;
}

.avatar{
    width:84px;
    height:84px;
    border-radius:50%;
    background:var(--krem);
    color:var(--coklat);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:36px;
    flex-shrink:0;
    border:4px solid rgba(255,243,232,.35);
    box-shadow:0 8px 20px rgba(0,0,0,.3);
}

.hero-text{ min-width:0; }
.hero-text h1{
    font-family:Georgia, serif;
    font-size:28px;
    margin-bottom:6px;
    overflow-wrap:anywhere;
}
.hero-text p{
    font-size:15px;
    color:rgba(255,243,232,.8);
    overflow-wrap:anywhere;
}

.profile-actions{
    position:relative;
    display:flex;
    align-items:center;
    gap:10px;
    flex-wrap:wrap;
}

.btn{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:8px;
    padding:11px 20px;
    border-radius:999px;
    font-size:14px;
    font-weight:bold;
    text-decoration:none;
    white-space:nowrap;
    border:none;
    cursor:pointer;
    transition:transform .2s ease, box-shadow .2s ease, background .2s ease;
}
.btn:hover{ transform:translateY(-2px); }

.btn-krem{
    background:var(--krem);
    color:var(--coklat-tua);
    box-shadow:0 6px 14px rgba(0,0,0,.2);
}
.btn-krem:hover{ background:white; }

.btn-keluar{
    background:rgba(255,243,232,.12);
    color:#FFD6D6;
    border:1.5px solid rgba(255,214,214,.45);
}
.btn-keluar:hover{ background:rgba(255,214,214,.2); }

/* ===== JUDUL SECTION ===== */
.section-head{
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:16px;
}
.section-head .icon{
    width:36px;
    height:36px;
    border-radius:50%;
    background:var(--coklat);
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:15px;
}
.section-head h2{
    font-size:22px;
    color:var(--coklat-tua);
    font-family:Georgia, serif;
}
.section-head small{
    color:var(--coklat-muda);
    font-size:14px;
}

/* ===== EDIT PROFIL ===== */
.edit-profile-box{
    background:white;
    border-radius:18px;
    padding:24px 28px;
    margin-bottom:34px;
    box-shadow:0 6px 18px rgba(91,46,15,.08);
    scroll-margin-top:20px;
}

.form-grid{
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:16px 20px;
}
.form-group{ min-width:0; }
.form-group.full{ grid-column:1 / -1; }

.form-group label{
    display:block;
    color:var(--coklat-tua);
    font-weight:bold;
    font-size:14px;
    margin-bottom:7px;
}

.form-group input,
.form-group textarea{
    width:100%;
    padding:12px 16px;
    border:1.5px solid #E2CBB4;
    border-radius:14px;
    outline:none;
    font-size:14px;
    background:#FFFCF8;
    color:var(--coklat-tua);
    transition:border-color .2s ease, box-shadow .2s ease;
}
.form-group input:focus,
.form-group textarea:focus{
    border-color:var(--emas);
    box-shadow:0 0 0 4px rgba(185,138,85,.15);
}
.form-group textarea{ resize:vertical; }

.save-btn{
    display:inline-flex;
    align-items:center;
    gap:8px;
    margin-top:20px;
    padding:12px 24px;
    border:none;
    border-radius:999px;
    background:var(--coklat);
    color:white;
    font-size:14px;
    font-weight:bold;
    cursor:pointer;
    box-shadow:0 6px 14px rgba(91,46,15,.25);
    transition:transform .2s ease, background .2s ease;
}
.save-btn:hover{ transform:translateY(-2px); background:#6D3815; }

.message{
    display:flex;
    align-items:center;
    gap:10px;
    padding:12px 16px;
    margin-bottom:18px;
    border-radius:14px;
    font-size:14px;
}
.message.success{ color:#216B35; background:#E7F5E9; }
.message.error{ color:#A61B25; background:#FDE8E8; }

/* ===== STATUS PESANAN ===== */
.status-box{
    display:grid;
    grid-template-columns:repeat(3, minmax(0, 1fr));
    gap:18px;
    margin-bottom:34px;
}

.status-item{
    background:white;
    border-radius:18px;
    padding:24px 18px;
    text-align:center;
    box-shadow:0 6px 18px rgba(91,46,15,.08);
    transition:transform .25s ease, box-shadow .25s ease;
}
.status-item:hover{
    transform:translateY(-6px);
    box-shadow:0 16px 30px rgba(91,46,15,.18);
}

.status-icon{
    width:60px;
    height:60px;
    border-radius:50%;
    background:linear-gradient(160deg, var(--krem) 0%, #EAD2B5 100%);
    color:var(--coklat);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:24px;
    margin:0 auto 12px;
}

.status-item h3{
    font-size:14px;
    color:var(--coklat-muda);
    margin-bottom:8px;
}
.status-item .count{
    font-size:36px;
    font-weight:bold;
    color:var(--coklat-tua);
    line-height:1;
}
.status-item .label{
    font-size:13px;
    color:var(--coklat-muda);
    margin-top:6px;
}

/* ===== DETAIL PESANAN ===== */
.order-grid{
    display:grid;
    grid-template-columns:repeat(2, minmax(0, 1fr));
    gap:18px;
}

.order-item{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:14px;
    background:white;
    border-radius:18px;
    padding:14px 18px;
    box-shadow:0 6px 18px rgba(91,46,15,.08);
    min-width:0;
    transition:transform .25s ease, box-shadow .25s ease;
}
.order-item:hover{
    transform:translateY(-4px);
    box-shadow:0 14px 26px rgba(91,46,15,.16);
}

.order-left{
    display:flex;
    align-items:center;
    gap:14px;
    min-width:0;
    flex:1;
}

.order-cover{
    width:70px;
    min-height:96px;
    display:flex;
    align-items:center;
    justify-content:center;
    background:linear-gradient(160deg, var(--krem) 0%, #EAD2B5 100%);
    border-radius:10px;
    padding:6px;
    flex-shrink:0;
}
.order-cover img{
    display:block;
    width:auto;
    height:auto;
    max-width:100%;
    max-height:84px;
    object-fit:contain;
    filter:drop-shadow(0 5px 6px rgba(91,46,15,.35));
}

.order-info{ min-width:0; flex:1; }
.order-info h4{
    font-size:15px;
    color:var(--coklat-tua);
    margin-bottom:4px;
    line-height:1.3;
    overflow-wrap:anywhere;
}
.order-info p{
    font-size:13px;
    color:var(--coklat-muda);
    overflow-wrap:anywhere;
}

.badge{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:6px;
    padding:7px 14px;
    border-radius:999px;
    color:white;
    font-weight:bold;
    font-size:12px;
    flex-shrink:0;
}
.badge.process{ background:var(--emas); }
.badge.ship{ background:#7A3E13; }
.badge.done{ background:var(--coklat); }
.badge.waiting{background:#D8A44A;color:#fff;}
.badge.cancel{background:#B42318;color:#fff;}
.order-status-area{display:flex;flex-direction:column;align-items:flex-end;gap:8px;}
.hapus-riwayat{border:0;background:transparent;color:#9B3B2F;font-size:12px;font-weight:700;cursor:pointer;padding:5px;}
.hapus-riwayat:hover{text-decoration:underline;}


/* ===== KOSONG ===== */
.kosong{
    background:white;
    border-radius:18px;
    text-align:center;
    padding:46px 20px;
    color:var(--coklat-muda);
    box-shadow:0 6px 18px rgba(91,46,15,.08);
}
.kosong i{ font-size:46px; color:#D9BFA3; margin-bottom:14px; display:block; }
.kosong a{ color:var(--coklat); font-weight:bold; }

/* ===== RESPONSIF ===== */
@media(max-width:1000px){
    .order-grid{ grid-template-columns:1fr; }
    .hero .deco, .hero .deco2{ display:none; }
}
@media(max-width:900px){
    .status-box{ grid-template-columns:1fr; }
    .hero{ flex-direction:column; align-items:flex-start; }
}
@media(max-width:700px){
    .content{ padding:18px; }
    .sidebar{ width:72px; padding:16px 8px; }
    .sidebar-brand{ justify-content:center; padding:4px 0 16px; font-size:0; }
    .nav-item{ justify-content:center; padding:8px 0; font-size:0; gap:0; }
    .nav-item:hover{ transform:none; }
    .bantuan{ padding:12px 4px; }
    .bantuan strong, .bantuan span{ display:none; }
    .bantuan .ikon-besar{ margin:0 auto; }
    .form-grid{ grid-template-columns:1fr; }
    .form-group.full{ grid-column:auto; }
    .hero{ padding:24px; }
    .hero-text h1{ font-size:22px; }
    .avatar{ width:64px; height:64px; font-size:28px; }
}
</style>
</head>

<body>

<div class="wrapper">

    <!-- SIDEBAR -->
    <nav class="sidebar">

        <div class="sidebar-brand">
            <div class="logo-ikon"><i class="fa-solid fa-book-open-reader"></i></div>
            Mille Pagine
        </div>

        <div class="sidebar-menu">
            <a class="nav-item" href="index.php">
                <span class="ikon"><i class="fa-solid fa-house"></i></span> Beranda
            </a>

            <a class="nav-item" href="keranjang.php">
                <span class="ikon"><i class="fa-solid fa-cart-shopping"></i></span> Keranjang
            </a>

            <a class="nav-item" href="tentang.php">
                <span class="ikon"><i class="fa-solid fa-circle-info"></i></span> Tentang Kita
            </a>

            <a class="nav-item active" href="profil.php">
                <span class="ikon"><i class="fa-solid fa-user"></i></span> Profil
            </a>
        </div>

        <div class="sidebar-bawah">
            <a href="chat.php" class="bantuan">
                <div class="ikon-besar"><i class="fa-solid fa-headset"></i></div>
                <strong>Butuh bantuan?</strong>
                <span>Hubungi Admin</span>
            </a>
        </div>

    </nav>

    <!-- CONTENT -->
    <div class="content">

        <!-- HERO PROFIL -->
        <div class="hero anim">
            <i class="fa-solid fa-book-open deco"></i>
            <i class="fa-solid fa-feather-pointed deco2"></i>

            <div class="hero-info">
                <div class="avatar">
                    <i class="fa-regular fa-user"></i>
                </div>

                <div class="hero-text">
                    <h1><?= htmlspecialchars($nama); ?></h1>
                    <p><?= htmlspecialchars($email); ?></p>
                </div>
            </div>

            <div class="profile-actions">
                <?php if($login): ?>

                    <a href="#form-edit-profil" class="btn btn-krem">
                        <i class="fa-solid fa-pen"></i> Edit Profil
                    </a>

                    <a href="logout.php" class="btn btn-keluar">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </a>

                <?php else: ?>

                    <a href="landing.php" class="btn btn-krem">
                        <i class="fa-solid fa-right-to-bracket"></i> Masuk/Daftar
                    </a>

                <?php endif; ?>
            </div>
        </div>

        <?php if($login): ?>

        <!-- EDIT PROFIL -->
        <div class="section-head anim" style="--i:1">
            <div class="icon"><i class="fa-solid fa-user-pen"></i></div>
            <h2>Edit Profil</h2>
        </div>

        <div class="edit-profile-box anim" style="--i:2" id="form-edit-profil">

            <?php if($pesan !== ''): ?>
                <div class="message <?= htmlspecialchars($tipe_pesan); ?>">
                    <i class="fa-solid <?= $tipe_pesan === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'; ?>"></i>
                    <?= htmlspecialchars($pesan); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="profil.php#form-edit-profil">
                <div class="form-grid">

                    <div class="form-group">
                        <label for="nama">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama"
                               value="<?= htmlspecialchars($nama); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email"
                               value="<?= htmlspecialchars($email); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="telepon">Nomor Telepon</label>
                        <input type="text" id="telepon" name="telepon"
                               value="<?= htmlspecialchars($telepon); ?>">
                    </div>

                    <div class="form-group full">
                        <label for="alamat">Alamat Lengkap</label>
                        <textarea id="alamat" name="alamat" rows="3"
                                  placeholder="Masukkan alamat lengkap..."><?= htmlspecialchars($alamat); ?></textarea>
                    </div>

                </div>

                <button type="submit" name="simpan_profil" class="save-btn">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </form>
        </div>

        <?php endif; ?>

        <!-- STATUS PESANAN -->
        <div class="section-head anim" style="--i:3">
            <div class="icon"><i class="fa-solid fa-receipt"></i></div>
            <h2>Status Pesanan</h2>
        </div>

        <div class="status-box">

            <div class="status-item anim" style="--i:4">
                <div class="status-icon"><i class="fa-solid fa-box"></i></div>
                <h3>Sedang Diproses</h3>
                <div class="count"><?= $proses; ?></div>
                <div class="label">Pesanan</div>
            </div>

            <div class="status-item anim" style="--i:5">
                <div class="status-icon"><i class="fa-solid fa-truck"></i></div>
                <h3>Sedang Dikirim</h3>
                <div class="count"><?= $dikirim; ?></div>
                <div class="label">Pesanan</div>
            </div>

            <div class="status-item anim" style="--i:6">
                <div class="status-icon"><i class="fa-solid fa-check"></i></div>
                <h3>Selesai</h3>
                <div class="count"><?= $selesai; ?></div>
                <div class="label">Pesanan</div>
            </div>

        </div>

        <!-- DETAIL PESANAN -->
        <div id="detail-pesanan" class="section-head anim" style="--i:7">
            <div class="icon"><i class="fa-solid fa-book"></i></div>
            <h2>Detail Pesanan</h2>
        </div>

        <?php if($detail && mysqli_num_rows($detail) > 0): ?>

            <div class="order-grid">
                <?php $n = 0; $last_order_id = 0; while($row = mysqli_fetch_assoc($detail)): $n++; $order_id_current = (int)$row['pesanan_id']; $show_delete_history = ($order_id_current !== $last_order_id); $last_order_id = $order_id_current; ?>

                    <div class="order-item anim" style="--i:<?= min($n, 8) + 7; ?>">

                        <div class="order-left">
                            <div class="order-cover">
                                <img src="<?= htmlspecialchars($row['gambar']); ?>"
                                     alt="<?= htmlspecialchars($row['judul']); ?>">
                            </div>

                            <div class="order-info">
                                <h4>
                                    <?= htmlspecialchars($row['judul']); ?>
                                    × <?= (int)$row['jumlah']; ?>
                                </h4>
                                <p><?= htmlspecialchars($row['penulis']); ?></p>
                            </div>
                        </div>

                        <?php
                            $status_pesanan = $row['status'] ?? 'Menunggu Pembayaran';
                            $badge_class = 'process';
                            $badge_icon = 'fa-box';
                            if ($status_pesanan === 'Dikirim') {
                                $badge_class = 'ship'; $badge_icon = 'fa-truck';
                            } elseif ($status_pesanan === 'Selesai') {
                                $badge_class = 'done'; $badge_icon = 'fa-check';
                            } elseif ($status_pesanan === 'Dibatalkan') {
                                $badge_class = 'cancel'; $badge_icon = 'fa-xmark';
                            } elseif ($status_pesanan === 'Menunggu Verifikasi') {
                                $badge_class = 'waiting'; $badge_icon = 'fa-clock';
                            } elseif ($status_pesanan === 'Menunggu Pembayaran') {
                                $badge_class = 'waiting'; $badge_icon = 'fa-wallet';
                            }
                        ?>
                        <div class="order-status-area">
                            <span class="badge <?= $badge_class; ?>">
                                <i class="fa-solid <?= $badge_icon; ?>"></i>
                                <?= htmlspecialchars($status_pesanan); ?>
                            </span>
                            <?php if ($show_delete_history && in_array($status_pesanan, ['Dibatalkan', 'Selesai'], true)): ?>
                                <form action="hapus_riwayat.php" method="POST" onsubmit="return confirm('Hapus pesanan ini dari riwayat?');">
                                    <input type="hidden" name="pesanan_id" value="<?= (int)$row['pesanan_id']; ?>">
                                    <button type="submit" class="hapus-riwayat"><i class="fa-solid fa-trash"></i> Hapus Riwayat</button>
                                </form>
                            <?php endif; ?>
                        </div>

                    </div>

                <?php endwhile; ?>
            </div>

        <?php else: ?>

            <div class="kosong">
                <i class="fa-solid fa-box-open"></i>
                <?php if($login): ?>
                    <p>Belum ada pesanan.<br>
                       Yuk mulai belanja di <a href="index.php">beranda</a>.</p>
                <?php else: ?>
                    <p>Masuk atau daftar untuk melihat pesanan kamu.<br>
                       <a href="landing.php">Masuk/Daftar sekarang</a></p>
                <?php endif; ?>
            </div>

        <?php endif; ?>

    </div>

</div>

</body>
</html>