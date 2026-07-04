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
        $sql = sprintf('SELECT PictureID, LogID, Picture, Description FROM %sPictures WHERE LogID = :logId ORDER BY PictureID', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':logId', $logId, PDO::PARAM_INT);
        $statement->execute();

        return array_map(
            static fn (array $row): Picture => new Picture(
                (int) ($row['PictureID'] ?? 0),
                (int) ($row['LogID'] ?? 0),
                (string) ($row['Picture'] ?? ''),
                isset($row['Description']) ? (string) $row['Description'] : null,
            ),
            $statement->fetchAll()
        );
    }
}
