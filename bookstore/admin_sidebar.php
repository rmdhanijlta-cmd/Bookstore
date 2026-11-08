<?php
// Menyediakan navigasi samping untuk halaman admin.
/*
 * Sidebar admin bersama.
 * Pakai di tiap halaman admin:
 *   <?php $halaman_aktif = 'buku'; include 'admin_sidebar.php'; ?>
 * Nilai $halaman_aktif: beranda, pesanan, tentang, user, buku, kategori, verifikasi, chat
 */
$halaman_aktif = $halaman_aktif ?? '';

$menu_admin = [
    'Utama' => [
        'beranda'    => ['admin-dashboard.php',        'fa-house',              'Beranda'],
        'pesanan'    => ['pesanan_user.php',           'fa-cart-shopping',      'Pesanan User'],
        'verifikasi' => ['verifikasi_pembayaran.php',  'fa-money-check-dollar', 'Verifikasi Pembayaran'],
        'chat'       => ['chat_admin.php',             'fa-comments',           'Hubungi User'],
    ],
    'Kelola' => [
        'buku'       => ['kelola_buku.php',            'fa-book-open',          'Kelola Buku'],
        'kategori'   => ['kelola_kategori.php',        'fa-table-list',         'Kelola Kategori'],
        'user'       => ['admin user.php',             'fa-user',               'User Terdaftar'],
        'tentang'    => ['tentang copy.php',           'fa-circle-info',        'Tentang Kita'],
    ],
];
?>
<nav class="sidebar">

    <a class="sidebar-brand" href="admin-dashboard.php">
        <span class="logo-ikon"><i class="fa-solid fa-book-open-reader"></i></span>
        <span class="teks">Mille Pagine<small>Admin Panel</small></span>
    </a>

    <?php foreach ($menu_admin as $grup => $items): ?>
        <div class="sidebar-label"><?= htmlspecialchars($grup); ?></div>
        <div class="sidebar-menu">
            <?php foreach ($items as $kunci => [$href, $ikon, $nama]): ?>
                <a class="nav-item <?= $halaman_aktif === $kunci ? 'active' : ''; ?>"
                   href="<?= htmlspecialchars($href); ?>">
                    <span class="ikon"><i class="fa-solid <?= $ikon; ?>"></i></span>
                    <span class="label"><?= htmlspecialchars($nama); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <div class="sidebar-bawah">
        <a href="index.php" class="side-card">
            <div class="ikon-besar"><i class="fa-solid fa-store"></i></div>
            <div><strong>Lihat Toko</strong><span>Tampilan user</span></div>
        </a>
        <a href="logout_admin.php" class="side-card keluar">
            <div class="ikon-besar"><i class="fa-solid fa-right-from-bracket"></i></div>
            <div><strong>Keluar</strong><span>Akhiri sesi admin</span></div>
        </a>
    </div>

</nav>
