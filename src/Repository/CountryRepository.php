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
        $sql = sprintf('SELECT CountryID, Country, FlagImage FROM %sCountry ORDER BY Country LIMIT :limit', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return array_map([$this, 'mapCountry'], $statement->fetchAll());
    }

    public function findById(int $id): ?Country
    {
        $sql = sprintf('SELECT CountryID, Country, FlagImage FROM %sCountry WHERE CountryID = :id', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch();

        return is_array($row) ? $this->mapCountry($row) : null;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function mapCountry(array $row): Country
    {
        return new Country(
            (int) ($row['CountryID'] ?? 0),
            (string) ($row['Country'] ?? ''),
            isset($row['FlagImage']) ? (string) $row['FlagImage'] : null
        );
    }
}
