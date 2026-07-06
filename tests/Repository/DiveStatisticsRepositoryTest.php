<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Repository;

use PhpDivingLog\Repository\DiveStatisticsRepository;
use PDO;
use PHPUnit\Framework\TestCase;

final class DiveStatisticsRepositoryTest extends TestCase
{
    public function testComputeReturnsExpectedStatistics(): void
    {
        if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('pdo_sqlite driver is not available in this environment.');
        }

        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $fixturesPath = dirname(__DIR__) . '/fixtures';
        $schema = file_get_contents($fixturesPath . '/schema.sql');
        $seed = file_get_contents($fixturesPath . '/seed.sql');
        if ($schema === false || $seed === false) {
            self::fail('Could not load SQL fixtures.');
        }

        $pdo->exec($schema);
        $pdo->exec($seed);

        $repo = new DiveStatisticsRepository($pdo, 'DL_');
        $stats = $repo->compute();

        self::assertSame(3, $stats->totalDives);
        self::assertSame('2026-01-01', $stats->firstDiveDate?->format('Y-m-d'));
        self::assertSame(1, $stats->firstDiveNumber);
        self::assertSame('2026-03-15', $stats->lastDiveDate?->format('Y-m-d'));
        self::assertSame(3, $stats->lastDiveNumber);
        self::assertSame(155, $stats->totalBottomTimeMinutes);

        self::assertSame(40, $stats->diveTime['min']);
        self::assertSame(1, $stats->diveTime['minNumber']);
        self::assertSame(65, $stats->diveTime['max']);
        self::assertSame(3, $stats->diveTime['maxNumber']);

        self::assertEqualsWithDelta(18.0, (float) $stats->depth['min'], 0.001);
        self::assertSame(1, $stats->depth['minNumber']);
        self::assertEqualsWithDelta(41.0, (float) $stats->depth['max'], 0.001);
        self::assertSame(3, $stats->depth['maxNumber']);

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
        self::assertSame(2, $stats->classifications['rep']);
        self::assertSame(1, $stats->classifications['norep']);
        self::assertSame(1, $stats->classifications['single']);
        self::assertSame(2, $stats->classifications['twin']);
        self::assertSame(1, $stats->classifications['oc']);
        self::assertSame(1, $stats->classifications['scr']);
        self::assertSame(1, $stats->classifications['ccr']);

        self::assertSame(1, $stats->depthBuckets['b0_18']);
        self::assertSame(1, $stats->depthBuckets['b19_30']);
        self::assertSame(0, $stats->depthBuckets['b31_40']);
        self::assertSame(1, $stats->depthBuckets['b41_55']);
        self::assertSame(0, $stats->depthBuckets['b55_plus']);
    }

    public function testComputeHandlesMissingOptionalColumns(): void
    {
        if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('pdo_sqlite driver is not available in this environment.');
        }

        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE DL_Logbook (Number INTEGER PRIMARY KEY, Divedate TEXT, Divetime INTEGER, Depth REAL)');
        $pdo->exec("INSERT INTO DL_Logbook (Number, Divedate, Divetime, Depth) VALUES (1, '2026-01-01', 40, 18.0)");

        $repo = new DiveStatisticsRepository($pdo, 'DL_');
        $stats = $repo->compute();

        self::assertSame(1, $stats->totalDives);
        self::assertNull($stats->classifications['shore']);
        self::assertNull($stats->classifications['deco']);
        self::assertSame(1, $stats->depthBuckets['b0_18']);
    }
}
