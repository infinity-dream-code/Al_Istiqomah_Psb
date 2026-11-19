<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

use WsSpmb\Services\AkunService;
use WsSpmb\Services\SiswaService;
use WsSpmb\Services\TagihanService;
use WsSpmb\Support\Database;
use WsSpmb\Support\JsonResponse;
use WsSpmb\Support\JwtAuth;
use WsSpmb\Support\Validator;

$input = file_get_contents('php://input') ?: '';
$body = json_decode($input, true);
if (!is_array($body)) {
    JsonResponse::error(422, 'Body JSON tidak valid.');
}

$method = $body['method'] ?? null;
$token = $body['token'] ?? null;

try {
    switch ($method) {
        case 'CreateTagihan':
            requireToken($token);
            createTagihan(JwtAuth::decode($token));
            break;

        case 'CreateTagihanBulk':
            requireToken($token);
            createTagihanBulk(JwtAuth::decode($token));
            break;

        case 'CekTagihan':
            requireToken($token);
            cekTagihan(JwtAuth::decode($token));
            break;

        case 'cekTagihanDibayar':
            requireToken($token);
            cekTagihanDibayar(JwtAuth::decode($token));
            break;

        case 'getUAkun':
            JsonResponse::data((new AkunService())->listActive());
            break;

        case 'GenerateNewKey':
            JsonResponse::ok(['key' => bin2hex(random_bytes(32))], 'Key berhasil dibuat');
            break;

        default:
            JsonResponse::error(422, 'Method tidak dikenali.');
    }
} catch (Throwable $e) {
    error_log(
        '[' . date('Y-m-d H:i:s') . '] ' . $method . ': ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine() . "\n",
        3,
        __DIR__ . '/error.log'
    );
    JsonResponse::error(422, 'Gagal memproses permintaan.');
}

function requireToken(mixed $token): void
{
    if (!$token || !is_string($token)) {
        JsonResponse::error(422, 'Token tidak ditemukan');
    }
}

function createTagihan(array $payload): never
{
    $siswaPayload = $payload['siswa'] ?? null;
    $tagihanPayload = $payload['tagihan'] ?? null;

    if (!is_array($siswaPayload)) {
        JsonResponse::error(422, "Data 'siswa' tidak boleh kosong");
    }
    if (!is_array($tagihanPayload)) {
        JsonResponse::error(422, "Data 'tagihan' tidak boleh kosong");
    }

    $err = Validator::requireKeys($siswaPayload, ['nama_siswa', 'nomor_pendaftaran', 'tahun_akademik', 'alamat_rumah']);
    if ($err) {
        JsonResponse::error(422, $err);
    }

    $pdo = Database::pdo();
    $siswaService = new SiswaService($pdo);
    $tagihanService = new TagihanService($pdo);

    $pdo->beginTransaction();
    try {
        $siswa = $siswaService->createFromSpmb($siswaPayload);
        $tagihanService->createForSiswa($siswa, $tagihanPayload);
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }

    JsonResponse::ok([], 'Sukses menambah data siswa baru');
}

function createTagihanBulk(array $payload): never
{
    $rows = $payload['data'] ?? null;
    if (!is_array($rows) || $rows === []) {
        JsonResponse::error(422, 'data tidak boleh kosong, dan berisi setidaknya satu siswa!');
    }

    $pdo = Database::pdo();
    $siswaService = new SiswaService($pdo);
    $tagihanService = new TagihanService($pdo);

    $pdo->beginTransaction();
    try {
        foreach ($rows as $item) {
            $siswaPayload = $item['siswa'] ?? null;
            $tagihanPayload = $item['tagihan'] ?? null;

            if (!is_array($siswaPayload) || !is_array($tagihanPayload)) {
                JsonResponse::error(422, "Setiap item wajib punya 'siswa' dan 'tagihan'");
            }

            $err = Validator::requireKeys($siswaPayload, ['nomor_pendaftaran']);
            if ($err) {
                JsonResponse::error(422, $err);
            }

            $siswa = $siswaService->findByNomorPendaftaran((string) $siswaPayload['nomor_pendaftaran']);
            if (!$siswa) {
                JsonResponse::error(
                    422,
                    'Siswa dengan nomor pendaftaran ' . $siswaPayload['nomor_pendaftaran'] . ' tidak ada'
                );
            }

            $tagihanService->createForSiswa($siswa, $tagihanPayload);
        }
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }

    JsonResponse::ok([], 'Sukses menambah tagihan baru');
}

function cekTagihan(array $payload): never
{
    $err = Validator::requireKeys($payload, ['nomor_pendaftaran']);
    if ($err) {
        JsonResponse::error(422, $err);
    }

    $nomor = (string) $payload['nomor_pendaftaran'];
    $siswa = (new SiswaService())->findByNomorPendaftaran($nomor);
    if (!$siswa) {
        JsonResponse::error(422, "Siswa dengan nomor pendaftaran {$nomor} tidak ditemukan");
    }

    JsonResponse::data((new TagihanService())->listByNomorPendaftaran($nomor));
}

function cekTagihanDibayar(array $payload): never
{
    $err = Validator::requireKeys($payload, ['nomor_pendaftaran', 'nama_tagihan']);
    if ($err) {
        JsonResponse::error(422, $err);
    }

    if (!is_array($payload['nomor_pendaftaran']) || $payload['nomor_pendaftaran'] === []) {
        JsonResponse::error(422, "Field 'nomor_pendaftaran' harus array minimal 1.");
    }

    $data = (new TagihanService())->listPaidByNomorList(
        $payload['nomor_pendaftaran'],
        (string) $payload['nama_tagihan']
    );

    JsonResponse::data($data);
}
