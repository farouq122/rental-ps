-- =============================================
-- Database: rental_ps
-- Aplikasi Penyewaan PlayStation
-- Tanpa fitur login/autentikasi
-- =============================================
CREATE DATABASE IF NOT EXISTS `rental_ps` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `rental_ps`;

-- Tabel playstation
CREATE TABLE IF NOT EXISTS `playstation` (
  `id_ps` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_ps` VARCHAR(100) NOT NULL,
  `tipe_ps` ENUM('PS3', 'PS4', 'PS5') NOT NULL,
  `tarif_per_jam` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel pelanggan
CREATE TABLE IF NOT EXISTS `pelanggan` (
  `id_pelanggan` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(100) NOT NULL,
  `no_hp` VARCHAR(20) NOT NULL,
  `alamat` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel sewa
CREATE TABLE IF NOT EXISTS `sewa` (
  `id_sewa` INT AUTO_INCREMENT PRIMARY KEY,
  `id_ps` INT NOT NULL,
  `id_pelanggan` INT NOT NULL,
  `waktu_mulai` DATETIME NOT NULL,
  `durasi_jam` INT NOT NULL,
  `waktu_selesai` DATETIME NOT NULL,
  FOREIGN KEY (`id_ps`) REFERENCES `playstation` (`id_ps`) ON DELETE CASCADE,
  FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seeding data awal PlayStation (10 Unit Tetap)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE `playstation`;
INSERT INTO `playstation` (`id_ps`, `nama_ps`, `tipe_ps`, `tarif_per_jam`) VALUES
(1,  'PS 1',  'PS3', 5000),
(2,  'PS 2',  'PS3', 5000),
(3,  'PS 3',  'PS3', 5000),
(4,  'PS 4',  'PS4', 7000),
(5,  'PS 5',  'PS4', 7000),
(6,  'PS 6',  'PS4', 7000),
(7,  'PS 7',  'PS4', 7000),
(8,  'PS 8',  'PS5', 10000),
(9,  'PS 9',  'PS5', 10000),
(10, 'PS 10', 'PS5', 10000);
SET FOREIGN_KEY_CHECKS = 1;
