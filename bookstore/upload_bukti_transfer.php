
<?php
session_start();
include 'koneksi.php';

function kembali($id, $pesan) {
    $_SESSION['pesan_pembayaran'] = $pesan;
    header("Location: pembayaran.php?id=" . (int)$id);
    exit;
}

// Pastikan user sudah login.
if (!isset($_SESSION['id'])) {
    header("Location: landing.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: profil.php");
    exit;
}

$user_id = (int)$_SESSION['id'];
$pesanan_id = filter_input(
    INPUT_POST,
    'pesanan_id',
    FILTER_VALIDATE_INT
);

if (!$pesanan_id || $pesanan_id < 1) {
    header("Location: profil.php");
    exit;
}

// Pastikan pesanan milik user dan masih menunggu pembayaran.
$stmt = mysqli_prepare($conn, "
    SELECT id, metode_bayar, status, status_pembayaran
    FROM pesanan
    WHERE id = ? AND user_id = ?
    LIMIT 1
");

mysqli_stmt_bind_param($stmt, "ii", $pesanan_id, $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$pesanan = mysqli_fetch_assoc($result);

if (!$pesanan) {
    kembali($pesanan_id, "Pesanan tidak ditemukan.");
}

if (
    $pesanan['metode_bayar'] !== 'Transfer Bank' ||
    $pesanan['status'] !== 'Menunggu Pembayaran' ||
    $pesanan['status_pembayaran'] !== 'Belum Bayar'
) {
    kembali(
        $pesanan_id,
        "Pesanan ini tidak dapat mengunggah bukti transfer lagi."
    );
}

// Pastikan file benar-benar diunggah.
if (
    !isset($_FILES['bukti_transfer']) ||
    $_FILES['bukti_transfer']['error'] !== UPLOAD_ERR_OK
) {
    kembali($pesanan_id, "Upload gagal. Silakan pilih ulang file bukti transfer.");
}

$file = $_FILES['bukti_transfer'];

// Maksimal 2 MB.
$batas = 2 * 1024 * 1024;

if ($file['size'] <= 0 || $file['size'] > $batas) {
    kembali($pesanan_id, "Ukuran bukti transfer maksimal 2 MB.");
}

// Validasi jenis file berdasarkan isi file, bukan nama file saja.
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);

$tipe_diizinkan = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp'
];

if (!isset($tipe_diizinkan[$mime])) {
    kembali(
        $pesanan_id,
        "Format file harus JPG, PNG, atau WEBP."
    );
}

// Pastikan file gambar dapat dibaca sebagai gambar.
if (@getimagesize($file['tmp_name']) === false) {
    kembali($pesanan_id, "File yang diunggah bukan gambar yang valid.");
}

// Folder penyimpanan bukti transfer.
$folder = __DIR__ . '/uploads/bukti_transfer/';

if (!is_dir($folder)) {
    if (!mkdir($folder, 0755, true) && !is_dir($folder)) {
        kembali($pesanan_id, "Folder penyimpanan bukti gagal dibuat.");
    }
}

if (!is_writable($folder)) {
    kembali($pesanan_id, "Folder bukti transfer tidak bisa ditulisi.");
}

// Nama file acak untuk mencegah nama file bentrok.
$nama_file = bin2hex(random_bytes(16))
    . '.'
    . $tipe_diizinkan[$mime];

$tujuan = $folder . $nama_file;

// Simpan file ke folder.
if (!move_uploaded_file($file['tmp_name'], $tujuan)) {
    kembali($pesanan_id, "Bukti transfer gagal disimpan.");
}

// Simpan path relatif ke database.
$path_db = 'uploads/bukti_transfer/' . $nama_file;


try {
    mysqli_begin_transaction($conn);

    // Kunci baris pesanan dan periksa ulang agar tidak terjadi
    // unggahan ganda ketika dua request masuk bersamaan.
    $cek = mysqli_prepare($conn, "
        SELECT status, status_pembayaran, metode_bayar
        FROM pesanan
        WHERE id = ? AND user_id = ?
        FOR UPDATE
    ");

    mysqli_stmt_bind_param($cek, "ii", $pesanan_id, $user_id);
    mysqli_stmt_execute($cek);

    $hasil_cek = mysqli_stmt_get_result($cek);
    $data_cek = mysqli_fetch_assoc($hasil_cek);

    if (
        !$data_cek ||
        $data_cek['metode_bayar'] !== 'Transfer Bank' ||
        $data_cek['status'] !== 'Menunggu Pembayaran' ||
        $data_cek['status_pembayaran'] !== 'Belum Bayar'
    ) {
        throw new Exception(
            "Status pesanan sudah berubah. Bukti tidak dapat dikirim."
        );
    }

    $update = mysqli_prepare($conn, "
        UPDATE pesanan
        SET bukti_transfer = ?,
            status = 'Menunggu Verifikasi',
            status_pembayaran = 'Menunggu Verifikasi'
        WHERE id = ? AND user_id = ?
    ");

    mysqli_stmt_bind_param(
        $update,
        "sii",
        $path_db,
        $pesanan_id,
        $user_id
    );

    if (!mysqli_stmt_execute($update)) {
        throw new Exception("Gagal memperbarui data pembayaran.");
    }

    mysqli_commit($conn);

} catch (Throwable $e) {
    mysqli_rollback($conn);

    // Hapus file jika database gagal menyimpan.
    if (is_file($tujuan)) {
        unlink($tujuan);
    }

    error_log($e->getMessage());

    kembali(
        $pesanan_id,
        "Bukti transfer gagal diproses. Silakan coba lagi."
    );
}

kembali(
    $pesanan_id,
    "Bukti transfer berhasil dikirim. Silakan tunggu verifikasi admin."
);
?>
