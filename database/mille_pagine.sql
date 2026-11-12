-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 12 Nov 2026 pada 12.50
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mille_pagine`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `buku`
--

CREATE TABLE `buku` (
  `id` int(11) NOT NULL,
  `kategori_id` int(11) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `penulis` varchar(100) NOT NULL,
  `harga` int(11) NOT NULL,
  `stok` int(11) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `link_sinopsis` varchar(100) DEFAULT NULL,
  `harga_modal` int(11) NOT NULL DEFAULT 0,
  `rating` decimal(2,1) DEFAULT 5.0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `buku`
--

INSERT INTO `buku` (`id`, `kategori_id`, `judul`, `penulis`, `harga`, `stok`, `gambar`, `deskripsi`, `link_sinopsis`, `harga_modal`, `rating`) VALUES
(1, 1, 'Aroma Karsa', 'Dee Lestari', 70000, 148, 'aroma.png', 'Jati Wesi memiliki kemampuan luar biasa dalam mengenali berbagai aroma, sebuah bakat yang membawanya bertemu dengan Tanaya Suma dan terlibat dalam pencarian bunga mistis bernama Puspa Karsa. Di balik perjalanan tersebut, mereka harus menghadapi rahasia keluarga, ambisi, dan kekuatan aroma yang mampu memengaruhi keinginan manusia.', 'detail_buku.php', 49000, 5.0),
(2, 2, 'Atomic Habits', 'James Clear', 150000, 148, 'atomic.png', 'Atomic Habits menjelaskan bagaimana perubahan kecil yang dilakukan secara konsisten dapat membawa hasil besar dalam hidup. James Clear membahas cara membangun kebiasaan baik, menghentikan kebiasaan buruk, dan menciptakan sistem sederhana yang membantu seseorang mencapai tujuan secara bertahap dan berkelanjutan.', 'detail_buku.php', 105000, 5.0),
(3, 3, 'Diary of a Wimpy Kid', 'Jeff Kinney', 75000, 63, 'diary.png', 'Diary of a Wimpy Kid menceritakan kehidupan Greg Heffley, seorang anak sekolah yang mencoba bertahan menghadapi berbagai masalah sehari-hari, mulai dari pertemanan, keluarga, hingga kekacauan di sekolah. Dengan gaya seperti buku harian dan ilustrasi lucu, cerita ini penuh humor dan menggambarkan suka duka masa remaja dengan cara yang ringan dan menghibur.', 'detail_buku.php', 52500, 5.0),
(4, 2, 'Factfulness', 'Hans Rosling', 210000, 95, 'fact.png', 'Factfulness mengajak pembaca melihat dunia berdasarkan data dan fakta, bukan asumsi atau ketakutan yang berlebihan. Hans Rosling menjelaskan bagaimana banyak pandangan kita tentang kemiskinan, kesehatan, pendidikan, dan perkembangan dunia sering kali keliru, serta menunjukkan cara berpikir yang lebih tenang, rasional, dan akurat.', 'detail_buku.php', 147000, 5.0),
(5, 1, 'Better Than The Movie', 'Lynn Painter', 100000, 150, 'better than.png', 'Enam musim panas tak terpisahkan di tepi danau hancur seketika akibat satu kesalahan fatal. Setelah lima tahun saling asing, Percy akhirnya kembali dan dipertemukan lagi dengan Sam. Pertemuan ini memaksa mereka menghadapi perasaan mendalam yang belum usai di antara mereka. Kini mereka harus mencari tahu apakah cinta patah itu masih bisa diselamatkan.', 'detail_buku.php', 70000, 5.0),
(6, 3, 'Geronimo Stilton', 'Elisabetta Dami', 120000, 87, 'geronimo.png', 'Geronimo Stilton mengisahkan petualangan seekor tikus bernama Geronimo, seorang editor surat kabar yang sering terlibat dalam kejadian lucu, menegangkan, dan penuh misteri. Bersama keluarga dan teman-temannya, ia menghadapi berbagai tantangan sambil belajar tentang keberanian, persahabatan, dan pentingnya membantu orang lain.', 'detail_buku.php', 84000, 5.0),
(7, 3, 'Goodnight Moon', 'Margaret Wise Brown', 48000, 50, 'morning.png', 'Goodnight Moon menceritakan seekor kelinci kecil yang bersiap tidur dengan mengucapkan selamat malam kepada benda-benda di sekitarnya, seperti bulan, bintang, dan mainannya. Dengan bahasa yang sederhana dan menenangkan, buku karya Margaret Wise Brown ini menggambarkan suasana hangat sebelum tidur dan memberikan rasa nyaman bagi anak-anak.', 'detail_buku.php', 33600, 5.0),
(8, 1, 'It Ends With Us', 'Colleen Hoover', 150000, 150, 'it ends .png', 'Lily Bloom memulai hidup baru di Boston dan jatuh cinta pada Ryle Kincaid, seorang dokter bedah saraf yang tampan dan sukses. Namun, ketika cinta pertamanya, Atlas Corrigan, kembali hadir, Lily mulai menghadapi kenyataan pahit tentang hubungannya dan harus memilih antara mempertahankan cinta atau menghentikan siklus luka yang terus berulang.', 'detail_buku.php', 105000, 5.0),
(9, 1, 'Lotus in the Mud', 'Anniele', 90000, 150, 'lotus.png', ' Lotus in the Mud mengisahkan perjalanan seorang tokoh yang harus bertahan di tengah kesulitan, luka masa lalu, dan tekanan hidup yang berat. Seperti bunga lotus yang tumbuh di lumpur, cerita ini menggambarkan proses bangkit, menemukan harapan, dan mencapai kekuatan diri meski berada dalam keadaan yang tidak sempurna.', 'detail_buku.php', 63000, 5.0),
(10, 3, 'Matilda', 'Roald Dahl', 177000, 97, 'matilda.png', 'Matilda menceritakan seorang gadis kecil yang sangat cerdas dan gemar membaca, tetapi kurang mendapat perhatian dari keluarganya. Di sekolah, ia menghadapi kepala sekolah yang kejam, namun dengan keberanian, kecerdasan, dan kekuatan luar biasa yang dimilikinya, Matilda belajar membela dirinya dan membantu orang-orang yang baik di sekitarnya.', 'detail_buku.php', 123900, 5.0),
(11, 1, 'Messy Little Hearts', 'Dev Kane', 105000, 150, 'messy.png', 'Messy Little Hearts menceritakan tentang perasaan cinta, persahabatan, dan pencarian jati diri yang penuh kekacauan. Di tengah hubungan yang rumit dan emosi yang tidak menentu, para tokohnya belajar bahwa hati yang berantakan pun tetap bisa tumbuh, memaafkan, dan menemukan tempat untuk pulang.', 'detail_buku.php', 73500, 5.0),
(12, 2, 'Self Driving', 'Rhenald Kasali', 85000, 50, 'selfd.png', 'Self Driving membahas pentingnya menjadi pengendali utama dalam hidup dan karier di tengah perubahan zaman yang cepat. Rhenald Kasali menjelaskan bahwa setiap orang perlu memiliki inisiatif, disiplin, kemampuan beradaptasi, dan pola pikir berkembang agar tidak hanya mengikuti arus, tetapi mampu menentukan arah hidupnya sendiri.', 'detail_buku.php', 59500, 5.0),
(13, 1, 'Seporsi Mie Ayam Sebelum Besok Mati', 'Brian Khrisna', 93000, 100, 'mie.png', ' Ale, seorang pemuda yang hidupnya terasa biasa saja, tiba-tiba mengetahui bahwa waktunya tidak akan lama lagi. Di tengah ketakutan dan penyesalan, ia mulai menghargai hal-hal sederhana dalam hidup, termasuk keinginannya menikmati semangkuk mie ayam, sambil menemukan makna tentang keluarga, persahabatan, dan kesempatan terakhir untuk hidup dengan tulus.', 'detail_buku.php', 65100, 5.0),
(14, 2, 'Surrounded by Idiots', 'Thomas Erikson', 170000, 150, 'idiot.png', 'Surrounded by Idiots membahas bagaimana perbedaan kepribadian memengaruhi cara orang berpikir, berkomunikasi, dan bekerja sama. Thomas Erikson menjelaskan empat tipe perilaku utama dan membantu pembaca memahami orang lain dengan lebih baik agar hubungan, komunikasi, dan kerja tim menjadi lebih efektif dan minim kesalahpahaman.', 'detail_buku.php', 119000, 5.0),
(15, 1, 'The Alchemist', 'Paulo Coelho', 150000, 150, 'alkemis.png', 'Santiago, seorang penggembala muda dari Spanyol, memulai perjalanan mencari harta karun yang muncul dalam mimpinya. Dalam petualangannya, ia bertemu berbagai orang yang mengajarkannya tentang keberanian, takdir, dan pentingnya mengikuti impian serta mendengarkan suara hati sendiri.', 'detail_buku.php', 105000, 5.0),
(16, 2, 'The Alpha Girl’s Guide', 'Henry Manampiring', 109000, 47, 'alpager.png', 'The Alpha Girl\'s Guide membahas tentang pengembangan diri perempuan agar lebih percaya diri, mandiri, dan berani mengambil keputusan dalam pendidikan, karier, maupun kehidupan pribadi. Dengan gaya ringan dan inspiratif, buku ini mengajak pembaca mengenali potensi diri, membangun kebiasaan positif, serta menjadi versi terbaik dari dirinya sendiri tanpa harus kehilangan jati diri.', 'detail_buku.php', 76300, 5.0),
(17, 12, 'The Midnight Library', 'Matt Haig', 160000, 87, 'the midnight.png', 'Nora Seed merasa hidupnya penuh penyesalan dan kehilangan arah, hingga ia menemukan The Midnight Library, sebuah tempat di antara hidup dan mati yang berisi berbagai kemungkinan kehidupan yang bisa ia jalani. Melalui perjalanan tersebut, Nora belajar memahami pilihan, penyesalan, dan arti sebenarnya dari kehidupan yang berharga.', 'detail_buku.php', 112000, 5.0),
(18, 2, 'The Psychology of Money', 'Morgan Housel', 170000, 150, 'maniey.png', 'The Psychology of Money menjelaskan bahwa keberhasilan dalam mengelola keuangan tidak hanya bergantung pada pengetahuan tentang uang, tetapi juga pada perilaku, kebiasaan, dan cara berpikir seseorang. Morgan Housel menunjukkan bagaimana kesabaran, pengendalian diri, dan keputusan sederhana yang konsisten dapat memberikan dampak besar terhadap kondisi keuangan jangka panjang.', 'detail_buku.php', 119000, 5.0),
(19, 3, 'The Secret Garden', 'Frances', 80000, 89, 'sikrit.png', ' The Secret Garden menceritakan Mary Lennox, seorang gadis pemurung yang pindah ke rumah pamannya yang besar dan penuh misteri. Ketika ia menemukan taman rahasia yang telah lama terkunci, hidupnya perlahan berubah, dan ia belajar tentang persahabatan, harapan, serta kekuatan alam yang mampu menyembuhkan hati dan jiwa.', 'detail_buku.php', 56000, 5.0),
(20, 3, 'Winnie-the-Pooh', 'A.A.Milne', 142000, 87, 'winni.png', 'Winnie-the-Pooh menceritakan petualangan beruang kecil bernama Pooh bersama teman-temannya di Hutan Seratus Ekar. Dengan cerita yang hangat dan sederhana, buku ini menggambarkan persahabatan, kebaikan, imajinasi, dan kebahagiaan dalam hal-hal kecil sehari-hari.', 'detail_buku.php', 99400, 5.0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `chat`
--

CREATE TABLE `chat` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `pengirim` enum('user','admin') NOT NULL,
  `pesan` text NOT NULL,
  `waktu` datetime DEFAULT current_timestamp(),
  `status` enum('terbaca','belum') DEFAULT 'belum'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id` int(11) NOT NULL,
  `pesanan_id` int(11) DEFAULT NULL,
  `buku_id` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `harga` int(11) DEFAULT NULL,
  `modal` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id`, `pesanan_id`, `buku_id`, `jumlah`, `harga`, `modal`) VALUES
(50, 49, 1, 1, 70000, 0),
(52, 51, 3, 2, 75000, 0),
(53, 51, 4, 1, 210000, 0),
(54, 52, 16, 1, 109000, 0),
(55, 53, 4, 1, 210000, 0),
(56, 54, 16, 1, 109000, 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id`, `nama_kategori`) VALUES
(1, 'Novel'),
(2, 'Pendidikan'),
(3, 'Anak-Anak');

-- --------------------------------------------------------

--
-- Struktur dari tabel `keranjang`
--

CREATE TABLE `keranjang` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `buku_id` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesanan`
--

CREATE TABLE `pesanan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `tanggal` datetime DEFAULT current_timestamp(),
  `total` int(11) DEFAULT NULL,
  `metode_pembayaran` varchar(20) DEFAULT NULL,
  `status` enum('Menunggu Pembayaran','Menunggu Verifikasi','Diproses','Dikirim','Selesai','Dibatalkan') DEFAULT 'Menunggu Pembayaran',
  `metode_bayar` varchar(20) NOT NULL DEFAULT 'QRIS',
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `status_pembayaran` varchar(30) NOT NULL DEFAULT 'Belum Bayar',
  `alasan_pembatalan` text DEFAULT NULL,
  `ongkir` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pesanan`
--

INSERT INTO `pesanan` (`id`, `user_id`, `tanggal`, `total`, `metode_pembayaran`, `status`, `metode_bayar`, `bukti_transfer`, `status_pembayaran`, `alasan_pembatalan`, `ongkir`) VALUES
(49, 5, '2026-11-09 14:15:05', 70000, 'Transfer Bank', 'Selesai', 'Transfer Bank', 'uploads/bukti_transfer/8e7788832981336d4864acb380c2d7ba.jpg', 'Lunas', NULL, 0),
(51, 5, '2026-11-09 14:27:19', 370000, 'Transfer Bank', 'Dikirim', 'Transfer Bank', 'uploads/bukti_transfer/feb407534acf6debe59a5b8d2a989363.jpg', 'Lunas', NULL, 10000),
(52, 5, '2026-11-09 15:06:03', 119000, 'Transfer Bank', 'Diproses', 'Transfer Bank', 'uploads/bukti_transfer/a5423854fc4f61c7a320aa0165fba4da.jpg', 'Lunas', NULL, 10000),
(53, 5, '2026-11-09 15:12:32', 220000, 'Transfer Bank', 'Menunggu Pembayaran', 'Transfer Bank', NULL, 'Belum Bayar', NULL, 10000),
(54, 5, '2026-11-09 15:12:54', 119000, 'Transfer Bank', 'Menunggu Verifikasi', 'Transfer Bank', 'uploads/bukti_transfer/bc35aff129fd0cf63f62f48c5386d80b.jpg', 'Menunggu Verifikasi', NULL, 10000);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `telepon` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `alamat`, `telepon`, `password`, `role`) VALUES
(1, 'Admin', 'admin@bookstore.com', 'Jakarta', '08123456789', '112233', 'admin'),
(5, 'ijel', 'ijel@gmail.com', 'jl.katedrall, no 210, rt 009/ rw 006', '09876543456', '9999', 'user');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `buku`
--
ALTER TABLE `buku`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `chat`
--
ALTER TABLE `chat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT untuk tabel `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
