<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Api;

final class JsonResponse
{
    /**
     * @param array<string, mixed> $payload
     */
    public static function send(array $payload, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function error(string $code, string $message, int $status): void
    {
        self::send(
            [
                'error' => [
                    'code' => $code,
                    'message' => $message,
                ],
            ],
            $status
        );
    }
}
