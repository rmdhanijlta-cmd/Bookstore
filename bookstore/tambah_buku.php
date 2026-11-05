<?php
session_start();
include 'koneksi.php';
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $judul = $_POST['judul'];
    $penulis = $_POST['penulis'];
    $kategori_id = $_POST['kategori'];
    $harga = $_POST['harga'];
    $stok = 100;

    $nama_file = $_FILES['sampul']['name'];
    $tmp = $_FILES['sampul']['tmp_name'];

    move_uploaded_file($tmp, "uploads/".$nama_file);

    mysqli_query($conn, "
        INSERT INTO buku(
            kategori_id,
            judul,
            penulis,
            harga,
            stok,
            gambar
        ) VALUES(
            '$kategori_id',
            '$judul',
            '$penulis',
            '$harga',
            '$stok',
            'uploads/$nama_file'
        )
    ");

    header("Location: kelola_buku.php");
    exit;
}

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: login.php");
    exit;
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
