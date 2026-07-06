<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use PhpDivingLog\Model\Personal;
use PhpDivingLog\Support\TextNormalizer;
use PDO;
use PDOException;

final readonly class PersonalRepository
{
    public function __construct(private PDO $pdo, private string $tablePrefix)
    {
    }

    public function getProfile(): ?Personal
    {
        $row = $this->fetchProfileRow();

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

    /**
     * @return array<string, mixed>|false
     */
    private function fetchProfileRow(): array|false
    {
        $queries = [
            'SELECT Firstname, Lastname, Email, City, Country, Comment, Picture FROM %sPersonal LIMIT 1',
            'SELECT Firstname, Lastname, Email, City, Country, Comments AS Comment, Picture FROM %sPersonal LIMIT 1',
            'SELECT Firstname, Lastname, Email, City, Country, NULL AS Comment, Picture FROM %sPersonal LIMIT 1',
        ];

        foreach ($queries as $index => $template) {
            $sql = sprintf($template, $this->tablePrefix);

            try {
                return $this->pdo->query($sql)->fetch();
            } catch (PDOException $exception) {
                if ($index === array_key_last($queries)) {
                    throw $exception;
                }
            }
        }

        return false;
    }
}
