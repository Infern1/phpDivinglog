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
        $row = $this->fetchProfileRow();

        if (!is_array($row)) {
            return null;
        }

        $firstName = $this->stringOrNull($this->pickValue($row, ['Firstname', 'FirstName', 'Vorname']));
        $lastName = $this->stringOrNull($this->pickValue($row, ['Lastname', 'LastName', 'Surname', 'Nachname']));
        $email = $this->stringOrNull($this->pickValue($row, ['Email', 'EMail', 'Mail']));
        $city = $this->stringOrNull($this->pickValue($row, ['City', 'Ort']));
        $country = $this->stringOrNull($this->pickValue($row, ['Country', 'Land']));
        $comment = $this->stringOrNull($this->pickValue($row, ['Comment', 'Comments', 'Remark', 'Remarks', 'Notes']));
        $picture = $this->stringOrNull($this->pickValue($row, ['Picture', 'Photo', 'Image', 'Avatar']));

        return new Personal(
            TextNormalizer::normalizeLikelyMojibake($firstName ?? ''),
            TextNormalizer::normalizeLikelyMojibake($lastName ?? ''),
            $email,
            $city !== null ? TextNormalizer::normalizeLikelyMojibake($city) : null,
            $country !== null ? TextNormalizer::normalizeLikelyMojibake($country) : null,
            $comment !== null ? TextNormalizer::normalizeLikelyMojibake($comment) : null,
            $picture
        );
    }

    /**
     * @return array<string, mixed>|false
     */
    private function fetchProfileRow(): array|false
    {
        $sql = sprintf('SELECT * FROM %sPersonal LIMIT 1', $this->tablePrefix);

        return $this->pdo->query($sql)->fetch();
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

    private function stringOrNull(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $string = trim((string) $value);
        return $string === '' ? null : $string;
    }
}
