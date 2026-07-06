<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Api;

final class Router
{
    /**
     * @return array{resource: string, id: int|null}
     */
    public function resolve(string $requestUri): array
    {
        $path = parse_url($requestUri, PHP_URL_PATH) ?: '/';
        $segments = array_values(array_filter(explode('/', trim($path, '/')), static fn (string $part): bool => $part !== ''));

        if ($segments === [] || $segments[0] !== 'api') {
            return ['resource' => 'not-found', 'id' => null];
        }

        $resource = $segments[1] ?? 'not-found';
        $id = isset($segments[2]) && ctype_digit($segments[2]) ? (int) $segments[2] : null;

        return ['resource' => $resource, 'id' => $id];
    }
}
