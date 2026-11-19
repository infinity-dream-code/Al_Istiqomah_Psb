<?php
/**
 * Helper billing VA untuk skema SIKEU modern:
 * siswa, tagihan, transaksi, transaksi_detail, saldo_siswa, mutasi_saldo, log_va.
 */

function sikeu_identity_keys(string $vano): array
{
    $full = preg_replace('/\D+/', '', $vano) ?: $vano;
    $id10 = strlen($full) > 10 ? substr($full, -10) : str_pad($full, 10, '0', STR_PAD_LEFT);
    // Kompatibel cara lama: buang 6 digit prefix bank/institution.
    $legacy10 = strlen($full) > 6 ? substr($full, 6, 16) : $full;
    $legacy10 = str_pad(substr(preg_replace('/\D+/', '', $legacy10), 0, 10), 10, '0', STR_PAD_LEFT);

    return [
        'full' => $full,
        'id10' => $id10,
        'legacy10' => $legacy10,
    ];
}

function sikeu_find_siswa(mysqli $db, string $vano): array
{
    $keys = sikeu_identity_keys($vano);
    $sql = "SELECT id, nis, nomor_pendaftaran, va_number, nama_lengkap, status
            FROM siswa
            WHERE va_number = ?
               OR va_number = ?
               OR LPAD(IFNULL(nomor_pendaftaran, ''), 10, '0') = ?
               OR LPAD(IFNULL(nomor_pendaftaran, ''), 10, '0') = ?
               OR LPAD(IFNULL(nis, ''), 10, '0') = ?
               OR LPAD(IFNULL(nis, ''), 10, '0') = ?
               OR nomor_pendaftaran = ?
               OR nis = ?
            LIMIT 5";

    $stmt = mysqli_prepare($db, $sql);
    $full = $keys['full'];
    $id10 = $keys['id10'];
    $leg = $keys['legacy10'];
    mysqli_stmt_bind_param($stmt, 'ssssssss', $full, $id10, $id10, $leg, $id10, $leg, $full, $full);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $rows[] = $row;
    }
    mysqli_stmt_close($stmt);

    return $rows;
}

function sikeu_unpaid_bills(mysqli $db, int $siswaId): array
{
    $sql = "SELECT id, nama_tagihan, sisa_tagihan, jumlah_tagihan, jumlah_bayar, status
            FROM tagihan
            WHERE siswa_id = ?
              AND status = 'belum_lunas'
              AND sisa_tagihan > 0
            ORDER BY id ASC";
    $stmt = mysqli_prepare($db, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $siswaId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $rows = [];
    while ($row = mysqli_fetch_assoc($res)) {
        $rows[] = $row;
    }
    mysqli_stmt_close($stmt);

    return $rows;
}

function sikeu_inquiry_summary(mysqli $db, array $siswa): array
{
    $bills = sikeu_unpaid_bills($db, (int) $siswa['id']);
    $total = 0.0;
    $namaTagihan = 'TOP UP';

    foreach ($bills as $bill) {
        $total += (float) $bill['sisa_tagihan'];
        $namaTagihan = $bill['nama_tagihan'];
    }

    return [
        'has_bill' => count($bills) > 0,
        'nominal' => $total,
        'nama_tagihan' => $namaTagihan,
        'nama' => $siswa['nama_lengkap'] ?? '',
    ];
}

function sikeu_log_va(
    mysqli $db,
    string $method,
    string $vano,
    ?int $siswaId,
    float $nominal,
    string $code,
    string $message,
    string $status,
    array $request,
    array $response
): void {
    $sql = "INSERT INTO log_va
            (method, nomor_pembayaran, id_siswa, nominal, response_code, response_message, status, request_payload, response_payload, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $stmt = mysqli_prepare($db, $sql);
    $req = json_encode($request, JSON_UNESCAPED_UNICODE);
    $res = json_encode($response, JSON_UNESCAPED_UNICODE);
    mysqli_stmt_bind_param(
        $stmt,
        'ssidsisss',
        $method,
        $vano,
        $siswaId,
        $nominal,
        $code,
        $message,
        $status,
        $req,
        $res
    );
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

function sikeu_ensure_saldo(mysqli $db, int $siswaId): array
{
    $stmt = mysqli_prepare($db, 'SELECT * FROM saldo_siswa WHERE siswa_id = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 'i', $siswaId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if ($row) {
        return $row;
    }

    $ins = mysqli_prepare(
        $db,
        'INSERT INTO saldo_siswa (siswa_id, saldo_debit, saldo_kredit, saldo_akhir, updated_at) VALUES (?, 0, 0, 0, NOW())'
    );
    mysqli_stmt_bind_param($ins, 'i', $siswaId);
    mysqli_stmt_execute($ins);
    mysqli_stmt_close($ins);

    return [
        'siswa_id' => $siswaId,
        'saldo_debit' => 0,
        'saldo_kredit' => 0,
        'saldo_akhir' => 0,
    ];
}

function sikeu_apply_payment(
    mysqli $db,
    string $vano,
    string $refno,
    string $trxdate,
    string $channelId,
    float $paymentAmount
): array {
    $siswaList = sikeu_find_siswa($db, $vano);
    if (count($siswaList) === 0) {
        return ['ok' => false, 'err' => '15', 'message' => 'Nomor Identitas Pembayaran tidak ditemukan di basis data Billing Provider'];
    }
    if (count($siswaList) > 1) {
        return ['ok' => false, 'err' => '12', 'message' => 'Declined - Invalid transaction'];
    }

    $siswa = $siswaList[0];
    $siswaId = (int) $siswa['id'];

    // Cegah double payment dengan REFNO yang sama.
    $cek = mysqli_prepare($db, "SELECT id FROM transaksi WHERE keterangan LIKE ? LIMIT 1");
    $like = '%REFNO:' . $refno . '%';
    mysqli_stmt_bind_param($cek, 's', $like);
    mysqli_stmt_execute($cek);
    $dup = mysqli_stmt_get_result($cek);
    if (mysqli_fetch_assoc($dup)) {
        mysqli_stmt_close($cek);
        return ['ok' => true, 'err' => '00', 'message' => 'Top Up Payment Success', 'amount' => $paymentAmount];
    }
    mysqli_stmt_close($cek);

    mysqli_begin_transaction($db);
    try {
        $bills = sikeu_unpaid_bills($db, $siswaId);
        $remaining = round($paymentAmount, 2);
        $allocated = 0.0;
        $firstTagihanId = null;
        $details = [];

        foreach ($bills as $bill) {
            if ($remaining <= 0) {
                break;
            }
            $sisa = (float) $bill['sisa_tagihan'];
            if ($sisa <= 0) {
                continue;
            }
            $bayar = min($sisa, $remaining);
            $jumlahBayarBaru = (float) $bill['jumlah_bayar'] + $bayar;
            $sisaBaru = (float) $bill['jumlah_tagihan'] - $jumlahBayarBaru;
            if ($sisaBaru < 0) {
                $sisaBaru = 0;
            }
            $status = $sisaBaru <= 0.009 ? 'lunas' : 'belum_lunas';

            $upd = mysqli_prepare(
                $db,
                "UPDATE tagihan
                 SET jumlah_bayar = ?, sisa_tagihan = ?, status = ?, updated_at = NOW()
                 WHERE id = ?"
            );
            $tid = (int) $bill['id'];
            mysqli_stmt_bind_param($upd, 'ddsi', $jumlahBayarBaru, $sisaBaru, $status, $tid);
            mysqli_stmt_execute($upd);
            mysqli_stmt_close($upd);

            $details[] = ['tagihan_id' => $tid, 'jumlah_bayar' => $bayar];
            if ($firstTagihanId === null) {
                $firstTagihanId = $tid;
            }
            $allocated += $bayar;
            $remaining = round($remaining - $bayar, 2);
        }

        $kode = 'VA' . date('YmdHis') . substr(preg_replace('/\W+/', '', $refno), 0, 8);
        $ket = 'REFNO:' . $refno . ' CHANNEL:' . $channelId . ' VANO:' . $vano;
        $tanggal = date('Y-m-d', strtotime($trxdate) ?: time());

        $insTrx = mysqli_prepare(
            $db,
            "INSERT INTO transaksi
             (kode_transaksi, siswa_id, tagihan_id, metode_pembayaran, virtual_account, jumlah_bayar, jumlah_potongan, total_bayar, tanggal_bayar, waktu_bayar, status, keterangan, created_at, updated_at)
             VALUES (?, ?, ?, 'virtual_account', ?, ?, 0, ?, ?, ?, 'verified', ?, NOW(), NOW())"
        );
        mysqli_stmt_bind_param(
            $insTrx,
            'siisddsss',
            $kode,
            $siswaId,
            $firstTagihanId,
            $vano,
            $paymentAmount,
            $paymentAmount,
            $tanggal,
            $trxdate,
            $ket
        );
        mysqli_stmt_execute($insTrx);
        $trxId = (int) mysqli_insert_id($db);
        mysqli_stmt_close($insTrx);

        foreach ($details as $d) {
            $insD = mysqli_prepare(
                $db,
                "INSERT INTO transaksi_detail (transaksi_id, tagihan_id, jumlah_bayar, created_at, isActive)
                 VALUES (?, ?, ?, NOW(), 1)"
            );
            mysqli_stmt_bind_param($insD, 'iid', $trxId, $d['tagihan_id'], $d['jumlah_bayar']);
            mysqli_stmt_execute($insD);
            mysqli_stmt_close($insD);
        }

        if ($remaining > 0) {
            $saldo = sikeu_ensure_saldo($db, $siswaId);
            $sebelum = (float) $saldo['saldo_akhir'];
            $sesudah = $sebelum + $remaining;
            $debit = (float) $saldo['saldo_debit'] + $remaining;

            $updSaldo = mysqli_prepare(
                $db,
                "UPDATE saldo_siswa SET saldo_debit = ?, saldo_akhir = ?, updated_at = NOW() WHERE siswa_id = ?"
            );
            mysqli_stmt_bind_param($updSaldo, 'ddi', $debit, $sesudah, $siswaId);
            mysqli_stmt_execute($updSaldo);
            mysqli_stmt_close($updSaldo);

            $insMut = mysqli_prepare(
                $db,
                "INSERT INTO mutasi_saldo
                 (siswa_id, transaksi_id, jenis, jumlah, saldo_sebelum, saldo_sesudah, keterangan, created_at)
                 VALUES (?, ?, 'debit', ?, ?, ?, ?, NOW())"
            );
            $mutKet = 'Top up sisa VA REFNO:' . $refno;
            mysqli_stmt_bind_param($insMut, 'iiddds', $siswaId, $trxId, $remaining, $sebelum, $sesudah, $mutKet);
            mysqli_stmt_execute($insMut);
            mysqli_stmt_close($insMut);
        }

        mysqli_commit($db);

        return [
            'ok' => true,
            'err' => '00',
            'message' => 'Top Up Payment Success',
            'amount' => $paymentAmount,
            'siswa_id' => $siswaId,
        ];
    } catch (Throwable $e) {
        mysqli_rollback($db);
        error_log('[' . date('Y-m-d H:i:s') . '] sikeu_apply_payment: ' . $e->getMessage() . "\n", 3, __DIR__ . '/../error.log');
        return ['ok' => false, 'err' => '12', 'message' => 'Declined - Invalid transaction Query'];
    }
}

function sikeu_reverse_payment(
    mysqli $db,
    string $vano,
    string $refno,
    string $trxdate,
    float $paymentAmount
): array {
    $like = '%REFNO:' . $refno . '%';
    $stmt = mysqli_prepare($db, "SELECT * FROM transaksi WHERE keterangan LIKE ? AND status = 'verified' LIMIT 1");
    mysqli_stmt_bind_param($stmt, 's', $like);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $trx = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if (!$trx) {
        return ['ok' => false, 'err' => '12', 'message' => 'Declined - Invalid transaction Query'];
    }

    mysqli_begin_transaction($db);
    try {
        $trxId = (int) $trx['id'];
        $siswaId = (int) $trx['siswa_id'];

        $det = mysqli_prepare($db, 'SELECT * FROM transaksi_detail WHERE transaksi_id = ? AND isActive = 1');
        mysqli_stmt_bind_param($det, 'i', $trxId);
        mysqli_stmt_execute($det);
        $detRes = mysqli_stmt_get_result($det);
        while ($d = mysqli_fetch_assoc($detRes)) {
            $tid = (int) $d['tagihan_id'];
            $bayar = (float) $d['jumlah_bayar'];
            $tg = mysqli_prepare($db, 'SELECT * FROM tagihan WHERE id = ? LIMIT 1');
            mysqli_stmt_bind_param($tg, 'i', $tid);
            mysqli_stmt_execute($tg);
            $tgRow = mysqli_fetch_assoc(mysqli_stmt_get_result($tg));
            mysqli_stmt_close($tg);
            if (!$tgRow) {
                continue;
            }
            $jumlahBayar = max(0, (float) $tgRow['jumlah_bayar'] - $bayar);
            $sisa = (float) $tgRow['jumlah_tagihan'] - $jumlahBayar;
            $status = $sisa <= 0.009 ? 'lunas' : 'belum_lunas';
            $upd = mysqli_prepare(
                $db,
                'UPDATE tagihan SET jumlah_bayar = ?, sisa_tagihan = ?, status = ?, updated_at = NOW() WHERE id = ?'
            );
            mysqli_stmt_bind_param($upd, 'ddsi', $jumlahBayar, $sisa, $status, $tid);
            mysqli_stmt_execute($upd);
            mysqli_stmt_close($upd);
        }
        mysqli_stmt_close($det);

        $off = mysqli_prepare($db, 'UPDATE transaksi_detail SET isActive = 0 WHERE transaksi_id = ?');
        mysqli_stmt_bind_param($off, 'i', $trxId);
        mysqli_stmt_execute($off);
        mysqli_stmt_close($off);

        $rej = mysqli_prepare($db, "UPDATE transaksi SET status = 'rejected', updated_at = NOW(), keterangan = CONCAT(IFNULL(keterangan,''), ' | REVERSED:', ?) WHERE id = ?");
        mysqli_stmt_bind_param($rej, 'si', $trxdate, $trxId);
        mysqli_stmt_execute($rej);
        mysqli_stmt_close($rej);

        mysqli_commit($db);
        return ['ok' => true, 'err' => '00', 'message' => 'Reversal Success', 'amount' => $paymentAmount, 'siswa_id' => $siswaId];
    } catch (Throwable $e) {
        mysqli_rollback($db);
        error_log('[' . date('Y-m-d H:i:s') . '] sikeu_reverse_payment: ' . $e->getMessage() . "\n", 3, __DIR__ . '/../error.log');
        return ['ok' => false, 'err' => '12', 'message' => 'Declined - Invalid transaction Query'];
    }
}
