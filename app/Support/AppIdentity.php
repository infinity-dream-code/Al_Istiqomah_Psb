<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Identitas unik per instalasi supaya cookie/session tidak nabrak project lain.
 */
class AppIdentity
{
    public static function id(): string
    {
        $explicit = env('APP_INSTANCE');
        if (is_string($explicit) && $explicit !== '') {
            return Str::slug($explicit, '_');
        }

        // Fallback: dari nama app + cuplikan APP_KEY
        $name = Str::slug((string) config('app.name', 'psbku'), '_');
        $keyBit = substr(sha1((string) config('app.key', 'key')), 0, 6);

        return $name . '_' . $keyBit;
    }

    public static function fingerprint(): string
    {
        return substr(sha1((string) config('app.key', '') . '|' . self::id()), 0, 20);
    }

    public static function sessionCookie(): string
    {
        return env('SESSION_COOKIE') ?: (self::id() . '_session');
    }

    public static function authCookie(): string
    {
        return env('AUTH_COOKIE') ?: (self::id() . '_uid');
    }

    public static function cachePrefix(): string
    {
        return env('CACHE_PREFIX') ?: (self::id() . '_cache_');
    }
}
