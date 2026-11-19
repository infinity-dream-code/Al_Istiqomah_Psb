<?php

namespace WsSpmb\Support;

final class JsonResponse
{
    public static function ok(array $payload = [], string $message = 'OK'): never
    {
        self::send(200, array_merge(['status' => 200, 'message' => $message], $payload));
    }

    public static function data(array $data): never
    {
        self::send(200, ['status' => 200, 'data' => $data]);
    }

    public static function error(int $status, string $message): never
    {
        self::send($status, ['status' => $status, 'message' => $message]);
    }

    private static function send(int $httpStatus, array $body): never
    {
        http_response_code($httpStatus);
        header('Content-Type: application/json');
        echo json_encode($body, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
