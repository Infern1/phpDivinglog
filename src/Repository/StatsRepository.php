<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use DateTimeImmutable;
use PhpDivingLog\Model\Stats;
use PDO;

final readonly class StatsRepository
{
    public function __construct(private PDO $pdo, private string $tablePrefix)
    {
    }

    public function aggregate(): Stats
    {
        $sql = sprintf('SELECT COUNT(*) AS DiveCount, MIN(Divedate) AS FirstDive, MAX(Divedate) AS LastDive, MAX(Depth) AS MaxDepth, AVG(Depth) AS AvgDepth, SUM(Divetime) AS TotalDuration FROM %sLogbook', $this->tablePrefix);
        $row = $this->pdo->query($sql)->fetch();

        if (!is_array($row)) {
            return new Stats(0, null, null, null, null, null);
        }

        return new Stats(
            (int) ($row['DiveCount'] ?? 0),
            isset($row['FirstDive']) && $row['FirstDive'] !== null ? new DateTimeImmutable((string) $row['FirstDive']) : null,
            isset($row['LastDive']) && $row['LastDive'] !== null ? new DateTimeImmutable((string) $row['LastDive']) : null,
            isset($row['MaxDepth']) ? (float) $row['MaxDepth'] : null,
            isset($row['AvgDepth']) ? (float) $row['AvgDepth'] : null,
            isset($row['TotalDuration']) ? (int) $row['TotalDuration'] : null
        );
    }
}
