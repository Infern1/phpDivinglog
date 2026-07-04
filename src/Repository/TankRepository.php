<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use PhpDivingLog\Model\Tank;
use PDO;

final readonly class TankRepository
{
    public function __construct(private PDO $pdo, private string $tablePrefix)
    {
    }

    /**
     * @return list<Tank>
     */
    public function findByDiveNumber(int $diveNumber): array
    {
        $sql = sprintf('SELECT TankID, Number, Volume, Pstart, Pend, O2 FROM %sTank WHERE Number = :number ORDER BY TankID', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':number', $diveNumber, PDO::PARAM_INT);
        $statement->execute();

        return array_map(
            static fn (array $row): Tank => new Tank(
                (int) ($row['TankID'] ?? 0),
                (int) ($row['Number'] ?? 0),
                isset($row['Volume']) ? (float) $row['Volume'] : null,
                isset($row['Pstart']) ? (float) $row['Pstart'] : null,
                isset($row['Pend']) ? (float) $row['Pend'] : null,
                isset($row['O2']) ? (float) $row['O2'] : null,
            ),
            $statement->fetchAll()
        );
    }
}
