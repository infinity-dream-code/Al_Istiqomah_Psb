<?php

namespace App\Services;

use App\Models\DataPeserta;
use App\Models\ParentAccount;
use App\Models\ParentSiswaMapping;
use App\Models\Siswa;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class KelulusanSiswaService
{
    /**
     * Saat lulus: is_calon_spmb=0, buat/hubungkan parents + mapping.
     */
    public function markLulus(DataPeserta $peserta): void
    {
        $noWa = $this->resolveWa($peserta);
        $parentNama = $this->resolveParentNama($peserta);
        $passwordPlain = (string) ($peserta->plain_password ?: str_pad((string) random_int(0, 99999999), 8, '0', STR_PAD_LEFT));

        $parent = null;
        if ($noWa !== '') {
            $parent = ParentAccount::where('no_wa', $noWa)->first();
        }

        if ($parent) {
            if (empty($parent->plain_password) && $passwordPlain !== '') {
                $parent->password = Hash::make($passwordPlain);
                $parent->plain_password = $passwordPlain;
            }
            if ($parent->status !== 'active') {
                $parent->status = 'active';
            }
            if ($parentNama !== '' && empty($parent->nama_lengkap)) {
                $parent->nama_lengkap = $parentNama;
            }
            if ($parent->isDirty()) {
                $parent->save();
            }
        } else {
            $parent = ParentAccount::create([
                'no_wa' => $noWa !== '' ? $noWa : ('SPMB' . $peserta->no_pendaftaran),
                'password' => Hash::make($passwordPlain),
                'plain_password' => $passwordPlain,
                'nama_lengkap' => $parentNama !== '' ? $parentNama : $peserta->nama_peserta,
                'email' => $peserta->email ?: null,
                'alamat' => $peserta->alamat_lengkap ?: null,
                'status' => 'active',
            ]);
        }

        $peserta->id_user = $parent->id;
        if (empty($peserta->plain_password)) {
            $peserta->plain_password = $passwordPlain;
            $peserta->password = Hash::make($passwordPlain);
        }
        $peserta->save();

        $siswa = Siswa::where('nomor_pendaftaran', $peserta->no_pendaftaran)->first();
        if (!$siswa) {
            Log::warning('kelulusan.siswa_missing', [
                'no_pendaftaran' => $peserta->no_pendaftaran,
            ]);
            return;
        }

        $siswa->is_calon_spmb = false;
        $siswa->parent_id = $parent->id;
        $siswa->save();

        ParentSiswaMapping::updateOrCreate(
            [
                'parent_id' => $parent->id,
                'siswa_id' => $siswa->id,
            ],
            [
                'is_primary' => true,
                'created_at' => now(),
            ]
        );
    }

    /**
     * Saat gagal / batal lulus: is_calon_spmb=1, lepas mapping, hapus parents orphan.
     */
    public function markGagal(DataPeserta $peserta): void
    {
        $siswa = Siswa::where('nomor_pendaftaran', $peserta->no_pendaftaran)->first();
        $parentId = $siswa?->parent_id ?: $peserta->id_user;

        if ($siswa) {
            $siswaId = $siswa->id;
            if ($parentId) {
                ParentSiswaMapping::where('parent_id', $parentId)
                    ->where('siswa_id', $siswaId)
                    ->delete();
            } else {
                ParentSiswaMapping::where('siswa_id', $siswaId)->delete();
            }

            $siswa->is_calon_spmb = true;
            $siswa->parent_id = null;
            $siswa->save();
        }

        if ($parentId) {
            $stillUsed = ParentSiswaMapping::where('parent_id', $parentId)->exists()
                || Siswa::where('parent_id', $parentId)->exists()
                || DataPeserta::where('id_user', $parentId)->where('id', '!=', $peserta->id)->exists();

            if (!$stillUsed) {
                ParentAccount::where('id', $parentId)->delete();
            }
        }

        $peserta->id_user = null;
        $peserta->save();
    }

    private function resolveWa(DataPeserta $peserta): string
    {
        $raw = $peserta->no_hp
            ?: (($peserta->kontak_utama === 'ibu') ? $peserta->ibu_no_tlp : $peserta->ayah_no_tlp)
            ?: $peserta->ayah_no_tlp
            ?: $peserta->ibu_no_tlp;

        return preg_replace('/\D+/', '', (string) $raw) ?: '';
    }

    private function resolveParentNama(DataPeserta $peserta): string
    {
        if ($peserta->kontak_utama === 'ibu') {
            return trim((string) ($peserta->ibu_nama ?: $peserta->ayah_nama ?: $peserta->nama_peserta));
        }

        return trim((string) ($peserta->ayah_nama ?: $peserta->ibu_nama ?: $peserta->nama_peserta));
    }
}
