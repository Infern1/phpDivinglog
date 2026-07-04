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
        $sql = sprintf('SELECT * FROM %sPlace ORDER BY Place LIMIT :limit', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return array_map([$this, 'mapSite'], $statement->fetchAll());
    }

    public function findById(int $id): ?DiveSite
    {
        $row = $this->queryByIdColumn('PlaceID', $id);
        if (!is_array($row)) {
            $row = $this->queryByIdColumn('ID', $id);
        }

        return is_array($row) ? $this->mapSite($row) : null;
    }

    /**
     * @return array<string, mixed>|false
     */
    private function queryByIdColumn(string $column, int $id): array|false
    {
        $sql = sprintf('SELECT * FROM %sPlace WHERE %s = :id', $this->tablePrefix, $column);

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
    private function mapSite(array $row): DiveSite
    {
        return new DiveSite(
            (int) ($row['PlaceID'] ?? $row['ID'] ?? 0),
            (string) ($row['Place'] ?? ''),
            isset($row['CountryID']) ? (int) $row['CountryID'] : null,
            isset($row['CityID']) ? (int) $row['CityID'] : null,
            isset($row['Latitude']) ? (float) $row['Latitude'] : (isset($row['Lat']) ? (float) $row['Lat'] : null),
            isset($row['Longitude']) ? (float) $row['Longitude'] : (isset($row['Lon']) ? (float) $row['Lon'] : null),
            isset($row['PlaceMap']) ? (string) $row['PlaceMap'] : (isset($row['MapPath']) ? (string) $row['MapPath'] : null),
            isset($row['PlaceComment']) ? (string) $row['PlaceComment'] : (isset($row['Comments']) ? (string) $row['Comments'] : null)
        );
    }
}
