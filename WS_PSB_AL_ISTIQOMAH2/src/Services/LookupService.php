<?php

namespace WsSpmb\Services;

use PDO;
use WsSpmb\Support\Database;
use WsSpmb\Support\JsonResponse;

final class LookupService
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?: Database::pdo();
    }

    public function resolveTahunAjaranId(?string $tahunAkademik): int
    {
        $tahunAkademik = trim((string) $tahunAkademik);

        if ($tahunAkademik !== '') {
            $exact = $this->pdo->prepare(
                "SELECT id FROM tahun_ajaran WHERE tahun_ajaran = :ta LIMIT 1"
            );
            $exact->execute([':ta' => $tahunAkademik]);
            $id = $exact->fetchColumn();
            if ($id) {
                return (int) $id;
            }

            $like = $this->pdo->prepare(
                "SELECT id FROM tahun_ajaran
                 WHERE tahun_ajaran LIKE :ta
                 ORDER BY id DESC
                 LIMIT 1"
            );
            $like->execute([':ta' => $tahunAkademik . '%']);
            $id = $like->fetchColumn();
            if ($id) {
                return (int) $id;
            }
        }

        $aktif = $this->pdo->query(
            "SELECT id FROM tahun_ajaran WHERE status = 'aktif' ORDER BY id DESC LIMIT 1"
        )->fetchColumn();

        if ($aktif) {
            return (int) $aktif;
        }

        JsonResponse::error(422, 'tahun_ajaran tidak ditemukan di SIKEU. Isi master tahun_ajaran dulu.');
    }

    public function resolveUnitId(?string $unitName): ?int
    {
        $unitName = trim((string) $unitName);
        if ($unitName === '') {
            return null;
        }

        $stmt = $this->pdo->prepare(
            "SELECT id FROM unit_sekolah
             WHERE nama_unit = :nama OR kode_unit = :nama
             LIMIT 1"
        );
        $stmt->execute([':nama' => $unitName]);
        $id = $stmt->fetchColumn();
        if ($id) {
            return (int) $id;
        }

        $like = $this->pdo->prepare(
            "SELECT id FROM unit_sekolah
             WHERE nama_unit LIKE :nama
             LIMIT 1"
        );
        $like->execute([':nama' => '%' . $unitName . '%']);
        $id = $like->fetchColumn();

        return $id ? (int) $id : null;
    }

    public function resolveKelasId(?string $kelasName, ?int $unitId): ?int
    {
        $kelasName = trim((string) $kelasName);
        if ($kelasName === '') {
            return null;
        }

        if ($unitId) {
            $stmt = $this->pdo->prepare(
                "SELECT id FROM kelas
                 WHERE (nama_kelas = :nama OR kode_kelas = :nama)
                   AND (unit_id = :unit_id OR unit_id IS NULL)
                 LIMIT 1"
            );
            $stmt->execute([':nama' => $kelasName, ':unit_id' => $unitId]);
        } else {
            $stmt = $this->pdo->prepare(
                "SELECT id FROM kelas WHERE nama_kelas = :nama OR kode_kelas = :nama LIMIT 1"
            );
            $stmt->execute([':nama' => $kelasName]);
        }

        $id = $stmt->fetchColumn();
        return $id ? (int) $id : null;
    }
}
