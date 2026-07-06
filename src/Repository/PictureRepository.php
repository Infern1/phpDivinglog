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

        return array_map([$this, 'mapPicture'], $rows);
    }

    public function countAll(): int
    {
        $sql = sprintf('SELECT COUNT(*) AS PictureCount FROM %sPictures', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return is_array($row) ? (int) ($row['PictureCount'] ?? 0) : 0;
    }

    /**
     * @return list<Picture>
     */
    public function findPage(int $limit, int $offset): array
    {
        $sql = sprintf(
            'SELECT * FROM %sPictures ORDER BY LogID DESC, PictureID DESC LIMIT :limit OFFSET :offset',
            $this->tablePrefix
        );

        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();

        return array_map([$this, 'mapPicture'], $statement->fetchAll());
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

    /**
     * @param array<string, mixed> $row
     */
    private function mapPicture(array $row): Picture
    {
        return new Picture(
            (int) ($row['PictureID'] ?? $row['ID'] ?? 0),
            (int) ($row['LogID'] ?? $row['Number'] ?? 0),
            (string) ($row['Picture'] ?? $row['Path'] ?? ''),
            isset($row['Description']) ? (string) $row['Description'] : null,
        );
    }
}
