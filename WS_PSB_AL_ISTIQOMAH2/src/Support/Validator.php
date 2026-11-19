<?php

namespace WsSpmb\Support;

final class Validator
{
    public static function requireKeys(array $data, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $data) || $data[$key] === null || $data[$key] === '') {
                return "Field '{$key}' wajib diisi.";
            }
        }

        return null;
    }

    public static function periodeYm(string $periode): bool
    {
        return (bool) preg_match('/^\d{4}(0[1-9]|1[0-2])$/', $periode);
    }
}
