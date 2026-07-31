<?php

declare(strict_types=1);

namespace PhpDivingLog\Support\Seo;

use PhpDivingLog\Support\Config;

/**
 * Decides which SEO override case applies to a given request and returns the array of keys to
 * merge into the controller's view-model payload before rendering.
 */
final readonly class PageSeoContextBuilder
{
    public function __construct(
        private Config $config,
        private CanonicalUrlBuilder $canonicalUrlBuilder,
        private WebPageSchemaBuilder $webPageSchemaBuilder,
    ) {
    }

    /**
     * @param array<string, mixed> $query
     *
     * @return array<string, mixed>
     */
    public function build(string $route, ?int $id, array $query, ?string $title, ?string $description): array
    {
        if (!$this->config->seoEnabled()) {
            return [
                'title' => null,
                'meta_description' => null,
                'robots' => 'noindex,nofollow',
            ];
        }

        if ($route === 'summary.overview') {
            return ['robots' => 'noindex,nofollow'];
        }

        $page = isset($query['page']) && is_numeric($query['page']) ? max(1, (int) $query['page']) : 1;

        $canonicalUrl = $this->canonicalUrlBuilder->build($route, $id, $page);
        if ($canonicalUrl === null || $title === null) {
            return [];
        }

        $schemaJson = $this->webPageSchemaBuilder->build($canonicalUrl, $title, $description, $this->config->language());

        return [
            'canonical_url' => $canonicalUrl,
            'schema_json' => $schemaJson,
        ];
    }
}
