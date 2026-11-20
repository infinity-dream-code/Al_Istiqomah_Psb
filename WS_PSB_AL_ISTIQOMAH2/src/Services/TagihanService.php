<?php

namespace WsSpmb\Services;

use PDO;
use WsSpmb\Support\Database;
use WsSpmb\Support\JsonResponse;
use WsSpmb\Support\Validator;

final class TagihanService
{
    private PDO $pdo;
    private AkunService $akun;
    private LookupService $lookup;

    public function __construct(
        ?PDO $pdo = null,
        ?AkunService $akun = null,
        ?LookupService $lookup = null
    ) {
        $this->pdo = $pdo ?: Database::pdo();
        $this->akun = $akun ?: new AkunService($this->pdo);
        $this->lookup = $lookup ?: new LookupService($this->pdo);
    }

    public function createForSiswa(array $siswaRow, array $tagihan): void
    {
        $error = Validator::requireKeys($tagihan, ['periode', 'nama_tagihan', 'tahun_akademik', 'detail_tagihan']);
        if ($error) {
            JsonResponse::error(422, $error);
        }

        if (!Validator::periodeYm((string) $tagihan['periode'])) {
            JsonResponse::error(422, "Field 'periode' harus format YYYYMM.");
        }

        if (!is_array($tagihan['detail_tagihan']) || count($tagihan['detail_tagihan']) < 1) {
            JsonResponse::error(422, "Field 'detail_tagihan' minimal 1 baris.");
        }

        $tahunAjaranId = $this->lookup->resolveTahunAjaranId($tagihan['tahun_akademik'] ?? null);
        $periodeTahun = (int) substr((string) $tagihan['periode'], 0, 4);
        $periodeBulan = (int) substr((string) $tagihan['periode'], 4, 2);
        $namaHeader = trim((string) $tagihan['nama_tagihan']);

        foreach ($tagihan['detail_tagihan'] as $detail) {
            $errDetail = Validator::requireKeys($detail, ['kode_tagihan', 'nominal']);
            if ($errDetail) {
                JsonResponse::error(422, $errDetail);
            }
            if (!is_numeric($detail['nominal'])) {
                JsonResponse::error(422, "Field 'nominal' harus angka.");
            }

            $nominal = (float) $detail['nominal'];
            $akunId = $this->akun->findIdByKode((string) $detail['kode_tagihan']);
            $namaDetail = trim((string) ($detail['nama_tagihan'] ?? $namaHeader));
            $kode = $this->generateKodeTagihan();

            $jenisTagihan = strtolower(trim((string) ($detail['jenis_tagihan'] ?? $tagihan['jenis_tagihan'] ?? 'cicil')));
            if (!in_array($jenisTagihan, ['cicil', 'lunas'], true)) {
                $jenisTagihan = 'cicil';
            }

            $stmt = $this->pdo->prepare(
                "INSERT INTO tagihan (
                    kode_tagihan, siswa_id, tahun_ajaran_id, akun_penerimaan_id,
                    jenis_tagihan, nama_tagihan, jumlah_tagihan, jumlah_bayar, sisa_tagihan,
                    periode_tahun, periode_bulan, status, keterangan, created_at, updated_at
                 ) VALUES (
                    :kode, :siswa_id, :tahun_ajaran_id, :akun_id,
                    :jenis, :nama, :jumlah, 0, :sisa,
                    :periode_tahun, :periode_bulan, 'belum_lunas', :ket, NOW(), NOW()
                 )"
            );

            $stmt->execute([
                ':kode' => $kode,
                ':siswa_id' => (int) $siswaRow['id'],
                ':tahun_ajaran_id' => $tahunAjaranId,
                ':akun_id' => $akunId,
                ':jenis' => $jenisTagihan,
                ':nama' => $namaHeader !== '' ? $namaHeader : $namaDetail,
                ':jumlah' => $nominal,
                ':sisa' => $nominal,
                ':periode_tahun' => $periodeTahun,
                ':periode_bulan' => $periodeBulan,
                ':ket' => $namaDetail !== $namaHeader ? $namaDetail : null,
            ]);
        }
    }

    /** Format respons kompatibel SPMB lama (StatusBayar 0/1). */
    public function listByNomorPendaftaran(string $nomor): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT
                t.nama_tagihan AS NamaTagihan,
                ta.tahun_ajaran AS TahunTagihan,
                CONCAT(LPAD(t.periode_tahun, 4, '0'), LPAD(IFNULL(t.periode_bulan, 0), 2, '0')) AS PeriodeTagihan,
                t.kode_tagihan AS KodeTagihan,
                CASE WHEN t.status = 'lunas' THEN '1' ELSE '0' END AS StatusBayar,
                CASE WHEN t.status = 'lunas' THEN DATE_FORMAT(t.updated_at, '%Y-%m-%d %H:%i:%s') ELSE NULL END AS TanggalBayar
             FROM tagihan t
             INNER JOIN siswa s ON s.id = t.siswa_id
             LEFT JOIN tahun_ajaran ta ON ta.id = t.tahun_ajaran_id
             WHERE s.nomor_pendaftaran = :no
               AND t.status <> 'batal'
             ORDER BY t.id ASC"
        );
        $stmt->execute([':no' => $nomor]);

        return $stmt->fetchAll() ?: [];
    }

    /** Format respons cekTagihanDibayar kompatibel SPMB. */
    public function listPaidByNomorList(array $nomorList, string $namaTagihan): array
    {
        if ($nomorList === []) {
            return [];
        }

        $placeholders = [];
        $params = [':nama' => $namaTagihan];
        foreach (array_values($nomorList) as $i => $no) {
            $key = ':no' . $i;
            $placeholders[] = $key;
            $params[$key] = (string) $no;
        }

        $sql = "SELECT
                    s.nomor_pendaftaran AS NomorPendaftaran,
                    s.nama_lengkap AS NamaSiswa,
                    u.nama_unit AS Unit,
                    k.nama_kelas AS Kelas,
                    t.nama_tagihan AS NamaTagihan,
                    ta.tahun_ajaran AS TahunTagihan,
                    CONCAT(LPAD(t.periode_tahun, 4, '0'), LPAD(IFNULL(t.periode_bulan, 0), 2, '0')) AS PeriodeTagihan,
                    t.kode_tagihan AS KodeTagihan,
                    '1' AS StatusBayar,
                    DATE_FORMAT(t.updated_at, '%Y-%m-%d %H:%i:%s') AS TanggalBayar
                FROM tagihan t
                INNER JOIN siswa s ON s.id = t.siswa_id
                LEFT JOIN unit_sekolah u ON u.id = s.unit_id
                LEFT JOIN kelas k ON k.id = s.kelas_id
                LEFT JOIN tahun_ajaran ta ON ta.id = t.tahun_ajaran_id
                WHERE t.nama_tagihan = :nama
                  AND t.status = 'lunas'
                  AND s.nomor_pendaftaran IN (" . implode(',', $placeholders) . ")
                ORDER BY t.id ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll() ?: [];
    }

    private function generateKodeTagihan(): string
    {
        return 'TAG' . date('Ymd') . strtoupper(bin2hex(random_bytes(3)));
    }
}
