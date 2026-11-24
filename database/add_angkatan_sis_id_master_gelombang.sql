-- Jalankan di DB 56 (al_istiqomah_sikeu) pada tabel master_gelombang SPMB.
-- Skip kalau kolom sudah ada.

ALTER TABLE master_gelombang
  ADD COLUMN angkatan_id INT NULL AFTER tahun_akademik;

ALTER TABLE master_gelombang
  ADD COLUMN angkatan_sis_id INT NULL AFTER angkatan_id;

-- Mapping:
--   angkatan_id     = angkatan.id      (gelombang)
--   angkatan_sis_id = angkatansis.id   (tahun angkatan)
