<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use PhpDivingLog\Model\DiveSite;
use PDO;

final readonly class DiveSiteRepository
{
    public function __construct(private PDO $pdo, private string $tablePrefix)
    {
    }

    /**
     * @return list<DiveSite>
     */
    public function list(int $limit = 200): array
    {
        $sql = sprintf('SELECT PlaceID, Place, CountryID, CityID, Latitude, Longitude, PlaceMap, PlaceComment FROM %sPlace ORDER BY Place LIMIT :limit', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return array_map([$this, 'mapSite'], $statement->fetchAll());
    }

    public function findById(int $id): ?DiveSite
    {
        $sql = sprintf('SELECT PlaceID, Place, CountryID, CityID, Latitude, Longitude, PlaceMap, PlaceComment FROM %sPlace WHERE PlaceID = :id', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch();

        return is_array($row) ? $this->mapSite($row) : null;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function mapSite(array $row): DiveSite
    {
        return new DiveSite(
            (int) ($row['PlaceID'] ?? 0),
            (string) ($row['Place'] ?? ''),
            isset($row['CountryID']) ? (int) $row['CountryID'] : null,
            isset($row['CityID']) ? (int) $row['CityID'] : null,
            isset($row['Latitude']) ? (float) $row['Latitude'] : null,
            isset($row['Longitude']) ? (float) $row['Longitude'] : null,
            isset($row['PlaceMap']) ? (string) $row['PlaceMap'] : null,
            isset($row['PlaceComment']) ? (string) $row['PlaceComment'] : null
        );
    }
}
