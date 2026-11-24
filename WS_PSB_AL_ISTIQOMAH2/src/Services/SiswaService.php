<?php

namespace WsSpmb\Services;

use PDO;
use WsSpmb\Support\Database;
use WsSpmb\Support\JsonResponse;

final class SiswaService
{
    private PDO $pdo;
    private LookupService $lookup;

    public function __construct(?PDO $pdo = null, ?LookupService $lookup = null)
    {
        $this->pdo = $pdo ?: Database::pdo();
        $this->lookup = $lookup ?: new LookupService($this->pdo);
    }

    public function findByNomorPendaftaran(string $nomor): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT s.*,
                    u.nama_unit,
                    k.nama_kelas
             FROM siswa s
             LEFT JOIN unit_sekolah u ON u.id = s.unit_id
             LEFT JOIN kelas k ON k.id = s.kelas_id
             WHERE s.nomor_pendaftaran = :no
             LIMIT 1"
        );
        $stmt->execute([':no' => $nomor]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function createFromSpmb(array $siswa): array
    {
        $nomor = trim((string) ($siswa['nomor_pendaftaran'] ?? ''));
        if ($nomor === '') {
            JsonResponse::error(422, "Field 'nomor_pendaftaran' wajib diisi.");
        }

        if ($this->findByNomorPendaftaran($nomor)) {
            JsonResponse::error(422, "Siswa dengan nomor pendaftaran {$nomor} sudah ada.");
        }

        $unitId = $this->lookup->resolveUnitId($siswa['unit'] ?? null);
        $kelasId = $this->lookup->resolveKelasId($siswa['kelas'] ?? null, $unitId);
        $tahunAjaranId = $this->lookup->resolveTahunAjaranId($siswa['tahun_akademik'] ?? null);
        $jenisSiswaId = $this->lookup->resolveJenisSiswaId($siswa['jenis_siswa'] ?? $siswa['jalur'] ?? 'Reguler');
        // Mapping SIKEU: angkatan_id = gelombang, angkatan_sis_id = tahun angkatan.
        // Prefer ID langsung dari master_gelombang (dropdown DB 56); fallback resolve dari nomor/nama.
        $angkatanId = null;
        if (!empty($siswa['angkatan_id']) && is_numeric($siswa['angkatan_id'])) {
            $angkatanId = $this->lookup->resolveAngkatanIdById((int) $siswa['angkatan_id'])
                ?: $this->lookup->resolveAngkatanId($siswa['gelombang'] ?? null);
        } else {
            $angkatanId = $this->lookup->resolveAngkatanId($siswa['gelombang'] ?? null);
        }
        $angkatanSisId = $this->lookup->resolveAngkatanSisId(
            $siswa['angkatan_sis_id'] ?? null,
            $siswa['tahun_akademik'] ?? null
        );

        $jk = strtoupper(trim((string) ($siswa['jenis_kelamin'] ?? 'L')));
        if (!in_array($jk, ['L', 'P'], true)) {
            $jk = 'L';
        }

        $va = trim((string) ($siswa['va_number'] ?? ''));
        $va = $va !== '' ? $va : null;

        $parentId = null;
        if (!empty($siswa['parent_id']) && is_numeric($siswa['parent_id'])) {
            $chk = $this->pdo->prepare('SELECT id FROM parents WHERE id = :id LIMIT 1');
            $chk->execute([':id' => (int) $siswa['parent_id']]);
            $parentId = $chk->fetchColumn() ? (int) $siswa['parent_id'] : null;
        }

        // Calon SPMB: NIS dikosongkan (NULL). Identitas sementara = nomor_pendaftaran + va_number.
        $stmt = $this->pdo->prepare(
            "INSERT INTO siswa (
                nis, nomor_pendaftaran, va_number, is_calon_spmb,
                nama_lengkap, jenis_kelamin, alamat, no_hp, parent_id,
                kelas_id, jenis_siswa_id, angkatan_id, angkatan_sis_id,
                unit_id, tahun_ajaran_id, status, created_at, updated_at
             ) VALUES (
                NULL, :nomor, :va, 1,
                :nama, :jk, :alamat, :hp, :parent_id,
                :kelas_id, :jenis_siswa_id, :angkatan_id, :angkatan_sis_id,
                :unit_id, :tahun_ajaran_id, 'active', NOW(), NOW()
             )"
        );

        $stmt->execute([
            ':nomor' => $nomor,
            ':va' => $va,
            ':nama' => trim((string) ($siswa['nama_siswa'] ?? '')),
            ':jk' => $jk,
            ':alamat' => trim((string) ($siswa['alamat_rumah'] ?? '')) ?: null,
            ':hp' => trim((string) ($siswa['no_hp'] ?? '')) ?: null,
            ':parent_id' => $parentId,
            ':kelas_id' => $kelasId,
            ':jenis_siswa_id' => $jenisSiswaId,
            ':angkatan_id' => $angkatanId,
            ':angkatan_sis_id' => $angkatanSisId,
            ':unit_id' => $unitId,
            ':tahun_ajaran_id' => $tahunAjaranId,
        ]);

        $siswaId = (int) $this->pdo->lastInsertId();

        if ($parentId) {
            $map = $this->pdo->prepare(
                "INSERT INTO parent_siswa_mapping (parent_id, siswa_id, is_primary, created_at)
                 VALUES (:parent_id, :siswa_id, 1, NOW())
                 ON DUPLICATE KEY UPDATE parent_id = VALUES(parent_id), is_primary = 1"
            );
            $map->execute([
                ':parent_id' => $parentId,
                ':siswa_id' => $siswaId,
            ]);
        }

        $saldo = $this->pdo->prepare(
            "INSERT INTO saldo_siswa (siswa_id, saldo_debit, saldo_kredit, saldo_akhir, updated_at)
             VALUES (:siswa_id, 0, 0, 0, NOW())"
        );
        $saldo->execute([':siswa_id' => $siswaId]);

        $created = $this->findByNomorPendaftaran($nomor);
        if (!$created) {
            JsonResponse::error(500, 'Gagal membaca ulang data siswa.');
        }

        return $created;
    }
}
