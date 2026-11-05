<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: kelola_buku.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM buku WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if (!$data) {
    header("Location: kelola_buku.php");
    exit;
}

$kategori_list = mysqli_query($conn, "SELECT id, nama_kategori FROM kategori ORDER BY nama_kategori ASC");
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $penulis = trim($_POST['penulis'] ?? '');
    $harga = filter_input(INPUT_POST, 'harga', FILTER_VALIDATE_INT);
    $kategori_id = filter_input(INPUT_POST, 'kategori', FILTER_VALIDATE_INT);
    $gambar = $data['gambar'];

    $cek_kategori = mysqli_prepare($conn, "SELECT id FROM kategori WHERE id = ?");
    mysqli_stmt_bind_param($cek_kategori, "i", $kategori_id);
    mysqli_stmt_execute($cek_kategori);
    $kategori_valid = mysqli_fetch_assoc(mysqli_stmt_get_result($cek_kategori));

    if ($judul === '' || $penulis === '' || !$harga || $harga < 0 || !$kategori_valid) {
        $error = 'Lengkapi data buku dengan benar.';
    } else {
        if (!empty($_FILES['sampul']['name'])) {
            if ($_FILES['sampul']['error'] !== UPLOAD_ERR_OK) {
                $error = 'Gagal mengunggah gambar sampul.';
            } else {
                $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
                $mime = mime_content_type($_FILES['sampul']['tmp_name']);
                if (!isset($allowed[$mime])) {
                    $error = 'Format gambar harus JPG, PNG, atau WEBP.';
                } else {
                    if (!is_dir('uploads')) mkdir('uploads', 0775, true);
                    $nama_file = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
                    if (move_uploaded_file($_FILES['sampul']['tmp_name'], 'uploads/' . $nama_file)) {
                        $gambar = 'uploads/' . $nama_file;
                    } else {
                        $error = 'Gambar gagal disimpan.';
                    }
                }
            }
        }

        if ($error === '') {
            $update = mysqli_prepare($conn, "UPDATE buku SET judul = ?, penulis = ?, kategori_id = ?, harga = ?, gambar = ? WHERE id = ?");
            mysqli_stmt_bind_param($update, "ssiisi", $judul, $penulis, $kategori_id, $harga, $gambar, $id);
            // Bind parameter types explicitly (string, string, integer, integer, string, integer).
            mysqli_stmt_execute($update);
            if ($gambar !== $data['gambar'] && !empty($data['gambar']) && is_file($data['gambar'])) {
                @unlink($data['gambar']);
            }
            header("Location: kelola_buku.php");
            exit;
        }
    }
}

$judul = $data['judul'];
$penulis = $data['penulis'];
$harga = $data['harga'];
$sampul = $data['gambar'];
$kategori = (int)$data['kategori_id'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Buku - Mille Pagine</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="admin.css">
</head>
<body>

<div class="wrapper">

    <?php $halaman_aktif = 'buku'; include 'admin_sidebar.php'; ?>

    <div class="content">

        <div class="page-head anim" style="--i:0">
            <i class="fa-solid fa-pen-to-square deco"></i>
            <i class="fa-solid fa-feather-pointed deco2"></i>
            <div class="page-head-main">
                <a href="kelola_buku.php" class="back" title="Kembali"><i class="fa-solid fa-arrow-left"></i></a>
                <div>
                    <h1>Mengubah Buku</h1>
                    <p>Perbarui informasi buku atau ganti sampulnya.</p>
                </div>
            </div>
            <div class="page-head-actions">

            </div>
        </div>

        <?php if ($error !== ''): ?>
            <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="form-card anim" style="--i:1">

            <div class="form-group">
                <label>Judul Buku</label>
                <input type="text" name="judul" value="<?= htmlspecialchars($judul, ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label>Penulis</label>
                <input type="text" name="penulis" value="<?= htmlspecialchars($penulis, ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori" required>
                    <?php mysqli_data_seek($kategori_list, 0); while ($kat = mysqli_fetch_assoc($kategori_list)): ?>
                        <option value="<?= (int)$kat['id']; ?>" <?= $kategori === (int)$kat['id'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($kat['nama_kategori'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Harga</label>
                <input type="number" name="harga" value="<?= htmlspecialchars((string)$harga, ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="form-group">
                <label>Sampul Buku</label>

                <div class="upload-box" onclick="document.getElementById('sampulInput').click()">
                    <img src="<?= htmlspecialchars($sampul, ENT_QUOTES, 'UTF-8') ?>" alt="Sampul Buku" id="preview">

                    <div class="change">
                        <span class="change-icon"><i class="fa-solid fa-camera"></i></span>
                        Ganti Gambar
                    </div>

                    <input type="file" name="sampul" id="sampulInput" hidden onchange="previewGambar(this)">
                </div>
            </div>

            <button type="submit" class="save-btn"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>

        </form>

<script>
function previewGambar(input){
    if(input.files && input.files[0]){
        var reader = new FileReader();
        reader.onload = function(e){
            document.getElementById('preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

    </div>

</div>

<script src="admin.js"></script>
</body>
</html>
