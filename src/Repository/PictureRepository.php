<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use PhpDivingLog\Model\Picture;
use PDO;

final readonly class PictureRepository
{
    public function __construct(private PDO $pdo, private string $tablePrefix)
    {
    }

    /**
     * @return list<Picture>
     */
    public function findByLogId(int $logId): array
    {
        $rows = $this->queryByColumn('LogID', $logId);
        if ($rows === null) {
            $rows = $this->queryByColumn('Number', $logId) ?? [];
        } elseif ($rows === []) {
            $rows = $this->queryByColumn('Number', $logId) ?? [];
        }

        return array_map(
            static fn (array $row): Picture => new Picture(
                (int) ($row['PictureID'] ?? $row['ID'] ?? 0),
                (int) ($row['LogID'] ?? $row['Number'] ?? 0),
                (string) ($row['Picture'] ?? $row['Path'] ?? ''),
                isset($row['Description']) ? (string) $row['Description'] : null,
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
            'SELECT * FROM %sPictures WHERE %s = :id',
            $this->tablePrefix,
            $column
        );

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetchAll();
        } catch (\PDOException $exception) {
            $sqlState = $exception->errorInfo[0] ?? null;
            if ($sqlState === '42S22' || ($sqlState === 'HY000' && str_contains(strtolower($exception->getMessage()), 'no such column'))) {
                return null;
            }

            throw $exception;
        }
    }
}
