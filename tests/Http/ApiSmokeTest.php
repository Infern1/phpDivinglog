<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Http;

use PHPUnit\Framework\TestCase;

final class ApiSmokeTest extends TestCase
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

    public function testDiveCollectionEndpointReturnsData(): void
    {
        $response = $this->request('/api/dives');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('"data"', $response['body']);
    }

    public function testDiveItemEndpointReturnsData(): void
    {
        $response = $this->request('/api/dives/1');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('"number":1', $response['body']);
    }

    public function testDiveItemUnknownReturnsNotFound(): void
    {
        $response = $this->request('/api/dives/9999');

        self::assertSame(404, $response['status']);
        self::assertStringContainsString('"code":"not_found"', $response['body']);
    }

    public function testStatsEndpointReturnsData(): void
    {
        $response = $this->request('/api/stats');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('"diveCount"', $response['body']);
    }

    public function testSitesEndpointsReturnData(): void
    {
        $collection = $this->request('/api/sites');
        $item = $this->request('/api/sites/10');

        self::assertSame(200, $collection['status']);
        self::assertSame(200, $item['status']);
        self::assertStringContainsString('Blue Hole', $item['body']);
    }

    public function testCountriesEndpointsReturnData(): void
    {
        $collection = $this->request('/api/countries');
        $item = $this->request('/api/countries/1');

        self::assertSame(200, $collection['status']);
        self::assertSame(200, $item['status']);
        self::assertStringContainsString('Bahamas', $item['body']);
    }

    public function testCitiesEndpointsReturnData(): void
    {
        $collection = $this->request('/api/cities');
        $item = $this->request('/api/cities/1');

        self::assertSame(200, $collection['status']);
        self::assertSame(200, $item['status']);
        self::assertStringContainsString('Nassau', $item['body']);
    }

    public function testShopsEndpointsReturnData(): void
    {
        $collection = $this->request('/api/shops');
        $item = $this->request('/api/shops/1');

        self::assertSame(200, $collection['status']);
        self::assertSame(200, $item['status']);
        self::assertStringContainsString('Ocean Dive Center', $item['body']);
    }

    public function testTripsEndpointsReturnData(): void
    {
        $collection = $this->request('/api/trips');
        $item = $this->request('/api/trips/1');

        self::assertSame(200, $collection['status']);
        self::assertSame(200, $item['status']);
        self::assertStringContainsString('Spring Bahamas', $item['body']);
    }

    public function testEquipmentEndpointsReturnData(): void
    {
        $collection = $this->request('/api/equipment');
        $item = $this->request('/api/equipment/1');

        self::assertSame(200, $collection['status']);
        self::assertSame(200, $item['status']);
        self::assertStringContainsString('Regulator', $item['body']);
    }

    public function testResourceUnknownReturnsNotFound(): void
    {
        $response = $this->request('/api/unknown');

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

        putenv('DB_DSN=sqlite:' . dirname(__DIR__) . '/fixtures/http-smoke.sqlite');
        putenv('DB_USER=test');
        putenv('DB_PASSWORD=');
        putenv('APP_ENV=test');

        ob_start();
        include dirname(__DIR__, 2) . '/public/api.php';
        $body = (string) ob_get_clean();

        return [
            'status' => http_response_code(),
            'body' => $body,
        ];
    }
}
