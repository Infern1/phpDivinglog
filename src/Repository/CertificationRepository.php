<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use DateTimeImmutable;
use PhpDivingLog\Model\Certification;
use PhpDivingLog\Support\TextNormalizer;
use PDO;

final readonly class CertificationRepository
{
    public function __construct(private PDO $pdo, private string $tablePrefix)
    {
    }

    /**
     * @return list<Certification>
     */
    public function listAll(int $limit = 200): array
    {
        $rows = $this->fetchRows();
        if ($rows === null) {
            return [];
        }

        return array_slice(array_map([$this, 'mapCertification'], $rows), 0, $limit);
    }

    /**
     * @return list<array<string, mixed>>|null
     */
    private function fetchRows(): ?array
    {
        $sql = sprintf('SELECT * FROM %sBrevet ORDER BY DateBrevet DESC', $this->tablePrefix);

        try {
            return $this->pdo->query($sql)->fetchAll();
        } catch (\PDOException $exception) {
            $sqlState = $exception->errorInfo[0] ?? null;
            if ($sqlState === '42S02' || $sqlState === '42S22' || ($sqlState === 'HY000' && str_contains(strtolower($exception->getMessage()), 'no such table'))) {
                return null;
            }

            throw $exception;
        }
    }

    /**
     * @param array<string, mixed> $row
     */
    private function mapCertification(array $row): Certification
    {
        return new Certification(
            $this->normalizedString($this->pickValue($row, ['Organisation', 'Organization', 'Org', 'Agency'])),
            $this->normalizedString($this->pickValue($row, ['Brevet', 'Certification', 'Cert', 'Title', 'Name'])),
            $this->parseDate($this->pickValue($row, ['DateBrevet', 'Date', 'CertDate'])),
            $this->normalizedString($this->pickValue($row, ['BrevetNr', 'BrevetNo', 'CertNr', 'CertNo', 'CertNumber', 'Number'])),
            $this->normalizedString($this->pickValue($row, ['Instructor', 'DiveInstructor', 'Teacher'])),
            $this->normalizedString($this->pickValue($row, ['Picture1', 'PictureFront', 'Picture', 'Image', 'CardFront'])),
            $this->normalizedString($this->pickValue($row, ['Picture2', 'PictureBack', 'BackImage', 'CardBack'])),
        );
    }

    /**
     * @param array<string, mixed> $row
     * @param list<string> $preferredKeys
     */
    private function pickValue(array $row, array $preferredKeys): mixed
    {
        foreach ($preferredKeys as $key) {
            if (array_key_exists($key, $row)) {
                return $row[$key];
            }
        }

        $normalized = [];
        foreach ($row as $key => $value) {
            if (is_string($key)) {
                $normalized[strtolower($key)] = $value;
            }
        }

        foreach ($preferredKeys as $key) {
            $lookup = strtolower($key);
            if (array_key_exists($lookup, $normalized)) {
                return $normalized[$lookup];
            }
        }

        return null;
    }

    private function parseDate(mixed $value): ?DateTimeImmutable
    {
        if ($value === null) {
            return null;
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return null;
        }

        try {
            return new DateTimeImmutable($raw);
        } catch (\Exception) {
            return null;
        }
    }

    private function normalizedString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $text = trim((string) $value);
        if ($text === '') {
            return null;
        }

        return TextNormalizer::normalizeLikelyMojibake($text);
    }
}
