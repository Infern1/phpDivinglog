<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use PhpDivingLog\Model\UserDefinedField;
use PDO;

final readonly class UserDefinedRepository
{
    public function __construct(private PDO $pdo, private string $tablePrefix)
    {
    }

    /**
     * @return list<UserDefinedField>
     */
    public function findByLogId(int $logId): array
    {
        $rows = $this->queryByColumn('LogID', $logId);
        if ($rows === null) {
            $rows = $this->queryByColumn('Number', $logId) ?? [];
        }

        return array_map(
            static fn (array $row): UserDefinedField => new UserDefinedField(
                (int) ($row['UserdefinedID'] ?? 0),
                (int) ($row['LogID'] ?? $row['Number'] ?? 0),
                (string) ($row['Name'] ?? ''),
                isset($row['Value']) ? (string) $row['Value'] : null,
            ),
            $rows
        );
    }

    /**
     * @return list<array<string, mixed>>|null
     */
    private function queryByColumn(string $column, int $id): ?array
    {
        $sql = sprintf(
            'SELECT * FROM %sUserdefined WHERE %s = :id ORDER BY UserdefinedID',
            $this->tablePrefix,
            $column
        );

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetchAll();
        } catch (\PDOException $exception) {
            if (($exception->errorInfo[0] ?? null) === '42S22') {
                return null;
            }

            throw $exception;
        }
    }
}
