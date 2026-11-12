<?php
session_name('MILLE_ADMIN');
session_start();
include 'koneksi.php';

$kategori = mysqli_query($conn, "
    SELECT * FROM kategori
    ORDER BY id ASC
");

$id = $_GET['id']; 

$data = mysqli_fetch_assoc(
     mysqli_query($conn, "SELECT * FROM kategori WHERE id='$id'") ); 
     
     if(!$data){
         header("Location: kelola_kategori.php"); 
         exit; 
    } 
    
    if(isset($_POST['simpan'])){
        $baru = trim($_POST['kategori_baru']); 
        if($baru != ''){ 
            mysqli_query($conn, 
            "UPDATE kategori 
            SET nama_kategori='$baru' 
            WHERE id='$id'" ); 
            
            header("Location: kelola_kategori.php");
             exit; 
        } 
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
<title>Ubah Kategori - Mille Pagine</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="admin.css">
<link rel="stylesheet" href="admin-fix.css">
</head>
<body>

<div class="wrapper">

    <?php $halaman_aktif = 'kategori'; include 'admin_sidebar.php'; ?>

    <div class="content">

        <div class="page-head anim" style="--i:0">
            <i class="fa-solid fa-table-list deco"></i>
            <i class="fa-solid fa-feather-pointed deco2"></i>
            <div class="page-head-main">
                <a href="kelola_kategori.php" class="back" title="Kembali"><i class="fa-solid fa-arrow-left"></i></a>
                <div>
                    <h1>Kelola Kategori</h1>
                    <p>Atur kategori supaya buku mudah ditemukan pembaca.</p>
                </div>
            </div>
            <div class="page-head-actions">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Cari kategori..." data-filter="#daftar">
                </div>
                <a href="tambah kategori.php" class="btn btn-emas"><i class="fa-solid fa-plus"></i> Tambah Kategori</a>
            </div>
        </div>

        <div class="list" id="daftar">

        <?php $no = 0; while($k = mysqli_fetch_assoc($kategori)): $no++; ?>

            <div class="item anim" style="--i:<?= min($no, 8); ?>" data-cari="<?= htmlspecialchars($k['nama_kategori']); ?>">

                <div class="item-left">
                    <div class="kategori-ikon"><i class="fa-solid fa-layer-group"></i></div>
                    <h2 class="judul-kategori"><?= htmlspecialchars($k['nama_kategori']); ?></h2>
                </div>

                <div class="item-right">
                    <a href="edit kategori.php?id=<?= (int)$k['id']; ?>" class="btn btn-edit">
                        <i class="fa-solid fa-pen"></i> Ubah
                    </a>

                    <a href="hapus_kategori.php?id=<?= (int)$k['id']; ?>" class="btn btn-delete"
                       onclick="return confirm('Hapus kategori ini?')">
                        <i class="fa-solid fa-trash"></i> Hapus
                    </a>
                </div>

            </div>

        <?php endwhile; ?>

        </div>

        <!-- MODAL UBAH -->
        <div class="modal">
            <form method="POST">
                <div class="modal-box">
                    <div class="modal-ikon"><i class="fa-solid fa-pen-to-square"></i></div>
                    <h2>Ubah Kategori</h2>

                    <div class="field">
                        <label>Kategori Lama</label>
                        <input type="text" value="<?= htmlspecialchars($data['nama_kategori']); ?>" readonly>
                    </div>

                    <div class="field">
                        <label>Kategori Baru</label>
                        <input type="text" name="kategori_baru" placeholder="Masukkan kategori baru"
                               value="<?= htmlspecialchars($data['nama_kategori']); ?>" autofocus>
                    </div>

                    <div class="buttons">
                        <button type="submit" class="save" name="simpan">SIMPAN</button>
                        <a href="kelola_kategori.php" class="cancel">BATAL</a>
                    </div>
                </div>
            </form>
        </div>

    </div>

</div>

<script src="admin.js"></script>
</body>
</html>
