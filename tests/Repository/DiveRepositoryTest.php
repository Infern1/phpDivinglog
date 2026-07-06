<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Repository;

use PhpDivingLog\Repository\DiveRepository;
use PDO;
use PHPUnit\Framework\TestCase;

final class DiveRepositoryTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('pdo_sqlite driver is not available in this environment.');
        }

        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('CREATE TABLE DL_Logbook (Number INTEGER PRIMARY KEY, LogID INTEGER, PlaceID INTEGER, CountryID INTEGER, ShopID INTEGER, TripID INTEGER, Divedate TEXT, Entrytime TEXT, Divetime INTEGER, Depth REAL, Profile TEXT, ProfileInt INTEGER, Place TEXT, City TEXT, Country TEXT)');
        $this->pdo->exec("INSERT INTO DL_Logbook (Number, LogID, PlaceID, CountryID, ShopID, TripID, Divedate, Entrytime, Divetime, Depth, Profile, ProfileInt, Place, City, Country) VALUES (1, 10, 100, 5, 1, 1, '2026-01-01', '12:00:00', 44, 20.5, '010000000000015000000000', 60, 'Blue Hole', 'Nassau', 'Bahamas')");
    }

    public function testFindByNumberReturnsDive(): void
    {
        $repo = new DiveRepository($this->pdo, 'DL_');
        $dive = $repo->findByNumber(1);

        self::assertNotNull($dive);
        self::assertSame(1, $dive->number);
        self::assertSame('010000000000015000000000', $dive->extra['profile']);
    }

    public function testListOverviewByCountryReturnsRows(): void
    {
        $repo = new DiveRepository($this->pdo, 'DL_');
        $rows = $repo->listOverviewByCountry(5);

        self::assertCount(1, $rows);
        self::assertSame(1, $rows[0]['number']);
        self::assertSame('Blue Hole, Nassau, Bahamas', $rows[0]['location']);
    }

    public function testFindMetaByLogIdsReturnsLogIdKeyedMetadata(): void
    {
        $repo = new DiveRepository($this->pdo, 'DL_');

        $meta = $repo->findMetaByLogIds([10]);

        self::assertArrayHasKey(10, $meta);
        self::assertSame(1, $meta[10]['number']);
        self::assertSame(100, $meta[10]['place_id']);
        self::assertSame(5, $meta[10]['country_id']);
        self::assertSame('Blue Hole', $meta[10]['place_name']);
        self::assertSame('Nassau', $meta[10]['city_name']);
        self::assertSame('Bahamas', $meta[10]['country_name']);
        self::assertSame('2026-01-01 12:00:00', $meta[10]['date_time']->format('Y-m-d H:i:s'));
    }

    public function testFindMetaByLogIdsReturnsEmptyArrayForEmptyInput(): void
    {
        $repo = new DiveRepository($this->pdo, 'DL_');

        self::assertSame([], $repo->findMetaByLogIds([]));
    }

    public function testFindMetaByLogIdsFallsBackToNumberWhenLogIdColumnMissing(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE DL_Logbook (Number INTEGER PRIMARY KEY, PlaceID INTEGER, CountryID INTEGER, Divedate TEXT, Entrytime TEXT, Place TEXT, City TEXT, Country TEXT)');
        $pdo->exec("INSERT INTO DL_Logbook (Number, PlaceID, CountryID, Divedate, Entrytime, Place, City, Country) VALUES (42, 7, 9, '2026-06-01', '11:22:00', 'Drop Off', 'Porto', 'Portugal')");

        $repo = new DiveRepository($pdo, 'DL_');
        $meta = $repo->findMetaByLogIds([42]);

        self::assertArrayHasKey(42, $meta);
        self::assertSame(42, $meta[42]['number']);
        self::assertSame(7, $meta[42]['place_id']);
        self::assertSame(9, $meta[42]['country_id']);
        self::assertSame('Drop Off', $meta[42]['place_name']);
        self::assertSame('Porto', $meta[42]['city_name']);
        self::assertSame('Portugal', $meta[42]['country_name']);
        self::assertSame('2026-06-01 11:22:00', $meta[42]['date_time']->format('Y-m-d H:i:s'));
    }
}
