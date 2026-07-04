<?php

declare(strict_types=1);

namespace PhpDivingLog\Tests\Repository;

use PhpDivingLog\Repository\StatsRepository;
use PDO;
use PHPUnit\Framework\TestCase;

final class StatsRepositoryTest extends TestCase
{
    public function testAggregateReturnsStats(): void
    {
        if (!in_array('sqlite', PDO::getAvailableDrivers(), true)) {
            $this->markTestSkipped('pdo_sqlite driver is not available in this environment.');
        }

        $pdo = new PDO('sqlite::memory:');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('CREATE TABLE DL_Logbook (Divedate TEXT, Divetime INTEGER, Depth REAL)');
        $pdo->exec("INSERT INTO DL_Logbook (Divedate, Divetime, Depth) VALUES ('2026-01-01', 40, 20.5), ('2026-02-01', 50, 25.5)");

        $repo = new StatsRepository($pdo, 'DL_');
        $stats = $repo->aggregate();

        self::assertSame(2, $stats->diveCount);
        self::assertEqualsWithDelta(25.5, (float) $stats->maxDepth, 0.001);
    }
}
