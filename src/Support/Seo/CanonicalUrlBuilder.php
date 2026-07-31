<?php

declare(strict_types=1);

namespace PhpDivingLog\Support\Seo;

use PhpDivingLog\Support\Config;

/**
 * Builds the absolute canonical URL for a route, matching whichever routing mode
 * (query-string vs. path-based) is currently active, so search engines are pointed at a URL
 * that actually resolves on this deployment.
 *
 * Framework/adapter-agnostic on purpose: it depends only on Config and a plain resource-segment
 * map supplied by the caller (e.g. Router::RESOURCE_SEGMENTS, wired in via bootstrap.php) rather
 * than importing the web adapter's Router directly, keeping core Support code free of any
 * dependency on adapters/.
 */
final readonly class CanonicalUrlBuilder
{
    /**
     * @param array<string, string> $resourceSegments Resource key to URL path segment map.
     */
    public function __construct(
        private Config $config,
        private array $resourceSegments,
    ) {
    }

    /**
     * @return string|null Absolute canonical URL, or null when no public base URL is
     *                      configured or the route's resource has no known path segment.
     */
    public function build(string $route, ?int $id, int $page = 1): ?string
    {
        $base = rtrim($this->config->appUrl(), '/');
        if ($base === '') {
            return null;
        }

        $parts = explode('.', $route, 2);
        if (count($parts) !== 2) {
            return null;
        }

        [$resource, $kind] = $parts;

        $segment = $this->resourceSegments[$resource] ?? null;
        if ($segment === null) {
            return null;
        }

        $isOverview = $kind === 'overview';
        $includePage = $isOverview && $page > 1;

        // dives.overview page 1 is also reachable at the bare root (Router::resolve() treats an
        // empty path the same as /dives), in both routing modes -- canonicalize to it so the
        // root URL and its ?type=dives / /dives equivalents don't compete as duplicate content.
        $isDivesRootPageOne = $isOverview && $resource === 'dives' && $page <= 1;

        if ($this->config->queryStringMode()) {
            if ($isDivesRootPageOne) {
                return $base . '/';
            }

            $params = ['type' => $resource];
            if ($id !== null) {
                $params['id'] = $id;
            }

            if ($includePage) {
                $params['page'] = $page;
            }

            return $base . '/?' . http_build_query($params);
        }

        $path = $isDivesRootPageOne ? '/' : ($isOverview ? '/' . $segment : '/' . $segment . '/' . $id);
        $query = $includePage ? '?page=' . $page : '';

        return $base . $path . $query;
    }
}
