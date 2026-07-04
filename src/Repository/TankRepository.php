<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use PhpDivingLog\Model\Tank;
use PDO;

final readonly class TankRepository
{
    public function __construct(private PDO $pdo, private string $tablePrefix)
    {
    }

    /**
     * @return list<Tank>
     */
    public function findByDiveNumber(int $diveNumber, ?int $logId = null): array
    {
        $rows = $this->queryByColumn('Number', $diveNumber);
        if ($rows === null || $rows === []) {
            if ($logId !== null && $logId > 0) {
                $rows = $this->queryByColumn('LogID', $logId) ?? [];
            } else {
                $rows = [];
            }
        }

        return array_map(
            static fn (array $row): Tank => new Tank(
                (int) ($row['TankID'] ?? $row['ID'] ?? 0),
                (int) ($row['Number'] ?? $row['LogID'] ?? 0),
                isset($row['Volume']) ? (float) $row['Volume'] : (isset($row['Tanksize']) ? (float) $row['Tanksize'] : null),
                isset($row['Pstart']) ? (float) $row['Pstart'] : (isset($row['PresS']) ? (float) $row['PresS'] : null),
                isset($row['Pend']) ? (float) $row['Pend'] : (isset($row['PresE']) ? (float) $row['PresE'] : null),
                isset($row['O2']) ? (float) $row['O2'] : null,
            ),
            $rows
        );
    }

    /**
     * @return list<array<string, mixed>>|null
     */
    private function queryByColumn(string $column, int $diveNumber): ?array
    {
        $sql = sprintf('SELECT * FROM %sTank WHERE %s = :number', $this->tablePrefix, $column);

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':number', $diveNumber, PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetchAll();
        } catch (\PDOException $exception) {
            $sqlState = $exception->errorInfo[0] ?? null;
            if ($sqlState === '42S22' || ($sqlState === 'HY000' && str_contains(strtolower($exception->getMessage()), 'no such column'))) {
                return null;
            }

            throw $exception;
        }
    }
}
