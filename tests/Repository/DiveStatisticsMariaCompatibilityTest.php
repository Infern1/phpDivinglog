<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Repository;

use PhpDivingLog\Repository\DiveStatisticsRepository;
use PDO;
use PHPUnit\Framework\TestCase;

final class DiveStatisticsMariaCompatibilityTest extends TestCase
{
    public function testComputeDoesNotUseReservedAliasNamesInOptionalAggregates(): void
    {
        if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('pdo_sqlite driver is not available in this environment.');
        }

        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE DL_Logbook (Number INTEGER PRIMARY KEY, Divedate TEXT, Divetime INTEGER, Depth REAL, Watertemp REAL, Airtemp REAL)');
        $pdo->exec("INSERT INTO DL_Logbook (Number, Divedate, Divetime, Depth, Watertemp, Airtemp) VALUES (1, '2026-01-01', 42, 19.5, 21.0, 25.0)");

        $repo = new DiveStatisticsRepository($pdo, 'DL_');
        $stats = $repo->compute();

        self::assertSame(1, $stats->totalDives);
        self::assertEqualsWithDelta(21.0, (float) $stats->waterTemp['min'], 0.001);
        self::assertEqualsWithDelta(25.0, (float) $stats->airTemp['max'], 0.001);
    }
}
