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

 Date: 19/11/2026 10:38:40
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for transaksi
-- ----------------------------
DROP TABLE IF EXISTS `transaksi`;
CREATE TABLE `transaksi`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_transaksi` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `siswa_id` int NOT NULL,
  `tagihan_id` int NULL DEFAULT NULL,
  `metode_pembayaran` enum('cash','teller','virtual_account','saldo','tunai','transfer_bank','qris') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `virtual_account` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `bank_code` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `jumlah_bayar` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `jumlah_potongan` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `total_bayar` decimal(15, 2) NOT NULL DEFAULT 0.00,
  `tanggal_bayar` date NOT NULL,
  `waktu_bayar` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','verified','rejected') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'pending',
  `bukti_pembayaran` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `keterangan` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `verified_by` int NULL DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_by` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `kode_transaksi`(`kode_transaksi` ASC) USING BTREE,
  INDEX `transaksi_ibfk_1`(`siswa_id` ASC) USING BTREE,
  INDEX `transaksi_ibfk_2`(`tagihan_id` ASC) USING BTREE,
  INDEX `transaksi_ibfk_3`(`verified_by` ASC) USING BTREE,
  INDEX `transaksi_ibfk_4`(`created_by` ASC) USING BTREE,
  CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`tagihan_id`) REFERENCES `tagihan` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `transaksi_ibfk_3` FOREIGN KEY (`verified_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `transaksi_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 458 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of transaksi
-- ----------------------------
INSERT INTO `transaksi` VALUES (455, 'TRX20261118CBE0ED', 101, 497, 'saldo', NULL, NULL, 100000.00, 0.00, 100000.00, '2026-11-18', '2026-11-18 13:31:08', 'verified', NULL, '', NULL, NULL, 20, '2026-11-18 13:31:08', '2026-11-18 13:31:08');
INSERT INTO `transaksi` VALUES (456, 'TRX20261118E0DC6F', 101, 497, 'tunai', NULL, NULL, 500000.00, 0.00, 500000.00, '2026-11-18', '2026-11-18 13:31:58', 'verified', NULL, '', NULL, NULL, 20, '2026-11-18 13:31:58', '2026-11-18 13:31:58');
INSERT INTO `transaksi` VALUES (457, 'TRX20261118F4F96A', 109, 510, 'tunai', NULL, NULL, 300000.00, 0.00, 300000.00, '2026-11-18', '2026-11-18 15:25:35', 'verified', NULL, '', NULL, NULL, 20, '2026-11-18 15:25:35', '2026-11-18 15:25:35');

SET FOREIGN_KEY_CHECKS = 1;
