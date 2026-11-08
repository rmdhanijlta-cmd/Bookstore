
<?php
// Menampilkan informasi pembayaran dan formulir unggah bukti transfer.
session_name('MILLE_USER');
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id'])) {
    $_SESSION['redirect_after_login'] = 'keranjang.php';
    header("Location: landing.php");
    exit;
}

$user_id = (int) $_SESSION['id'];
$pesanan_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$pesanan_id) {
    header("Location: profil.php");
    exit;
}

// Pastikan pesanan milik user yang sedang login.
$stmt = mysqli_prepare($conn, "
    SELECT id, total, tanggal, metode_bayar, status,
           status_pembayaran, bukti_transfer
    FROM pesanan
    WHERE id = ? AND user_id = ?
    LIMIT 1
");
mysqli_stmt_bind_param($stmt, "ii", $pesanan_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$pesanan = mysqli_fetch_assoc($result);

if (!$pesanan) {
    http_response_code(404);
    exit("Pesanan tidak ditemukan.");
}

if ($pesanan['metode_bayar'] !== 'Transfer Bank') {
    header("Location: profil.php");
    exit;
}

$pesan = $_SESSION['pesan_pembayaran'] ?? '';
unset($_SESSION['pesan_pembayaran']);

$rekening = [
    'bank' => 'BCA',
    'nomor' => '1234567890',
    'pemilik' => 'MILLE PAGINE'
];

$boleh_upload = (
    $pesanan['status'] === 'Menunggu Pembayaran'
    && $pesanan['status_pembayaran'] === 'Belum Bayar'
);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pembayaran - Mille Pagine</title>

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>
:root {
    --coklat: #5B2E0F;
    --coklat-tua: #4A2410;
    --coklat-muda: #7B5A3D;
    --krem: #F5E6D3;
    --krem-terang: #FFF3E8;
    --latar: #F4F1ED;
    --emas: #B98A55;
}
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial,sans-serif;
}
body {
    background: var(--latar);
    min-height: 100vh;
}
.wrapper {
    min-height: 100vh;
    display: flex;
}
/* SIDEBAR - mempertahankan desain Mille Pagine */
.sidebar {
    width: 220px;
    min-height: 100vh;
    background: linear-gradient(180deg,#5B2E0F 0%,#3F1E08 100%);
    padding: 22px 14px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
}
.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 4px 8px 20px;
    margin-bottom: 16px;
    border-bottom: 1px solid rgba(255,243,232,.15);
    color: var(--krem-terang);
    font-family: Georgia,serif;
    font-size: 20px;
    letter-spacing: .5px;
}
.logo-ikon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: var(--krem);
    color: var(--coklat);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    box-shadow: 0 6px 14px rgba(0,0,0,.3);
    flex-shrink: 0;
}
.sidebar-menu {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    color: rgba(255,243,232,.8);
    text-decoration: none;
    font-size: 14px;
    padding: 9px 10px;
    border-radius: 14px;
    transition: background .2s ease,transform .2s ease,color .2s ease;
}
.nav-item .ikon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: rgba(255,243,232,.08);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    flex-shrink: 0;
}
.nav-item:hover {
    background: rgba(255,243,232,.08);
    color: white;
    transform: translateX(4px);
}
.nav-item.active {
    background: var(--krem-terang);
    color: var(--coklat);
    font-weight: bold;
    box-shadow: 0 8px 18px rgba(0,0,0,.25);
}
.nav-item.active .ikon {
    background: var(--coklat);
    color: white;
}
.sidebar-bawah {
    margin-top: auto;
}
.bantuan {
    display: block;
    text-align: center;
    text-decoration: none;
    color: var(--krem-terang);
    background: rgba(255,243,232,.08);
    border: 1px solid rgba(255,243,232,.18);
    border-radius: 18px;
    padding: 18px 14px;
    font-size: 13px;
    line-height: 1.4;
}
.ikon-besar {
    width: 44px;
    height: 44px;
    margin: 0 auto 10px;
    border-radius: 50%;
    background: var(--emas);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}
.bantuan strong {
    display: block;
    font-size: 14px;
    margin-bottom: 2px;
}
.bantuan span {
    color: rgba(255,243,232,.7);
}
/* CONTENT */
.content {
    flex: 1;
    min-width: 0;
    padding: 30px;
    overflow-y: auto;
}
.header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 26px;
}
.back {
    width: 44px;
    height: 44px;
    border: 1px solid #E2CBB4;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--coklat);
    background: white;
    text-decoration: none;
}
.header h1 {
    color: var(--coklat-tua);
    font-family: Georgia,serif;
    font-size: 30px;
    margin-bottom: 5px;
}
.header p {
    color: var(--coklat-muda);
    font-size: 14px;
}
.layout {
    display: grid;
    grid-template-columns: minmax(0,1fr) 340px;
    gap: 24px;
    align-items: start;
}
.card {
    background: white;
    padding: 24px;
    border-radius: 20px;
    box-shadow: 0 6px 18px rgba(91,46,15,.07);
    margin-bottom: 20px;
}
.card h2 {
    font-family: Georgia,serif;
    color: var(--coklat-tua);
    font-size: 21px;
    margin-bottom: 18px;
}
.rekening {
    background: linear-gradient(125deg,#5B2E0F,#8B5428);
    color: white;
    border-radius: 18px;
    padding: 24px;
    margin-bottom: 18px;
}
.rekening small {
    color: #EBDDC7;
    font-size: 13px;
}
.rekening h3 {
    font-size: 25px;
    margin: 12px 0 6px;
    letter-spacing: 1px;
}
.rekening p {
    font-size: 14px;
    color: #FFF3E8;
}
.copy-btn {
    margin-top: 16px;
    padding: 9px 14px;
    border: 1px solid rgba(255,255,255,.4);
    border-radius: 9px;
    background: rgba(255,255,255,.12);
    color: white;
    cursor: pointer;
}
.info {
    background: #FFF8F0;
    border: 1px solid #EAD6BF;
    padding: 14px;
    border-radius: 12px;
    color: var(--coklat-muda);
    font-size: 13px;
    line-height: 1.6;
    margin-bottom: 18px;
}
.total {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    border-top: 1px dashed #D8C2AB;
    padding-top: 18px;
    margin-top: 18px;
}
.total span {
    color: var(--coklat-muda);
}
.total strong {
    color: var(--coklat);
    font-size: 22px;
}
.field {
    margin-bottom: 18px;
}
.field label {
    display: block;
    color: var(--coklat-tua);
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 9px;
}
.field input {
    width: 100%;
    padding: 12px;
    border: 1px solid #D8C2AB;
    border-radius: 10px;
    background: #FFFCF8;
    color: var(--coklat-tua);
}
.field input[type=file] {
    font-size: 13px;
}
.preview {
    width: 100%;
    max-height: 300px;
    object-fit: contain;
    display: none;
    margin-top: 12px;
    border-radius: 12px;
    background: var(--krem-terang);
}
.btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    width: 100%;
    padding: 14px;
    border: 0;
    border-radius: 999px;
    background: var(--coklat);
    color: white;
    text-decoration: none;
    font-size: 15px;
    font-weight: bold;
    cursor: pointer;
    transition: background .2s ease,transform .2s ease;
}
.btn:hover {
    background: #6D3815;
    transform: translateY(-2px);
}
.btn:disabled {
    opacity: .5;
    cursor: not-allowed;
    transform: none;
}
.notice {
    padding: 14px 16px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-size: 14px;
    line-height: 1.5;
}
.notice.info {
    background: #FFF3E8;
    border: 1px solid #E2CBB4;
}
.notice.success {
    background: #E7F5E9;
    border: 1px solid #B9DCC0;
    color: #216B35;
}
.notice.danger {
    background: #FDE8E8;
    border: 1px solid #F5C2C2;
    color: #9B1C1C;
}
.steps {
    list-style: none;
    display: grid;
    gap: 14px;
}
.steps li {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    color: var(--coklat-muda);
    font-size: 14px;
    line-height: 1.5;
}
.steps i {
    color: var(--emas);
    margin-top: 3px;
}
@media(max-width:1100px) {
    .layout {
        grid-template-columns: 1fr;
    }
}
@media(max-width:700px) {
    .sidebar {
        width: 72px;
        padding: 16px 8px;
    }
    .sidebar-brand {
        justify-content: center;
        padding: 4px 0 16px;
        font-size: 0;
    }
    .nav-item {
        justify-content: center;
        padding: 8px 0;
        font-size: 0;
        gap: 0;
    }
    .nav-item:hover {
        transform: none;
    }
    .bantuan {
        padding: 12px 4px;
    }
    .bantuan strong,
    .bantuan span {
        display: none;
    }
    .content {
        padding: 18px 14px;
    }
    .header h1 {
        font-size: 24px;
    }
    .card {
        padding: 18px;
    }
    .rekening h3 {
        font-size: 21px;
    }
}
</style>
<link rel="stylesheet" href="simple.css">
</head>

<body>
<div class="wrapper">

<nav class="sidebar">
    <div class="sidebar-brand">
        <div class="logo-ikon">
            <i class="fa-solid fa-book-open-reader"></i>
        </div>
        Mille Pagine
    </div>

    <div class="sidebar-menu">
        <a class="nav-item" href="index.php">
            <span class="ikon"><i class="fa-solid fa-house"></i></span>
            Beranda
        </a>

        <a class="nav-item active" href="keranjang.php">
            <span class="ikon"><i class="fa-solid fa-cart-shopping"></i></span>
            Keranjang
        </a>

        <a class="nav-item" href="tentang.php">
            <span class="ikon"><i class="fa-solid fa-circle-info"></i></span>
            Tentang Kita
        </a>

        <a class="nav-item" href="profil.php">
            <span class="ikon"><i class="fa-solid fa-user"></i></span>
            Profil
        </a>
    </div>

    <div class="sidebar-bawah">
        <a href="chat.php" class="bantuan">
            <div class="ikon-besar">
                <i class="fa-solid fa-headset"></i>
            </div>
            <strong>Butuh bantuan?</strong>
            <span>Hubungi Admin</span>
        </a>
    </div>
</nav>

<main class="content">

    <div class="header">
        <a href="profil.php" class="back" title="Kembali">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1>Pembayaran</h1>
            <p>Selesaikan pembayaran pesanan Mille Pagine kamu.</p>
        </div>
    </div>

    <?php if ($pesan !== ''): ?>
        <div class="notice info">
            <?= htmlspecialchars($pesan, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <div class="layout">

        <div>
            <section class="card">
                <h2><i class="fa-solid fa-building-columns"></i> Transfer Bank</h2>

                <div class="rekening">
                    <small>Bank tujuan</small>
                    <h3><?= htmlspecialchars($rekening['bank']); ?></h3>
                    <p>Atas nama <?= htmlspecialchars($rekening['pemilik']); ?></p>

                    <h3 id="nomorRekening">
                        <?= htmlspecialchars($rekening['nomor']); ?>
                    </h3>

                    <button class="copy-btn" type="button" onclick="salinRekening()">
                        <i class="fa-regular fa-copy"></i> Salin nomor rekening
                    </button>
                </div>

                <div class="total">
                    <span>Total yang harus dibayar</span>
                    <strong>
                        Rp <?= number_format((int)$pesanan['total'], 0, ',', '.'); ?>
                    </strong>
                </div>

                <div class="info">
                    <i class="fa-solid fa-circle-info"></i>
                    Transfer sesuai nominal tagihan. Setelah itu, unggah bukti
                    transfer yang jelas dan pastikan nominal serta penerimanya terlihat.
                </div>
            </section>

            <section class="card">
                <h2><i class="fa-solid fa-receipt"></i> Bukti Transfer</h2>

                <?php if ($boleh_upload): ?>
                    <form
                        action="upload_bukti_transfer.php"
                        method="POST"
                        enctype="multipart/form-data"
                        id="formBukti"
                    >
                        <input
                            type="hidden"
                            name="pesanan_id"
                            value="<?= (int)$pesanan['id']; ?>"
                        >

                        <div class="field">
                            <label for="bukti">
                                Pilih foto atau screenshot bukti transfer
                            </label>

                            <input
                                type="file"
                                name="bukti_transfer"
                                id="bukti"
                                accept=".jpg,.jpeg,.png,.webp"
                                required
                            >

                            <img
                                src=""
                                alt="Pratinjau bukti transfer"
                                id="preview"
                                class="preview"
                            >
                        </div>

                        <p style="color:#7B5A3D;font-size:12px;line-height:1.6;margin-bottom:18px">
                            Format JPG, PNG, atau WEBP. Maksimal 2 MB.
                            Bukti akan diperiksa admin sebelum pesanan diproses.
                        </p>

                        <button class="btn" type="submit">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            Kirim Bukti Transfer
                        </button>
                    </form>

                <?php elseif ($pesanan['status'] === 'Menunggu Verifikasi'): ?>
                    <div class="notice success">
                        <i class="fa-solid fa-clock"></i>
                        Bukti transfer sudah dikirim dan sedang menunggu verifikasi admin.
                    </div>

                    <?php if (!empty($pesanan['bukti_transfer'])): ?>
                        <p style="color:#7B5A3D;font-size:13px">
                            Bukti transfer sudah tersimpan di sistem.
                        </p>
                    <?php endif; ?>

                <?php elseif ($pesanan['status'] === 'Diproses'
                    || $pesanan['status'] === 'Dikirim'
                    || $pesanan['status'] === 'Selesai'): ?>

                    <div class="notice success">
                        <i class="fa-solid fa-circle-check"></i>
                        Pembayaran telah dikonfirmasi admin.
                    </div>

                <?php elseif ($pesanan['status'] === 'Dibatalkan'): ?>

                    <div class="notice danger">
                        <i class="fa-solid fa-circle-xmark"></i>
                        Pesanan ini telah dibatalkan.
                    </div>

                <?php else: ?>
                    <div class="notice info">
                        Status pembayaran:
                        <?= htmlspecialchars($pesanan['status_pembayaran'] ?? 'Belum Bayar'); ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>

        <aside>
            <section class="card">
                <h2>Detail Pesanan</h2>

                <div class="total" style="margin-top:0">
                    <span>Nomor pesanan</span>
                    <strong style="font-size:17px">
                        #<?= (int)$pesanan['id']; ?>
                    </strong>
                </div>

                <div class="total">
                    <span>Tanggal</span>
                    <strong style="font-size:13px">
                        <?= htmlspecialchars($pesanan['tanggal']); ?>
                    </strong>
                </div>

                <div class="total">
                    <span>Metode</span>
                    <strong style="font-size:14px">Transfer Bank</strong>
                </div>

                <div class="total">
                    <span>Status pesanan</span>
                    <strong style="font-size:13px">
                        <?= htmlspecialchars($pesanan['status']); ?>
                    </strong>
                </div>
            </section>

            <section class="card">
                <h2>Cara Pembayaran</h2>
                <ul class="steps">
                    <li>
                        <i class="fa-solid fa-circle-check"></i>
                        <span>Transfer ke rekening yang tercantum di halaman ini.</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-image"></i>
                        <span>Unggah bukti transfer sesuai nominal tagihan.</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-user-shield"></i>
                        <span>Tunggu admin memeriksa pembayaran.</span>
                    </li>
                    <li>
                        <i class="fa-solid fa-box"></i>
                        <span>Pesanan diproses setelah pembayaran dikonfirmasi.</span>
                    </li>
                </ul>
            </section>

            <a href="profil.php" class="btn">
                <i class="fa-solid fa-receipt"></i>
                Lihat Pesanan Saya
            </a>
        </aside>

    </div>
</main>
</div>

<script>
function salinRekening() {
    const nomor = document.getElementById('nomorRekening').textContent.trim();

    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(nomor)
            .then(() => alert('Nomor rekening berhasil disalin.'))
            .catch(() => prompt('Salin nomor rekening ini:', nomor));
    } else {
        prompt('Salin nomor rekening ini:', nomor);
    }
}

const inputBukti = document.getElementById('bukti');
const preview = document.getElementById('preview');

if (inputBukti && preview) {
    inputBukti.addEventListener('change', function() {
        const file = this.files[0];

        if (!file) {
            preview.style.display = 'none';
            preview.src = '';
            return;
        }

        const tipeValid = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!tipeValid.includes(file.type) || file.size > 2 * 1024 * 1024) {
            alert('Pilih gambar JPG, PNG, atau WEBP maksimal 2 MB.');
            this.value = '';
            preview.style.display = 'none';
            preview.src = '';
            return;
        }

        preview.src = URL.createObjectURL(file);
        preview.style.display = 'block';
    });
}
</script>
</body>
</html>
