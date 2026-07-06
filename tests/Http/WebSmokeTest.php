<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Http;

use PhpDivingLog\Support\Config;
use PHPUnit\Framework\TestCase;

final class WebSmokeTest extends TestCase
{
    private int $initialOutputBufferLevel = 0;

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('pdo_sqlite driver is not available in this environment.');
        }

        $this->seedFixtureDatabase();
        $this->initialOutputBufferLevel = ob_get_level();
    }

    protected function tearDown(): void
    {
        while (ob_get_level() > $this->initialOutputBufferLevel) {
            ob_end_clean();
        }
    }

    public function testHomePageRendersDiveOverview(): void
    {
        $response = $this->request('/');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('dives', $response['body']);
        self::assertStringContainsString('data-dives-table', $response['body']);
        self::assertStringContainsString('data-href="/dives/1"', $response['body']);
        self::assertStringContainsString('role="link"', $response['body']);
        self::assertStringContainsString('Search location, number...', $response['body']);
        self::assertStringContainsString('name="q"', $response['body']);
        self::assertStringContainsString('name="sort"', $response['body']);
        self::assertStringContainsString('Apply', $response['body']);
        self::assertStringContainsString('/assets/vendor/beercss/beer.min.css', $response['body']);
        self::assertStringContainsString('/assets/vendor/beercss/material-dynamic-colors.min.js', $response['body']);
        self::assertStringContainsString('/assets/js/theme.js', $response['body']);
        self::assertStringContainsString('data-theme-toggle', $response['body']);
    }

    public function testDiveDetailRendersContent(): void
    {
        $response = $this->request('/dives/1');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Dive #1', $response['body']);
        self::assertStringContainsString('dive-hero-wide', $response['body']);
        self::assertStringContainsString('Depth', $response['body']);
        self::assertStringContainsString('Duration', $response['body']);
        self::assertStringContainsString('Temp', $response['body']);
        self::assertStringContainsString('Visibility', $response['body']);
        self::assertStringContainsString('Avg depth', $response['body']);
        self::assertStringContainsString('Weather', $response['body']);
        self::assertStringContainsString('Weight', $response['body']);
        self::assertStringContainsString('Buddy', $response['body']);
        self::assertStringContainsString('Tanks', $response['body']);
        self::assertStringContainsString('Main tank', $response['body']);
        self::assertStringContainsString('Press. Start', $response['body']);
        self::assertStringContainsString('Details', $response['body']);
        self::assertStringContainsString('Dive site', $response['body']);
        self::assertStringContainsString('Location', $response['body']);
        self::assertStringContainsString('Country', $response['body']);
        self::assertStringContainsString('/countries/1', $response['body']);
        self::assertStringContainsString('Temp. Air', $response['body']);
        self::assertStringContainsString('Blue Hole', $response['body']);
        self::assertStringContainsString('Ocean Dive Center', $response['body']);
        self::assertStringContainsString('Spring Bahamas', $response['body']);
        self::assertStringContainsString('Dive profile', $response['body']);
        self::assertStringContainsString('Ascent / descent rates', $response['body']);
        self::assertStringContainsString('profile-chart', $response['body']);
        self::assertStringContainsString('profile-rate-chart', $response['body']);
        self::assertStringContainsString('data-profile-live="depth"', $response['body']);
        self::assertStringContainsString('data-profile-live="rate"', $response['body']);
        self::assertStringContainsString('Logbook', $response['body']);
        self::assertStringContainsString('data-logbook-pane', $response['body']);
        self::assertStringContainsString('data-logbook-list', $response['body']);
        self::assertStringContainsString('data-logbook-link', $response['body']);
        self::assertStringContainsString('dive-sequence-nav-top', $response['body']);
        self::assertStringContainsString('aria-label="Next dive"', $response['body']);
        self::assertStringContainsString('/assets/js/profile-chart.js', $response['body']);
    }

    public function testUnknownDiveReturnsNotFound(): void
    {
        $response = $this->request('/dives/9999');

        self::assertSame(404, $response['status']);
        self::assertStringContainsString('Dive not found', $response['body']);
    }

    public function testSitesOverviewRenders(): void
    {
        $response = $this->request('/sites');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Dive Sites', $response['body']);
        self::assertStringContainsString('Blue Hole', $response['body']);
        self::assertStringContainsString('data-href="/sites/10"', $response['body']);
        self::assertStringContainsString('<td>2</td>', $response['body']);
    }

    public function testSiteDetailRenders(): void
    {
        $response = $this->request('/sites/10');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Blue Hole', $response['body']);
        self::assertStringContainsString('Dives at this site', $response['body']);
        self::assertStringContainsString('data-href="/dives/1"', $response['body']);
        self::assertStringContainsString('data-href="/dives/3"', $response['body']);
    }

    public function testSiteUnknownReturnsNotFound(): void
    {
        $response = $this->request('/sites/9999');

        self::assertSame(404, $response['status']);
        self::assertStringContainsString('Site not found', $response['body']);
    }

    public function testCountriesOverviewRenders(): void
    {
        $response = $this->request('/countries');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Countries', $response['body']);
        self::assertStringContainsString('Bahamas', $response['body']);
        self::assertStringContainsString('data-href="/countries/1"', $response['body']);
        self::assertStringContainsString('>3</td>', $response['body']);
    }

    public function testCountryDetailRenders(): void
    {
        $response = $this->request('/countries/1');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Bahamas', $response['body']);
        self::assertStringContainsString('Dives in this country', $response['body']);
        self::assertStringContainsString('data-href="/sites/10"', $response['body']);
        self::assertStringContainsString('data-href="/dives/2"', $response['body']);
    }

    public function testCountryUnknownReturnsNotFound(): void
    {
        $response = $this->request('/countries/9999');

        self::assertSame(404, $response['status']);
        self::assertStringContainsString('Country not found', $response['body']);
    }

    public function testCitiesOverviewRenders(): void
    {
        $response = $this->request('/cities');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Cities', $response['body']);
        self::assertStringContainsString('Nassau', $response['body']);
    }

    public function testCityDetailRenders(): void
    {
        $response = $this->request('/cities/1');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Nassau', $response['body']);
    }

    public function testCityUnknownReturnsNotFound(): void
    {
        $response = $this->request('/cities/9999');

        self::assertSame(404, $response['status']);
        self::assertStringContainsString('City not found', $response['body']);
    }

    public function testShopsOverviewRenders(): void
    {
        $response = $this->request('/shops');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Shops', $response['body']);
        self::assertStringContainsString('Ocean Dive Center', $response['body']);
    }

    public function testShopDetailRenders(): void
    {
        $response = $this->request('/shops/1');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Ocean Dive Center', $response['body']);
    }

    public function testShopUnknownReturnsNotFound(): void
    {
        $response = $this->request('/shops/9999');

        self::assertSame(404, $response['status']);
        self::assertStringContainsString('Shop not found', $response['body']);
    }

    public function testTripsOverviewRenders(): void
    {
        $response = $this->request('/trips');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Trips', $response['body']);
        self::assertStringContainsString('Spring Bahamas', $response['body']);
        self::assertStringContainsString('data-href="/trips/1"', $response['body']);
        self::assertStringContainsString('>2</td>', $response['body']);
    }

    public function testTripDetailRenders(): void
    {
        $response = $this->request('/trips/1');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Spring Bahamas', $response['body']);
        self::assertStringContainsString('Dives in this trip', $response['body']);
        self::assertStringContainsString('data-href="/dives/1"', $response['body']);
        self::assertStringContainsString('/countries/1', $response['body']);
    }

    public function testTripUnknownReturnsNotFound(): void
    {
        $response = $this->request('/trips/9999');

        self::assertSame(404, $response['status']);
        self::assertStringContainsString('Trip not found', $response['body']);
    }

    public function testEquipmentOverviewRenders(): void
    {
        $response = $this->request('/equipment');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Equipment', $response['body']);
        self::assertStringContainsString('Regulator', $response['body']);
        self::assertStringContainsString('data-href="/equipment/1"', $response['body']);
    }

    public function testEquipmentDetailRenders(): void
    {
        $response = $this->request('/equipment/1');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Regulator', $response['body']);
        self::assertStringContainsString('Dives using this equipment', $response['body']);
        self::assertStringContainsString('data-href="/dives/1"', $response['body']);
    }

    public function testEquipmentUnknownReturnsNotFound(): void
    {
        $response = $this->request('/equipment/9999');

        self::assertSame(404, $response['status']);
        self::assertStringContainsString('Equipment not found', $response['body']);
    }

    public function testStatsOverviewRenders(): void
    {
        $response = $this->request('/stats');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Dive Statistics', $response['body']);
        self::assertStringContainsString('Total dives', $response['body']);
        self::assertStringContainsString('Depth distribution', $response['body']);
        self::assertStringContainsString('id="stats-depth-chart"', $response['body']);
        self::assertStringContainsString('data-depth-distribution=', $response['body']);
        self::assertStringContainsString('No-deco dives', $response['body']);
    }

    public function testGalleryRenders(): void
    {
        $response = $this->request('/gallery/100');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Gallery', $response['body']);
    }

    public function testSummaryRenders(): void
    {
        $response = $this->request('/summary');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Dive Summary', $response['body']);
    }

    public function testProfileDetailReturnsJson(): void
    {
        $response = $this->request('/profile/1');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('"diveNumber":1', $response['body']);
        self::assertStringContainsString('"depthUnit"', $response['body']);
        self::assertStringContainsString('"averageSeries"', $response['body']);
        self::assertStringContainsString('"ascentRateSeries"', $response['body']);
        self::assertStringContainsString('"descentRateSeries"', $response['body']);
    }

    public function testProfileUnknownReturnsJsonNotFound(): void
    {
        $response = $this->request('/profile/9999');

        self::assertSame(404, $response['status']);
        self::assertStringContainsString('"code":"not_found"', $response['body']);
    }

    private function seedFixtureDatabase(): void
    {
        $fixturesPath = dirname(__DIR__) . '/fixtures';
        $dbPath = $fixturesPath . '/http-smoke.sqlite';

        if (is_file($dbPath)) {
            unlink($dbPath);
        }

        $pdo = new \PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $schema = file_get_contents($fixturesPath . '/schema.sql');
        $seed = file_get_contents($fixturesPath . '/seed.sql');
        if ($schema === false || $seed === false) {
            self::fail('Could not load SQL fixtures.');
        }

        $pdo->exec($schema);
        $pdo->exec($seed);
    }

    /**
     * @return array{status:int, body:string}
     */
    private function request(string $uri): array
    {
        http_response_code(200);

        $_SERVER = [
            'REQUEST_URI' => $uri,
            'REQUEST_METHOD' => 'GET',
        ];
        $_GET = [];

        putenv('DB_DSN=sqlite:' . dirname(__DIR__) . '/fixtures/http-smoke.sqlite');
        putenv('DB_USER=test');
        putenv('DB_PASSWORD=');
        putenv('APP_QUERY_STRING=false');
        putenv('APP_ENV=test');

        ob_start();
        include dirname(__DIR__, 2) . '/public/index.php';
        $body = (string) ob_get_clean();

        return [
            'status' => http_response_code(),
            'body' => $body,
        ];
    }
}
