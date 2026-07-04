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
        $sql = sprintf('SELECT TripID, TripName, DateFrom, DateTo, CountryID, ShopID, TripComment FROM %sTrip ORDER BY DateFrom DESC LIMIT :limit', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return array_map([$this, 'mapTrip'], $statement->fetchAll());
    }

    public function findById(int $id): ?Trip
    {
        $sql = sprintf('SELECT TripID, TripName, DateFrom, DateTo, CountryID, ShopID, TripComment FROM %sTrip WHERE TripID = :id', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch();

        return is_array($row) ? $this->mapTrip($row) : null;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function mapTrip(array $row): Trip
    {
        return new Trip(
            (int) ($row['TripID'] ?? 0),
            (string) ($row['TripName'] ?? ''),
            isset($row['DateFrom']) && $row['DateFrom'] !== null ? new DateTimeImmutable((string) $row['DateFrom']) : null,
            isset($row['DateTo']) && $row['DateTo'] !== null ? new DateTimeImmutable((string) $row['DateTo']) : null,
            isset($row['CountryID']) ? (int) $row['CountryID'] : null,
            isset($row['ShopID']) ? (int) $row['ShopID'] : null,
            isset($row['TripComment']) ? (string) $row['TripComment'] : null
        );
    }
}
