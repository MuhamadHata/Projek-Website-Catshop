-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 10, 2025 at 07:43 AM
-- Server version: 8.0.30
-- PHP Version: 8.3.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `catshop`
--

-- --------------------------------------------------------

--
-- Table structure for table `grooming`
--

CREATE TABLE `grooming` (
  `ID_Grooming` int NOT NULL,
  `tempat_grooming` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jumlah_kucing` int DEFAULT NULL,
  `tipe_grooming` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `harga_grooming` decimal(10,2) DEFAULT NULL,
  `tanggal_grooming` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grooming`
--

INSERT INTO `grooming` (`ID_Grooming`, `tempat_grooming`, `jumlah_kucing`, `tipe_grooming`, `harga_grooming`, `tanggal_grooming`) VALUES
(1, 'toko', 2, 'sultan', 220000.00, '2025-06-03'),
(2, 'rumah', 2, 'normal', 200000.00, '2025-06-28'),
(3, 'rumah', 3, 'hemat', 190000.00, '2025-06-06'),
(4, 'toko', 1, 'hemat', 50000.00, '2025-06-03'),
(5, 'toko', 1, 'sultan', 110000.00, '2025-06-03'),
(6, 'toko', 1, NULL, 50000.00, '2025-06-03'),
(7, 'rumah', 3, NULL, 280000.00, '2025-06-27'),
(8, 'toko', 1, NULL, 50000.00, '2025-06-04'),
(9, 'toko', 4, NULL, 440000.00, '2025-06-04'),
(10, 'toko', 1, 'hemat', 50000.00, '2025-06-13'),
(11, 'toko', 1, 'hemat', 50000.00, '2025-06-05'),
(12, 'rumah', 2, 'sultan', 260000.00, '2025-06-05'),
(13, 'rumah', 10, 'normal', 840000.00, '2025-06-07'),
(14, 'toko', 1, 'normal', 80000.00, '2025-06-05'),
(15, 'rumah', 1, 'hemat', 90000.00, '2025-06-10');

-- --------------------------------------------------------

--
-- Table structure for table `keranjang`
--

CREATE TABLE `keranjang` (
  `ID_Keranjang` int NOT NULL,
  `user_id` int NOT NULL,
  `produk_id` int NOT NULL,
  `jumlah` int NOT NULL DEFAULT '1',
  `ditambahkan_pada` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `keranjang`
--

INSERT INTO `keranjang` (`ID_Keranjang`, `user_id`, `produk_id`, `jumlah`, `ditambahkan_pada`) VALUES
(52, 15, 5, 1, '2025-06-05 00:26:52'),
(53, 15, 9, 1, '2025-06-05 00:27:02');

-- --------------------------------------------------------

--
-- Table structure for table `penitipan`
--

CREATE TABLE `penitipan` (
  `ID_Penitipan` int NOT NULL,
  `lama_penitipan_hari` int DEFAULT NULL COMMENT 'Lama penitipan dalam hari',
  `jumlah_kucing` int DEFAULT NULL,
  `nama_obat_harian` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Nama obat jika menggunakan_obat_harian = 1',
  `keterangan_penggunaan_obat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT 'Keterangan penggunaan obat',
  `jumlah_kucing_diberi_obat` int DEFAULT NULL COMMENT 'Jumlah kucing yang diberi obat harian',
  `harga_penitipan` decimal(10,2) DEFAULT NULL,
  `tanggal_penitipan` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penitipan`
--

INSERT INTO `penitipan` (`ID_Penitipan`, `lama_penitipan_hari`, `jumlah_kucing`, `nama_obat_harian`, `keterangan_penggunaan_obat`, `jumlah_kucing_diberi_obat`, `harga_penitipan`, `tanggal_penitipan`) VALUES
(1, 1, 1, NULL, NULL, 0, 30000.00, '2025-06-03'),
(2, 1, 2, 'Obat kutu', 'sehari sekali saja buat sikuning', 1, 65000.00, '2025-07-04'),
(3, 1, 1, 'obat benjol', '1 kali sehari', 1, 35000.00, '2025-06-03'),
(4, 2, 2, 'cek', 'ce1', 2, 140000.00, '2025-06-19'),
(5, 1, 1, 'sss', 'sss', 1, 35000.00, '2025-06-26'),
(6, 1, 1, NULL, NULL, 0, 30000.00, '2025-06-04'),
(7, 1, 1, '31234', '412412', 1, 35000.00, '2025-06-27'),
(8, 1, 1, 'da', 'sadad', 1, 35000.00, '2025-06-08'),
(9, 1, 1, NULL, NULL, 0, 30000.00, '2025-06-08'),
(10, 1, 1, 'sfa', 'lasmfa', 1, 35000.00, '2025-06-27'),
(11, 1, 1, 'dasd', 'sfafs', 1, 35000.00, '2025-06-10');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `ID_Produk` int NOT NULL,
  `nama_produk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `detail_produk` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `stok_produk` int DEFAULT '0',
  `gambar_produk` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Path atau URL ke gambar produk',
  `kategori_produk` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `harga_produk` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`ID_Produk`, `nama_produk`, `detail_produk`, `stok_produk`, `gambar_produk`, `kategori_produk`, `harga_produk`) VALUES
(1, 'Whiskas® Kering Rasa Tuna', 'Makanan kucing kering lengkap dan seimbang dengan rasa tuna yang disukai kucing, kaya akan nutrisi esensial untuk kesehatan dan energi.', 8, 'Whiskas kering.png', 'Makanan', 100.00),
(2, 'Mainan Kucing', 'Berbagai mainan interaktif untuk kucing Anda, membantu menjaga kebugaran dan menghilangkan kebosanan. Tersedia dalam berbagai bentuk dan warna.', 8, 'Mainan kucing.png', 'Aksesoris', 25000.00),
(3, 'Whiskas® Pouch Dewasa', 'Makanan basah untuk kucing dewasa dalam kemasan pouch praktis. Memberikan nutrisi lengkap dan hidrasi yang baik, dengan berbagai varian rasa lezat.', 10, 'Whiskas Pouch Dewasa.png', 'Makanan', 5500.00),
(4, 'Tempat Tidur Kucing', 'Tempat tidur yang nyaman dan empuk untuk kucing Anda beristirahat. Desain ergonomis memastikan kucing Anda merasa aman dan hangat.', 10, 'tempat tidur kucing.png', 'Furniture', 20500.00),
(5, 'Kalung Kucing', 'Kalung stylish dan aman untuk kucing, dilengkapi dengan lonceng kecil. Terbuat dari bahan berkualitas tinggi yang tidak mengiritasi kulit.', 4, 'kalung kucing.png', 'Aksesoris', 35000.00),
(6, 'Mangkuk Kucing', 'Mangkuk makanan atau minuman anti-slip dengan desain modern. Mudah dibersihkan dan terbuat dari bahan food-grade yang aman untuk hewan peliharaan Anda.', 6, 'mangkuk kucing.png', 'Aksesoris', 45000.00),
(7, 'Litter Box', 'Litter box yang mudah dibersihkan, didesain untuk kenyamanan kucing Anda. Memiliki dinding tinggi untuk mencegah pasir bertebaran.', 0, 'litter box.png', 'Perlengkapan', 30000.00),
(8, 'Obat Flu Kucing', 'Obat flu khusus kucing yang efektif meredakan gejala flu seperti bersin dan pilek. Pastikan berkonsultasi dengan dokter hewan sebelum penggunaan.', 8, 'obat flu.png', 'Kesehatan', 30000.00),
(9, 'Obat Diare Kucing', 'Obat diare yang diformulasikan khusus untuk kucing, membantu mengatasi masalah pencernaan dan mengembalikan kesehatan usus. Selalu gunakan sesuai petunjuk dokter hewan.', 5, 'obat diare.png', 'Kesehatan', 35000.00),
(10, 'Royal Canin Hair & Skin (85g)', 'Makanan basah Royal Canin untuk kucing dengan kulit sensitif dan bulu kusam. Formula khusus untuk mendukung kesehatan kulit dan keindahan bulu.', 8, 'Royal Cannin basah.png', 'Makanan', 23000.00),
(11, 'Royal Canin Aroma Exigent', 'Makanan kering Royal Canin untuk kucing pemilih aroma. Formula khusus dengan aroma yang sangat menggoda untuk merangsang nafsu makan.', 8, 'royal cannin aroma exigent.png', 'Makanan', 58000.00),
(12, 'Royal Canin Protein Exigent', 'Makanan kering Royal Canin untuk kucing pemilih protein. Diformulasikan dengan tingkat protein tinggi untuk memenuhi kebutuhan kucing yang cerdas memilih makanan.', 10, 'royal cannin protein exigent.png', 'Makanan', 56000.00),
(13, 'Hata', 'Temen guweh nih boy', 0, 'foto Hata.jpg', 'Kesehatan', 100.00),
(17, 'coba', 'cek ajah', 0, 'motor benda 252.PNG', 'Perlengkapan', 10.00);

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `ID_Transaksi` int NOT NULL,
  `ID_User` int NOT NULL,
  `ID_Grooming` int DEFAULT NULL,
  `ID_Pentipan` int DEFAULT NULL,
  `total_harga` decimal(12,2) NOT NULL,
  `detail_transaksi_catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT 'Catatan umum untuk transaksi',
  `tanggal_transaksi` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`ID_Transaksi`, `ID_User`, `ID_Grooming`, `ID_Pentipan`, `total_harga`, `detail_transaksi_catatan`, `tanggal_transaksi`) VALUES
(23, 8, NULL, NULL, 115000.00, 'Pesanan Produk via Midtrans. Payment: Qris. Order ID Midtrans: TSK-8-1749393985', '2025-06-08 14:47:07'),
(24, 8, NULL, NULL, 70000.00, 'Pesanan Produk via Midtrans. Payment: Qris. Order ID Midtrans: TSK-8-1749394216', '2025-06-08 14:50:45'),
(25, 8, NULL, NULL, 100.00, 'Pesanan Produk via Midtrans. Payment: Qris. Order ID Midtrans: TSK-8-1749394820', '2025-06-08 15:00:57'),
(26, 8, NULL, NULL, 30000.00, 'Rincian Pesanan Produk:\n--------------------------------------\n- Obat Flu Kucing (1 x Rp 30.000) = Rp 30.000\n--------------------------------------\nTotal Harga: Rp 30.000\nMetode Pembayaran: Qris\nOrder ID Midtrans: TSK-8-1749398145', '2025-06-08 15:56:13'),
(27, 8, NULL, NULL, 58000.00, 'Rincian Pesanan Produk:\n- Royal Canin Aroma Exigent (1 x Rp 58.000) = Rp 58.000\nTotal Harga: Rp 58.000\nMetode Pembayaran: Qris\nOrder ID Midtrans: TSK-8-1749398374', '2025-06-08 16:00:05'),
(28, 8, NULL, NULL, 23000.00, 'Rincian Pesanan Produk:\n- Royal Canin Hair &amp; Skin (85g) (1 x Rp 23.000) = Rp 23.000\nTotal Harga: Rp 23.000\nMetode Pembayaran: Qris\nOrder ID Midtrans: TSK-8-1749398853', '2025-06-08 16:07:59'),
(29, 8, NULL, NULL, 100.00, 'Rincian Pesanan Produk:\n--------------------------------------\n- Whiskas® Kering Rasa Tuna (1 x Rp 100) = Rp 100\n--------------------------------------\nTotal Harga: Rp 100\nMetode Pembayaran: Qris\nOrder ID Midtrans: TSK-8-1749399205', '2025-06-08 16:14:02'),
(30, 8, NULL, NULL, 45000.00, 'Rincian Pesanan Produk:\n--------------------------------------\n- Mangkuk Kucing (1 x Rp 45.000) = Rp 45.000\n--------------------------------------\nTotal Harga: Rp 45.000\nMetode Pembayaran: Qris\nOrder ID Midtrans: TSK-8-1749399812', '2025-06-08 16:23:58'),
(31, 8, NULL, 8, 35000.00, 'Pesanan Penitipan via Midtrans. Tgl: 08/06/2025. Order ID Midtrans: PNP-8-1749400843', '2025-06-08 16:41:13'),
(32, 8, NULL, 9, 30000.00, 'Rincian Pesanan Penitipan\n--------------------------------------\nTanggal Mulai: 08 June 2025\nLama Penitipan: 1 hari\nJumlah Kucing: 1 ekor\nLayanan Obat Harian: Tidak\n--------------------------------------\nTotal Harga: Rp 30.000\nMetode Pembayaran: Qris\nOrder ID Midtrans: PNP-8-1749401075', '2025-06-08 16:45:00'),
(33, 8, NULL, 10, 35000.00, 'Rincian Pesanan Penitipan\n--------------------------------------\nTanggal Mulai: 27 June 2025\nLama Penitipan: 1 hari\nJumlah Kucing: 1 ekor\n\nLayanan Obat Harian: Ya\nNama Obat: sfa\nKeterangan: lasmfa\nJumlah Kucing (Diberi Obat): 1 ekor\n--------------------------------------\nTotal Harga: Rp 35.000\nMetode Pembayaran: Qris\nOrder ID Midtrans: PNP-8-1749401124', '2025-06-08 16:45:48'),
(34, 8, 14, NULL, 80000.00, 'Rincian Pesanan Grooming\n--------------------------------------\nTanggal: 05 June 2025\nLokasi: Toko\nJumlah Kucing: 1 ekor\nPaket: Paket Normal\nLayanan: Mandi Standar, Potong Kuku, Pembersihan Telinga\n--------------------------------------\nTotal Harga: Rp 80.000\nMetode Pembayaran: Qris\nOrder ID Midtrans: GRM-8-1749401716', '2025-06-08 16:55:49'),
(35, 8, 15, NULL, 90000.00, 'Rincian Pesanan Grooming\n--------------------------------------\nTanggal: 10 June 2025\nLokasi: Rumah\nJumlah Kucing: 1 ekor\nPaket: Paket Hemat\nLayanan: Mandi Standar\nBiaya Home Service: Rp 40.000\n--------------------------------------\nTotal Harga: Rp 90.000\nMetode Pembayaran: Qris\nOrder ID Midtrans: GRM-8-1749513671', '2025-06-10 00:01:53'),
(36, 8, NULL, 11, 35000.00, 'Rincian Pesanan Penitipan\n--------------------------------------\nTanggal Mulai: 10 June 2025\nLama Penitipan: 1 hari\nJumlah Kucing: 1 ekor\n\nLayanan Obat Harian: Ya\nNama Obat: dasd\nKeterangan: sfafs\nJumlah Kucing (Diberi Obat): 1 ekor\n--------------------------------------\nTotal Harga: Rp 35.000\nMetode Pembayaran: Qris\nOrder ID Midtrans: PNP-8-1749515399', '2025-06-10 00:30:46'),
(37, 8, NULL, NULL, 100.00, 'Rincian Pesanan Produk:\n--------------------------------------\n- coba (10 x Rp 10) = Rp 100\n--------------------------------------\nTotal Harga: Rp 100\nMetode Pembayaran: Qris\nOrder ID Midtrans: TSK-8-1749515907', '2025-06-10 00:39:00'),
(38, 8, NULL, NULL, 25000.00, 'Rincian Pesanan Produk:\n--------------------------------------\n- Mainan Kucing (1 x Rp 25.000) = Rp 25.000\n--------------------------------------\nTotal Harga: Rp 25.000\nMetode Pembayaran: Qris\nOrder ID Midtrans: TSK-8-1749516107', '2025-06-10 00:42:23');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_produk`
--

CREATE TABLE `transaksi_produk` (
  `ID_Transaksi` int NOT NULL,
  `ID_Produk` int NOT NULL,
  `jumlah_produk` int NOT NULL DEFAULT '1',
  `harga_saat_transaksi` decimal(10,2) NOT NULL COMMENT 'Harga produk pada saat transaksi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi_produk`
--

INSERT INTO `transaksi_produk` (`ID_Transaksi`, `ID_Produk`, `jumlah_produk`, `harga_saat_transaksi`) VALUES
(23, 6, 1, 45000.00),
(23, 9, 2, 35000.00),
(24, 9, 2, 35000.00),
(25, 1, 1, 100.00),
(26, 8, 1, 30000.00),
(27, 11, 1, 58000.00),
(28, 10, 1, 23000.00),
(29, 1, 1, 100.00),
(30, 6, 1, 45000.00),
(37, 17, 10, 10.00),
(38, 2, 1, 25000.00);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `ID_User` int NOT NULL,
  `username` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `full_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pass` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Disarankan untuk menyimpan password yang sudah di-hash',
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `alamat` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `no_telepon` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`ID_User`, `username`, `full_name`, `pass`, `email`, `alamat`, `no_telepon`) VALUES
(8, 'Ino', 'Rhyno Melin Fairuz', '$2y$10$eDIgku5uczoQ8BnuDdFEQOspi6rRtjXd2YNo36x8fAkIzO0Fg/o3C', 'ino@gmail.com', 'B12no2', '123123421'),
(13, 'Rifki', 'Rifki Rochiman', '$2y$10$TiZ4/mEdE5s.z1Dq6myE6.lej6Q66vlykvznq1Xu7JcpWwEUT4aL2', 'rifki@gmail.com', 'Bandug', '085871217027'),
(14, 'jgeng2', 'jgeng bismillah', '$2y$10$S5kyrsu92TQTAkEhftq9MObkgjtogAE8IH.mdk1T7zCWbuoB25nvK', 'jgeng2@gmail.com', 'B12no2', '081312004206'),
(15, 'rhyn', 'rhyno part 2', '$2a$12$I9kY/DZxpHZ9qVan9PIDzukxpw8Jq4T5XePEDw8AG58/oP.GKyIRa', 'rhyn@gmail.com', 'Cipamokolan Rt 05/01', '089745328'),
(666, 'admin_666', 'SYSTEM ADMIN', '*THIS_ACCOUNT_IS_NOT_LOGGABLE*', 'no-reply@catshop.system', 'SYSTEM', '000'),
(667, 'hapus', 'hapus', '$2a$12$xhlHgJTauunxVkuFsIpYFuT1h7qvIcu/hq9U8QKTh512QUlsux2Au', 'hapus', 'hapus', 'hapus');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `grooming`
--
ALTER TABLE `grooming`
  ADD PRIMARY KEY (`ID_Grooming`);

--
-- Indexes for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`ID_Keranjang`),
  ADD UNIQUE KEY `user_produk_unique` (`user_id`,`produk_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `penitipan`
--
ALTER TABLE `penitipan`
  ADD PRIMARY KEY (`ID_Penitipan`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`ID_Produk`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`ID_Transaksi`),
  ADD KEY `ID_User` (`ID_User`),
  ADD KEY `ID_Grooming` (`ID_Grooming`),
  ADD KEY `ID_Pentipan` (`ID_Pentipan`);

--
-- Indexes for table `transaksi_produk`
--
ALTER TABLE `transaksi_produk`
  ADD PRIMARY KEY (`ID_Transaksi`,`ID_Produk`),
  ADD KEY `ID_Produk` (`ID_Produk`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`ID_User`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `grooming`
--
ALTER TABLE `grooming`
  MODIFY `ID_Grooming` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `ID_Keranjang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `penitipan`
--
ALTER TABLE `penitipan`
  MODIFY `ID_Penitipan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `ID_Produk` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `ID_Transaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `ID_User` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=670;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `keranjang`
--
ALTER TABLE `keranjang`
  ADD CONSTRAINT `keranjang_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`ID_User`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `keranjang_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`ID_Produk`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `Transaksi_ibfk_1` FOREIGN KEY (`ID_User`) REFERENCES `user` (`ID_User`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `Transaksi_ibfk_2` FOREIGN KEY (`ID_Grooming`) REFERENCES `grooming` (`ID_Grooming`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `Transaksi_ibfk_3` FOREIGN KEY (`ID_Pentipan`) REFERENCES `penitipan` (`ID_Penitipan`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `transaksi_produk`
--
ALTER TABLE `transaksi_produk`
  ADD CONSTRAINT `Transaksi_Produk_ibfk_1` FOREIGN KEY (`ID_Transaksi`) REFERENCES `transaksi` (`ID_Transaksi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `Transaksi_Produk_ibfk_2` FOREIGN KEY (`ID_Produk`) REFERENCES `produk` (`ID_Produk`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
