-- Jalankan di DB 43 (al_istiqomah_psb)
-- Relasi master_gelombang → SIKEU.angkatansis (tahun angkatan)

ALTER TABLE master_gelombang
  ADD COLUMN angkatan_sis_id INT NULL AFTER tahun_akademik;
