<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use PhpDivingLog\Model\Country;
use PDO;
use PhpDivingLog\Support\TextNormalizer;

final readonly class CountryRepository
{
    public function __construct(private PDO $pdo, private string $tablePrefix)
    {
    }

    /**
     * @return list<Country>
     */
    public function list(int $limit = 200): array
    {
        $sql = sprintf('SELECT * FROM %sCountry ORDER BY Country LIMIT :limit', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return array_map([$this, 'mapCountry'], $statement->fetchAll());
    }

    /**
     * @return list<array{country:Country,diveCount:int}>
     */
    public function listWithDiveCounts(int $limit = 500): array
    {
        $rows = $this->queryDirectCountryCounts('CountryID', $limit)
            ?? $this->queryDirectCountryCounts('ID', $limit)
            ?? $this->queryPlaceBasedCountryCounts('CountryID', 'PlaceID', $limit)
            ?? $this->queryPlaceBasedCountryCounts('CountryID', 'ID', $limit)
            ?? $this->queryPlaceBasedCountryCounts('ID', 'PlaceID', $limit)
            ?? $this->queryPlaceBasedCountryCounts('ID', 'ID', $limit);

        if ($rows === null) {
            return array_map(
                static fn (Country $country): array => ['country' => $country, 'diveCount' => 0],
                $this->list($limit),
            );
        }

        return $this->mapCountryCountRows($rows);
    }

    /**
     * @return list<array<string, mixed>>|null
     */
    private function queryDirectCountryCounts(string $countryColumn, int $limit): ?array
    {
        $sql = sprintf(
            'SELECT c.*, COUNT(l.Number) AS DiveCount FROM %1$sCountry c LEFT JOIN %1$sLogbook l ON l.CountryID = c.%2$s GROUP BY c.%2$s ORDER BY c.Country LIMIT :limit',
            $this->tablePrefix,
            $countryColumn,
        );

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetchAll();
        } catch (\PDOException $exception) {
            if ($this->isMissingColumn($exception)) {
                return null;
            }

            throw $exception;
        }
    }

    /**
     * @return list<array<string, mixed>>|null
     */
    private function queryPlaceBasedCountryCounts(string $countryColumn, string $placeColumn, int $limit): ?array
    {
        $sql = sprintf(
            'SELECT c.*, COUNT(l.Number) AS DiveCount ' .
            'FROM %1$sCountry c ' .
            'LEFT JOIN %1$sPlace p ON p.CountryID = c.%2$s ' .
            'LEFT JOIN %1$sLogbook l ON l.PlaceID = p.%3$s ' .
            'GROUP BY c.%2$s ORDER BY c.Country LIMIT :limit',
            $this->tablePrefix,
            $countryColumn,
            $placeColumn,
        );

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetchAll();
        } catch (\PDOException $exception) {
            if ($this->isMissingColumn($exception)) {
                return null;
            }

            throw $exception;
        }
    }

    public function findById(int $id): ?Country
    {
        $row = $this->queryByIdColumn('CountryID', $id);
        if (!is_array($row)) {
            $row = $this->queryByIdColumn('ID', $id);
        }

        return is_array($row) ? $this->mapCountry($row) : null;
    }

    /**
     * @return array<string, mixed>|false
     */
    private function queryByIdColumn(string $column, int $id): array|false
    {
        $sql = sprintf('SELECT * FROM %sCountry WHERE %s = :id', $this->tablePrefix, $column);

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
    private function mapCountry(array $row): Country
    {
        return new Country(
            (int) ($row['CountryID'] ?? $row['ID'] ?? 0),
            TextNormalizer::normalizeLikelyMojibake((string) ($row['Country'] ?? '')),
            isset($row['FlagImage']) ? (string) $row['FlagImage'] : (isset($row['FlagPath']) ? (string) $row['FlagPath'] : null)
        );
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<array{country:Country,diveCount:int}>
     */
    private function mapCountryCountRows(array $rows): array
    {
        return array_map(function (array $row): array {
            return [
                'country' => $this->mapCountry($row),
                'diveCount' => (int) ($row['DiveCount'] ?? 0),
            ];
        }, $rows);
    }

    private function isMissingColumn(\PDOException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;
        return $sqlState === '42S22' || ($sqlState === 'HY000' && str_contains(strtolower($exception->getMessage()), 'no such column'));
    }
}
