<?php
/**
 * SIREHAT (Sistem Rapor Kesehatan) Dummy Data Generator
 * Author: Antigravity AI
 * Year: 2026
 *
 * This script generates realistic, relational, and statistically consistent dummy data
 * for the SIREHAT system in Samarinda. It writes individual SQL files and a combined
 * SQL file to the `dummy_data` directory.
 */

// Set time limit and memory limit for large data generation
ini_set('max_execution_time', 600);
ini_set('memory_limit', '512M');

// Directory setup
$outputDir = __DIR__ . '/dummy_data';
if (!file_exists($outputDir)) {
    mkdir($outputDir, 0755, true);
}

// ---------------------------------------------------------
// SEED DATA & HELPER ARRAYS
// ---------------------------------------------------------

$kecamatanKelurahan = [
    'Kecamatan Wowo' => ['Kelurahan Mimi', 'Kelurahan Mumu', 'Kelurahan Momo', 'Kelurahan Pepe', 'Kelurahan Pupu', 'Kelurahan Popo', 'Kelurahan Lili'],
    'Kecamatan Hehe' => ['Kelurahan Haha', 'Kelurahan Hihi', 'Kelurahan Huhu', 'Kelurahan Cici', 'Kelurahan Cucu', 'Kelurahan Coco', 'Kelurahan Didi'],
    'Kecamatan Lala' => ['Kelurahan Lulu', 'Kelurahan Lolo', 'Kelurahan Nana', 'Kelurahan Nene', 'Kelurahan Nono'],
    'Kecamatan Koko' => ['Kelurahan Kiki', 'Kelurahan Kuku', 'Kelurahan Riri', 'Kelurahan Ruru', 'Kelurahan Roro'],
    'Kecamatan Titi' => ['Kelurahan Tutu', 'Kelurahan Toto', 'Kelurahan Yoyo', 'Kelurahan Yuyu', 'Kelurahan Sisi'],
    'Kecamatan Gogo' => ['Kelurahan Gigi', 'Kelurahan Gugu', 'Kelurahan Wawa', 'Kelurahan Wiwi', 'Kelurahan Wowo'],
    'Kecamatan Jojo' => ['Kelurahan Jiji', 'Kelurahan Juju', 'Kelurahan Kaka', 'Kelurahan Keke', 'Kelurahan Koko'],
    'Kecamatan Bibi' => ['Kelurahan Bubu', 'Kelurahan Bobo', 'Kelurahan Fafa', 'Kelurahan Fifi', 'Kelurahan Fofo'],
    'Kecamatan Zaza' => ['Kelurahan Zizi', 'Kelurahan Zuzu', 'Kelurahan Zozo', 'Kelurahan Lele', 'Kelurahan Lala'],
    'Kecamatan Shisha' => ['Kelurahan Shishi', 'Kelurahan Shushu', 'Kelurahan Tatas', 'Kelurahan Titis']
];

$kecamatanKeys = array_keys($kecamatanKelurahan);

$firstNamesMale = [
    'Ahmad', 'Muhammad', 'Budi', 'Joko', 'Andi', 'Agus', 'Hendra', 'Eko', 'Rudi', 'Bambang',
    'Wawan', 'Dedi', 'Heri', 'Rian', 'Aditya', 'Fajar', 'Taufik', 'Aris', 'Dwi', 'Tri',
    'Wahyu', 'Slamet', 'Hadi', 'Mulyadi', 'Iwan', 'Dani', 'Arif', 'Roni', 'Yanto', 'Rizal',
    'Yudi', 'Faisal', 'Eka', 'Guntur', 'Surya', 'Bintang', 'Dian', 'Fikri', 'Galih', 'Indra',
    'Reza', 'Dimas', 'Adit', 'Gilang', 'Raka', 'Aldi', 'Angga', 'Rian', 'Bayu', 'Fahmi',
    'Ilham', 'Rian', 'Robby', 'Roni', 'Tio', 'Yusuf', 'Zainal', 'Abdi', 'Akbar', 'Anwar'
];

$firstNamesFemale = [
    'Siti', 'Maria', 'Dewi', 'Indah', 'Rina', 'Sri', 'Putri', 'Sari', 'Anisa', 'Fitri',
    'Wati', 'Endang', 'Yanti', 'Kartika', 'Dian', 'Mega', 'Laras', 'Nia', 'Ayu', 'Intan',
    'Ratna', 'Lestari', 'Utami', 'Ria', 'Maya', 'Nanda', 'Gita', 'Ratih', 'Siska', 'Silvia',
    'Febri', 'Eka', 'Windy', 'Restu', 'Bella', 'Chandra', 'Desi', 'Amalia', 'Safitri', 'Nur',
    'Aulia', 'Rahma', 'Suci', 'Kurnia', 'Mila', 'Tari', 'Yuni', 'Elsa', 'Kiki', 'Novi',
    'Putri', 'Rani', 'Riska', 'Selvi', 'Tia', 'Ulan', 'Vina', 'Wulan', 'Yeni', 'Zahra'
];

$lastNames = [
    'Prasetyo', 'Wibowo', 'Saputra', 'Kurniawan', 'Hidayat', 'Santoso', 'Susanto', 'Setiawan', 'Pratama', 'Nugroho',
    'Gunawan', 'Wijaya', 'Siregar', 'Lubis', 'Nasution', 'Simanjuntak', 'Sitorus', 'Harahap', 'Ginting', 'Tarigan',
    'Sinaga', 'Panjaitan', 'Pohan', 'Panggabean', 'Tanjung', 'Pasaribu', 'Manurung', 'Rajagukguk', 'Situmorang', 'Nainggolan',
    'Sembiring', 'Limbong', 'Pane', 'Hasibuan', 'Sihombing', 'Mulyono', 'Subagyo', 'Supriadi', 'Purwanto', 'Haryanto',
    'Kusuma', 'Purnama', 'Budiman', 'Suherman', 'Rosidi', 'Saefullah', 'Subakti', 'Wicaksono', 'Pramono', 'Dharmawan',
    'Firmansyah', 'Hadi', 'Herianto', 'Irawan', 'Kuswanto', 'Lestari', 'Mahendra', 'Putra', 'Ramadhan', 'Sari'
];

$streets = [
    'Jl. Mulawarman', 'Jl. Yos Sudarso', 'Jl. Pahlawan', 'Jl. Bhayangkara', 'Jl. Pemuda',
    'Jl. Ahmad Yani', 'Jl. Gatot Subroto', 'Jl. S. Parman', 'Jl. Juanda', 'Jl. Antasari',
    'Jl. Kadrie Oening', 'Jl. M. Yamin', 'Jl. Wahid Hasyim', 'Jl. Letjen Suprapto', 'Jl. DI Panjaitan',
    'Jl. AW Syahranie', 'Jl. PM Noor', 'Jl. RE Martadinata', 'Jl. Gajah Mada', 'Jl. Slamet Riyadi',
    'Jl. Untung Suropati', 'Jl. Cipto Mangunkusumo', 'Jl. Bung Tomo', 'Jl. KH. Harun Nafsi', 'Jl. Sultan Alimuddin',
    'Jl. Otto Iskandardinata', 'Jl. Aminah Syukur', 'Jl. KH. Abul Hasan', 'Jl. H. Agus Salim', 'Jl. Basuki Rahmat'
];

$religions = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];

$schoolsSD = ['SD Harvard', 'SD Oxford', 'SD Cambridge', 'SD Stanford', 'SD MIT', 'SD Berkeley', 'SD Princeton', 'SD Yale', 'SD Columbia', 'SD Cornell'];
$schoolsSMP = ['SMP Gaul', 'SMP Keren', 'SMP Hits', 'SMP Kece', 'SMP Pintar', 'SMP Cerdas', 'SMP Bijak', 'SMP Juara', 'SMP Utama'];
$schoolsSMA = ['SMA Koboh', 'SMA Maju', 'SMA Jaya', 'SMA Hebat', 'SMA Unggul', 'SMA Prestasi', 'SMA Harapan'];
$schoolsSMK = ['SMK Kreatif', 'SMK Inovatif', 'SMK Mandiri', 'SMK Bisa', 'SMK Mahir', 'SMK Trampil'];

$posyanduNames = ['Melati', 'Mawar', 'Anggrek', 'Kenanga', 'Flamboyan', 'Cempaka', 'Dahlia', 'Teratai', 'Kamboja', 'Bougenville', 'Tulip', 'Soka', 'Sakura', 'Lily', 'Matahari'];

// ---------------------------------------------------------
// GENERATOR HELPER FUNCTIONS
// ---------------------------------------------------------

function getRandomElement(array $arr) {
    return $arr[array_rand($arr)];
}

function generateIndonesianName($gender) {
    global $firstNamesMale, $firstNamesFemale, $lastNames;
    $first = ($gender === 'L') ? getRandomElement($firstNamesMale) : getRandomElement($firstNamesFemale);
    $last = getRandomElement($lastNames);
    if (rand(0, 10) > 3) {
        $middle = getRandomElement(($gender === 'L') ? $firstNamesMale : $firstNamesFemale);
        if ($middle !== $first) {
            return "$first $middle $last";
        }
    }
    return "$first $last";
}

function generateNIK($gender, $dobString, $kecamatanIndex) {
    // NIK: 64 (East Kalimantan) + 72 (Samarinda) + Kecamatan (01-10) + DOB (DDMMYY, DD+40 for Female) + Random Serial
    $prov = '64';
    $kab = '72';
    $kec = str_pad($kecamatanIndex + 1, 2, '0', STR_PAD_LEFT);
    
    $parts = explode('-', $dobString); // YYYY-MM-DD
    $year = substr($parts[0], 2, 2);
    $month = $parts[1];
    $day = intval($parts[2]);
    
    if ($gender === 'P') {
        $day += 40;
    }
    $dayStr = str_pad($day, 2, '0', STR_PAD_LEFT);
    
    $serial = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    return $prov . $kab . $kec . $dayStr . $month . $year . $serial;
}

function generateNISN($dobString) {
    // NISN: 10 digits. First 3 digits are usually the last 3 digits of birth year
    $parts = explode('-', $dobString);
    $yearPart = substr($parts[0], 1, 3);
    $randomPart = str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);
    return $yearPart . $randomPart;
}

function generatePhone() {
    $prefixes = ['0811', '0812', '0813', '0821', '0822', '0852', '0853', '0878', '0896', '0882', '0857'];
    return getRandomElement($prefixes) . rand(1000000, 99999999);
}

// ---------------------------------------------------------
// WRITING SQL HEADER & TABULAR SCHEMAS
// ---------------------------------------------------------

$combinedFile = fopen("$outputDir/sirehat_all_dummy_data.sql", 'w');
fwrite($combinedFile, "-- SIREHAT ALL DUMMY DATA IMPORT SCRIPT\n");
fwrite($combinedFile, "-- Generated on: " . date('Y-m-d H:i:s') . "\n");
fwrite($combinedFile, "SET FOREIGN_KEY_CHECKS = 0;\n\n");

function writeSqlAndCombined($fp, $sql) {
    global $combinedFile;
    fwrite($fp, $sql);
    fwrite($combinedFile, $sql);
}

// 1. PUSKESMAS Table Definition
$fpPuskesmas = fopen("$outputDir/01_puskesmas.sql", 'w');
$schemaPuskesmas = "
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

";
writeSqlAndCombined($fpPuskesmas, $schemaPuskesmas);

// 2. POSYANDU Table Definition
$fpPosyandu = fopen("$outputDir/02_posyandu.sql", 'w');
$schemaPosyandu = "
-- ---------------------------------------------------------
-- Table structure for POSYANDU
-- ---------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `posyandu`;
CREATE TABLE `posyandu` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `puskesmas_id` bigint unsigned NOT NULL,
  `nama_posyandu` varchar(100) NOT NULL,
  `alamat` text,
  `rt` varchar(5),
  `rw` varchar(5),
  `kelurahan` varchar(100),
  `kecamatan` varchar(100),
  `nama_kader` varchar(100),
  `nomor_hp` varchar(20),
  `status_aktif` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_posyandu_puskesmas` (`puskesmas_id`),
  CONSTRAINT `fk_posyandu_puskesmas` FOREIGN KEY (`puskesmas_id`) REFERENCES `puskesmas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SET FOREIGN_KEY_CHECKS = 1;

";
writeSqlAndCombined($fpPosyandu, $schemaPosyandu);

// 3. SEKOLAH Table Definition
$fpSekolah = fopen("$outputDir/03_sekolah.sql", 'w');
$schemaSekolah = "
-- ---------------------------------------------------------
-- Table structure for SEKOLAH
-- ---------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `sekolah`;
CREATE TABLE `sekolah` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `puskesmas_id` bigint unsigned NOT NULL,
  `npsn` varchar(20) NOT NULL UNIQUE,
  `nama_sekolah` varchar(100) NOT NULL,
  `jenjang` enum('SD','SMP','SMA','SMK') NOT NULL,
  `negeri_swasta` enum('Negeri','Swasta') NOT NULL,
  `alamat` text,
  `kelurahan` varchar(100),
  `kecamatan` varchar(100),
  `kepala_sekolah` varchar(100),
  `jumlah_siswa` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_sekolah_puskesmas` (`puskesmas_id`),
  CONSTRAINT `fk_sekolah_puskesmas` FOREIGN KEY (`puskesmas_id`) REFERENCES `puskesmas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SET FOREIGN_KEY_CHECKS = 1;

";
writeSqlAndCombined($fpSekolah, $schemaSekolah);

// 4. SISWA Table Definition
$fpSiswa = fopen("$outputDir/04_siswa.sql", 'w');
$schemaSiswa = "
-- ---------------------------------------------------------
-- Table structure for SISWA
-- ---------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `siswa`;
CREATE TABLE `siswa` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sekolah_id` bigint unsigned NOT NULL,
  `nisn` varchar(20) NOT NULL UNIQUE,
  `nik` varchar(20) NOT NULL UNIQUE,
  `nama_lengkap` varchar(100) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `tempat_lahir` varchar(100) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `umur` int NOT NULL,
  `agama` varchar(50),
  `alamat` text,
  `nama_ayah` varchar(100),
  `nama_ibu` varchar(100),
  `nomor_hp_orangtua` varchar(20),
  `kelas` varchar(10) NOT NULL,
  `status_aktif` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_siswa_sekolah` (`sekolah_id`),
  CONSTRAINT `fk_siswa_sekolah` FOREIGN KEY (`sekolah_id`) REFERENCES `sekolah` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SET FOREIGN_KEY_CHECKS = 1;

";
writeSqlAndCombined($fpSiswa, $schemaSiswa);

// 5. ANAK POSYANDU Table Definition
$fpAnakPosyandu = fopen("$outputDir/05_anak_posyandu.sql", 'w');
$schemaAnakPosyandu = "
-- ---------------------------------------------------------
-- Table structure for ANAK_POSYANDU
-- ---------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `anak_posyandu`;
CREATE TABLE `anak_posyandu` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `posyandu_id` bigint unsigned NOT NULL,
  `nik` varchar(20) NOT NULL UNIQUE,
  `nama_lengkap` varchar(100) NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `tempat_lahir` varchar(100) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `umur_bulan` int NOT NULL,
  `nama_ayah` varchar(100),
  `nama_ibu` varchar(100),
  `alamat` text,
  `nomor_hp` varchar(20),
  `status_aktif` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_anak_posyandu_posyandu` (`posyandu_id`),
  CONSTRAINT `fk_anak_posyandu_posyandu` FOREIGN KEY (`posyandu_id`) REFERENCES `posyandu` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SET FOREIGN_KEY_CHECKS = 1;

";
writeSqlAndCombined($fpAnakPosyandu, $schemaAnakPosyandu);

// 6. PEMERIKSAAN UMUM Table Definition
$fpPemeriksaanUmum = fopen("$outputDir/06_pemeriksaan_umum.sql", 'w');
$schemaPemeriksaanUmum = "
-- ---------------------------------------------------------
-- Table structure for PEMERIKSAAN_UMUM
-- ---------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `pemeriksaan_umum`;
CREATE TABLE `pemeriksaan_umum` (
  `siswa_id` bigint unsigned NOT NULL,
  `tanggal_pemeriksaan` date NOT NULL,
  `tekanan_darah_sistolik` int,
  `tekanan_darah_diastolik` int,
  `denyut_nadi` int,
  `frekuensi_pernapasan` int,
  `suhu_tubuh` decimal(4,2),
  `kondisi_kulit` varchar(50),
  `kondisi_rambut` varchar(50),
  `kondisi_kuku` varchar(50),
  `catatan` text,
  PRIMARY KEY (`siswa_id`,`tanggal_pemeriksaan`),
  CONSTRAINT `fk_pemeriksaan_umum_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SET FOREIGN_KEY_CHECKS = 1;

";
writeSqlAndCombined($fpPemeriksaanUmum, $schemaPemeriksaanUmum);

// 7. PEMERIKSAAN GIZI Table Definition
$fpPemeriksaanGizi = fopen("$outputDir/07_pemeriksaan_gizi.sql", 'w');
$schemaPemeriksaanGizi = "
-- ---------------------------------------------------------
-- Table structure for PEMERIKSAAN_GIZI
-- ---------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `pemeriksaan_gizi`;
CREATE TABLE `pemeriksaan_gizi` (
  `siswa_id` bigint unsigned NOT NULL,
  `tanggal_pemeriksaan` date NOT NULL,
  `berat_badan` decimal(5,2) NOT NULL,
  `tinggi_badan` decimal(5,2) NOT NULL,
  `lingkar_lengan_atas` decimal(4,2),
  `imt` decimal(4,2) NOT NULL,
  `kategori_imt` enum('Kurus','Normal','Gemuk','Obesitas') NOT NULL,
  `status_stunting` enum('Sangat Pendek','Pendek','Normal','Tinggi') NOT NULL,
  `status_gizi` varchar(50) NOT NULL,
  `status_anemia` enum('Normal','Anemia') NOT NULL,
  PRIMARY KEY (`siswa_id`,`tanggal_pemeriksaan`),
  CONSTRAINT `fk_pemeriksaan_gizi_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SET FOREIGN_KEY_CHECKS = 1;

";
writeSqlAndCombined($fpPemeriksaanGizi, $schemaPemeriksaanGizi);

// 8. PEMERIKSAAN GIGI DAN MULUT Table Definition
$fpPemeriksaanGigi = fopen("$outputDir/08_pemeriksaan_gigi_dan_mulut.sql", 'w');
$schemaPemeriksaanGigi = "
-- ---------------------------------------------------------
-- Table structure for PEMERIKSAAN_GIGI_DAN_MULUT
-- ---------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `pemeriksaan_gigi_dan_mulut`;
CREATE TABLE `pemeriksaan_gigi_dan_mulut` (
  `siswa_id` bigint unsigned NOT NULL,
  `tanggal_pemeriksaan` date NOT NULL,
  `karies` enum('Ya','Tidak') NOT NULL,
  `karang_gigi` enum('Ya','Tidak') NOT NULL,
  `gusi` varchar(50) NOT NULL,
  `jumlah_gigi_berlubang` int NOT NULL DEFAULT '0',
  `jumlah_gigi_hilang` int NOT NULL DEFAULT '0',
  `jumlah_gigi_tambal` int NOT NULL DEFAULT '0',
  `perlu_rujukan` enum('Ya','Tidak') NOT NULL,
  PRIMARY KEY (`siswa_id`,`tanggal_pemeriksaan`),
  CONSTRAINT `fk_pemeriksaan_gigi_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SET FOREIGN_KEY_CHECKS = 1;

";
writeSqlAndCombined($fpPemeriksaanGigi, $schemaPemeriksaanGigi);

// 9. PEMERIKSAAN MATA Table Definition
$fpPemeriksaanMata = fopen("$outputDir/09_pemeriksaan_mata.sql", 'w');
$schemaPemeriksaanMata = "
-- ---------------------------------------------------------
-- Table structure for PEMERIKSAAN_MATA
-- ---------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `pemeriksaan_mata`;
CREATE TABLE `pemeriksaan_mata` (
  `siswa_id` bigint unsigned NOT NULL,
  `tanggal_pemeriksaan` date NOT NULL,
  `visus_mata_kanan` varchar(10) NOT NULL,
  `visus_mata_kiri` varchar(10) NOT NULL,
  `buta_warna` enum('Ya','Tidak') NOT NULL,
  `memakai_kacamata` enum('Ya','Tidak') NOT NULL,
  `perlu_rujukan` enum('Ya','Tidak') NOT NULL,
  PRIMARY KEY (`siswa_id`,`tanggal_pemeriksaan`),
  CONSTRAINT `fk_pemeriksaan_mata_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SET FOREIGN_KEY_CHECKS = 1;

";
writeSqlAndCombined($fpPemeriksaanMata, $schemaPemeriksaanMata);

// 10. PEMERIKSAAN TELINGA Table Definition
$fpPemeriksaanTelinga = fopen("$outputDir/10_pemeriksaan_telinga.sql", 'w');
$schemaPemeriksaanTelinga = "
-- ---------------------------------------------------------
-- Table structure for PEMERIKSAAN_TELINGA
-- ---------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `pemeriksaan_telinga`;
CREATE TABLE `pemeriksaan_telinga` (
  `siswa_id` bigint unsigned NOT NULL,
  `tanggal_pemeriksaan` date NOT NULL,
  `kondisi_telinga_kanan` varchar(50) NOT NULL,
  `kondisi_telinga_kiri` varchar(50) NOT NULL,
  `serumen` enum('Ya','Tidak') NOT NULL,
  `gangguan_pendengaran` enum('Ya','Tidak') NOT NULL,
  PRIMARY KEY (`siswa_id`,`tanggal_pemeriksaan`),
  CONSTRAINT `fk_pemeriksaan_telinga_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SET FOREIGN_KEY_CHECKS = 1;

";
writeSqlAndCombined($fpPemeriksaanTelinga, $schemaPemeriksaanTelinga);

// 11. PEMERIKSAAN BALITA Table Definition
$fpPemeriksaanBalita = fopen("$outputDir/11_pemeriksaan_balita.sql", 'w');
$schemaPemeriksaanBalita = "
-- ---------------------------------------------------------
-- Table structure for PEMERIKSAAN_BALITA
-- ---------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `pemeriksaan_balita`;
CREATE TABLE `pemeriksaan_balita` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `anak_id` bigint unsigned NOT NULL,
  `tanggal_pemeriksaan` date NOT NULL,
  `berat_badan` decimal(5,2) NOT NULL,
  `tinggi_badan` decimal(5,2) NOT NULL,
  `lingkar_kepala` decimal(4,2),
  `lingkar_lengan` decimal(4,2),
  `status_gizi` varchar(50) NOT NULL,
  `status_stunting` enum('Sangat Pendek','Pendek','Normal','Tinggi') NOT NULL,
  `vitamin_a` enum('Ya','Tidak') NOT NULL,
  `imunisasi` varchar(100),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_anak_tanggal` (`anak_id`,`tanggal_pemeriksaan`),
  CONSTRAINT `fk_pemeriksaan_balita_anak` FOREIGN KEY (`anak_id`) REFERENCES `anak_posyandu` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SET FOREIGN_KEY_CHECKS = 1;

";
writeSqlAndCombined($fpPemeriksaanBalita, $schemaPemeriksaanBalita);

// 12. RIWAYAT IMUNISASI Table Definition
$fpRiwayatImunisasi = fopen("$outputDir/12_riwayat_imunisasi.sql", 'w');
$schemaRiwayatImunisasi = "
-- ---------------------------------------------------------
-- Table structure for RIWAYAT_IMUNISASI
-- ---------------------------------------------------------
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `riwayat_imunisasi`;
CREATE TABLE `riwayat_imunisasi` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `anak_id` bigint unsigned NOT NULL,
  `jenis_imunisasi` varchar(50) NOT NULL,
  `tanggal_imunisasi` date NOT NULL,
  `status` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_imunisasi_anak` (`anak_id`),
  CONSTRAINT `fk_imunisasi_anak` FOREIGN KEY (`anak_id`) REFERENCES `anak_posyandu` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SET FOREIGN_KEY_CHECKS = 1;

";
writeSqlAndCombined($fpRiwayatImunisasi, $schemaRiwayatImunisasi);

// Helper function to escape strings safely for SQL
function sqlEscape($str) {
    if ($str === null) return "NULL";
    $escaped = str_replace("'", "''", $str);
    return "'$escaped'";
}

// ---------------------------------------------------------
// DATA GENERATION: 1. PUSKESMAS (10 rows)
// ---------------------------------------------------------
echo "Generating PUSKESMAS...\n";

$puskesmasData = [];
// Generate exactly 10 Puskesmas, one for each Samarinda Kecamatan
for ($i = 0; $i < 10; $i++) {
    $kec = $kecamatanKeys[$i];
    $kel = $kecamatanKelurahan[$kec][0]; // Pick the first kelurahan as location
    $id = $i + 1;
    $kode = 'P' . str_pad(6472000 + $id, 7, '0', STR_PAD_LEFT);
    $nama = "Puskesmas " . chr(65 + $i);
    $alamat = "Jl. Raya " . $kec . " No. " . rand(1, 100) . ", Kota Dummy";
    $kepala = generateIndonesianName('L');
    $telepon = '0541' . rand(200000, 999999);
    $email = strtolower(str_replace(' ', '', $nama)) . '@kotadummy.go.id';
    $aktif = 1;
    
    $puskesmasData[] = [
        'id' => $id,
        'kode' => $kode,
        'nama' => $nama,
        'alamat' => $alamat,
        'kelurahan' => $kel,
        'kecamatan' => $kec,
        'kepala' => $kepala,
        'telepon' => $telepon,
        'email' => $email,
        'aktif' => $aktif
    ];
}

$sql = "INSERT INTO `puskesmas` (`id`, `kode_puskesmas`, `nama_puskesmas`, `alamat`, `kelurahan`, `kecamatan`, `kepala_puskesmas`, `nomor_telepon`, `email`, `status_aktif`) VALUES\n";
$values = [];
foreach ($puskesmasData as $p) {
    $values[] = "(" . implode(', ', [
        $p['id'],
        sqlEscape($p['kode']),
        sqlEscape($p['nama']),
        sqlEscape($p['alamat']),
        sqlEscape($p['kelurahan']),
        sqlEscape($p['kecamatan']),
        sqlEscape($p['kepala']),
        sqlEscape($p['telepon']),
        sqlEscape($p['email']),
        $p['aktif']
    ]) . ")";
}
$sql .= implode(",\n", $values) . ";\n\n";
writeSqlAndCombined($fpPuskesmas, $sql);
fclose($fpPuskesmas);

// ---------------------------------------------------------
// DATA GENERATION: 2. POSYANDU (50 rows)
// ---------------------------------------------------------
echo "Generating POSYANDU...\n";

$posyanduData = [];
$posyanduPerPuskesmas = 5;
$posyanduId = 1;

foreach ($puskesmasData as $p) {
    $kec = $p['kecamatan'];
    $kelurahans = $kecamatanKelurahan[$kec];
    
    for ($k = 0; $k < $posyanduPerPuskesmas; $k++) {
        $pName = getRandomElement($posyanduNames) . " " . ($k + 1);
        $kel = getRandomElement($kelurahans);
        $alamat = "Gang " . getRandomElement(['Mawar', 'Melati', 'Kamboja', 'Kenanga', 'Bougenville', 'Cempaka']) . " RT " . str_pad(rand(1, 40), 2, '0', STR_PAD_LEFT);
        $rt = str_pad(rand(1, 30), 2, '0', STR_PAD_LEFT);
        $rw = str_pad(rand(1, 10), 2, '0', STR_PAD_LEFT);
        $kader = generateIndonesianName('P');
        $hp = generatePhone();
        $aktif = 1;
        
        $posyanduData[] = [
            'id' => $posyanduId,
            'puskesmas_id' => $p['id'],
            'nama' => "Posyandu " . $pName,
            'alamat' => $alamat,
            'rt' => $rt,
            'rw' => $rw,
            'kelurahan' => $kel,
            'kecamatan' => $kec,
            'kader' => $kader,
            'hp' => $hp,
            'aktif' => $aktif
        ];
        $posyanduId++;
    }
}

$sql = "INSERT INTO `posyandu` (`id`, `puskesmas_id`, `nama_posyandu`, `alamat`, `rt`, `rw`, `kelurahan`, `kecamatan`, `nama_kader`, `nomor_hp`, `status_aktif`) VALUES\n";
$values = [];
foreach ($posyanduData as $pos) {
    $values[] = "(" . implode(', ', [
        $pos['id'],
        $pos['puskesmas_id'],
        sqlEscape($pos['nama']),
        sqlEscape($pos['alamat']),
        sqlEscape($pos['rt']),
        sqlEscape($pos['rw']),
        sqlEscape($pos['kelurahan']),
        sqlEscape($pos['kecamatan']),
        sqlEscape($pos['kader']),
        sqlEscape($pos['hp']),
        $pos['aktif']
    ]) . ")";
}
$sql .= implode(",\n", $values) . ";\n\n";
writeSqlAndCombined($fpPosyandu, $sql);
fclose($fpPosyandu);

// ---------------------------------------------------------
// DATA GENERATION: 3. SEKOLAH (100 rows)
// ---------------------------------------------------------
echo "Generating SEKOLAH...\n";

$sekolahData = [];
$sekolahPerPuskesmas = 10;
$sekolahId = 1;

$jenjangOpts = ['SD', 'SMP', 'SMA', 'SMK'];

foreach ($puskesmasData as $p) {
    $kec = $p['kecamatan'];
    $kelurahans = $kecamatanKelurahan[$kec];
    
    for ($k = 0; $k < $sekolahPerPuskesmas; $k++) {
        // Distribute jenjang: 5 SD, 2 SMP, 2 SMA/SMK per Puskesmas to represent population distributions
        if ($k < 5) {
            $jenjang = 'SD';
            $namaSekolah = $schoolsSD[array_rand($schoolsSD)] . " " . $kec;
        } elseif ($k < 7) {
            $jenjang = 'SMP';
            $namaSekolah = $schoolsSMP[array_rand($schoolsSMP)] . " " . $kec;
        } elseif ($k < 9) {
            $jenjang = 'SMA';
            $namaSekolah = $schoolsSMA[array_rand($schoolsSMA)] . " " . $kec;
        } else {
            $jenjang = 'SMK';
            $namaSekolah = $schoolsSMK[array_rand($schoolsSMK)] . " " . $kec;
        }
        
        // Ensure uniqueness of school name in list
        $uniqueName = $namaSekolah;
        $counter = 1;
        $existingNames = array_column($sekolahData, 'nama');
        while (in_array($uniqueName, $existingNames)) {
            $uniqueName = $namaSekolah . " " . chr(64 + $counter);
            $counter++;
        }
        $namaSekolah = $uniqueName;

        $npsn = rand(30400000, 30499999);
        $negeriSwasta = (rand(1, 10) > 3) ? 'Negeri' : 'Swasta';
        $kel = getRandomElement($kelurahans);
        $alamat = getRandomElement($streets) . " No. " . rand(1, 150) . ", " . $kel . ", " . $kec;
        $kepsek = generateIndonesianName('L');
        
        $sekolahData[] = [
            'id' => $sekolahId,
            'puskesmas_id' => $p['id'],
            'npsn' => $npsn,
            'nama' => $namaSekolah,
            'jenjang' => $jenjang,
            'negeri_swasta' => $negeriSwasta,
            'alamat' => $alamat,
            'kelurahan' => $kel,
            'kecamatan' => $kec,
            'kepsek' => $kepsek,
            'jumlah_siswa' => 0 // Will count when generating students
        ];
        $sekolahId++;
    }
}

// ---------------------------------------------------------
// DATA GENERATION: 4. SISWA (5,000 rows)
// ---------------------------------------------------------
echo "Generating SISWA (5,000 rows)...\n";

$siswaData = [];
$totalSiswa = 5000;
$siswaPerSekolah = array_fill(1, 100, 0);

// We will generate students and associate them with schools
for ($s = 1; $s <= $totalSiswa; $s++) {
    // Pick a random school
    $sekolah = getRandomElement($sekolahData);
    $sekolahId = $sekolah['id'];
    $siswaPerSekolah[$sekolahId]++;
    
    $jenjang = $sekolah['jenjang'];
    $gender = (rand(1, 100) > 50) ? 'L' : 'P';
    
    // Age and Birthday rules
    if ($jenjang === 'SD') {
        $umur = rand(6, 12);
    } elseif ($jenjang === 'SMP') {
        $umur = rand(12, 15);
    } else { // SMA / SMK
        $umur = rand(15, 18);
    }
    
    $birthYear = 2026 - $umur;
    $birthMonth = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
    $birthDay = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
    $dob = "$birthYear-$birthMonth-$birthDay";
    
    // Match NIK & NISN
    $kecId = $sekolah['puskesmas_id'] - 1; // kecamatan mapping based on Puskesmas
    $nik = generateNIK($gender, $dob, $kecId);
    $nisn = generateNISN($dob);
    
    $nama = generateIndonesianName($gender);
    $tempatLahir = 'Kota Dummy';
    $agama = getRandomElement($religions);
    
    // Alamat matches the school's kecamatan
    $kelSiswa = getRandomElement($kecamatanKelurahan[$sekolah['kecamatan']]);
    $alamatSiswa = getRandomElement($streets) . " RT " . str_pad(rand(1, 30), 2, '0', STR_PAD_LEFT) . ", " . $kelSiswa . ", " . $sekolah['kecamatan'];
    
    $namaAyah = generateIndonesianName('L');
    $namaIbu = generateIndonesianName('P');
    $hpOrangtua = generatePhone();
    
    // Class mapping based on age/jenjang
    if ($jenjang === 'SD') {
        $kelasNo = min(6, $umur - 5);
        $kelas = $kelasNo . ' ' . getRandomElement(['A', 'B', 'C']);
    } elseif ($jenjang === 'SMP') {
        $kelasNo = min(9, $umur - 5); // 7-9
        $kelas = $kelasNo . ' ' . getRandomElement(['A', 'B', 'C']);
    } else { // SMA / SMK
        $kelasNo = min(12, $umur - 5); // 10-12
        $kelas = $kelasNo . ' ' . getRandomElement(['IPA 1', 'IPA 2', 'IPS 1', 'IPS 2', 'TKJ', 'AK']);
    }
    
    $siswaData[] = [
        'id' => $s,
        'sekolah_id' => $sekolahId,
        'nisn' => $nisn,
        'nik' => $nik,
        'nama' => $nama,
        'gender' => $gender,
        'tempat_lahir' => $tempatLahir,
        'tanggal_lahir' => $dob,
        'umur' => $umur,
        'agama' => $agama,
        'alamat' => $alamatSiswa,
        'nama_ayah' => $namaAyah,
        'nama_ibu' => $namaIbu,
        'hp_orangtua' => $hpOrangtua,
        'kelas' => $kelas,
        'status_aktif' => 1
    ];
}

// Update jumlah_siswa in sekolahData array
for ($i = 0; $i < count($sekolahData); $i++) {
    $sid = $sekolahData[$i]['id'];
    $sekolahData[$i]['jumlah_siswa'] = $siswaPerSekolah[$sid];
}

// Write Sekolah Inserts
$sql = "INSERT INTO `sekolah` (`id`, `puskesmas_id`, `npsn`, `nama_sekolah`, `jenjang`, `negeri_swasta`, `alamat`, `kelurahan`, `kecamatan`, `kepala_sekolah`, `jumlah_siswa`) VALUES\n";
$values = [];
foreach ($sekolahData as $sek) {
    $values[] = "(" . implode(', ', [
        $sek['id'],
        $sek['puskesmas_id'],
        sqlEscape($sek['npsn']),
        sqlEscape($sek['nama']),
        sqlEscape($sek['jenjang']),
        sqlEscape($sek['negeri_swasta']),
        sqlEscape($sek['alamat']),
        sqlEscape($sek['kelurahan']),
        sqlEscape($sek['kecamatan']),
        sqlEscape($sek['kepsek']),
        $sek['jumlah_siswa']
    ]) . ")";
}
$sql .= implode(",\n", $values) . ";\n\n";
writeSqlAndCombined($fpSekolah, $sql);
fclose($fpSekolah);

// Write Siswa Inserts in chunks of 500
$chunkSize = 500;
$chunks = array_chunk($siswaData, $chunkSize);
foreach ($chunks as $chunkIndex => $chunk) {
    $sql = "INSERT INTO `siswa` (`id`, `sekolah_id`, `nisn`, `nik`, `nama_lengkap`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `umur`, `agama`, `alamat`, `nama_ayah`, `nama_ibu`, `nomor_hp_orangtua`, `kelas`, `status_aktif`) VALUES\n";
    $values = [];
    foreach ($chunk as $sis) {
        $values[] = "(" . implode(', ', [
            $sis['id'],
            $sis['sekolah_id'],
            sqlEscape($sis['nisn']),
            sqlEscape($sis['nik']),
            sqlEscape($sis['nama']),
            sqlEscape($sis['gender']),
            sqlEscape($sis['tempat_lahir']),
            sqlEscape($sis['tanggal_lahir']),
            $sis['umur'],
            sqlEscape($sis['agama']),
            sqlEscape($sis['alamat']),
            sqlEscape($sis['nama_ayah']),
            sqlEscape($sis['nama_ibu']),
            sqlEscape($sis['hp_orangtua']),
            sqlEscape($sis['kelas']),
            $sis['status_aktif']
        ]) . ")";
    }
    $sql .= implode(",\n", $values) . ";\n\n";
    writeSqlAndCombined($fpSiswa, $sql);
}
fclose($fpSiswa);

// ---------------------------------------------------------
// DATA GENERATION: 5. ANAK POSYANDU (3,000 rows)
// ---------------------------------------------------------
echo "Generating ANAK POSYANDU (3,000 rows)...\n";

$anakPosyanduData = [];
$totalAnak = 3000;

for ($a = 1; $a <= $totalAnak; $a++) {
    $posyandu = getRandomElement($posyanduData);
    $posyanduId = $posyandu['id'];
    
    $gender = (rand(1, 100) > 50) ? 'L' : 'P';
    
    // Balita Age in Months (0 to 60)
    $umurBulan = rand(0, 60);
    
    // We calculate birthdate relative to June 2026
    $currentDate = new DateTime('2026-06-23');
    $intervalSpec = 'P' . $umurBulan . 'M';
    $currentDate->sub(new DateInterval($intervalSpec));
    $dob = $currentDate->format('Y-m-d');
    
    // Determine NIK
    $kecId = $posyandu['puskesmas_id'] - 1;
    $nik = generateNIK($gender, $dob, $kecId);
    
    $nama = generateIndonesianName($gender);
    $tempatLahir = 'Kota Dummy';
    
    $namaAyah = generateIndonesianName('L');
    $namaIbu = generateIndonesianName('P');
    
    // Address matches Posyandu
    $alamatAnak = $posyandu['alamat'] . ", " . $posyandu['kelurahan'] . ", " . $posyandu['kecamatan'];
    $hp = generatePhone();
    
    $anakPosyanduData[] = [
        'id' => $a,
        'posyandu_id' => $posyanduId,
        'nik' => $nik,
        'nama' => $nama,
        'gender' => $gender,
        'tempat_lahir' => $tempatLahir,
        'tanggal_lahir' => $dob,
        'umur_bulan' => $umurBulan,
        'nama_ayah' => $namaAyah,
        'nama_ibu' => $namaIbu,
        'alamat' => $alamatAnak,
        'hp' => $hp,
        'status_aktif' => 1
    ];
}

// Write Anak Posyandu in chunks of 500
$chunks = array_chunk($anakPosyanduData, $chunkSize);
foreach ($chunks as $chunk) {
    $sql = "INSERT INTO `anak_posyandu` (`id`, `posyandu_id`, `nik`, `nama_lengkap`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `umur_bulan`, `nama_ayah`, `nama_ibu`, `alamat`, `nomor_hp`, `status_aktif`) VALUES\n";
    $values = [];
    foreach ($chunk as $anak) {
        $values[] = "(" . implode(', ', [
            $anak['id'],
            $anak['posyandu_id'],
            sqlEscape($anak['nik']),
            sqlEscape($anak['nama']),
            sqlEscape($anak['gender']),
            sqlEscape($anak['tempat_lahir']),
            sqlEscape($anak['tanggal_lahir']),
            $anak['umur_bulan'],
            sqlEscape($anak['nama_ayah']),
            sqlEscape($anak['nama_ibu']),
            sqlEscape($anak['alamat']),
            sqlEscape($anak['hp']),
            $anak['status_aktif']
        ]) . ")";
    }
    $sql .= implode(",\n", $values) . ";\n\n";
    writeSqlAndCombined($fpAnakPosyandu, $sql);
}
fclose($fpAnakPosyandu);

// ---------------------------------------------------------
// DATA GENERATION: STUDENT EXAMINATIONS (6. PEMERIKSAAN UMUM, 7. PEMERIKSAAN GIZI, 8. PEMERIKSAAN GIGI, 9. PEMERIKSAAN MATA, 10. PEMERIKSAAN TELINGA)
// ---------------------------------------------------------
echo "Generating Student Examinations (8,000 - 10,000 records)...\n";

$pemeriksaanUmumRecords = [];
$pemeriksaanGiziRecords = [];
$pemeriksaanGigiRecords = [];
$pemeriksaanMataRecords = [];
$pemeriksaanTelingaRecords = [];

// Track statistical distributions for verification
$stats = [
    'total_checks' => 0,
    'stunting_cases' => 0,
    'anemia_checked' => 0,
    'anemia_cases' => 0,
    'karies_cases' => 0,
    'mata_cases' => 0
];

foreach ($siswaData as $sis) {
    $siswaId = $sis['id'];
    $gender = $sis['gender'];
    $umur = $sis['umur'];
    
    // Choose random number of examinations (1 to 3)
    $numExams = rand(1, 3);
    
    // Generate distinct exam dates during 2025 - 2026
    $examDates = [];
    while (count($examDates) < $numExams) {
        $year = rand(2025, 2026);
        $month = str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT);
        $day = str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT);
        $dateStr = "$year-$month-$day";
        if (!in_array($dateStr, $examDates)) {
            $examDates[] = $dateStr;
        }
    }
    sort($examDates); // chronological order
    
    foreach ($examDates as $date) {
        $stats['total_checks']++;
        
        // 1. PEMERIKSAAN UMUM Data
        $sistolik = rand(90 + ($umur * 2), 110 + ($umur * 2));
        $diastolik = rand(60 + ($umur), 75 + ($umur));
        $nadi = rand(70, 95);
        $napas = rand(16, 22);
        $suhu = round(36.0 + (rand(0, 15) / 10), 1);
        
        $kondisiKulit = (rand(1, 100) > 95) ? getRandomElement(['Kering', 'Pucat', 'Bersisik']) : 'Normal';
        $kondisiRambut = (rand(1, 100) > 95) ? getRandomElement(['Kusam', 'Rontok']) : 'Sehat';
        $kondisiKuku = (rand(1, 100) > 95) ? getRandomElement(['Kotor', 'Rapuh']) : 'Bersih';
        $catatan = ($kondisiKulit !== 'Normal' || $kondisiRambut !== 'Sehat' || $kondisiKuku !== 'Bersih') 
            ? "Perlu perhatian higienitas diri." 
            : "Kondisi fisik luar baik.";
            
        $pemeriksaanUmumRecords[] = [
            'siswa_id' => $siswaId,
            'tanggal' => $date,
            'sistolik' => $sistolik,
            'diastolik' => $diastolik,
            'nadi' => $nadi,
            'napas' => $napas,
            'suhu' => $suhu,
            'kulit' => $kondisiKulit,
            'rambut' => $kondisiRambut,
            'kuku' => $kondisiKuku,
            'catatan' => $catatan
        ];
        
        // 2. PEMERIKSAAN GIZI Data
        // Growth charts for average height/weight
        $avgHeights = [
            6 => 110, 7 => 116, 8 => 121, 9 => 127, 10 => 132, 11 => 138,
            12 => 144, 13 => 150, 14 => 155, 15 => 159, 16 => 162, 17 => 164, 18 => 166
        ];
        $avgWeights = [
            6 => 19, 7 => 21, 8 => 24, 9 => 27, 10 => 31, 11 => 35,
            12 => 40, 13 => 45, 14 => 50, 15 => 54, 16 => 57, 17 => 59, 18 => 62
        ];
        
        $hAvg = isset($avgHeights[$umur]) ? $avgHeights[$umur] : 140;
        $wAvg = isset($avgWeights[$umur]) ? $avgWeights[$umur] : 35;
        
        // Roll for stunting: target ~8-10% stunting
        $isStunted = (rand(1, 100) <= 9); // 9% probability
        if ($isStunted) {
            $stats['stunting_cases']++;
            $statusStunting = (rand(1, 10) <= 3) ? 'Sangat Pendek' : 'Pendek';
            $heightMultiplier = (rand(85, 92) / 100.0);
            $height = round($hAvg * $heightMultiplier, 1);
            // Weight is also low
            $weight = round($wAvg * (rand(75, 85) / 100.0), 1);
        } else {
            $statusStunting = (rand(1, 100) > 96) ? 'Tinggi' : 'Normal';
            $heightMultiplier = ($statusStunting === 'Tinggi') ? (rand(106, 112) / 100.0) : (rand(96, 105) / 100.0);
            $height = round($hAvg * $heightMultiplier, 1);
            $weight = round($wAvg * (rand(92, 115) / 100.0), 1);
        }
        
        // IMT & Category Calculation
        $heightInM = $height / 100.0;
        $imt = round($weight / ($heightInM * $heightInM), 2);
        
        if ($imt < 17.0) {
            $kategoriImt = 'Kurus';
            $statusGizi = 'Gizi Kurang';
        } elseif ($imt < 18.5) {
            $kategoriImt = 'Kurus';
            $statusGizi = 'Kurus';
        } elseif ($imt < 25.0) {
            $kategoriImt = 'Normal';
            $statusGizi = 'Gizi Baik';
        } elseif ($imt < 27.0) {
            $kategoriImt = 'Gemuk';
            $statusGizi = 'Gizi Lebih';
        } else {
            $kategoriImt = 'Obesitas';
            $statusGizi = 'Obesitas';
        }
        
        // Lingkar Lengan Atas (LiLA)
        $lila = round(14.0 + ($umur * 0.7) + (rand(-15, 15) / 10), 1);
        
        // Anemia Check: Target 10-15% of females
        $statusAnemia = 'Normal';
        if ($gender === 'P') {
            $stats['anemia_checked']++;
            $isAnemic = (rand(1, 100) <= 12); // 12% probability
            if ($isAnemic) {
                $stats['anemia_cases']++;
                $statusAnemia = 'Anemia';
            }
        }
        
        $pemeriksaanGiziRecords[] = [
            'siswa_id' => $siswaId,
            'tanggal' => $date,
            'berat_badan' => $weight,
            'tinggi_badan' => $height,
            'lila' => $lila,
            'imt' => $imt,
            'kategori_imt' => $kategoriImt,
            'status_stunting' => $statusStunting,
            'status_gizi' => $statusGizi,
            'status_anemia' => $statusAnemia
        ];
        
        // 3. PEMERIKSAAN GIGI DAN MULUT Data
        // Target: ~20% karies gigi
        $hasKaries = (rand(1, 100) <= 20);
        $kariesVal = $hasKaries ? 'Ya' : 'Tidak';
        if ($hasKaries) {
            $stats['karies_cases']++;
            $lubang = rand(1, 4);
            $hilang = rand(0, 1);
            $tambal = rand(0, 2);
            $karang = (rand(1, 10) > 4) ? 'Ya' : 'Tidak';
            $gusi = (rand(1, 10) > 7) ? 'Radang' : 'Sehat';
            $rujukanGigi = ($lubang >= 3 || $gusi === 'Radang') ? 'Ya' : 'Tidak';
        } else {
            $lubang = 0;
            $hilang = rand(0, 1);
            $tambal = rand(0, 1);
            $karang = (rand(1, 10) > 8) ? 'Ya' : 'Tidak';
            $gusi = 'Sehat';
            $rujukanGigi = 'Tidak';
        }
        
        $pemeriksaanGigiRecords[] = [
            'siswa_id' => $siswaId,
            'tanggal' => $date,
            'karies' => $kariesVal,
            'karang_gigi' => $karang,
            'gusi' => $gusi,
            'lubang' => $lubang,
            'hilang' => $hilang,
            'tambal' => $tambal,
            'rujukan' => $rujukanGigi
        ];
        
        // 4. PEMERIKSAAN MATA Data
        // Target: ~15% gangguan penglihatan / rujukan
        $hasMataDisorder = (rand(1, 100) <= 15);
        if ($hasMataDisorder) {
            $stats['mata_cases']++;
            $visusKanan = getRandomElement(['6/9', '6/12', '6/18', '6/24']);
            $visusKiri = getRandomElement(['6/6', '6/9', '6/12', '6/18']);
            $rujukanMata = 'Ya';
        } else {
            $visusKanan = '6/6';
            $visusKiri = '6/6';
            $rujukanMata = 'Tidak';
        }
        $butaWarna = (rand(1, 100) > 98) ? 'Ya' : 'Tidak'; // low chance
        $kacamata = (rand(1, 100) <= 8) ? 'Ya' : 'Tidak';
        
        $pemeriksaanMataRecords[] = [
            'siswa_id' => $siswaId,
            'tanggal' => $date,
            'visus_kanan' => $visusKanan,
            'visus_kiri' => $visusKiri,
            'buta_warna' => $butaWarna,
            'kacamata' => $kacamata,
            'rujukan' => $rujukanMata
        ];
        
        // 5. PEMERIKSAAN TELINGA Data
        $serumen = (rand(1, 100) <= 8) ? 'Ya' : 'Tidak';
        $telingaKanan = ($serumen === 'Ya' && rand(1, 2) === 1) ? 'Serumen Menyumbat' : 'Bersih';
        $telingaKiri = ($serumen === 'Ya' && $telingaKanan === 'Bersih') ? 'Serumen Menyumbat' : 'Bersih';
        $gangguanDengar = ($telingaKanan === 'Serumen Menyumbat' && $telingaKiri === 'Serumen Menyumbat' && rand(1, 10) > 7) ? 'Ya' : 'Tidak';
        
        $pemeriksaanTelingaRecords[] = [
            'siswa_id' => $siswaId,
            'tanggal' => $date,
            'telinga_kanan' => $telingaKanan,
            'telinga_kiri' => $telingaKiri,
            'serumen' => $serumen,
            'gangguan_pendengaran' => $gangguanDengar
        ];
    }
}

// Write Pemeriksaan Umum SQL Inserts in chunks of 500
echo "Writing Pemeriksaan Umum SQL...\n";
$chunks = array_chunk($pemeriksaanUmumRecords, $chunkSize);
foreach ($chunks as $chunk) {
    $sql = "INSERT INTO `pemeriksaan_umum` (`siswa_id`, `tanggal_pemeriksaan`, `tekanan_darah_sistolik`, `tekanan_darah_diastolik`, `denyut_nadi`, `frekuensi_pernapasan`, `suhu_tubuh`, `kondisi_kulit`, `kondisi_rambut`, `kondisi_kuku`, `catatan`) VALUES\n";
    $values = [];
    foreach ($chunk as $rec) {
        $values[] = "(" . implode(', ', [
            $rec['siswa_id'],
            sqlEscape($rec['tanggal']),
            $rec['sistolik'],
            $rec['diastolik'],
            $rec['nadi'],
            $rec['napas'],
            $rec['suhu'],
            sqlEscape($rec['kulit']),
            sqlEscape($rec['rambut']),
            sqlEscape($rec['kuku']),
            sqlEscape($rec['catatan'])
        ]) . ")";
    }
    $sql .= implode(",\n", $values) . ";\n\n";
    writeSqlAndCombined($fpPemeriksaanUmum, $sql);
}
fclose($fpPemeriksaanUmum);

// Write Pemeriksaan Gizi SQL Inserts
echo "Writing Pemeriksaan Gizi SQL...\n";
$chunks = array_chunk($pemeriksaanGiziRecords, $chunkSize);
foreach ($chunks as $chunk) {
    $sql = "INSERT INTO `pemeriksaan_gizi` (`siswa_id`, `tanggal_pemeriksaan`, `berat_badan`, `tinggi_badan`, `lingkar_lengan_atas`, `imt`, `kategori_imt`, `status_stunting`, `status_gizi`, `status_anemia`) VALUES\n";
    $values = [];
    foreach ($chunk as $rec) {
        $values[] = "(" . implode(', ', [
            $rec['siswa_id'],
            sqlEscape($rec['tanggal']),
            $rec['berat_badan'],
            $rec['tinggi_badan'],
            $rec['lila'],
            $rec['imt'],
            sqlEscape($rec['kategori_imt']),
            sqlEscape($rec['status_stunting']),
            sqlEscape($rec['status_gizi']),
            sqlEscape($rec['status_anemia'])
        ]) . ")";
    }
    $sql .= implode(",\n", $values) . ";\n\n";
    writeSqlAndCombined($fpPemeriksaanGizi, $sql);
}
fclose($fpPemeriksaanGizi);

// Write Pemeriksaan Gigi SQL Inserts
echo "Writing Pemeriksaan Gigi dan Mulut SQL...\n";
$chunks = array_chunk($pemeriksaanGigiRecords, $chunkSize);
foreach ($chunks as $chunk) {
    $sql = "INSERT INTO `pemeriksaan_gigi_dan_mulut` (`siswa_id`, `tanggal_pemeriksaan`, `karies`, `karang_gigi`, `gusi`, `jumlah_gigi_berlubang`, `jumlah_gigi_hilang`, `jumlah_gigi_tambal`, `perlu_rujukan`) VALUES\n";
    $values = [];
    foreach ($chunk as $rec) {
        $values[] = "(" . implode(', ', [
            $rec['siswa_id'],
            sqlEscape($rec['tanggal']),
            sqlEscape($rec['karies']),
            sqlEscape($rec['karang_gigi']),
            sqlEscape($rec['gusi']),
            $rec['lubang'],
            $rec['hilang'],
            $rec['tambal'],
            sqlEscape($rec['rujukan'])
        ]) . ")";
    }
    $sql .= implode(",\n", $values) . ";\n\n";
    writeSqlAndCombined($fpPemeriksaanGigi, $sql);
}
fclose($fpPemeriksaanGigi);

// Write Pemeriksaan Mata SQL Inserts
echo "Writing Pemeriksaan Mata SQL...\n";
$chunks = array_chunk($pemeriksaanMataRecords, $chunkSize);
foreach ($chunks as $chunk) {
    $sql = "INSERT INTO `pemeriksaan_mata` (`siswa_id`, `tanggal_pemeriksaan`, `visus_mata_kanan`, `visus_mata_kiri`, `buta_warna`, `memakai_kacamata`, `perlu_rujukan`) VALUES\n";
    $values = [];
    foreach ($chunk as $rec) {
        $values[] = "(" . implode(', ', [
            $rec['siswa_id'],
            sqlEscape($rec['tanggal']),
            sqlEscape($rec['visus_kanan']),
            sqlEscape($rec['visus_kiri']),
            sqlEscape($rec['buta_warna']),
            sqlEscape($rec['kacamata']),
            sqlEscape($rec['rujukan'])
        ]) . ")";
    }
    $sql .= implode(",\n", $values) . ";\n\n";
    writeSqlAndCombined($fpPemeriksaanMata, $sql);
}
fclose($fpPemeriksaanMata);

// Write Pemeriksaan Telinga SQL Inserts
echo "Writing Pemeriksaan Telinga SQL...\n";
$chunks = array_chunk($pemeriksaanTelingaRecords, $chunkSize);
foreach ($chunks as $chunk) {
    $sql = "INSERT INTO `pemeriksaan_telinga` (`siswa_id`, `tanggal_pemeriksaan`, `kondisi_telinga_kanan`, `kondisi_telinga_kiri`, `serumen`, `gangguan_pendengaran`) VALUES\n";
    $values = [];
    foreach ($chunk as $rec) {
        $values[] = "(" . implode(', ', [
            $rec['siswa_id'],
            sqlEscape($rec['tanggal']),
            sqlEscape($rec['telinga_kanan']),
            sqlEscape($rec['telinga_kiri']),
            sqlEscape($rec['serumen']),
            sqlEscape($rec['gangguan_pendengaran'])
        ]) . ")";
    }
    $sql .= implode(",\n", $values) . ";\n\n";
    writeSqlAndCombined($fpPemeriksaanTelinga, $sql);
}
fclose($fpPemeriksaanTelinga);

// ---------------------------------------------------------
// DATA GENERATION: BALITA EXAMINATIONS (11. PEMERIKSAAN BALITA, 12. RIWAYAT IMUNISASI)
// ---------------------------------------------------------
echo "Generating Balita Examinations & Immunizations (9,000+ visits)...\n";

$pemeriksaanBalitaRecords = [];
$riwayatImunisasiRecords = [];

$balitaStats = [
    'total_checks' => 0,
    'stunting_cases' => 0
];

$imunisasiTypes = [
    'HB-0' => 0,       // Age 0 months
    'BCG' => 1,        // Age 1 month
    'Polio 1' => 1,    // Age 1 month
    'DPT-HB-Hib 1' => 2, // Age 2 months
    'Polio 2' => 2,
    'DPT-HB-Hib 2' => 3, // Age 3 months
    'Polio 3' => 3,
    'DPT-HB-Hib 3' => 4, // Age 4 months
    'Polio 4' => 4,
    'IPV' => 4,
    'Campak-Rubella' => 9, // Age 9 months
    'DPT-HB-Hib Lanjutan' => 18, // Age 18 months
    'Campak-Rubella Lanjutan' => 18 // Age 18 months
];

$balitaCheckId = 1;
$imunisasiId = 1;

foreach ($anakPosyanduData as $anak) {
    $anakId = $anak['id'];
    $gender = $anak['gender'];
    $dobString = $anak['tanggal_lahir']; // YYYY-MM-DD
    $dob = new DateTime($dobString);
    
    // Every balita has at least 3 checkups
    $numChecks = rand(3, 6);
    
    // Generate dates: 2025-2026, which must be after DOB!
    $checkDates = [];
    $maxAgeInMonths = $anak['umur_bulan'];
    
    $attempts = 0;
    while (count($checkDates) < $numChecks && $attempts < 100) {
        $attempts++;
        $ageAtCheck = ($maxAgeInMonths <= 3) ? rand(0, $maxAgeInMonths) : rand(1, $maxAgeInMonths);
        
        $checkDate = clone $dob;
        $checkDate->add(new DateInterval('P' . $ageAtCheck . 'M'));
        // Add random days (0 to 20) to make date realistic
        $checkDate->add(new DateInterval('P' . rand(0, 20) . 'D'));
        
        // Ensure checkDate is within 2025-2026
        $checkYear = intval($checkDate->format('Y'));
        if ($checkYear < 2025) {
            $checkDate = new DateTime('2025-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT));
            // Make sure it is still after DOB!
            if ($checkDate < $dob) {
                $checkDate = clone $dob;
                $checkDate->add(new DateInterval('P15D'));
            }
        }
        if (intval($checkDate->format('Y')) > 2026) {
            $checkDate = new DateTime('2026-' . str_pad(rand(6, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT));
        }
        
        $dateStr = $checkDate->format('Y-m-d');
        if (!in_array($dateStr, array_column($checkDates, 'date'))) {
            $checkDates[] = [
                'date' => $dateStr,
                'age_months' => $ageAtCheck
            ];
        }
    }
    
    // Sort chronologically
    usort($checkDates, function ($item1, $item2) {
        return strcmp($item1['date'], $item2['date']);
    });
    
    // Generate Checkup Records
    foreach ($checkDates as $cd) {
        $balitaStats['total_checks']++;
        $cDate = $cd['date'];
        $cAge = $cd['age_months'];
        
        // Growth curve calculations based on age in months
        if ($gender === 'L') {
            $hAvg = 49.0 + (1.8 * $cAge) - (0.014 * $cAge * $cAge);
            $wAvg = 3.3 + (0.5 * $cAge) - (0.0045 * $cAge * $cAge);
        } else {
            $hAvg = 48.0 + (1.75 * $cAge) - (0.0135 * $cAge * $cAge);
            $wAvg = 3.1 + (0.46 * $cAge) - (0.0042 * $cAge * $cAge);
        }
        
        // Roll stunting target: 8-10% (say, 9%)
        $isStunted = (rand(1, 100) <= 9);
        if ($isStunted) {
            $balitaStats['stunting_cases']++;
            $statusStunting = (rand(1, 10) <= 3) ? 'Sangat Pendek' : 'Pendek';
            $height = round($hAvg * (rand(86, 91) / 100.0), 1);
            $weight = round($wAvg * (rand(76, 86) / 100.0), 1);
        } else {
            $statusStunting = (rand(1, 100) > 96) ? 'Tinggi' : 'Normal';
            $height = round($hAvg * (rand(97, 105) / 100.0), 1);
            $weight = round($wAvg * (rand(92, 112) / 100.0), 1);
        }
        
        // Nutritive Status base on weight deviation
        $weightRatio = $weight / $wAvg;
        if ($weightRatio < 0.75) {
            $statusGizi = 'Gizi Buruk';
        } elseif ($weightRatio < 0.88) {
            $statusGizi = 'Gizi Kurang';
        } elseif ($weightRatio < 1.15) {
            $statusGizi = 'Gizi Baik';
        } elseif ($weightRatio < 1.25) {
            $statusGizi = 'Gizi Lebih';
        } else {
            $statusGizi = 'Obesitas';
        }
        
        $lingkarKepala = round(34.0 + ($cAge * 0.4) - ($cAge * $cAge * 0.002) + (rand(-10, 10) / 10), 1);
        $lingkarLengan = round(9.5 + ($cAge * 0.15) + (rand(-5, 5) / 10), 1);
        
        // Vitamin A (Typically given in February and August for children aged 6-59 months)
        $vitaminA = 'Tidak';
        if ($cAge >= 6) {
            $examMonth = intval(substr($cDate, 5, 2));
            if ($examMonth === 2 || $examMonth === 8 || rand(1, 10) > 8) {
                $vitaminA = 'Ya';
            }
        }
        
        // Immunization status in checkup (refers to whether immunizations are active/completed)
        $imunisasiCheck = ($cAge >= 9 && rand(1, 10) > 2) ? 'Lengkap' : 'Belum Lengkap';
        
        $pemeriksaanBalitaRecords[] = [
            'id' => $balitaCheckId,
            'anak_id' => $anakId,
            'tanggal' => $cDate,
            'berat_badan' => $weight,
            'tinggi_badan' => $height,
            'lingkar_kepala' => $lingkarKepala,
            'lingkar_lengan' => $lingkarLengan,
            'status_gizi' => $statusGizi,
            'status_stunting' => $statusStunting,
            'vitamin_a' => $vitaminA,
            'imunisasi' => $imunisasiCheck
        ];
        $balitaCheckId++;
    }
    
    // Generate Riwayat Imunisasi based on age
    // We award immunizations that match their age
    foreach ($imunisasiTypes as $type => $recAgeMonths) {
        if ($maxAgeInMonths >= $recAgeMonths) {
            // Child has reached the age for this immunization
            // Roll if they actually got it (90% coverage rate)
            $gotIt = (rand(1, 100) <= 90);
            if ($gotIt) {
                $imunDate = clone $dob;
                $imunDate->add(new DateInterval('P' . $recAgeMonths . 'M'));
                // Add some offset days
                $imunDate->add(new DateInterval('P' . rand(0, 15) . 'D'));
                
                // Keep dates logical
                if ($imunDate > new DateTime('2026-06-23')) {
                    continue; // Skip future immunizations
                }
                
                $riwayatImunisasiRecords[] = [
                    'id' => $imunisasiId,
                    'anak_id' => $anakId,
                    'jenis' => $type,
                    'tanggal' => $imunDate->format('Y-m-d'),
                    'status' => 'Lengkap'
                ];
                $imunisasiId++;
            } else {
                // Not received yet or missed
                $riwayatImunisasiRecords[] = [
                    'id' => $imunisasiId,
                    'anak_id' => $anakId,
                    'jenis' => $type,
                    'tanggal' => '0000-00-00', // standard for missing or just skip. We will generate a date after DOB and set status to 'Belum'
                    'status' => 'Belum'
                ];
                // Adjust date to a default dummy date (e.g. current date or DOB + age months)
                $dummyDate = clone $dob;
                $dummyDate->add(new DateInterval('P' . $recAgeMonths . 'M'));
                $riwayatImunisasiRecords[count($riwayatImunisasiRecords)-1]['tanggal'] = $dummyDate->format('Y-m-d');
                $imunisasiId++;
            }
        }
    }
}

// Write Pemeriksaan Balita SQL Inserts
echo "Writing Pemeriksaan Balita SQL...\n";
$chunks = array_chunk($pemeriksaanBalitaRecords, $chunkSize);
foreach ($chunks as $chunk) {
    $sql = "INSERT INTO `pemeriksaan_balita` (`id`, `anak_id`, `tanggal_pemeriksaan`, `berat_badan`, `tinggi_badan`, `lingkar_kepala`, `lingkar_lengan`, `status_gizi`, `status_stunting`, `vitamin_a`, `imunisasi`) VALUES\n";
    $values = [];
    foreach ($chunk as $rec) {
        $values[] = "(" . implode(', ', [
            $rec['id'],
            $rec['anak_id'],
            sqlEscape($rec['tanggal']),
            $rec['berat_badan'],
            $rec['tinggi_badan'],
            $rec['lingkar_kepala'],
            $rec['lingkar_lengan'],
            sqlEscape($rec['status_gizi']),
            sqlEscape($rec['status_stunting']),
            sqlEscape($rec['vitamin_a']),
            sqlEscape($rec['imunisasi'])
        ]) . ")";
    }
    $sql .= implode(",\n", $values) . ";\n\n";
    writeSqlAndCombined($fpPemeriksaanBalita, $sql);
}
fclose($fpPemeriksaanBalita);

// Write Riwayat Imunisasi SQL Inserts
echo "Writing Riwayat Imunisasi SQL...\n";
$chunks = array_chunk($riwayatImunisasiRecords, $chunkSize);
foreach ($chunks as $chunk) {
    $sql = "INSERT INTO `riwayat_imunisasi` (`id`, `anak_id`, `jenis_imunisasi`, `tanggal_imunisasi`, `status`) VALUES\n";
    $values = [];
    foreach ($chunk as $rec) {
        $values[] = "(" . implode(', ', [
            $rec['id'],
            $rec['anak_id'],
            sqlEscape($rec['jenis']),
            sqlEscape($rec['tanggal']),
            sqlEscape($rec['status'])
        ]) . ")";
    }
    $sql .= implode(",\n", $values) . ";\n\n";
    writeSqlAndCombined($fpRiwayatImunisasi, $sql);
}
fclose($fpRiwayatImunisasi);

// Write Footer for Combined File
fwrite($combinedFile, "SET FOREIGN_KEY_CHECKS = 1;\n");
fclose($combinedFile);

// ---------------------------------------------------------
// PRINT STATISTICAL REPORT
// ---------------------------------------------------------

$stuntingPct = $stats['total_checks'] > 0 ? round(($stats['stunting_cases'] / $stats['total_checks']) * 100, 2) : 0;
$anemiaPct = $stats['anemia_checked'] > 0 ? round(($stats['anemia_cases'] / $stats['anemia_checked']) * 100, 2) : 0;
$kariesPct = $stats['total_checks'] > 0 ? round(($stats['karies_cases'] / $stats['total_checks']) * 100, 2) : 0;
$mataPct = $stats['total_checks'] > 0 ? round(($stats['mata_cases'] / $stats['total_checks']) * 100, 2) : 0;

$balitaStuntingPct = $balitaStats['total_checks'] > 0 ? round(($balitaStats['stunting_cases'] / $balitaStats['total_checks']) * 100, 2) : 0;

echo "\n============================================\n";
echo "SIREHAT DUMMY DATA GENERATION SUCCESSFUL!\n";
echo "============================================\n";
echo "Output Directory: " . realpath($outputDir) . "\n\n";
echo "Generated counts:\n";
echo "  - Puskesmas          : 10\n";
echo "  - Posyandu           : 50\n";
echo "  - Sekolah            : 100\n";
echo "  - Siswa              : 5,000\n";
echo "  - Anak Posyandu      : 3,000\n";
echo "  - Student Checks     : " . $stats['total_checks'] . "\n";
echo "  - Balita Checks      : " . $balitaStats['total_checks'] . "\n";
echo "  - Immunization Recs  : " . count($riwayatImunisasiRecords) . "\n\n";

echo "Statistical Verification:\n";
echo "  - Stunting Rate (Siswa)   : " . $stuntingPct . "% (Target: 8-10%)\n";
echo "  - Stunting Rate (Balita)  : " . $balitaStuntingPct . "% (Target: 8-10%)\n";
echo "  - Anemia Rate (Female S.) : " . $anemiaPct . "% (Target: 10-15%)\n";
echo "  - Karies Gigi (Siswa)     : " . $kariesPct . "% (Target: ~20%)\n";
echo "  - Eye Disorders (Siswa)   : " . $mataPct . "% (Target: ~15%)\n";
echo "============================================\n";
