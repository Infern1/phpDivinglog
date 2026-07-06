<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use PhpDivingLog\Model\City;
use PDO;
use PhpDivingLog\Support\TextNormalizer;

final readonly class CityRepository
{
    public function __construct(private PDO $pdo, private string $tablePrefix)
    {
    }

    /**
     * @return list<City>
     */
    public function list(int $limit = 200): array
    {
        $sql = sprintf('SELECT * FROM %sCity ORDER BY City LIMIT :limit', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return array_map([$this, 'mapCity'], $statement->fetchAll());
    }

    public function findById(int $id): ?City
    {
        $row = $this->queryByIdColumn('CityID', $id);
        if (!is_array($row)) {
            $row = $this->queryByIdColumn('ID', $id);
        }

        return is_array($row) ? $this->mapCity($row) : null;
    }

    /**
     * @return array<string, mixed>|false
     */
    private function queryByIdColumn(string $column, int $id): array|false
    {
        $sql = sprintf('SELECT * FROM %sCity WHERE %s = :id', $this->tablePrefix, $column);

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
    private function mapCity(array $row): City
    {
        return new City(
            (int) ($row['CityID'] ?? $row['ID'] ?? 0),
            (int) ($row['CountryID'] ?? 0),
            TextNormalizer::normalizeLikelyMojibake((string) ($row['City'] ?? '')),
            isset($row['CityComment'])
                ? TextNormalizer::normalizeLikelyMojibake((string) $row['CityComment'])
                : (isset($row['Comments']) ? TextNormalizer::normalizeLikelyMojibake((string) $row['Comments']) : null)
        );
    }
}
