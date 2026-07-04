<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use PhpDivingLog\Model\Country;
use PDO;

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
            (string) ($row['Country'] ?? ''),
            isset($row['FlagImage']) ? (string) $row['FlagImage'] : (isset($row['FlagPath']) ? (string) $row['FlagPath'] : null)
        );
    }
}
