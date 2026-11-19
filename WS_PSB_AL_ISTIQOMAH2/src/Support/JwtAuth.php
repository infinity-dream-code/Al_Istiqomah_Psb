<?php

namespace WsSpmb\Support;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Throwable;

final class JwtAuth
{
    public static function decode(string $token): array
    {
        $key = Env::get('JWT_KEY');
        if (!$key) {
            JsonResponse::error(500, 'JWT_KEY belum dikonfigurasi.');
        }

        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            return json_decode(json_encode($decoded), true) ?: [];
        } catch (Throwable $e) {
            JsonResponse::error(422, 'Token tidak valid.');
        }
    }
}
