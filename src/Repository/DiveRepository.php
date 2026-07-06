<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use DateTimeImmutable;
use PhpDivingLog\Model\Dive;
use PDO;
use PhpDivingLog\Support\TextNormalizer;

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
                'visibility' => $this->firstNonEmptyString($row, ['Visibility', 'Visib', 'Sicht', 'Viz']),
                'weather' => $this->firstNonEmptyString($row, ['Weather', 'Condition', 'Conditions']),
                'sac' => $this->firstNumeric($row, ['SAC', 'Sac', 'RMV', 'Rmv']),
                'tank_size' => $this->firstNumeric($row, ['Tanksize', 'TankSize', 'Volume']),
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
    public function listOverview(int $limit, int $offset, string $search = '', string $sort = 'newest'): array
    {
        $search = trim($search);
        $where = '';
        if ($search !== '') {
            $where = "WHERE (Number LIKE :search OR COALESCE(Place, '') LIKE :search OR COALESCE(City, '') LIKE :search OR COALESCE(Country, '') LIKE :search)";
        }

        $orderBy = match ($sort) {
            'oldest' => 'ORDER BY Number ASC',
            'deepest' => 'ORDER BY Depth DESC, Number DESC',
            'longest' => 'ORDER BY Divetime DESC, Number DESC',
            default => 'ORDER BY Number DESC',
        };

        $sql = sprintf('SELECT * FROM %sLogbook %s %s LIMIT :limit OFFSET :offset', $this->tablePrefix, $where, $orderBy);
        $statement = $this->pdo->prepare($sql);
        if ($search !== '') {
            $statement->bindValue(':search', '%' . $search . '%');
        }
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return array_map([$this, 'mapOverviewRow'], $statement->fetchAll());
    }

    /**
     * @return list<array{number:int,date_time:DateTimeImmutable,depth:float,duration:int,location:string}>
     */
    public function listOverviewByPlace(int $placeId, int $limit = 500): array
    {
        $sql = sprintf('SELECT * FROM %sLogbook WHERE PlaceID = :id ORDER BY Number DESC LIMIT :limit', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $placeId, PDO::PARAM_INT);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return array_map([$this, 'mapOverviewRow'], $statement->fetchAll());
    }

    /**
     * @return list<array{number:int,date_time:DateTimeImmutable,depth:float,duration:int,location:string}>
     */
    public function listOverviewByTrip(int $tripId, int $limit = 500): array
    {
        $sql = sprintf('SELECT * FROM %sLogbook WHERE TripID = :id ORDER BY Number DESC LIMIT :limit', $this->tablePrefix);

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':id', $tripId, PDO::PARAM_INT);
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
            $statement->execute();
            return array_map([$this, 'mapOverviewRow'], $statement->fetchAll());
        } catch (\PDOException $exception) {
            if ($this->isMissingColumn($exception)) {
                return [];
            }

            throw $exception;
        }
    }

    /**
     * @return list<array{number:int,date_time:DateTimeImmutable,depth:float,duration:int,location:string}>
     */
    public function listOverviewByCountry(int $countryId, int $limit = 500): array
    {
        $directSql = sprintf('SELECT * FROM %sLogbook WHERE CountryID = :id ORDER BY Number DESC LIMIT :limit', $this->tablePrefix);

        try {
            $statement = $this->pdo->prepare($directSql);
            $statement->bindValue(':id', $countryId, PDO::PARAM_INT);
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
            $statement->execute();
            return array_map([$this, 'mapOverviewRow'], $statement->fetchAll());
        } catch (\PDOException $exception) {
            if (!$this->isMissingColumn($exception)) {
                throw $exception;
            }
        }

        $joinSql = sprintf(
            'SELECT l.* FROM %1$sLogbook l INNER JOIN %1$sPlace p ON p.PlaceID = l.PlaceID WHERE p.CountryID = :id ORDER BY l.Number DESC LIMIT :limit',
            $this->tablePrefix
        );

        try {
            $statement = $this->pdo->prepare($joinSql);
            $statement->bindValue(':id', $countryId, PDO::PARAM_INT);
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
            $statement->execute();
            return array_map([$this, 'mapOverviewRow'], $statement->fetchAll());
        } catch (\PDOException $exception) {
            if ($this->isMissingColumn($exception)) {
                return [];
            }

            throw $exception;
        }
    }

    /**
     * @return list<array{number:int,date_time:DateTimeImmutable,depth:float,duration:int,location:string}>|null
     */
    public function listOverviewByEquipment(int $equipmentId, int $limit = 500): ?array
    {
        $sql = sprintf('SELECT * FROM %sLogbook WHERE EquipmentID = :id ORDER BY Number DESC LIMIT :limit', $this->tablePrefix);

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':id', $equipmentId, PDO::PARAM_INT);
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
            $statement->execute();
            return array_map([$this, 'mapOverviewRow'], $statement->fetchAll());
        } catch (\PDOException $exception) {
            if ($this->isMissingColumn($exception)) {
                return null;
            }

            throw $exception;
        }
    }

    public function countAll(string $search = ''): int
    {
        $search = trim($search);
        $where = '';
        if ($search !== '') {
            $where = "WHERE (Number LIKE :search OR COALESCE(Place, '') LIKE :search OR COALESCE(City, '') LIKE :search OR COALESCE(Country, '') LIKE :search)";
        }

        $sql = sprintf('SELECT COUNT(*) AS DiveCount FROM %sLogbook %s', $this->tablePrefix, $where);
        $statement = $this->pdo->prepare($sql);
        if ($search !== '') {
            $statement->bindValue(':search', '%' . $search . '%');
        }
        $statement->execute();
        $row = $statement->fetch();

        return is_array($row) ? (int) ($row['DiveCount'] ?? 0) : 0;
    }

    public function findPreviousNumber(int $number): ?int
    {
        $sql = sprintf('SELECT Number FROM %sLogbook WHERE Number < :number ORDER BY Number DESC LIMIT 1', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':number', $number, PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch();

        return is_array($row) ? (int) ($row['Number'] ?? 0) : null;
    }

    public function findNextNumber(int $number): ?int
    {
        $sql = sprintf('SELECT Number FROM %sLogbook WHERE Number > :number ORDER BY Number ASC LIMIT 1', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':number', $number, PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch();

        return is_array($row) ? (int) ($row['Number'] ?? 0) : null;
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
                    'visibility' => $this->firstNonEmptyString($row, ['Visibility', 'Visib', 'Sicht', 'Viz']),
                    'weather' => $this->firstNonEmptyString($row, ['Weather', 'Condition', 'Conditions']),
                    'sac' => $this->firstNumeric($row, ['SAC', 'Sac', 'RMV', 'Rmv']),
                    'tank_size' => $this->firstNumeric($row, ['Tanksize', 'TankSize', 'Volume']),
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
     * @param array<string, mixed> $row
     * @param list<string> $keys
     */
    private function firstNonEmptyString(array $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $row)) {
                continue;
            }

            $value = trim((string) $row[$key]);
            if ($value !== '') {
                return TextNormalizer::normalizeLikelyMojibake($value);
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $row
     * @param list<string> $keys
     */
    private function firstNumeric(array $row, array $keys): ?float
    {
        foreach ($keys as $key) {
            if (!array_key_exists($key, $row)) {
                continue;
            }

            $raw = trim((string) $row[$key]);
            if ($raw === '' || !is_numeric($raw)) {
                continue;
            }

            return (float) $raw;
        }

        return null;
    }

    /**
     * @param array<string, mixed> $row
     * @return array{number:int,date_time:DateTimeImmutable,depth:float,duration:int,location:string}
     */
    private function mapOverviewRow(array $row): array
    {
        $locationParts = array_values(array_filter([
            isset($row['Place']) ? trim(TextNormalizer::normalizeLikelyMojibake((string) $row['Place'])) : '',
            isset($row['City']) ? trim(TextNormalizer::normalizeLikelyMojibake((string) $row['City'])) : '',
            isset($row['Country']) ? trim(TextNormalizer::normalizeLikelyMojibake((string) $row['Country'])) : '',
        ], static fn (string $value): bool => $value !== ''));

        return [
            'number' => (int) ($row['Number'] ?? 0),
            'date_time' => $this->mapDateTime($row),
            'depth' => (float) ($row['Depth'] ?? 0.0),
            'duration' => (int) ($row['Divetime'] ?? 0),
            'location' => $locationParts !== [] ? implode(', ', $locationParts) : '-',
        ];
    }

    private function isMissingColumn(\PDOException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;
        return $sqlState === '42S22' || ($sqlState === 'HY000' && str_contains(strtolower($exception->getMessage()), 'no such column'));
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
