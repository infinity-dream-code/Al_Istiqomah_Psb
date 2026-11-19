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

 Date: 19/11/2026 10:38:29
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for tagihan
-- ----------------------------
DROP TABLE IF EXISTS `tagihan`;
CREATE TABLE `tagihan`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_tagihan` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `siswa_id` int NOT NULL,
  `tahun_ajaran_id` int NOT NULL,
  `akun_penerimaan_id` int NOT NULL,
  `prioritas_id` int NULL DEFAULT NULL,
  `jenis_tagihan` enum('cicil','lunas') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'lunas',
  `nama_tagihan` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `jumlah_tagihan` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `jumlah_bayar` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `sisa_tagihan` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `tanggal_jatuh_tempo` date NULL DEFAULT NULL,
  `periode_tahun` int NULL DEFAULT NULL,
  `periode_bulan` int NULL DEFAULT NULL,
  `status` enum('belum_lunas','lunas','batal') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'belum_lunas',
  `keterangan` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `created_by` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `kode_tagihan`(`kode_tagihan` ASC) USING BTREE,
  INDEX `tagihan_ibfk_4`(`prioritas_id` ASC) USING BTREE,
  INDEX `tagihan_ibfk_1`(`siswa_id` ASC) USING BTREE,
  INDEX `tagihan_ibfk_2`(`tahun_ajaran_id` ASC) USING BTREE,
  INDEX `tagihan_ibfk_3`(`akun_penerimaan_id` ASC) USING BTREE,
  INDEX `tagihan_ibfk_5`(`created_by` ASC) USING BTREE,
  CONSTRAINT `tagihan_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `tagihan_ibfk_2` FOREIGN KEY (`tahun_ajaran_id`) REFERENCES `tahun_ajaran` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `tagihan_ibfk_3` FOREIGN KEY (`akun_penerimaan_id`) REFERENCES `akun_penerimaan` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `tagihan_ibfk_4` FOREIGN KEY (`prioritas_id`) REFERENCES `prioritas_pembayaran` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `tagihan_ibfk_5` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 528 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tagihan
-- ----------------------------
INSERT INTO `tagihan` VALUES (495, 'TAG2026111890B6BB', 102, 27, 22, 13, 'cicil', 'SPP Bulan Januari', 1250000.00, 0.00, 1250000.00, '2026-11-20', 2026, 11, 'belum_lunas', '', 1, '2026-11-18 12:54:49', '2026-11-18 12:54:49');
INSERT INTO `tagihan` VALUES (496, 'TAG20261118911ED5', 103, 27, 22, 13, 'cicil', 'SPP Bulan Januari', 1250000.00, 0.00, 1250000.00, '2026-11-20', 2026, 11, 'belum_lunas', '', 1, '2026-11-18 12:54:49', '2026-11-18 12:54:49');
INSERT INTO `tagihan` VALUES (497, 'TAG20261118615AA1', 101, 27, 23, 13, 'lunas', 'SPP Bulanan Januari 2027', 600000.00, 600000.00, 0.00, '2026-11-10', 2027, 1, 'lunas', '', 1, '2026-11-18 12:59:18', '2026-11-18 12:59:18');
INSERT INTO `tagihan` VALUES (498, 'TAG2026111861678C', 101, 27, 23, 13, 'lunas', 'SPP Bulanan Februari 2027', 600000.00, 0.00, 600000.00, '2026-12-10', 2027, 2, 'belum_lunas', '', 1, '2026-11-18 12:59:18', '2026-11-18 12:59:18');
INSERT INTO `tagihan` VALUES (499, 'TAG20261118616C6D', 101, 27, 23, 13, 'lunas', 'SPP Bulanan Maret 2027', 600000.00, 0.00, 600000.00, '2027-01-10', 2027, 3, 'belum_lunas', '', 1, '2026-11-18 12:59:18', '2026-11-18 12:59:18');
INSERT INTO `tagihan` VALUES (500, 'TAG20261118617183', 101, 27, 23, 13, 'lunas', 'SPP Bulanan April 2027', 600000.00, 0.00, 600000.00, '2027-02-10', 2027, 4, 'belum_lunas', '', 1, '2026-11-18 12:59:18', '2026-11-18 12:59:18');
INSERT INTO `tagihan` VALUES (501, 'TAG20261118617652', 101, 27, 23, 13, 'lunas', 'SPP Bulanan Mei 2027', 600000.00, 0.00, 600000.00, '2027-03-10', 2027, 5, 'belum_lunas', '', 1, '2026-11-18 12:59:18', '2026-11-18 12:59:18');
INSERT INTO `tagihan` VALUES (502, 'TAG20261118617A49', 101, 27, 23, 13, 'lunas', 'SPP Bulanan Juni 2027', 600000.00, 0.00, 600000.00, '2027-04-10', 2027, 6, 'belum_lunas', '', 1, '2026-11-18 12:59:18', '2026-11-18 12:59:18');
INSERT INTO `tagihan` VALUES (503, 'TAG2026111861CEC9', 104, 27, 23, 13, 'lunas', 'SPP Bulanan Januari 2027', 600000.00, 0.00, 600000.00, '2026-11-10', 2027, 1, 'belum_lunas', '', 1, '2026-11-18 12:59:18', '2026-11-18 12:59:18');
INSERT INTO `tagihan` VALUES (504, 'TAG2026111861D36D', 104, 27, 23, 13, 'lunas', 'SPP Bulanan Februari 2027', 600000.00, 0.00, 600000.00, '2026-12-10', 2027, 2, 'belum_lunas', '', 1, '2026-11-18 12:59:18', '2026-11-18 12:59:18');
INSERT INTO `tagihan` VALUES (505, 'TAG2026111861D8AB', 104, 27, 23, 13, 'lunas', 'SPP Bulanan Maret 2027', 600000.00, 0.00, 600000.00, '2027-01-10', 2027, 3, 'belum_lunas', '', 1, '2026-11-18 12:59:18', '2026-11-18 12:59:18');
INSERT INTO `tagihan` VALUES (506, 'TAG2026111861DDFD', 104, 27, 23, 13, 'lunas', 'SPP Bulanan April 2027', 600000.00, 0.00, 600000.00, '2027-02-10', 2027, 4, 'belum_lunas', '', 1, '2026-11-18 12:59:18', '2026-11-18 12:59:18');
INSERT INTO `tagihan` VALUES (507, 'TAG2026111861E2E5', 104, 27, 23, 13, 'lunas', 'SPP Bulanan Mei 2027', 600000.00, 0.00, 600000.00, '2027-03-10', 2027, 5, 'belum_lunas', '', 1, '2026-11-18 12:59:18', '2026-11-18 12:59:18');
INSERT INTO `tagihan` VALUES (508, 'TAG2026111861E980', 104, 27, 23, 13, 'lunas', 'SPP Bulanan Juni 2027', 600000.00, 0.00, 600000.00, '2027-04-10', 2027, 6, 'belum_lunas', '', 1, '2026-11-18 12:59:18', '2026-11-18 12:59:18');
INSERT INTO `tagihan` VALUES (509, 'TAG202611184EFCFF', 108, 26, 33, 13, 'cicil', 'Tunggakan Daftar Ulang 2025', 1300000.00, 0.00, 1300000.00, '2026-12-31', 2025, 7, 'belum_lunas', '', 1, '2026-11-18 15:15:48', '2026-11-18 15:15:48');
INSERT INTO `tagihan` VALUES (510, 'TAG202611185014B5', 109, 26, 33, 13, 'cicil', 'Tunggakan Daftar Ulang 2025', 1300000.00, 300000.00, 1000000.00, '2026-12-31', 2025, 7, 'belum_lunas', '', 1, '2026-11-18 15:15:49', '2026-11-18 15:15:49');
INSERT INTO `tagihan` VALUES (511, 'TAG20261118F3821F', 108, 26, 23, 14, 'lunas', 'SPP Bulanan Juli 2026', 550000.00, 0.00, 550000.00, NULL, 2026, 7, 'belum_lunas', '', 1, '2026-11-18 15:17:35', '2026-11-18 15:17:35');
INSERT INTO `tagihan` VALUES (512, 'TAG20261118F38621', 108, 26, 23, 14, 'lunas', 'SPP Bulanan Agustus 2026', 550000.00, 0.00, 550000.00, NULL, 2026, 8, 'belum_lunas', '', 1, '2026-11-18 15:17:35', '2026-11-18 15:17:35');
INSERT INTO `tagihan` VALUES (513, 'TAG20261118F38906', 108, 26, 23, 14, 'lunas', 'SPP Bulanan September 2026', 550000.00, 0.00, 550000.00, NULL, 2026, 9, 'belum_lunas', '', 1, '2026-11-18 15:17:35', '2026-11-18 15:17:35');
INSERT INTO `tagihan` VALUES (514, 'TAG20261118F38B96', 108, 26, 23, 14, 'lunas', 'SPP Bulanan Oktober 2026', 550000.00, 0.00, 550000.00, NULL, 2026, 10, 'belum_lunas', '', 1, '2026-11-18 15:17:35', '2026-11-18 15:17:35');
INSERT INTO `tagihan` VALUES (515, 'TAG20261118F38E3A', 108, 26, 23, 14, 'lunas', 'SPP Bulanan November 2026', 550000.00, 0.00, 550000.00, NULL, 2026, 11, 'belum_lunas', '', 1, '2026-11-18 15:17:35', '2026-11-18 15:17:35');
INSERT INTO `tagihan` VALUES (516, 'TAG20261118F3911C', 108, 26, 23, 14, 'lunas', 'SPP Bulanan Desember 2026', 550000.00, 0.00, 550000.00, NULL, 2026, 12, 'belum_lunas', '', 1, '2026-11-18 15:17:35', '2026-11-18 15:17:35');
INSERT INTO `tagihan` VALUES (517, 'TAG20261118F3EF09', 109, 26, 23, 14, 'lunas', 'SPP Bulanan Juli 2026', 550000.00, 0.00, 550000.00, NULL, 2026, 7, 'belum_lunas', '', 1, '2026-11-18 15:17:35', '2026-11-18 15:17:35');
INSERT INTO `tagihan` VALUES (518, 'TAG20261118F3F247', 109, 26, 23, 14, 'lunas', 'SPP Bulanan Agustus 2026', 550000.00, 0.00, 550000.00, NULL, 2026, 8, 'belum_lunas', '', 1, '2026-11-18 15:17:35', '2026-11-18 15:17:35');
INSERT INTO `tagihan` VALUES (519, 'TAG20261118F3F544', 109, 26, 23, 14, 'lunas', 'SPP Bulanan September 2026', 550000.00, 0.00, 550000.00, NULL, 2026, 9, 'belum_lunas', '', 1, '2026-11-18 15:17:35', '2026-11-18 15:17:35');
INSERT INTO `tagihan` VALUES (520, 'TAG20261118F3F84E', 109, 26, 23, 14, 'lunas', 'SPP Bulanan Oktober 2026', 550000.00, 0.00, 550000.00, NULL, 2026, 10, 'belum_lunas', '', 1, '2026-11-18 15:17:35', '2026-11-18 15:17:35');
INSERT INTO `tagihan` VALUES (521, 'TAG20261118F3FB94', 109, 26, 23, 14, 'lunas', 'SPP Bulanan November 2026', 550000.00, 0.00, 550000.00, NULL, 2026, 11, 'belum_lunas', '', 1, '2026-11-18 15:17:35', '2026-11-18 15:17:35');
INSERT INTO `tagihan` VALUES (522, 'TAG20261118F3FEB5', 109, 26, 23, 14, 'lunas', 'SPP Bulanan Desember 2026', 550000.00, 0.00, 550000.00, NULL, 2026, 12, 'belum_lunas', '', 1, '2026-11-18 15:17:35', '2026-11-18 15:17:35');
INSERT INTO `tagihan` VALUES (523, 'TAG20261118974314', 103, 26, 25, NULL, 'lunas', 'Jemputan November', 150000.00, 0.00, 150000.00, NULL, 2026, 11, 'belum_lunas', '', 1, '2026-11-18 15:41:45', '2026-11-18 15:41:45');
INSERT INTO `tagihan` VALUES (524, 'TAG2026111897E60D', 109, 26, 25, NULL, 'lunas', 'Jemputan November', 150000.00, 0.00, 150000.00, NULL, 2026, 11, 'belum_lunas', '', 1, '2026-11-18 15:41:45', '2026-11-18 15:41:45');
INSERT INTO `tagihan` VALUES (525, 'TAG202611189886EF', 105, 26, 25, NULL, 'lunas', 'Jemputan November', 150000.00, 0.00, 150000.00, NULL, 2026, 11, 'belum_lunas', '', 1, '2026-11-18 15:41:45', '2026-11-18 15:41:45');
INSERT INTO `tagihan` VALUES (526, 'TAG2026111898B46D', 107, 26, 25, NULL, 'lunas', 'Jemputan November', 150000.00, 0.00, 150000.00, NULL, 2026, 11, 'belum_lunas', '', 1, '2026-11-18 15:41:45', '2026-11-18 15:41:45');
INSERT INTO `tagihan` VALUES (527, 'TAG2026111898DBBF', 104, 26, 25, NULL, 'lunas', 'Jemputan November', 150000.00, 0.00, 150000.00, NULL, 2026, 11, 'belum_lunas', '', 1, '2026-11-18 15:41:45', '2026-11-18 15:41:45');

SET FOREIGN_KEY_CHECKS = 1;
