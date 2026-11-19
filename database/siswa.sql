/*
 Navicat Premium Dump SQL

 Source Server         : 56
 Source Server Type    : MySQL
 Source Server Version : 80405 (8.4.5)
 Source Host           : 101.50.2.56:3306
 Source Schema         : al_istiqomah_sikeu

 Target Server Type    : MySQL
 Target Server Version : 80405 (8.4.5)
 File Encoding         : 65001

 Date: 19/11/2026 10:36:18
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for siswa
-- ----------------------------
DROP TABLE IF EXISTS `siswa`;
CREATE TABLE `siswa`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nis` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nisn` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nama_lengkap` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `jenis_kelamin` enum('L','P') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `tempat_lahir` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tanggal_lahir` date NULL DEFAULT NULL,
  `alamat` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `no_hp` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `parent_id` int NULL DEFAULT NULL,
  `kelas_id` int NULL DEFAULT NULL,
  `jenis_siswa_id` int NULL DEFAULT NULL,
  `angkatan_id` int NULL DEFAULT NULL,
  `angkatan_sis_id` int NULL DEFAULT NULL,
  `unit_id` int NULL DEFAULT NULL,
  `tahun_ajaran_id` int NULL DEFAULT NULL,
  `status` enum('active','lulus','pindah','dropout') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `nis`(`nis` ASC) USING BTREE,
  INDEX `siswa_ibfk_1`(`parent_id` ASC) USING BTREE,
  INDEX `siswa_ibfk_2`(`kelas_id` ASC) USING BTREE,
  INDEX `siswa_ibfk_3`(`jenis_siswa_id` ASC) USING BTREE,
  INDEX `siswa_ibfk_4`(`angkatan_id` ASC) USING BTREE,
  INDEX `siswa_ibfk_5`(`tahun_ajaran_id` ASC) USING BTREE,
  INDEX `siswa_ibfk_6`(`unit_id` ASC) USING BTREE,
  INDEX `idx_angkatan_sis`(`angkatan_sis_id` ASC) USING BTREE,
  CONSTRAINT `fk_siswa_angkatan_sis` FOREIGN KEY (`angkatan_sis_id`) REFERENCES `angkatansis` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `siswa_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `parents` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `siswa_ibfk_2` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `siswa_ibfk_3` FOREIGN KEY (`jenis_siswa_id`) REFERENCES `jenis_siswa` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `siswa_ibfk_4` FOREIGN KEY (`angkatan_id`) REFERENCES `angkatan` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `siswa_ibfk_5` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `siswa_ibfk_6` FOREIGN KEY (`unit_id`) REFERENCES `unit_sekolah` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 110 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of siswa
-- ----------------------------
INSERT INTO `siswa` VALUES (101, '1234567801', NULL, 'ICT Novia', 'P', 'Semarang', '2026-11-17', 'Jl. Dewi Sartika No.19, Kowangan, Jl. Dewi Sartika No.25, Tegalsari, Jampirejo, Kec. Temanggung, Kabupaten Temanggung, Jawa Tengah', '081227247571', NULL, 40, 19, 31, 7, NULL, NULL, 'active', '2026-11-17 13:43:23', '2026-11-17 13:43:23');
INSERT INTO `siswa` VALUES (102, '2026001', '98765431', 'Ahmad Rizky Pratama', 'L', 'Jakarta', '2012-12-03', 'Jl. Melati No. 10, Jakarta', '80000000001', 101, 40, 18, 31, 7, 1, NULL, 'active', '2026-11-18 12:44:30', '2026-11-18 12:44:30');
INSERT INTO `siswa` VALUES (103, '2026002', '98765432', 'Siti Nurhaliza', 'P', 'Bandung', '2011-07-25', 'Jl. Mawar No. 15, Bandung', '80000000002', 102, 40, 18, 31, 7, 1, NULL, 'active', '2026-11-18 12:44:30', '2026-11-18 12:44:30');
INSERT INTO `siswa` VALUES (104, '2026003', '98765433', 'Muhammad Farhan', 'L', 'Bekasi', '2012-08-11', 'Jl. Anggrek No. 21, Bekasi', '80000000003', 103, 40, 19, 31, 7, 1, NULL, 'active', '2026-11-18 12:44:31', '2026-11-18 12:44:31');
INSERT INTO `siswa` VALUES (105, '2026004', '98765434', 'Putri Amelia', 'P', 'Depok', '2011-05-19', 'Jl. Kenanga No. 8, Depok', '80000000004', 104, 40, 16, 31, 7, 1, NULL, 'active', '2026-11-18 12:44:31', '2026-11-18 12:44:31');
INSERT INTO `siswa` VALUES (106, '2026005', '98765435', 'Dimas Aditya Saputra', 'L', 'Tangerang', '2012-09-30', 'Jl. Flamboyan No. 5, Tangerang', '80000000005', 105, 40, 16, 31, 7, 1, NULL, 'active', '2026-11-18 12:44:31', '2026-11-18 12:44:31');
INSERT INTO `siswa` VALUES (107, '2026006', '98765436', 'Nur Aisyah Putri', 'P', 'Bogor', '2012-02-14', 'Jl. Dahlia No. 12, Bogor', '80000000006', 106, 40, 20, 31, 7, 1, NULL, 'active', '2026-11-18 12:44:32', '2026-11-18 12:44:32');
INSERT INTO `siswa` VALUES (108, '789456174', '0012345678', 'Ahmad Fauzi', 'L', 'Jakarta', '2010-01-15', 'Jl. Contoh No. 123', '081234567890', 108, 42, 20, 30, 8, 9, NULL, 'active', '2026-11-18 15:01:46', '2026-11-18 15:01:46');
INSERT INTO `siswa` VALUES (109, '98741583', '0012345679', 'Siti Alimah', 'P', 'Bandung', '2010-03-20', 'Jl. Contoh No. 456', '081234567891', 109, 42, 20, 30, 8, 9, NULL, 'active', '2026-11-18 15:01:46', '2026-11-18 15:01:46');

SET FOREIGN_KEY_CHECKS = 1;
