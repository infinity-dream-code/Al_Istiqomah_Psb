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

    /** Default jenis siswa SPMB = Reguler (fallback nama/kode). */
    public function resolveJenisSiswaId(?string $namaAtauKode = null): ?int
    {
        $key = trim((string) $namaAtauKode);
        if ($key !== '') {
            $stmt = $this->pdo->prepare(
                "SELECT id FROM jenis_siswa
                 WHERE status = 'active' AND (nama_jenis = :k OR kode_jenis = :k)
                 LIMIT 1"
            );
            $stmt->execute([':k' => $key]);
            $id = $stmt->fetchColumn();
            if ($id) {
                return (int) $id;
            }
        }

        $stmt = $this->pdo->prepare(
            "SELECT id FROM jenis_siswa
             WHERE status = 'active' AND (nama_jenis = 'Reguler' OR kode_jenis = 'JNS001')
             ORDER BY id ASC
             LIMIT 1"
        );
        $stmt->execute();
        $id = $stmt->fetchColumn();

        return $id ? (int) $id : null;
    }

    /** Ambil angkatan by primary key (ID dari dropdown SPMB). */
    public function resolveAngkatanIdById(int $id): ?int
    {
        if ($id <= 0) {
            return null;
        }
        $stmt = $this->pdo->prepare('SELECT id FROM angkatan WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $found = $stmt->fetchColumn();

        return $found ? (int) $found : null;
    }

    /**
     * siswa.angkatan_id = gelombang SIKEU (tabel `angkatan`, mis. "Gelombang 2").
     */
    public function resolveAngkatanId(mixed $gelombang): ?int
    {
        if (is_numeric($gelombang) && (int) $gelombang > 100) {
            // Nilai besar kemungkinan sudah ID langsung.
            $byId = $this->resolveAngkatanIdById((int) $gelombang);
            if ($byId) {
                return $byId;
            }
        }

        if (is_numeric($gelombang) && (int) $gelombang > 0) {
            $no = (int) $gelombang;
            $nama = 'Gelombang ' . $no;

            $stmt = $this->pdo->prepare(
                "SELECT id FROM angkatan WHERE nama_angkatan = :nama LIMIT 1"
            );
            $stmt->execute([':nama' => $nama]);
            $id = $stmt->fetchColumn();
            if ($id) {
                return (int) $id;
            }

            $ins = $this->pdo->prepare(
                "INSERT INTO angkatan (nama_angkatan, tahun, created_at, updated_at)
                 VALUES (:nama, :tahun, NOW(), NOW())"
            );
            $ins->execute([
                ':nama' => $nama,
                ':tahun' => (int) date('Y'),
            ]);

            return (int) $this->pdo->lastInsertId();
        }

        $nama = trim((string) $gelombang);
        if ($nama === '') {
            return null;
        }

        $stmt = $this->pdo->prepare(
            "SELECT id FROM angkatan WHERE nama_angkatan = :nama LIMIT 1"
        );
        $stmt->execute([':nama' => $nama]);
        $id = $stmt->fetchColumn();

        return $id ? (int) $id : null;
    }

    /**
     * siswa.angkatan_sis_id = tahun angkatan SIKEU (tabel `angkatansis`).
     */
    public function resolveAngkatanSisId(mixed $angkatanSisId, ?string $tahunAkademik = null): ?int
    {
        if (is_numeric($angkatanSisId) && (int) $angkatanSisId > 0) {
            $stmt = $this->pdo->prepare('SELECT id FROM angkatansis WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => (int) $angkatanSisId]);
            $id = $stmt->fetchColumn();
            if ($id) {
                return (int) $id;
            }
        }

        $ta = trim((string) $tahunAkademik);
        if ($ta === '') {
            return null;
        }

        $exact = $this->pdo->prepare(
            "SELECT id FROM angkatansis WHERE nama_angkatan = :nama LIMIT 1"
        );
        $exact->execute([':nama' => $ta]);
        $id = $exact->fetchColumn();
        if ($id) {
            return (int) $id;
        }

        $prefix = preg_replace('/\s*-\s*(GANJIL|GENAP).*$/i', '', $ta) ?: $ta;
        $like = $this->pdo->prepare(
            "SELECT id FROM angkatansis
             WHERE nama_angkatan LIKE :nama
             ORDER BY id DESC
             LIMIT 1"
        );
        $like->execute([':nama' => $prefix . '%']);
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
