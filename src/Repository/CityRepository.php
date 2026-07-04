<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use PhpDivingLog\Model\City;
use PDO;

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
        $sql = sprintf('SELECT CityID, CountryID, City, CityComment FROM %sCity ORDER BY City LIMIT :limit', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return array_map([$this, 'mapCity'], $statement->fetchAll());
    }

    public function findById(int $id): ?City
    {
        $sql = sprintf('SELECT CityID, CountryID, City, CityComment FROM %sCity WHERE CityID = :id', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch();

        return is_array($row) ? $this->mapCity($row) : null;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function mapCity(array $row): City
    {
        return new City(
            (int) ($row['CityID'] ?? 0),
            (int) ($row['CountryID'] ?? 0),
            (string) ($row['City'] ?? ''),
            isset($row['CityComment']) ? (string) $row['CityComment'] : null
        );
    }
}
