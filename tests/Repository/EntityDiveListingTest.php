<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Repository;

use PhpDivingLog\Repository\CountryRepository;
use PhpDivingLog\Repository\DiveRepository;
use PhpDivingLog\Repository\DiveSiteRepository;
use PhpDivingLog\Repository\TripRepository;
use PDO;
use PHPUnit\Framework\TestCase;

final class EntityDiveListingTest extends TestCase
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
        $schema = file_get_contents($fixturesPath . '/schema.sql');
        $seed = file_get_contents($fixturesPath . '/seed.sql');
        if ($schema === false || $seed === false) {
            self::fail('Could not load SQL fixtures.');
        }

        $this->pdo->exec($schema);
        $this->pdo->exec($seed);
    }

    public function testDiveOverviewByPlaceTripAndCountryIsFilteredAndOrdered(): void
    {
        $repo = new DiveRepository($this->pdo, 'DL_');

        $byPlace = $repo->listOverviewByPlace(10);
        self::assertCount(2, $byPlace);
        self::assertSame(3, $byPlace[0]['number']);
        self::assertSame(1, $byPlace[1]['number']);

        $byTrip = $repo->listOverviewByTrip(1);
        self::assertCount(2, $byTrip);
        self::assertSame(3, $byTrip[0]['number']);
        self::assertSame(1, $byTrip[1]['number']);

        $byCountry = $repo->listOverviewByCountry(1);
        self::assertCount(3, $byCountry);
        self::assertSame(3, $byCountry[0]['number']);
        self::assertSame(2, $byCountry[1]['number']);
        self::assertSame(1, $byCountry[2]['number']);
    }

    public function testSiteAndCountryListWithDiveCounts(): void
    {
        $siteRepo = new DiveSiteRepository($this->pdo, 'DL_');
        $countryRepo = new CountryRepository($this->pdo, 'DL_');

        $sites = $siteRepo->listWithDiveCounts();
        self::assertSame('Blue Hole', $sites[0]['site']->name);
        self::assertSame(2, $sites[0]['diveCount']);
        self::assertSame('Coral Garden', $sites[1]['site']->name);
        self::assertSame(1, $sites[1]['diveCount']);

        $countries = $countryRepo->listWithDiveCounts();
        self::assertSame('Bahamas', $countries[0]['country']->name);
        self::assertSame(3, $countries[0]['diveCount']);
        self::assertSame('Egypt', $countries[1]['country']->name);
        self::assertSame(0, $countries[1]['diveCount']);
    }

    public function testSiteDiveCountsFallbackToPlaceIdOnLogbookWhenPlaceUsesIdColumn(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE DL_Place (ID INTEGER PRIMARY KEY, Place TEXT)');
        $pdo->exec('CREATE TABLE DL_Logbook (Number INTEGER PRIMARY KEY, PlaceID INTEGER)');
        $pdo->exec("INSERT INTO DL_Place (ID, Place) VALUES (10, 'Blue Hole'), (11, 'Coral Garden')");
        $pdo->exec('INSERT INTO DL_Logbook (Number, PlaceID) VALUES (1, 10), (2, 10), (3, 11)');

        $repo = new DiveSiteRepository($pdo, 'DL_');
        $sites = $repo->listWithDiveCounts();

        self::assertCount(2, $sites);
        self::assertSame('Blue Hole', $sites[0]['site']->name);
        self::assertSame(2, $sites[0]['diveCount']);
        self::assertSame('Coral Garden', $sites[1]['site']->name);
        self::assertSame(1, $sites[1]['diveCount']);
    }

    public function testCountryDiveCountsFallbackWhenCountryUsesIdColumn(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE DL_Country (ID INTEGER PRIMARY KEY, Country TEXT)');
        $pdo->exec('CREATE TABLE DL_Logbook (Number INTEGER PRIMARY KEY, CountryID INTEGER)');
        $pdo->exec("INSERT INTO DL_Country (ID, Country) VALUES (1, 'Bahamas'), (2, 'Egypt')");
        $pdo->exec('INSERT INTO DL_Logbook (Number, CountryID) VALUES (1, 1), (2, 1), (3, 2)');

        $repo = new CountryRepository($pdo, 'DL_');
        $countries = $repo->listWithDiveCounts();

        self::assertCount(2, $countries);
        self::assertSame('Bahamas', $countries[0]['country']->name);
        self::assertSame(2, $countries[0]['diveCount']);
        self::assertSame('Egypt', $countries[1]['country']->name);
        self::assertSame(1, $countries[1]['diveCount']);
    }

    public function testCountryNameMojibakeIsNormalized(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE DL_Country (CountryID INTEGER PRIMARY KEY, Country TEXT, FlagImage TEXT)');
        $pdo->exec("INSERT INTO DL_Country (CountryID, Country, FlagImage) VALUES (1, 'AustraliÃ«', NULL)");

        $repo = new CountryRepository($pdo, 'DL_');
        $countries = $repo->list();

        self::assertCount(1, $countries);
        self::assertSame('Australië', $countries[0]->name);
    }

    public function testTripListWithDiveCounts(): void
    {
        $tripRepo = new TripRepository($this->pdo, 'DL_');
        $trips = $tripRepo->listWithDiveCounts();

        self::assertCount(3, $trips);
        self::assertSame('No Dives Trip', $trips[0]['trip']->name);
        self::assertSame(0, $trips[0]['diveCount']);
        self::assertSame('Reef Weekend', $trips[1]['trip']->name);
        self::assertSame(1, $trips[1]['diveCount']);
        self::assertSame('Spring Bahamas', $trips[2]['trip']->name);
        self::assertSame(2, $trips[2]['diveCount']);
    }
}
