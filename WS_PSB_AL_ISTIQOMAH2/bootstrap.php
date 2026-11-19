<?php

declare(strict_types=1);

date_default_timezone_set('Asia/Jakarta');
error_reporting(E_ALL);
ini_set('display_errors', '0');

require __DIR__ . '/src/jwt/JWTExceptionWithPayloadInterface.php';
require __DIR__ . '/src/jwt/JWT.php';
require __DIR__ . '/src/jwt/Key.php';
require __DIR__ . '/src/jwt/SignatureInvalidException.php';
require __DIR__ . '/src/jwt/ExpiredException.php';
require __DIR__ . '/src/jwt/BeforeValidException.php';

spl_autoload_register(static function (string $class): void {
    $prefix = 'WsSpmb\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = str_replace('\\', '/', substr($class, strlen($prefix)));
    $path = __DIR__ . '/src/' . $relative . '.php';
    if (is_file($path)) {
        require $path;
    }
});

use WsSpmb\Support\Env;

Env::load(__DIR__ . '/.env');
