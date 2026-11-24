<?php

namespace App\Auth;

use App\Models\DataPeserta;
use App\Models\ParentAccount;
use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Resolve auth id ke DataPeserta (peserta), User (admin),
 * atau ParentAccount (kompat data lama).
 */
class SpmbUserProvider extends EloquentUserProvider
{
    public function retrieveById($identifier)
    {
        $role = session('auth_backup_role');

        if ($role === 'peserta') {
            $peserta = DataPeserta::find($identifier);
            if ($peserta) {
                return $peserta;
            }

            // Kompat: session lama menyimpan parents.id
            return ParentAccount::find($identifier);
        }

        if ($role && $role !== 'peserta') {
            try {
                $user = User::find($identifier);
                if ($user) {
                    return $user;
                }
            } catch (\Throwable $e) {
                // tabel user belum ada di 56
            }
        }

        $peserta = DataPeserta::find($identifier);
        if ($peserta) {
            return $peserta;
        }

        $parent = ParentAccount::find($identifier);
        if ($parent) {
            return $parent;
        }

        try {
            return User::find($identifier);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function retrieveByCredentials(array $credentials)
    {
        $username = $credentials['username'] ?? $credentials['no_wa'] ?? $credentials['no_hp'] ?? null;
        if (!$username) {
            return null;
        }

        $wa = preg_replace('/\D+/', '', (string) $username) ?: (string) $username;

        $peserta = DataPeserta::where('no_hp', $wa)->orderByDesc('id')->first()
            ?? DataPeserta::where('no_hp', $username)->orderByDesc('id')->first();
        if ($peserta) {
            return $peserta;
        }

        $parent = ParentAccount::where('no_wa', $wa)->where('status', 'active')->first()
            ?? ParentAccount::where('no_wa', $username)->where('status', 'active')->first();
        if ($parent) {
            return $parent;
        }

        try {
            return User::where('username', $username)->first()
                ?? ($wa !== (string) $username ? User::where('username', $wa)->first() : null);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain = (string) ($credentials['password'] ?? '');
        if ($plain === '') {
            return false;
        }

        if ($user instanceof DataPeserta) {
            $storedPlain = (string) ($user->getAttributes()['plain_password'] ?? '');
            if ($storedPlain !== '' && hash_equals($storedPlain, $plain)) {
                return true;
            }

            $hash = (string) ($user->getAttributes()['password'] ?? '');
            return $hash !== '' && password_verify($plain, $hash);
        }

        if ($user instanceof ParentAccount) {
            $storedPlain = (string) ($user->getAttributes()['plain_password'] ?? '');
            if ($storedPlain !== '' && hash_equals($storedPlain, $plain)) {
                return true;
            }

            return password_verify($plain, (string) $user->getAuthPassword());
        }

        if ($user instanceof User) {
            $storedPlain = (string) ($user->getAttributes()['plain_password'] ?? $user->plain_password ?? '');
            if ($storedPlain !== '' && hash_equals($storedPlain, $plain)) {
                return true;
            }

            return password_verify($plain, (string) $user->getAuthPassword());
        }

        return parent::validateCredentials($user, $credentials);
    }
}
