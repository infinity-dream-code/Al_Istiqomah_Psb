-- Master SPMB di DB 56 (al_istiqomah_sikeu)
-- Tabel: master_jalur, master_gelombang, master_fakultas, master_prodi, master_harga
-- App Laravel baca lewat connection `sikeu` (DB_SIKEU_* di .env)

-- master_harga berisi:
--   harga / harga_final     = biaya pendaftaran
--   harga_registrasi        = total daftar ulang
--   detail / detail_pendaftaran = list akun daftar
--   detail_registrasi       = list akun daftar ulang
--   id_jalur / id_gelombang / id_fakultas = untuk form enroll
