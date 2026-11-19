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

 Date: 19/11/2026 10:38:21
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for jenis_siswa
-- ----------------------------
DROP TABLE IF EXISTS `jenis_siswa`;
CREATE TABLE `jenis_siswa`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode_jenis` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nama_jenis` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `unit_id` int NULL DEFAULT NULL,
  `keterangan` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `status` enum('active','inactive') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `kode_jenis`(`kode_jenis` ASC) USING BTREE,
  INDEX `jenis_siswa_ibfk_1`(`unit_id` ASC) USING BTREE,
  CONSTRAINT `jenis_siswa_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `unit_sekolah` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of jenis_siswa
-- ----------------------------
INSERT INTO `jenis_siswa` VALUES (16, 'JNS001', 'Reguler', NULL, NULL, 'active', '2026-11-17 13:29:51', '2026-11-17 13:29:51');
INSERT INTO `jenis_siswa` VALUES (17, 'JNS002', 'Beasiswa', NULL, NULL, 'active', '2026-11-17 13:30:20', '2026-11-17 13:30:20');
INSERT INTO `jenis_siswa` VALUES (18, 'JNS003', 'Anak Guru', NULL, NULL, 'active', '2026-11-17 13:30:34', '2026-11-17 13:30:34');
INSERT INTO `jenis_siswa` VALUES (19, 'JNS004', 'Yatim Piatu', NULL, NULL, 'active', '2026-11-17 13:31:01', '2026-11-17 13:31:01');
INSERT INTO `jenis_siswa` VALUES (20, 'JNS005', 'Jemputan', NULL, NULL, 'active', '2026-11-18 15:43:01', '2026-11-18 15:43:01');

SET FOREIGN_KEY_CHECKS = 1;
