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

        // nis wajib unik di SIKEU; untuk calon SPMB pakai nomor pendaftaran.
        $cekNis = $this->pdo->prepare('SELECT id FROM siswa WHERE nis = :nis LIMIT 1');
        $cekNis->execute([':nis' => $nomor]);
        if ($cekNis->fetchColumn()) {
            JsonResponse::error(422, "NIS/nomor pendaftaran {$nomor} sudah dipakai.");
        }

        $unitId = $this->lookup->resolveUnitId($siswa['unit'] ?? null);
        $kelasId = $this->lookup->resolveKelasId($siswa['kelas'] ?? null, $unitId);
        $tahunAjaranId = $this->lookup->resolveTahunAjaranId($siswa['tahun_akademik'] ?? null);

        $jk = strtoupper(trim((string) ($siswa['jenis_kelamin'] ?? 'L')));
        if (!in_array($jk, ['L', 'P'], true)) {
            $jk = 'L';
        }

        $va = trim((string) ($siswa['va_number'] ?? ''));
        $va = $va !== '' ? $va : null;

        $stmt = $this->pdo->prepare(
            "INSERT INTO siswa (
                nis, nomor_pendaftaran, va_number, is_calon_spmb,
                nama_lengkap, jenis_kelamin, alamat, no_hp,
                kelas_id, unit_id, tahun_ajaran_id, status, created_at, updated_at
             ) VALUES (
                :nis, :nomor, :va, 1,
                :nama, :jk, :alamat, :hp,
                :kelas_id, :unit_id, :tahun_ajaran_id, 'active', NOW(), NOW()
             )"
        );

        $stmt->execute([
            ':nis' => $nomor,
            ':nomor' => $nomor,
            ':va' => $va,
            ':nama' => trim((string) ($siswa['nama_siswa'] ?? '')),
            ':jk' => $jk,
            ':alamat' => trim((string) ($siswa['alamat_rumah'] ?? '')) ?: null,
            ':hp' => trim((string) ($siswa['no_hp'] ?? '')) ?: null,
            ':kelas_id' => $kelasId,
            ':unit_id' => $unitId,
            ':tahun_ajaran_id' => $tahunAjaranId,
        ]);

        $siswaId = (int) $this->pdo->lastInsertId();

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
