<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web;

final class Router
{
    /**
     * Canonical resource key to URL path segment, shared with
     * Support\Seo\CanonicalUrlBuilder so route resolution and canonical-URL construction stay
     * in sync instead of duplicating this table.
     *
     * @var array<string, string>
     */
    public const RESOURCE_SEGMENTS = [
        'dives' => 'dives',
        'sites' => 'sites',
        'countries' => 'countries',
        'cities' => 'cities',
        'shops' => 'shops',
        'trips' => 'trips',
        'equipment' => 'equipment',
        'gallery' => 'gallery',
        'stats' => 'stats',
        'summary' => 'summary',
        'profile' => 'profile',
    ];

    /**
     * Resource keys that support an overview (list) route.
     *
     * @var list<string>
     */
    private const OVERVIEW_RESOURCES = [
        'dives', 'sites', 'countries', 'cities', 'shops', 'trips', 'equipment', 'gallery', 'stats', 'summary',
    ];

    /**
     * Resource keys that support a detail (single-item) route.
     *
     * @var list<string>
     */
    private const DETAIL_RESOURCES = [
        'dives', 'sites', 'countries', 'cities', 'shops', 'trips', 'equipment', 'gallery', 'profile',
    ];

    /**
     * @return array{route: string, id: int|null}
     */
    public function resolve(string $requestUri): array
    {
        $path = parse_url($requestUri, PHP_URL_PATH) ?: '/';
        $segments = array_values(array_filter(explode('/', trim($path, '/')), static fn (string $part): bool => $part !== ''));

        if ($segments === []) {
            return ['route' => 'dives.overview', 'id' => null];
        }

        $resource = $segments[0];

        if (!isset(self::RESOURCE_SEGMENTS[$resource])) {
            return ['route' => 'not-found', 'id' => null];
        }

        if (in_array($resource, self::OVERVIEW_RESOURCES, true) && count($segments) === 1) {
            return ['route' => $resource . '.overview', 'id' => null];
        }

        if (in_array($resource, self::DETAIL_RESOURCES, true) && isset($segments[1]) && ctype_digit($segments[1])) {
            return ['route' => $resource . '.detail', 'id' => (int) $segments[1]];
        }

        return ['route' => 'not-found', 'id' => null];
    }
}
