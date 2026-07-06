<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use DateTimeImmutable;
use PhpDivingLog\Model\Equipment;
use PDO;

final readonly class EquipmentRepository
{
    public function __construct(private PDO $pdo, private string $tablePrefix)
    {
    }

    /**
     * @return list<Equipment>
     */
    public function list(int $limit = 200): array
    {
        $sql = sprintf('SELECT * FROM %sEquipment ORDER BY Object LIMIT :limit', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return array_map([$this, 'mapEquipment'], $statement->fetchAll());
    }

    /**
     * @return list<array{item:Equipment,diveCount:?int}>
     */
    public function listWithDiveCounts(int $limit = 500): array
    {
        $sql = sprintf(
            'SELECT e.*, COUNT(l.Number) AS DiveCount FROM %1$sEquipment e LEFT JOIN %1$sLogbook l ON l.EquipmentID = e.EquipmentID GROUP BY e.EquipmentID ORDER BY e.Object LIMIT :limit',
            $this->tablePrefix,
        );

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
            $statement->execute();
            return array_map(function (array $row): array {
                return [
                    'item' => $this->mapEquipment($row),
                    'diveCount' => (int) ($row['DiveCount'] ?? 0),
                ];
            }, $statement->fetchAll());
        } catch (\PDOException $exception) {
            $sqlState = $exception->errorInfo[0] ?? null;
            if ($sqlState === '42S22' || ($sqlState === 'HY000' && str_contains(strtolower($exception->getMessage()), 'no such column'))) {
                return array_map(
                    static fn (Equipment $item): array => ['item' => $item, 'diveCount' => null],
                    $this->list($limit),
                );
            }

            throw $exception;
        }
    }

    public function findById(int $id): ?Equipment
    {
        $row = $this->queryByIdColumn('EquipmentID', $id);
        if (!is_array($row)) {
            $row = $this->queryByIdColumn('ID', $id);
        }

        return is_array($row) ? $this->mapEquipment($row) : null;
    }

    /**
     * @return array<string, mixed>|false
     */
    private function queryByIdColumn(string $column, int $id): array|false
    {
        $sql = sprintf('SELECT * FROM %sEquipment WHERE %s = :id', $this->tablePrefix, $column);

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
    private function mapEquipment(array $row): Equipment
    {
        return new Equipment(
            (int) ($row['EquipmentID'] ?? $row['ID'] ?? 0),
            (string) ($row['Object'] ?? ''),
            isset($row['Manufacturer']) ? (string) $row['Manufacturer'] : null,
            isset($row['DatePurchase']) && $row['DatePurchase'] !== null
                ? new DateTimeImmutable((string) $row['DatePurchase'])
                : (isset($row['DateP']) && $row['DateP'] !== null ? new DateTimeImmutable((string) $row['DateP']) : null),
            isset($row['DateService']) && $row['DateService'] !== null
                ? new DateTimeImmutable((string) $row['DateService'])
                : (isset($row['DateR']) && $row['DateR'] !== null ? new DateTimeImmutable((string) $row['DateR']) : null),
            isset($row['DateServiceWarning']) && $row['DateServiceWarning'] !== null
                ? new DateTimeImmutable((string) $row['DateServiceWarning'])
                : (isset($row['DateRN']) && $row['DateRN'] !== null ? new DateTimeImmutable((string) $row['DateRN']) : null),
            isset($row['Comment']) ? (string) $row['Comment'] : (isset($row['Comments']) ? (string) $row['Comments'] : null),
            isset($row['Picture']) ? (string) $row['Picture'] : (isset($row['PhotoPath']) ? (string) $row['PhotoPath'] : null)
        );
    }
}
