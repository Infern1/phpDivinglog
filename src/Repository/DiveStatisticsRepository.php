<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use DateTimeImmutable;
use PDO;
use PhpDivingLog\Model\DiveStatistics;

final readonly class DiveStatisticsRepository
{
    public function __construct(private PDO $pdo, private string $tablePrefix)
    {
    }

    public function compute(): DiveStatistics
    {
        $aggregate = $this->aggregateRow();
        $total = (int) ($aggregate['DiveCount'] ?? 0);
        $firstDate = $this->toDateTime($aggregate['FirstDive'] ?? null);
        $lastDate = $this->toDateTime($aggregate['LastDive'] ?? null);

        $diveTimeMin = $this->toIntOrNull($aggregate['ShortestDiveTime'] ?? null);
        $diveTimeMax = $this->toIntOrNull($aggregate['LongestDiveTime'] ?? null);
        $depthMin = $this->toFloatOrNull($aggregate['ShallowestDepth'] ?? null);
        $depthMax = $this->toFloatOrNull($aggregate['DeepestDepth'] ?? null);
        $waterMin = $this->toFloatOrNull($aggregate['ColdestWaterTemp'] ?? null);
        $waterMax = $this->toFloatOrNull($aggregate['WarmestWaterTemp'] ?? null);
        $airMin = $this->toFloatOrNull($aggregate['ColdestAirTemp'] ?? null);
        $airMax = $this->toFloatOrNull($aggregate['WarmestAirTemp'] ?? null);

        $classifications = $this->computeClassifications($total);

        return new DiveStatistics(
            totalDives: $total,
            firstDiveDate: $firstDate,
            firstDiveNumber: $this->diveNumberFor('Divedate', $aggregate['FirstDive'] ?? null, 'ASC', 'ASC'),
            lastDiveDate: $lastDate,
            lastDiveNumber: $this->diveNumberFor('Divedate', $aggregate['LastDive'] ?? null, 'DESC', 'DESC'),
            totalBottomTimeMinutes: $this->toIntOrNull($aggregate['TotalBottomTime'] ?? null),
            diveTime: [
                'min' => $diveTimeMin,
                'minNumber' => $this->diveNumberFor('Divetime', $diveTimeMin, 'ASC', 'ASC'),
                'max' => $diveTimeMax,
                'maxNumber' => $this->diveNumberFor('Divetime', $diveTimeMax, 'DESC', 'DESC'),
                'avg' => $this->toFloatOrNull($aggregate['AverageDiveTime'] ?? null),
            ],
            depth: [
                'min' => $depthMin,
                'minNumber' => $this->diveNumberFor('Depth', $depthMin, 'ASC', 'ASC'),
                'max' => $depthMax,
                'maxNumber' => $this->diveNumberFor('Depth', $depthMax, 'DESC', 'DESC'),
                'avg' => $this->toFloatOrNull($aggregate['AverageDepth'] ?? null),
            ],
            waterTemp: [
                'min' => $waterMin,
                'minNumber' => $this->diveNumberFor('Watertemp', $waterMin, 'ASC', 'ASC'),
                'max' => $waterMax,
                'maxNumber' => $this->diveNumberFor('Watertemp', $waterMax, 'DESC', 'DESC'),
                'avg' => $this->toFloatOrNull($aggregate['AverageWaterTemp'] ?? null),
            ],
            airTemp: [
                'min' => $airMin,
                'minNumber' => $this->diveNumberFor('Airtemp', $airMin, 'ASC', 'ASC'),
                'max' => $airMax,
                'maxNumber' => $this->diveNumberFor('Airtemp', $airMax, 'DESC', 'DESC'),
                'avg' => $this->toFloatOrNull($aggregate['AverageAirTemp'] ?? null),
            ],
            classifications: $classifications,
            depthBuckets: $this->depthBuckets(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function aggregateRow(): array
    {
        $sql = sprintf(
            'SELECT COUNT(*) AS DiveCount, MIN(Divedate) AS FirstDive, MAX(Divedate) AS LastDive, ' .
            'SUM(Divetime) AS TotalBottomTime, MIN(Divetime) AS ShortestDiveTime, MAX(Divetime) AS LongestDiveTime, AVG(Divetime) AS AverageDiveTime, ' .
            'MIN(Depth) AS ShallowestDepth, MAX(Depth) AS DeepestDepth, AVG(Depth) AS AverageDepth ' .
            'FROM %sLogbook',
            $this->tablePrefix,
        );

        $row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
        $aggregate = is_array($row) ? $row : [];

        $water = $this->aggregateOptionalFloatColumn('Watertemp');
        $air = $this->aggregateOptionalFloatColumn('Airtemp');

        $aggregate['ColdestWaterTemp'] = $water['min'];
        $aggregate['WarmestWaterTemp'] = $water['max'];
        $aggregate['AverageWaterTemp'] = $water['avg'];
        $aggregate['ColdestAirTemp'] = $air['min'];
        $aggregate['WarmestAirTemp'] = $air['max'];
        $aggregate['AverageAirTemp'] = $air['avg'];

        return $aggregate;
    }

    /**
     * @return array{min:?float,max:?float,avg:?float}
     */
    private function aggregateOptionalFloatColumn(string $column): array
    {
        $sql = sprintf('SELECT MIN(%1$s) AS MinResult, MAX(%1$s) AS MaxResult, AVG(%1$s) AS AvgResult FROM %2$sLogbook', $column, $this->tablePrefix);

        try {
            $row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
            if (!is_array($row)) {
                return ['min' => null, 'max' => null, 'avg' => null];
            }

            return [
                'min' => $this->toFloatOrNull($row['MinResult'] ?? null),
                'max' => $this->toFloatOrNull($row['MaxResult'] ?? null),
                'avg' => $this->toFloatOrNull($row['AvgResult'] ?? null),
            ];
        } catch (\PDOException $exception) {
            if ($this->isMissingColumn($exception)) {
                return ['min' => null, 'max' => null, 'avg' => null];
            }

            throw $exception;
        }
    }

    /**
     * @return array{b0_18:int,b19_30:int,b31_40:int,b41_55:int,b55_plus:int}
     */
    private function depthBuckets(): array
    {
        return [
            'b0_18' => $this->countWhere('Depth <= :maxDepth', [':maxDepth' => 18.0]) ?? 0,
            'b19_30' => $this->countWhere('Depth > :minDepth AND Depth <= :maxDepth', [':minDepth' => 18.0, ':maxDepth' => 30.0]) ?? 0,
            'b31_40' => $this->countWhere('Depth > :minDepth AND Depth <= :maxDepth', [':minDepth' => 30.0, ':maxDepth' => 40.0]) ?? 0,
            'b41_55' => $this->countWhere('Depth > :minDepth AND Depth <= :maxDepth', [':minDepth' => 40.0, ':maxDepth' => 55.0]) ?? 0,
            'b55_plus' => $this->countWhere('Depth > :minDepth', [':minDepth' => 55.0]) ?? 0,
        ];
    }

    /**
     * @return array<string, int|null>
     */
    private function computeClassifications(int $total): array
    {
        $deco = $this->countWhere("Deco = 'True'");
        $rep = $this->countWhere("Rep = 'True'");

        return [
            'shore' => $this->countWhere('Entry = :entry', [':entry' => 1]),
            'boat' => $this->countWhere('Entry = :entry', [':entry' => 2]),
            'night' => $this->countDiveType(3),
            'drift' => $this->countDiveType(4),
            'deep' => $this->countDiveType(5),
            'cave' => $this->countDiveType(6),
            'wreck' => $this->countDiveType(7),
            'photo' => $this->countDiveType(8),
            'salt' => $this->countWhere('Water = :water', [':water' => 1]),
            'fresh' => $this->countWhere('Water = :water', [':water' => 2]),
            'brackish' => $this->countWhere('Water = :water', [':water' => 3]),
            'deco' => $deco,
            'nodeco' => $deco === null ? null : max(0, $total - $deco),
            'rep' => $rep,
            'norep' => $rep === null ? null : max(0, $total - $rep),
            'single' => $this->countWhere("(DblTank = 'False' OR DblTank = 'false')"),
            'twin' => $this->countWhere("DblTank = 'True'"),
            'oc' => $this->countWhere('SupplyType = :supply', [':supply' => 0]),
            'scr' => $this->countWhere('SupplyType = :supply', [':supply' => 1]),
            'ccr' => $this->countWhere('SupplyType = :supply', [':supply' => 2]),
        ];
    }

    private function countDiveType(int $code): ?int
    {
        return $this->countWhere(
            "(Divetype = :exact OR Divetype LIKE :prefix OR Divetype LIKE :middle OR Divetype LIKE :suffix)",
            [
                ':exact' => (string) $code,
                ':prefix' => $code . ',%',
                ':middle' => '%,' . $code . ',%',
                ':suffix' => '%,' . $code,
            ],
        );
    }

    /**
     * @param array<string, int|float|string> $params
     */
    private function countWhere(string $whereSql, array $params = []): ?int
    {
        $sql = sprintf('SELECT COUNT(*) AS Cnt FROM %sLogbook WHERE %s', $this->tablePrefix, $whereSql);

        try {
            $statement = $this->pdo->prepare($sql);
            foreach ($params as $key => $value) {
                $statement->bindValue($key, $value, $this->pdoType($value));
            }
            $statement->execute();
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            return is_array($row) ? (int) ($row['Cnt'] ?? 0) : 0;
        } catch (\PDOException $exception) {
            if ($this->isMissingColumn($exception)) {
                return null;
            }

            throw $exception;
        }
    }

    /**
     * @param int|float|string $value
     */
    private function pdoType(int|float|string $value): int
    {
        return match (true) {
            is_int($value) => PDO::PARAM_INT,
            default => PDO::PARAM_STR,
        };
    }

    private function isMissingColumn(\PDOException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;
        return $sqlState === '42S22' || ($sqlState === 'HY000' && str_contains(strtolower($exception->getMessage()), 'no such column'));
    }

    private function toDateTime(mixed $value): ?DateTimeImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        return new DateTimeImmutable((string) $value);
    }

    private function toIntOrNull(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function toFloatOrNull(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }

    private function diveNumberFor(string $column, mixed $value, string $dateDirection = 'ASC', string $numberDirection = 'ASC'): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $allowedDirections = ['ASC', 'DESC'];
        $safeDateDirection = in_array($dateDirection, $allowedDirections, true) ? $dateDirection : 'ASC';
        $safeNumberDirection = in_array($numberDirection, $allowedDirections, true) ? $numberDirection : 'ASC';

        $safeColumn = match ($column) {
            'Divedate', 'Divetime', 'Depth', 'Watertemp', 'Airtemp' => $column,
            default => null,
        };

        if ($safeColumn === null) {
            return null;
        }

        $sql = sprintf(
            'SELECT Number FROM %sLogbook WHERE %s = :value ORDER BY Divedate %s, Number %s LIMIT 1',
            $this->tablePrefix,
            $safeColumn,
            $safeDateDirection,
            $safeNumberDirection,
        );

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':value', $value, $this->pdoTypeForColumnValue($safeColumn, $value));
            $statement->execute();
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            return is_array($row) ? (int) ($row['Number'] ?? 0) : null;
        } catch (\PDOException $exception) {
            if ($this->isMissingColumn($exception)) {
                return null;
            }

            throw $exception;
        }
    }

    private function pdoTypeForColumnValue(string $column, mixed $value): int
    {
        if ($column === 'Divetime' && is_numeric((string) $value)) {
            return PDO::PARAM_INT;
        }

        if (($column === 'Depth' || $column === 'Watertemp' || $column === 'Airtemp') && is_numeric((string) $value)) {
            return PDO::PARAM_STR;
        }

        return PDO::PARAM_STR;
    }
}
