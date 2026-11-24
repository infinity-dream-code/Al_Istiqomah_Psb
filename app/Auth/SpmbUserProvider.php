<?php

namespace App\Auth;

use App\Models\ParentAccount;
use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * Resolve auth id ke ParentAccount (peserta) atau User (admin),
 * berdasarkan session/cookie role.
 */
class SpmbUserProvider extends EloquentUserProvider
{
    public function retrieveById($identifier)
    {
        $role = session('auth_backup_role');

        if ($role === 'peserta') {
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
        $username = $credentials['username'] ?? $credentials['no_wa'] ?? null;
        if (!$username) {
            return null;
        }

        $wa = preg_replace('/\D+/', '', (string) $username) ?: (string) $username;

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

        if ($user instanceof ParentAccount) {
            if (!empty($user->plain_password) && hash_equals((string) $user->plain_password, $plain)) {
                return true;
            }

            return password_verify($plain, (string) $user->getAuthPassword());
        }

        if ($user instanceof User) {
            if (!empty($user->plain_password) && hash_equals((string) $user->plain_password, $plain)) {
                return true;
            }

            return password_verify($plain, (string) $user->getAuthPassword());
        }

        return parent::validateCredentials($user, $credentials);
    }
}
