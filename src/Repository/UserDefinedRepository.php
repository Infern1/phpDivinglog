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
        $sql = sprintf('SELECT UserdefinedID, LogID, Name, Value FROM %sUserdefined WHERE LogID = :logId ORDER BY UserdefinedID', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':logId', $logId, PDO::PARAM_INT);
        $statement->execute();

        return array_map(
            static fn (array $row): UserDefinedField => new UserDefinedField(
                (int) ($row['UserdefinedID'] ?? 0),
                (int) ($row['LogID'] ?? 0),
                (string) ($row['Name'] ?? ''),
                isset($row['Value']) ? (string) $row['Value'] : null,
            ),
            $statement->fetchAll()
        );
    }
}
