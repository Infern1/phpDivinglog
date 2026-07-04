<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use DateTimeImmutable;
use PhpDivingLog\Model\Trip;
use PDO;

final readonly class TripRepository
{
    public function __construct(private PDO $pdo, private string $tablePrefix)
    {
    }

    /**
     * @return list<Trip>
     */
    public function list(int $limit = 200): array
    {
        $rows = $this->queryListOrderBy('StartDate', $limit);
        if ($rows === null) {
            $rows = $this->queryListOrderBy('DateFrom', $limit) ?? $this->queryListOrderBy('ID', $limit) ?? [];
        }

        return array_map([$this, 'mapTrip'], $rows);
    }

    /**
     * @return list<array<string, mixed>>|null
     */
    private function queryListOrderBy(string $column, int $limit): ?array
    {
        $sql = sprintf('SELECT * FROM %sTrip ORDER BY %s DESC LIMIT :limit', $this->tablePrefix, $column);

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
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

    public function findById(int $id): ?Trip
    {
        $row = $this->queryByIdColumn('TripID', $id);
        if (!is_array($row)) {
            $row = $this->queryByIdColumn('ID', $id);
        }

        return is_array($row) ? $this->mapTrip($row) : null;
    }

    /**
     * @return array<string, mixed>|false
     */
    private function queryByIdColumn(string $column, int $id): array|false
    {
        $sql = sprintf('SELECT * FROM %sTrip WHERE %s = :id', $this->tablePrefix, $column);

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetch();
        } catch (\PDOException $exception) {
            $sqlState = $exception->errorInfo[0] ?? null;
            if ($sqlState === '42S22' || ($sqlState === 'HY000' && str_contains(strtolower($exception->getMessage()), 'no such column'))) {
                return false;
            }

            throw $exception;
        }
    }

    /**
     * @param array<string, mixed> $row
     */
    private function mapTrip(array $row): Trip
    {
        return new Trip(
            (int) ($row['TripID'] ?? $row['ID'] ?? 0),
            (string) ($row['TripName'] ?? ''),
            isset($row['DateFrom']) && $row['DateFrom'] !== null
                ? new DateTimeImmutable((string) $row['DateFrom'])
                : (isset($row['StartDate']) && $row['StartDate'] !== null ? new DateTimeImmutable((string) $row['StartDate']) : null),
            isset($row['DateTo']) && $row['DateTo'] !== null
                ? new DateTimeImmutable((string) $row['DateTo'])
                : (isset($row['EndDate']) && $row['EndDate'] !== null ? new DateTimeImmutable((string) $row['EndDate']) : null),
            isset($row['CountryID']) ? (int) $row['CountryID'] : null,
            isset($row['ShopID']) ? (int) $row['ShopID'] : null,
            isset($row['TripComment']) ? (string) $row['TripComment'] : (isset($row['Comments']) ? (string) $row['Comments'] : null)
        );
    }
}
