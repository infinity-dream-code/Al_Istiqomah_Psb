<?php

namespace WsSpmb\Services;

use PDO;
use WsSpmb\Support\Database;
use WsSpmb\Support\JsonResponse;

final class AkunService
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?: Database::pdo();
    }

    /** @return list<array{KodeAkun:string,NamaAkun:string,NoRek:null}> */
    public function listActive(): array
    {
        $stmt = $this->pdo->query(
            "SELECT kode_akun AS KodeAkun, nama_akun AS NamaAkun, NULL AS NoRek
             FROM akun_penerimaan
             WHERE status = 'active'
             ORDER BY kode_akun ASC"
        );

        return $stmt->fetchAll() ?: [];
    }

    public function findIdByKode(string $kode): int
    {
        $stmt = $this->pdo->prepare(
            "SELECT id FROM akun_penerimaan
             WHERE kode_akun = :kode AND status = 'active'
             LIMIT 1"
        );
        $stmt->execute([':kode' => $kode]);
        $id = $stmt->fetchColumn();

        if (!$id) {
            JsonResponse::error(422, "Akun penerimaan '{$kode}' tidak ditemukan / nonaktif. Tambahkan di akun_penerimaan.");
        }

        return (int) $id;
    }
}
