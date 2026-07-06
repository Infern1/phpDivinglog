<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web;

final class Router
{
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

        $overviewRoutes = [
            'dives' => 'dives.overview',
            'sites' => 'sites.overview',
            'countries' => 'countries.overview',
            'cities' => 'cities.overview',
            'shops' => 'shops.overview',
            'trips' => 'trips.overview',
            'equipment' => 'equipment.overview',
            'gallery' => 'gallery.overview',
            'stats' => 'stats.overview',
            'summary' => 'summary.overview',
        ];

        $detailRoutes = [
            'dives' => 'dives.detail',
            'sites' => 'sites.detail',
            'countries' => 'countries.detail',
            'cities' => 'cities.detail',
            'shops' => 'shops.detail',
            'trips' => 'trips.detail',
            'equipment' => 'equipment.detail',
            'gallery' => 'gallery.detail',
            'profile' => 'profile.detail',
        ];

        $resource = $segments[0];

        if (isset($overviewRoutes[$resource]) && count($segments) === 1) {
            return ['route' => $overviewRoutes[$resource], 'id' => null];
        }

        if (isset($detailRoutes[$resource]) && isset($segments[1]) && ctype_digit($segments[1])) {
            return ['route' => $detailRoutes[$resource], 'id' => (int) $segments[1]];
        }

        return ['route' => 'not-found', 'id' => null];
    }
}
