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
}
