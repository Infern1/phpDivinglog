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
        $sql = sprintf('SELECT Number, LogID, PlaceID, ShopID, TripID, Divedate, Divetime, Depth, Profile, ProfileInt FROM %sLogbook WHERE Number = :number', $this->tablePrefix);
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
            (int) ($row['LogID'] ?? 0),
            (int) ($row['PlaceID'] ?? 0),
            $dateTime,
            (float) ($row['Depth'] ?? 0.0),
            (int) ($row['Divetime'] ?? 0),
            null,
            null,
            null,
            null,
            null,
            null,
            [],
            [
                'profile' => isset($row['Profile']) ? (string) $row['Profile'] : null,
                'profile_interval_seconds' => isset($row['ProfileInt']) ? (int) $row['ProfileInt'] : null,
                'shop_id' => isset($row['ShopID']) ? (int) $row['ShopID'] : null,
                'trip_id' => isset($row['TripID']) ? (int) $row['TripID'] : null,
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
     * @return list<Dive>
     */
    public function listByPlace(int $placeId, int $limit = 200): array
    {
        $sql = sprintf('SELECT Number, LogID, PlaceID, ShopID, TripID, Divedate, Divetime, Depth, Profile, ProfileInt FROM %sLogbook WHERE PlaceID = :placeId ORDER BY Number DESC LIMIT :limit', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':placeId', $placeId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return array_map(function (array $row): Dive {
            $dateTime = $this->mapDateTime($row);

            return new Dive(
                (int) ($row['Number'] ?? 0),
                (int) ($row['LogID'] ?? 0),
                (int) ($row['PlaceID'] ?? 0),
                $dateTime,
                (float) ($row['Depth'] ?? 0.0),
                (int) ($row['Divetime'] ?? 0),
                null,
                null,
                null,
                null,
                null,
                null,
                [],
                [
                    'profile' => isset($row['Profile']) ? (string) $row['Profile'] : null,
                    'profile_interval_seconds' => isset($row['ProfileInt']) ? (int) $row['ProfileInt'] : null,
                    'shop_id' => isset($row['ShopID']) ? (int) $row['ShopID'] : null,
                    'trip_id' => isset($row['TripID']) ? (int) $row['TripID'] : null,
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
        $timeValue = (string) ($row['Divetime'] ?? '00:00:00');

        if (!str_contains($timeValue, ':')) {
            $timeValue = '00:00:00';
        }

        return new DateTimeImmutable(trim($dateValue . ' ' . $timeValue));
    }
}
