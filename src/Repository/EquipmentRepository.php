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
        $sql = sprintf('SELECT EquipmentID, Object, Manufacturer, DatePurchase, DateService, DateServiceWarning, Comment, Picture FROM %sEquipment ORDER BY Object LIMIT :limit', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return array_map([$this, 'mapEquipment'], $statement->fetchAll());
    }

    public function findById(int $id): ?Equipment
    {
        $sql = sprintf('SELECT EquipmentID, Object, Manufacturer, DatePurchase, DateService, DateServiceWarning, Comment, Picture FROM %sEquipment WHERE EquipmentID = :id', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch();

        return is_array($row) ? $this->mapEquipment($row) : null;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function mapEquipment(array $row): Equipment
    {
        return new Equipment(
            (int) ($row['EquipmentID'] ?? 0),
            (string) ($row['Object'] ?? ''),
            isset($row['Manufacturer']) ? (string) $row['Manufacturer'] : null,
            isset($row['DatePurchase']) && $row['DatePurchase'] !== null ? new DateTimeImmutable((string) $row['DatePurchase']) : null,
            isset($row['DateService']) && $row['DateService'] !== null ? new DateTimeImmutable((string) $row['DateService']) : null,
            isset($row['DateServiceWarning']) && $row['DateServiceWarning'] !== null ? new DateTimeImmutable((string) $row['DateServiceWarning']) : null,
            isset($row['Comment']) ? (string) $row['Comment'] : null,
            isset($row['Picture']) ? (string) $row['Picture'] : null
        );
    }
}
