<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Support\Seo;

use PhpDivingLog\Adapters\Web\Router;
use PhpDivingLog\Support\Config;
use PhpDivingLog\Support\Seo\CanonicalUrlBuilder;
use PhpDivingLog\Support\Seo\PageSeoContextBuilder;
use PhpDivingLog\Support\Seo\WebPageSchemaBuilder;
use PHPUnit\Framework\TestCase;

final class PageSeoContextBuilderTest extends TestCase
{
    private function builder(array $overrides = []): PageSeoContextBuilder
    {
        $config = Config::fromArray(array_merge([
            'DB_HOST' => 'localhost',
            'DB_NAME' => 'divelog',
            'DB_USER' => 'divelog',
            'APP_URL' => 'https://dives.example.com',
        ], $overrides));

        return new PageSeoContextBuilder(
            $config,
            new CanonicalUrlBuilder($config, Router::RESOURCE_SEGMENTS),
            new WebPageSchemaBuilder()
        );
    }

    public function testOptedOutForcesTitleAndDescriptionNullAndSetsNoindex(): void
    {
        $builder = $this->builder(['APP_SEO_ENABLED' => 'false']);

        $result = $builder->build('dives.detail', 5, [], 'Dive #5', 'A great dive.');

        self::assertSame([
            'title' => null,
            'meta_description' => null,
            'robots' => 'noindex,nofollow',
        ], $result);
    }

    public function testOptedOutAppliesEvenWithoutTitleOrDescription(): void
    {
        $builder = $this->builder(['APP_SEO_ENABLED' => 'false']);

        $result = $builder->build('dives.overview', null, [], null, null);

        self::assertSame([
            'title' => null,
            'meta_description' => null,
            'robots' => 'noindex,nofollow',
        ], $result);
    }

    public function testSummaryOverviewOnlySetsRobotsNoIndex(): void
    {
        $builder = $this->builder();

        $result = $builder->build('summary.overview', null, [], 'Some title', 'Some description');

        self::assertSame(['robots' => 'noindex,nofollow'], $result);
        self::assertArrayNotHasKey('canonical_url', $result);
        self::assertArrayNotHasKey('schema_json', $result);
    }

    public function testNormalRouteReturnsConsistentCanonicalAndSchema(): void
    {
        $builder = $this->builder();

        $result = $builder->build('dives.detail', 5, [], 'Dive #5', 'A great dive.');

        self::assertSame(['canonical_url', 'schema_json'], array_keys($result));
        self::assertSame('https://dives.example.com/?type=dives&id=5', $result['canonical_url']);

        $decoded = json_decode($result['schema_json'], true);
        self::assertSame($result['canonical_url'], $decoded['url']);
        self::assertSame('Dive #5', $decoded['name']);
        self::assertSame('A great dive.', $decoded['description']);
    }

    public function testNormalRouteReflectsPaginationInCanonical(): void
    {
        $builder = $this->builder();

        $result = $builder->build('dives.overview', null, ['page' => '2'], 'All Dives', 'desc');

        self::assertSame('https://dives.example.com/?type=dives&page=2', $result['canonical_url']);
    }

    public function testNormalRouteReturnsEmptyArrayWhenTitleMissing(): void
    {
        $builder = $this->builder();

        self::assertSame([], $builder->build('dives.detail', 5, [], null, 'desc'));
    }

    public function testNormalRouteReturnsEmptyArrayWhenBaseUrlNotConfigured(): void
    {
        $builder = $this->builder(['APP_URL' => '']);

        self::assertSame([], $builder->build('dives.detail', 5, [], 'Dive #5', 'desc'));
    }
}
