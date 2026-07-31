<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Support\Seo;

use PhpDivingLog\Adapters\Web\Router;
use PhpDivingLog\Support\Config;
use PhpDivingLog\Support\Seo\CanonicalUrlBuilder;
use PHPUnit\Framework\TestCase;

final class CanonicalUrlBuilderTest extends TestCase
{
    private function builder(array $overrides = []): CanonicalUrlBuilder
    {
        $config = Config::fromArray(array_merge([
            'DB_HOST' => 'localhost',
            'DB_NAME' => 'divelog',
            'DB_USER' => 'divelog',
            'APP_URL' => 'https://dives.example.com',
        ], $overrides));

        return new CanonicalUrlBuilder($config, Router::RESOURCE_SEGMENTS);
    }

    public function testQueryStringModeOverviewWithoutPage(): void
    {
        $builder = $this->builder(['APP_QUERY_STRING' => 'true']);

        self::assertSame(
            'https://dives.example.com/?type=sites',
            $builder->build('sites.overview', null)
        );
    }

    public function testQueryStringModeOverviewWithPageBeyondOne(): void
    {
        $builder = $this->builder(['APP_QUERY_STRING' => 'true']);

        self::assertSame(
            'https://dives.example.com/?type=gallery&page=3',
            $builder->build('gallery.overview', null, 3)
        );
    }

    public function testQueryStringModeOverviewOmitsPageWhenOne(): void
    {
        $builder = $this->builder(['APP_QUERY_STRING' => 'true']);

        self::assertSame(
            'https://dives.example.com/?type=dives',
            $builder->build('dives.overview', null, 1)
        );
    }

    public function testQueryStringModeDetail(): void
    {
        $builder = $this->builder(['APP_QUERY_STRING' => 'true']);

        self::assertSame(
            'https://dives.example.com/?type=dives&id=5',
            $builder->build('dives.detail', 5)
        );
    }

    public function testPathModeDivesOverviewPageOneCollapsesToRoot(): void
    {
        $builder = $this->builder(['APP_QUERY_STRING' => 'false']);

        self::assertSame(
            'https://dives.example.com/',
            $builder->build('dives.overview', null, 1)
        );
    }

    public function testPathModeDivesOverviewBeyondPageOneUsesSegment(): void
    {
        $builder = $this->builder(['APP_QUERY_STRING' => 'false']);

        self::assertSame(
            'https://dives.example.com/dives?page=2',
            $builder->build('dives.overview', null, 2)
        );
    }

    public function testPathModeOtherOverviewUsesSegment(): void
    {
        $builder = $this->builder(['APP_QUERY_STRING' => 'false']);

        self::assertSame(
            'https://dives.example.com/sites',
            $builder->build('sites.overview', null)
        );
    }

    public function testPathModeDetailUsesSegmentAndId(): void
    {
        $builder = $this->builder(['APP_QUERY_STRING' => 'false']);

        self::assertSame(
            'https://dives.example.com/sites/42',
            $builder->build('sites.detail', 42)
        );
    }

    public function testPathModeOmitsPageQueryWhenOne(): void
    {
        $builder = $this->builder(['APP_QUERY_STRING' => 'false']);

        self::assertSame(
            'https://dives.example.com/gallery',
            $builder->build('gallery.overview', null, 1)
        );
    }

    public function testReturnsNullWhenBaseUrlNotConfigured(): void
    {
        $builder = $this->builder(['APP_URL' => '']);

        self::assertNull($builder->build('dives.detail', 5));
    }

    public function testReturnsNullForUnmappedResource(): void
    {
        $builder = $this->builder();

        self::assertNull($builder->build('unknown.overview', null));
    }

    public function testReturnsNullForMalformedRoute(): void
    {
        $builder = $this->builder();

        self::assertNull($builder->build('dives', null));
    }
}
