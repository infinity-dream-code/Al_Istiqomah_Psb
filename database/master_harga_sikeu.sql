-- master_harga SIKEU (DB 56 / al_istiqomah_sikeu)
-- Setara konsep master harga SPMB lama:
--   harga              = biaya pendaftaran
--   harga_registrasi   = total daftar ulang
--   detail_pendaftaran = list akun biaya daftar (no_akun, nama_tagihan, biaya)
--   detail_registrasi  = list akun biaya daftar ulang

CREATE TABLE IF NOT EXISTS `master_harga` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode` varchar(30) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `unit_id` int DEFAULT NULL,
  `tahun_ajaran_id` int DEFAULT NULL,
  `jenis_siswa_id` int DEFAULT NULL,
  `akun_penerimaan_id` int NOT NULL COMMENT 'akun default biaya pendaftaran',
  `akun_registrasi_id` int DEFAULT NULL COMMENT 'akun default daftar ulang',
  `jenis_biaya` enum('pendaftaran','registrasi','lainnya') NOT NULL DEFAULT 'pendaftaran',
  `harga` decimal(15,2) NOT NULL DEFAULT 0.00,
  `harga_registrasi` decimal(15,2) NOT NULL DEFAULT 0.00,
  `detail_pendaftaran` json DEFAULT NULL,
  `detail_registrasi` json DEFAULT NULL,
  `keterangan` text,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_master_harga_kode` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Contoh detail (format sama master harga SPMB lama):
-- detail_pendaftaran:
-- [
--   {"no_akun":"ACC_SPMB_DAFTAR","nama_tagihan":"Biaya Pendaftaran","biaya":150000}
-- ]
-- detail_registrasi:
-- [
--   {"no_akun":"ACC_SPMB_DAFUL","nama_tagihan":"Biaya Daftar Ulang","biaya":1500000},
--   {"no_akun":"ACC003","nama_tagihan":"Seragam","biaya":350000}
-- ]
