<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Http;

use PHPUnit\Framework\TestCase;

/**
 * A focused HTTP smoke test running the app end-to-end (through public/index.php) against
 * the native, unprefixed Diving Log SQLite export shape (TABLE_PREFIX=''), complementing
 * WebSmokeTest's much larger suite which runs against the MySQL-export-shaped fixture
 * (TABLE_PREFIX='DL_'). Not a re-implementation of WebSmokeTest's full route coverage --
 * just enough to catch bootstrap/controller/template breakage on this schema shape.
 */
final class NativeSchemaSmokeTest extends TestCase
{
    private const ENV_KEYS = [
        'DB_DSN', 'DB_USER', 'DB_PASSWORD', 'TABLE_PREFIX',
        'APP_QUERY_STRING', 'APP_ENV', 'APP_URL', 'APP_SEO_ENABLED',
    ];

    private int $initialOutputBufferLevel = 0;

    private string $dbPath;

    /** @var array<string, string|false> */
    private array $originalEnv = [];

    protected function setUp(): void
    {
        if (!in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('pdo_sqlite driver is not available in this environment.');
        }

        foreach (self::ENV_KEYS as $key) {
            $this->originalEnv[$key] = getenv($key);
        }

        $this->dbPath = dirname(__DIR__) . '/fixtures/native-smoke.sqlite';
        $this->seedFixtureDatabase();
        $this->initialOutputBufferLevel = ob_get_level();
    }

    protected function tearDown(): void
    {
        while (ob_get_level() > $this->initialOutputBufferLevel) {
            ob_end_clean();
        }

        // putenv() mutates process-wide state that outlives this test; restore it so later
        // test classes in the same PHPUnit run (e.g. WebSmokeTest) see their own expected
        // environment rather than whatever this test last set (e.g. TABLE_PREFIX='').
        foreach ($this->originalEnv as $key => $value) {
            if ($value === false) {
                putenv($key);
            } else {
                putenv($key . '=' . $value);
            }
        }

        if (is_file($this->dbPath)) {
            unlink($this->dbPath);
        }
    }

    public function testHomePageRendersDiveOverview(): void
    {
        $response = $this->request('/');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('data-dives-table', $response['body']);
        self::assertStringContainsString('data-href="/dives/1"', $response['body']);
    }

    public function testDiveDetailRendersContent(): void
    {
        $response = $this->request('/dives/1');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Dive #1', $response['body']);
        self::assertStringContainsString('Blue Hole', $response['body']);
    }

    public function testUnknownDiveReturnsNotFound(): void
    {
        $response = $this->request('/dives/999999');

        self::assertSame(404, $response['status']);
    }

    public function testStatsOverviewRenders(): void
    {
        $response = $this->request('/stats');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Dive Statistics', $response['body']);
        self::assertStringContainsString('Total dives', $response['body']);
    }

    public function testSitesOverviewRenders(): void
    {
        $response = $this->request('/sites');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Blue Hole', $response['body']);
        self::assertStringContainsString('Coral Garden', $response['body']);
    }

    private function seedFixtureDatabase(): void
    {
        if (is_file($this->dbPath)) {
            unlink($this->dbPath);
        }

        $fixturesPath = dirname(__DIR__) . '/fixtures';

        $pdo = new \PDO('sqlite:' . $this->dbPath);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $schema = file_get_contents($fixturesPath . '/native-schema.sql');
        $seed = file_get_contents($fixturesPath . '/native-seed.sql');
        if ($schema === false || $seed === false) {
            self::fail('Could not load native SQL fixtures.');
        }

        $pdo->exec($schema);
        $pdo->exec($seed);
    }

    /**
     * @param array<string, string> $env
     * @return array{status:int, body:string}
     */
    private function request(string $uri, array $env = []): array
    {
        http_response_code(200);

        $_SERVER = [
            'REQUEST_URI' => $uri,
            'REQUEST_METHOD' => 'GET',
        ];
        $_GET = [];

        $defaults = [
            'DB_DSN' => 'sqlite:' . $this->dbPath,
            'DB_USER' => '',
            'DB_PASSWORD' => '',
            'TABLE_PREFIX' => '',
            'APP_QUERY_STRING' => 'false',
            'APP_ENV' => 'test',
            'APP_URL' => '',
            'APP_SEO_ENABLED' => 'true',
        ];

        foreach (array_merge($defaults, $env) as $name => $value) {
            putenv($name . '=' . $value);
        }

        ob_start();
        include dirname(__DIR__, 2) . '/public/index.php';
        $body = (string) ob_get_clean();

        return [
            'status' => http_response_code(),
            'body' => $body,
        ];
    }
}
