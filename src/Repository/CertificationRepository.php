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
        $tableCandidates = ['Brevets', 'Brevet', 'Certification', 'Certifications', 'Certificate', 'Certificates', 'Cert'];

        foreach ($tableCandidates as $table) {
            $sql = sprintf('SELECT * FROM %s%s', $this->tablePrefix, $table);

            try {
                $rows = $this->pdo->query($sql)->fetchAll();
                if (!is_array($rows)) {
                    return null;
                }

                usort($rows, function (array $a, array $b): int {
                    $aDate = $this->parseDate($this->pickValue($a, ['CertDate', 'DateBrevet', 'Date', 'IssuedAt', 'IssueDate']));
                    $bDate = $this->parseDate($this->pickValue($b, ['CertDate', 'DateBrevet', 'Date', 'IssuedAt', 'IssueDate']));

                    if ($aDate === null && $bDate === null) {
                        return 0;
                    }

                    if ($aDate === null) {
                        return 1;
                    }

                    if ($bDate === null) {
                        return -1;
                    }

                    return $bDate <=> $aDate;
                });

                return $rows;
            } catch (\PDOException $exception) {
                $sqlState = $exception->errorInfo[0] ?? null;
                if ($sqlState === '42S02' || $sqlState === '42S22' || ($sqlState === 'HY000' && str_contains(strtolower($exception->getMessage()), 'no such table'))) {
                    continue;
                }

                throw $exception;
            }
        }

        return null;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function mapCertification(array $row): Certification
    {
        return new Certification(
            $this->normalizedString($this->pickValue($row, ['Org', 'Organisation', 'Organization', 'Agency'])),
            $this->normalizedString($this->pickValue($row, ['Brevet', 'Certification', 'Cert', 'Title', 'Name'])),
            $this->parseDate($this->pickValue($row, ['CertDate', 'DateBrevet', 'Date', 'IssuedAt', 'IssueDate'])),
            $this->normalizedString($this->pickValue($row, ['Number', 'BrevetNr', 'BrevetNo', 'CertNr', 'CertNo', 'CertNumber', 'LicenseNo'])),
            $this->normalizedString($this->pickValue($row, ['Instructor', 'DiveInstructor', 'Teacher', 'InstructorName'])),
            $this->normalizedString($this->pickValue($row, ['Scan1Path', 'Picture1', 'PictureFront', 'Picture', 'Image', 'CardFront', 'ImageFront'])),
            $this->normalizedString($this->pickValue($row, ['Scan2Path', 'Picture2', 'PictureBack', 'BackImage', 'CardBack', 'ImageBack'])),
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
