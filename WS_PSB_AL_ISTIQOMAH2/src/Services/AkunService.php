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
        $kode = trim($kode);
        if ($kode !== '') {
            $stmt = $this->pdo->prepare(
                "SELECT id FROM akun_penerimaan
                 WHERE kode_akun = :kode AND status = 'active'
                 LIMIT 1"
            );
            $stmt->execute([':kode' => $kode]);
            $id = $stmt->fetchColumn();
            if ($id) {
                return (int) $id;
            }
        }

        // Fallback SPMB: akun Biaya Pendaftaran (auto-create bila belum ada).
        return $this->ensurePendaftaranAkunId();
    }

    /** Pastikan ada akun khusus biaya pendaftaran SPMB. */
    public function ensurePendaftaranAkunId(): int
    {
        foreach (['ACC_SPMB_DAFTAR', '499', 'Biaya Pendaftaran'] as $key) {
            $stmt = $this->pdo->prepare(
                "SELECT id FROM akun_penerimaan
                 WHERE (kode_akun = :k OR nama_akun = :k2) AND status = 'active'
                 LIMIT 1"
            );
            $stmt->execute([':k' => $key, ':k2' => $key]);
            $id = $stmt->fetchColumn();
            if ($id) {
                return (int) $id;
            }
        }

        $ins = $this->pdo->prepare(
            "INSERT INTO akun_penerimaan
             (kode_akun, nama_akun, jenis, unit_id, keterangan, status, created_at, updated_at)
             VALUES ('ACC_SPMB_DAFTAR', 'Biaya Pendaftaran', 'pendapatan', NULL, 'Auto dari WS SPMB', 'active', NOW(), NOW())"
        );
        $ins->execute();

        return (int) $this->pdo->lastInsertId();
    }
}
