<?php

namespace WsSpmb\Support;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }

        $host = Env::get('DB_HOST', '127.0.0.1');
        $port = Env::get('DB_PORT', '3306');
        $name = Env::get('DB_DATABASE', 'test');
        $user = Env::get('DB_USERNAME', 'root');
        $pass = Env::get('DB_PASSWORD', '');

        try {
            self::$pdo = new PDO(
                "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            error_log('[' . date('Y-m-d H:i:s') . '] db: ' . $e->getMessage() . "\n", 3, dirname(__DIR__, 2) . '/error.log');
            JsonResponse::error(500, 'Tidak dapat terhubung ke database.');
        }

        return self::$pdo;
    }
}
