
-- ---------------------------------------------------------
-- Table structure for PUSKESMAS
-- ---------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `puskesmas`;
CREATE TABLE `puskesmas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `kode_puskesmas` varchar(50) NOT NULL UNIQUE,
  `nama_puskesmas` varchar(100) NOT NULL UNIQUE,
  `alamat` text,
  `kelurahan` varchar(100),
  `kecamatan` varchar(100),
  `kepala_puskesmas` varchar(100),
  `nomor_telepon` varchar(20),
  `email` varchar(100),
  `status_aktif` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO `puskesmas` (`id`, `kode_puskesmas`, `nama_puskesmas`, `alamat`, `kelurahan`, `kecamatan`, `kepala_puskesmas`, `nomor_telepon`, `email`, `status_aktif`) VALUES
(1, 'P6472001', 'Puskesmas A', 'Jl. Raya Kecamatan Wowo No. 1, Kota Dummy', 'Kelurahan Mimi', 'Kecamatan Wowo', 'Yudi Dian Sari', '0541319918', 'puskesmasa@kotadummy.go.id', 1),
(2, 'P6472002', 'Puskesmas B', 'Jl. Raya Kecamatan Hehe No. 97, Kota Dummy', 'Kelurahan Haha', 'Kecamatan Hehe', 'Aris Rian Lubis', '0541245170', 'puskesmasb@kotadummy.go.id', 1),
(3, 'P6472003', 'Puskesmas C', 'Jl. Raya Kecamatan Lala No. 7, Kota Dummy', 'Kelurahan Lulu', 'Kecamatan Lala', 'Yanto Simanjuntak', '0541452632', 'puskesmasc@kotadummy.go.id', 1),
(4, 'P6472004', 'Puskesmas D', 'Jl. Raya Kecamatan Koko No. 97, Kota Dummy', 'Kelurahan Kiki', 'Kecamatan Koko', 'Iwan Kuswanto', '0541442385', 'puskesmasd@kotadummy.go.id', 1),
(5, 'P6472005', 'Puskesmas E', 'Jl. Raya Kecamatan Titi No. 61, Kota Dummy', 'Kelurahan Tutu', 'Kecamatan Titi', 'Galih Tanjung', '0541600167', 'puskesmase@kotadummy.go.id', 1),
(6, 'P6472006', 'Puskesmas F', 'Jl. Raya Kecamatan Gogo No. 34, Kota Dummy', 'Kelurahan Gigi', 'Kecamatan Gogo', 'Rian Sitorus', '0541829765', 'puskesmasf@kotadummy.go.id', 1),
(7, 'P6472007', 'Puskesmas G', 'Jl. Raya Kecamatan Jojo No. 17, Kota Dummy', 'Kelurahan Jiji', 'Kecamatan Jojo', 'Yusuf Surya Subagyo', '0541639243', 'puskesmasg@kotadummy.go.id', 1),
(8, 'P6472008', 'Puskesmas H', 'Jl. Raya Kecamatan Bibi No. 43, Kota Dummy', 'Kelurahan Bubu', 'Kecamatan Bibi', 'Indra Zainal Situmorang', '0541643774', 'puskesmash@kotadummy.go.id', 1),
(9, 'P6472009', 'Puskesmas I', 'Jl. Raya Kecamatan Zaza No. 64, Kota Dummy', 'Kelurahan Zizi', 'Kecamatan Zaza', 'Surya Bambang Subakti', '0541960178', 'puskesmasi@kotadummy.go.id', 1),
(10, 'P6472010', 'Puskesmas J', 'Jl. Raya Kecamatan Shisha No. 86, Kota Dummy', 'Kelurahan Shishi', 'Kecamatan Shisha', 'Rian Hasibuan', '0541366262', 'puskesmasj@kotadummy.go.id', 1);

