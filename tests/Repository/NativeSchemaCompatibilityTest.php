<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Repository;

use PDO;
use PhpDivingLog\Repository\DiveRepository;
use PhpDivingLog\Repository\DiveSiteRepository;
use PhpDivingLog\Repository\DiveStatisticsRepository;
use PhpDivingLog\Repository\EquipmentRepository;
use PhpDivingLog\Repository\ShopRepository;
use PhpDivingLog\Repository\TripRepository;
use PHPUnit\Framework\TestCase;

/**
 * Exercises the repository layer against a fixture modeled on the native, unprefixed
 * Diving Log SQLite desktop export (TABLE_PREFIX=''), as opposed to the MySQL-export-shaped
 * fixture (tests/fixtures/schema.sql, TABLE_PREFIX='DL_') used elsewhere in this suite.
 */
final class NativeSchemaCompatibilityTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('pdo_sqlite driver is not available in this environment.');
        }

        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $fixturesPath = dirname(__DIR__) . '/fixtures';
        $schema = file_get_contents($fixturesPath . '/native-schema.sql');
        $seed = file_get_contents($fixturesPath . '/native-seed.sql');
        if ($schema === false || $seed === false) {
            self::fail('Could not load native SQL fixtures.');
        }

        $this->pdo->exec($schema);
        $this->pdo->exec($seed);
    }

    public function testDiveListingAndDetailResolveByNumber(): void
    {
        $repo = new DiveRepository($this->pdo, '');

        $dive = $repo->findByNumber(1);
        self::assertNotNull($dive);
        self::assertSame(1, $dive->number);
        self::assertEqualsWithDelta(18.0, $dive->depthMax, 0.001);
        self::assertSame('Blue Hole', $dive->extra['place_name']);
        self::assertSame('Nassau', $dive->extra['city_name']);
        self::assertSame('Bahamas', $dive->extra['country_name']);

        self::assertSame([3, 2, 1], $repo->listNumbers(10, 0));
        self::assertSame(3, $repo->countAll());
        self::assertSame(1, $repo->findPreviousNumber(2));
        self::assertSame(3, $repo->findNextNumber(2));

        $byPlace = $repo->listOverviewByPlace(10);
        self::assertCount(2, $byPlace);
        self::assertSame(3, $byPlace[0]['number']);
        self::assertSame(1, $byPlace[1]['number']);
    }

    public function testStatisticsClassificationsMatchNativeIntegerBooleans(): void
    {
        $repo = new DiveStatisticsRepository($this->pdo, '');
        $stats = $repo->compute();

        self::assertSame(3, $stats->totalDives);
        self::assertSame(2, $stats->classifications['shore']);
        self::assertSame(1, $stats->classifications['boat']);
        self::assertSame(1, $stats->classifications['night']);
        self::assertSame(1, $stats->classifications['drift']);
        self::assertSame(1, $stats->classifications['deep']);
        self::assertSame(1, $stats->classifications['cave']);
        self::assertSame(1, $stats->classifications['wreck']);
        self::assertSame(1, $stats->classifications['photo']);
        self::assertSame(1, $stats->classifications['salt']);
        self::assertSame(1, $stats->classifications['fresh']);
        self::assertSame(1, $stats->classifications['brackish']);
        self::assertSame(1, $stats->classifications['deco']);
        self::assertSame(2, $stats->classifications['nodeco']);
        self::assertSame(3, $stats->classifications['rep']);
        self::assertSame(0, $stats->classifications['norep']);
        self::assertSame(1, $stats->classifications['single']);
        self::assertSame(2, $stats->classifications['twin']);
        self::assertSame(1, $stats->classifications['oc']);
        self::assertSame(1, $stats->classifications['scr']);
        self::assertSame(1, $stats->classifications['ccr']);
    }

    public function testDiveSiteLookupsFallBackToIdColumn(): void
    {
        $repo = new DiveSiteRepository($this->pdo, '');

        $sites = $repo->listWithDiveCounts();
        self::assertCount(2, $sites);
        self::assertSame('Blue Hole', $sites[0]['site']->name);
        self::assertSame(2, $sites[0]['diveCount']);
        self::assertSame('Coral Garden', $sites[1]['site']->name);
        self::assertSame(1, $sites[1]['diveCount']);

        $site = $repo->findById(10);
        self::assertNotNull($site);
        self::assertSame('Blue Hole', $site->name);
        self::assertSame(1, $site->countryId);
    }

    public function testTripLookupsFallBackToIdColumn(): void
    {
        $repo = new TripRepository($this->pdo, '');

        $trips = $repo->listWithDiveCounts();
        self::assertCount(3, $trips);
        self::assertSame('No Dives Trip', $trips[0]['trip']->name);
        self::assertSame(0, $trips[0]['diveCount']);
        self::assertSame('Reef Weekend', $trips[1]['trip']->name);
        self::assertSame(1, $trips[1]['diveCount']);
        self::assertSame('Spring Bahamas', $trips[2]['trip']->name);
        self::assertSame(2, $trips[2]['diveCount']);

        $trip = $repo->findById(1);
        self::assertNotNull($trip);
        self::assertSame('Spring Bahamas', $trip->name);
    }

    public function testShopLookupFallsBackToIdColumn(): void
    {
        $repo = new ShopRepository($this->pdo, '');

        $shop = $repo->findById(1);
        self::assertNotNull($shop);
        self::assertSame('Ocean Dive Center', $shop->name);
    }

    public function testEquipmentLookupFallsBackToIdColumnAndDegradesDiveCounts(): void
    {
        $repo = new EquipmentRepository($this->pdo, '');

        $item = $repo->findById(1);
        self::assertNotNull($item);
        self::assertSame('Regulator', $item->object);

        // The native export has no EquipmentID column on Logbook, so the dive-count join
        // is expected to degrade gracefully (null counts) rather than error.
        $withCounts = $repo->listWithDiveCounts();
        self::assertCount(3, $withCounts);
        foreach ($withCounts as $entry) {
            self::assertNull($entry['diveCount']);
        }
    }
}
