<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use DateTimeImmutable;
use PhpDivingLog\Model\Dive;
use PDO;

final readonly class DiveRepository
{
    public function __construct(private PDO $pdo, private string $tablePrefix)
    {
    }

    public function findByNumber(int $number): ?Dive
    {
        $sql = sprintf('SELECT * FROM %sLogbook WHERE Number = :number', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':number', $number, PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch();

        if (!is_array($row)) {
            return null;
        }

        $dateTime = $this->mapDateTime($row);

        return new Dive(
            (int) ($row['Number'] ?? 0),
            (int) ($row['LogID'] ?? $row['ID'] ?? $row['Number'] ?? 0),
            (int) ($row['PlaceID'] ?? 0),
            $dateTime,
            (float) ($row['Depth'] ?? 0.0),
            (int) ($row['Divetime'] ?? 0),
            isset($row['Watertemp']) ? (float) $row['Watertemp'] : null,
            isset($row['Airtemp']) ? (float) $row['Airtemp'] : null,
            isset($row['Weight']) ? (float) $row['Weight'] : null,
            isset($row['PresS']) ? (float) $row['PresS'] : (isset($row['Pstart']) ? (float) $row['Pstart'] : null),
            isset($row['PresE']) ? (float) $row['PresE'] : (isset($row['Pend']) ? (float) $row['Pend'] : null),
            isset($row['Comments']) ? (string) $row['Comments'] : (isset($row['Comment']) ? (string) $row['Comment'] : null),
            $this->parseBuddyIds(isset($row['BuddyIDs']) ? (string) $row['BuddyIDs'] : ''),
            [
                'profile' => isset($row['Profile']) ? (string) $row['Profile'] : null,
                'profile_interval_seconds' => isset($row['ProfileInt']) ? (int) $row['ProfileInt'] : null,
                'shop_id' => isset($row['ShopID']) ? (int) $row['ShopID'] : null,
                'trip_id' => isset($row['TripID']) ? (int) $row['TripID'] : null,
                'place_name' => isset($row['Place']) ? (string) $row['Place'] : null,
                'city_name' => isset($row['City']) ? (string) $row['City'] : null,
                'country_name' => isset($row['Country']) ? (string) $row['Country'] : null,
            ]
        );
    }

    /**
     * @return list<int>
     */
    public function listNumbers(int $limit, int $offset): array
    {
        $sql = sprintf('SELECT Number FROM %sLogbook ORDER BY Number DESC LIMIT :limit OFFSET :offset', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return array_map(static fn (array $row): int => (int) $row['Number'], $statement->fetchAll());
    }

    /**
     * @return list<array{number:int,date_time:DateTimeImmutable,depth:float,duration:int,location:string}>
     */
    public function listOverview(int $limit, int $offset): array
    {
        $sql = sprintf('SELECT * FROM %sLogbook ORDER BY Number DESC LIMIT :limit OFFSET :offset', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return array_map(function (array $row): array {
            $locationParts = array_values(array_filter([
                isset($row['Place']) ? trim((string) $row['Place']) : '',
                isset($row['City']) ? trim((string) $row['City']) : '',
                isset($row['Country']) ? trim((string) $row['Country']) : '',
            ], static fn (string $value): bool => $value !== ''));

            return [
                'number' => (int) ($row['Number'] ?? 0),
                'date_time' => $this->mapDateTime($row),
                'depth' => (float) ($row['Depth'] ?? 0.0),
                'duration' => (int) ($row['Divetime'] ?? 0),
                'location' => $locationParts !== [] ? implode(', ', $locationParts) : '-',
            ];
        }, $statement->fetchAll());
    }

    public function countAll(): int
    {
        $sql = sprintf('SELECT COUNT(*) AS DiveCount FROM %sLogbook', $this->tablePrefix);
        $row = $this->pdo->query($sql)->fetch();

        return is_array($row) ? (int) ($row['DiveCount'] ?? 0) : 0;
    }

    /**
     * @return list<Dive>
     */
    public function listByPlace(int $placeId, int $limit = 200): array
    {
        $sql = sprintf('SELECT * FROM %sLogbook WHERE PlaceID = :placeId ORDER BY Number DESC LIMIT :limit', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':placeId', $placeId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return array_map(function (array $row): Dive {
            $dateTime = $this->mapDateTime($row);

            return new Dive(
                (int) ($row['Number'] ?? 0),
                (int) ($row['LogID'] ?? $row['ID'] ?? $row['Number'] ?? 0),
                (int) ($row['PlaceID'] ?? 0),
                $dateTime,
                (float) ($row['Depth'] ?? 0.0),
                (int) ($row['Divetime'] ?? 0),
                isset($row['Watertemp']) ? (float) $row['Watertemp'] : null,
                isset($row['Airtemp']) ? (float) $row['Airtemp'] : null,
                isset($row['Weight']) ? (float) $row['Weight'] : null,
                isset($row['PresS']) ? (float) $row['PresS'] : (isset($row['Pstart']) ? (float) $row['Pstart'] : null),
                isset($row['PresE']) ? (float) $row['PresE'] : (isset($row['Pend']) ? (float) $row['Pend'] : null),
                isset($row['Comments']) ? (string) $row['Comments'] : (isset($row['Comment']) ? (string) $row['Comment'] : null),
                $this->parseBuddyIds(isset($row['BuddyIDs']) ? (string) $row['BuddyIDs'] : ''),
                [
                    'profile' => isset($row['Profile']) ? (string) $row['Profile'] : null,
                    'profile_interval_seconds' => isset($row['ProfileInt']) ? (int) $row['ProfileInt'] : null,
                    'shop_id' => isset($row['ShopID']) ? (int) $row['ShopID'] : null,
                    'trip_id' => isset($row['TripID']) ? (int) $row['TripID'] : null,
                    'place_name' => isset($row['Place']) ? (string) $row['Place'] : null,
                    'city_name' => isset($row['City']) ? (string) $row['City'] : null,
                    'country_name' => isset($row['Country']) ? (string) $row['Country'] : null,
                ]
            );
        }, $statement->fetchAll());
    }

    /**
     * @param array<string, mixed> $row
     */
    private function mapDateTime(array $row): DateTimeImmutable
    {
        $dateValue = (string) ($row['Divedate'] ?? 'now');
        $timeValue = (string) ($row['Entrytime'] ?? $row['Divetime'] ?? '00:00:00');

        if (!str_contains($timeValue, ':')) {
            $timeValue = '00:00:00';
        }

        return new DateTimeImmutable(trim($dateValue . ' ' . $timeValue));
    }

    /**
     * @return list<int>
     */
    private function parseBuddyIds(string $raw): array
    {
        if ($raw === '') {
            return [];
        }

        $parts = preg_split('/[^0-9]+/', $raw) ?: [];
        $ids = array_map(
            static fn (string $part): int => (int) $part,
            array_filter($parts, static fn (string $part): bool => $part !== '')
        );

        return array_values(array_filter($ids, static fn (int $id): bool => $id > 0));
    }
}
