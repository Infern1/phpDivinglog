<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use PhpDivingLog\Model\Buddy;
use PDO;

final readonly class BuddyRepository
{
    public function __construct(private PDO $pdo, private string $tablePrefix)
    {
    }

    /**
     * @param list<int> $ids
     * @return list<Buddy>
     */
    public function findByIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = sprintf('SELECT BuddyID, Firstname, Lastname, email, comment, Picture FROM %sBuddy WHERE BuddyID IN (%s)', $this->tablePrefix, $placeholders);
        $statement = $this->pdo->prepare($sql);

        foreach ($ids as $idx => $id) {
            $statement->bindValue($idx + 1, $id, PDO::PARAM_INT);
        }

        $statement->execute();

        return array_map(
            static fn (array $row): Buddy => new Buddy(
                (int) ($row['BuddyID'] ?? 0),
                (string) ($row['Firstname'] ?? ''),
                (string) ($row['Lastname'] ?? ''),
                isset($row['email']) ? (string) $row['email'] : null,
                isset($row['comment']) ? (string) $row['comment'] : null,
                isset($row['Picture']) ? (string) $row['Picture'] : null,
            ),
            $statement->fetchAll()
        );
    }
}
