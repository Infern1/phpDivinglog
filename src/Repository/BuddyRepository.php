<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use PhpDivingLog\Model\Buddy;
use PhpDivingLog\Support\TextNormalizer;
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

        $rows = $this->queryByColumn('BuddyID', $ids);
        if ($rows === null) {
            $rows = $this->queryByColumn('ID', $ids) ?? [];
        }

        return array_map([$this, 'mapBuddy'], $rows);
    }

    /**
     * @param list<int> $ids
     * @return list<array<string, mixed>>|null
     */
    private function queryByColumn(string $column, array $ids): ?array
    {
        if ($ids === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = sprintf('SELECT * FROM %sBuddy WHERE %s IN (%s)', $this->tablePrefix, $column, $placeholders);

        try {
            $statement = $this->pdo->prepare($sql);
            foreach ($ids as $idx => $id) {
                $statement->bindValue($idx + 1, $id, PDO::PARAM_INT);
            }

            $statement->execute();
            return $statement->fetchAll();
        } catch (\PDOException $exception) {
            if (($exception->errorInfo[0] ?? null) === '42S22') {
                return null;
            }

            throw $exception;
        }
    }

    /**
     * @param array<string, mixed> $row
     */
    private function mapBuddy(array $row): Buddy
    {
        return new Buddy(
            (int) ($row['BuddyID'] ?? $row['ID'] ?? 0),
            TextNormalizer::normalizeLikelyMojibake((string) ($row['Firstname'] ?? $row['FirstName'] ?? '')),
            TextNormalizer::normalizeLikelyMojibake((string) ($row['Lastname'] ?? $row['LastName'] ?? '')),
            isset($row['email']) ? (string) $row['email'] : (isset($row['Email']) ? (string) $row['Email'] : null),
            isset($row['comment'])
                ? TextNormalizer::normalizeLikelyMojibake((string) $row['comment'])
                : (isset($row['Comments']) ? TextNormalizer::normalizeLikelyMojibake((string) $row['Comments']) : null),
            isset($row['Picture']) ? (string) $row['Picture'] : (isset($row['PhotoPath']) ? (string) $row['PhotoPath'] : null),
        );
    }
}
