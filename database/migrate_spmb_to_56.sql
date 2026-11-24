-- =============================================================================
-- Cutover SPMB → DB 56 (al_istiqomah_sikeu)
-- =============================================================================
-- Prasyarat:
--   1. Backup 43 (al_istiqomah_psb) dan 56 (al_istiqomah_sikeu)
--   2. Import tabel SPMB dari 43 ke 56 DENGAN PRESERVE ID
--   3. Jangan overwrite tabel SIKEU: siswa, tagihan, transaksi, akun_*,
--      angkatan, angkatansis, tahun_ajaran, unit_sekolah, kelas, jenis_siswa,
--      saldo_siswa, dll.
--
-- Tabel wajib diimport dari 43 (cek di 56; yang MISSING wajib diisi):
--   user, data_peserta, data_ayah, data_ibu, data_alamat, data_sekolah,
--   master_jalur, master_gelombang, master_fakultas, master_prodi, master_harga,
--   master_akademik, master_agama, master_*, pmb_ref_*, ujian, temporary, ...
--   tagihan (SPMB daful) — rename ke tagihan_daful di langkah 1
--
-- Setelah import, jalankan blok di bawah di 56.
-- =============================================================================

USE al_istiqomah_sikeu;

-- 1) Rename tabel daftar-ulang SPMB agar tidak bentrok dengan SIKEU.tagihan
--    Hanya jika tabel SPMB (punya kolom id_peserta) masih bernama `tagihan`.
SET @has_spmb_tagihan := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = 'al_istiqomah_sikeu'
    AND TABLE_NAME = 'tagihan'
    AND COLUMN_NAME = 'id_peserta'
);
SET @has_daful := (
  SELECT COUNT(*) FROM information_schema.TABLES
  WHERE TABLE_SCHEMA = 'al_istiqomah_sikeu' AND TABLE_NAME = 'tagihan_daful'
);

-- Rename aman via procedure singkat
DROP PROCEDURE IF EXISTS spmb_rename_tagihan_daful;
DELIMITER $$
CREATE PROCEDURE spmb_rename_tagihan_daful()
BEGIN
  IF @has_spmb_tagihan > 0 AND @has_daful = 0 THEN
    RENAME TABLE tagihan TO tagihan_daful;
  ELSEIF @has_daful = 0 THEN
    CREATE TABLE IF NOT EXISTS tagihan_daful (
      id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
      id_peserta BIGINT UNSIGNED NULL,
      biaya_daful DECIMAL(15,2) NULL,
      detail JSON NULL,
      status TINYINT NULL,
      tanggal_pembayaran_daful DATETIME NULL,
      created_at TIMESTAMP NULL,
      updated_at TIMESTAMP NULL
    );
  END IF;
END$$
DELIMITER ;
CALL spmb_rename_tagihan_daful();
DROP PROCEDURE IF EXISTS spmb_rename_tagihan_daful;

-- 2) Kolom link gelombang → SIKEU angkatan / angkatansis
SET @has_angkatan_id := (
  SELECT COUNT(*) FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = 'al_istiqomah_sikeu'
    AND TABLE_NAME = 'master_gelombang'
    AND COLUMN_NAME = 'angkatan_id'
);
SET @sql := IF(@has_angkatan_id = 0,
  'ALTER TABLE master_gelombang ADD COLUMN angkatan_id INT NULL AFTER tahun_akademik, ADD COLUMN angkatan_sis_id INT NULL AFTER angkatan_id',
  'SELECT ''master_gelombang angkatan cols already exist'' AS info'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3) Checklist setelah cutover (jalankan manual / cek hasil):
-- SELECT COUNT(*) FROM master_harga;
-- SELECT COUNT(*) FROM data_peserta;
-- SELECT COUNT(*) FROM user;
-- SELECT id, gelombang, tahun_akademik, angkatan_id, angkatan_sis_id FROM master_gelombang;
-- SELECT id, nomor_pendaftaran, is_calon_spmb FROM siswa ORDER BY id DESC LIMIT 5;
-- SELECT id, siswa_id, nama_tagihan, jenis_tagihan, status FROM tagihan ORDER BY id DESC LIMIT 5;
