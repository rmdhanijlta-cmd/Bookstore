<?php
session_name('MILLE_ADMIN');
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $penulis = trim($_POST['penulis'] ?? '');
    $kategori_id = filter_input(INPUT_POST, 'kategori', FILTER_VALIDATE_INT);
    $harga = filter_input(INPUT_POST, 'harga', FILTER_VALIDATE_INT);
    $stok = filter_input(INPUT_POST, 'stok', FILTER_VALIDATE_INT);

    if ($judul === '' || $penulis === '' || !$kategori_id || $harga === false || $harga < 0 || $stok === false || $stok < 0) {
        $error = 'Lengkapi semua data dengan benar. Stok awal boleh 0 atau lebih.';
    } elseif (!isset($_FILES['sampul']) || $_FILES['sampul']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Pilih gambar sampul buku terlebih dahulu.';
    } else {
        $cekKat = mysqli_prepare($conn, "SELECT id FROM kategori WHERE id = ?");
        mysqli_stmt_bind_param($cekKat, "i", $kategori_id);
        mysqli_stmt_execute($cekKat);
        if (!mysqli_fetch_assoc(mysqli_stmt_get_result($cekKat))) {
            $error = 'Kategori tidak ditemukan.';
        } else {
            $allowed = ['image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp'];
            $mime = mime_content_type($_FILES['sampul']['tmp_name']);
            if (!isset($allowed[$mime])) {
                $error = 'Format sampul harus JPG, PNG, atau WEBP.';
            } else {
                if (!is_dir('uploads')) mkdir('uploads', 0775, true);
                $nama_file = bin2hex(random_bytes(12)) . '.' . $allowed[$mime];
                $path = 'uploads/' . $nama_file;
                if (!move_uploaded_file($_FILES['sampul']['tmp_name'], $path)) {
                    $error = 'Gagal menyimpan gambar sampul.';
                } else {
                    $stmt = mysqli_prepare($conn, "INSERT INTO buku (kategori_id, judul, penulis, harga, stok, gambar) VALUES (?, ?, ?, ?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, "issiis", $kategori_id, $judul, $penulis, $harga, $stok, $path);
                    if (mysqli_stmt_execute($stmt)) {
                        header("Location: kelola_buku.php?pesan=tambah");
                        exit;
                    }
                    @unlink($path);
                    $error = 'Buku gagal ditambahkan. Periksa kembali data database.';
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tambah Buku - Mille Pagine</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="admin.css">
<link rel="stylesheet" href="admin-fix.css">
</head>
<body>

<div class="wrapper">

    <?php $halaman_aktif = 'buku'; include 'admin_sidebar.php'; ?>

    <div class="content">

        <div class="page-head anim" style="--i:0">
            <i class="fa-solid fa-circle-plus deco"></i>
            <i class="fa-solid fa-feather-pointed deco2"></i>
            <div class="page-head-main">
                <a href="kelola_buku.php" class="back" title="Kembali"><i class="fa-solid fa-arrow-left"></i></a>
                <div>
                    <h1>Tambah Buku</h1>
                    <p>Isi data buku baru lalu unggah sampulnya.</p>
                </div>
            </div>
            <div class="page-head-actions">

            </div>
        </div>

        <?php if ($error !== ''): ?><div class="alert alert-error"><?= htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="POST" enctype="multipart/form-data" class="form-card anim" style="--i:1">

            <div class="form-group">
                <label>Judul Buku</label>
                <input type="text" name="judul" placeholder="Masukkan judul buku">
            </div>

            <div class="form-group">
                <label>Penulis</label>
                <input type="text" name="penulis" placeholder="Nama penulis">
            </div>

            <div class="form-group">
                <label>Kategori</label>
                <select name="kategori" required>
                    <option value="">Pilih Kategori</option>
                    <?php
                    $kat = mysqli_query($conn, "SELECT * FROM kategori");
                    while($k = mysqli_fetch_assoc($kat)):
                    ?>
                        <option value="<?= (int)$k['id']; ?>"><?= htmlspecialchars($k['nama_kategori']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Harga</label>
                <input type="number" name="harga" placeholder="Contoh: 75000">
            </div>

            <div class="form-group">
                <label>Stok Awal</label>
                <input type="number" name="stok" min="0" step="1" value="0" required>
                <small>Masukkan jumlah buku yang tersedia saat pertama kali ditambahkan.</small>
            </div>

            <div class="form-group">
                <label>Unggah Sampul Buku</label>

                <div class="upload-box" onclick="document.getElementById('sampul').click()">
                    <img id="preview" src="" alt="Preview Sampul" style="display:none;">
                    <div class="upload-icon" id="uploadIcon"><i class="fa-solid fa-plus"></i></div>
                    <div id="namaFile">Pilih Gambar</div>
                    <input type="file" id="sampul" name="sampul" accept="image/*" required
                           style="display:none;" onchange="tampilNama(this)">
                </div>
            </div>

            <button type="submit" class="save-btn"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>

        </form>

<script>
function tampilNama(input){
    if(input.files && input.files[0]){
        var file = input.files[0];
        var preview = document.getElementById('preview');
        var icon = document.getElementById('uploadIcon');
        var namaFile = document.getElementById('namaFile');
        namaFile.innerText = file.name;
        var reader = new FileReader();
        reader.onload = function(e){
            preview.src = e.target.result;
            preview.style.display = "block";
            icon.style.display = "none";
            namaFile.style.display = "none";
        };
        reader.readAsDataURL(file);
    }
}
</script>

    </div>

</div>

<script src="admin.js"></script>
</body>
</html>
