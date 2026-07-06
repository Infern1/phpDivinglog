<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use PhpDivingLog\Model\Personal;
use PhpDivingLog\Support\TextNormalizer;
use PDO;

final readonly class PersonalRepository
{
    public function __construct(private PDO $pdo, private string $tablePrefix)
    {
    }

    public function getProfile(): ?Personal
    {
        $sql = sprintf('SELECT Firstname, Lastname, Email, City, Country, Comment, Picture FROM %sPersonal LIMIT 1', $this->tablePrefix);
        $row = $this->pdo->query($sql)->fetch();

        if (!is_array($row)) {
            return null;
        }

        return new Personal(
            TextNormalizer::normalizeLikelyMojibake((string) ($row['Firstname'] ?? '')),
            TextNormalizer::normalizeLikelyMojibake((string) ($row['Lastname'] ?? '')),
            isset($row['Email']) ? (string) $row['Email'] : null,
            isset($row['City']) ? TextNormalizer::normalizeLikelyMojibake((string) $row['City']) : null,
            isset($row['Country']) ? TextNormalizer::normalizeLikelyMojibake((string) $row['Country']) : null,
            isset($row['Comment']) ? TextNormalizer::normalizeLikelyMojibake((string) $row['Comment']) : null,
            isset($row['Picture']) ? (string) $row['Picture'] : null,
        );
    }
}
