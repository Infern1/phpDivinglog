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
        } elseif ($rows === []) {
            $rows = $this->queryByColumn('Number', $logId) ?? [];
        }

        return $this->mapRows($rows);
    }

    /**
     * @param list<array<string, mixed>> $rows
     * @return list<UserDefinedField>
     */
    private function mapRows(array $rows): array
    {
        if ($rows === []) {
            return [];
        }

        if ($rows !== [] && array_key_exists('Name', $rows[0])) {
            return array_values(
                array_filter(
                    array_map(
                        static fn (array $row): ?UserDefinedField => isset($row['Name']) && $row['Name'] !== ''
                            ? new UserDefinedField(
                                (int) ($row['UserdefinedID'] ?? $row['ID'] ?? 0),
                                (int) ($row['LogID'] ?? $row['Number'] ?? 0),
                                (string) $row['Name'],
                                isset($row['Value']) ? (string) $row['Value'] : null,
                            )
                            : null,
                        $rows
                    )
                )
            );
        }

        $fieldMap = [
            ['galid', 'Gallery'],
            ['Field2', 'Field2'],
            ['Field3', 'Field3'],
            ['Field4', 'Field4'],
            ['Field5', 'Field5'],
            ['Field6', 'Field6'],
            ['Field7', 'Field7'],
            ['Field8', 'Field8'],
            ['Field9', 'Field9'],
            ['Field10', 'Field10'],
        ];

        $items = [];
        foreach ($rows as $row) {
            $baseId = (int) ($row['ID'] ?? $row['UserdefinedID'] ?? 0);
            $rowLogId = (int) ($row['LogID'] ?? $row['Number'] ?? 0);
            $index = 0;

            foreach ($fieldMap as [$key, $label]) {
                $value = isset($row[$key]) ? trim((string) $row[$key]) : '';
                if ($value === '') {
                    $index++;
                    continue;
                }

                $items[] = new UserDefinedField(
                    ($baseId * 100) + $index,
                    $rowLogId,
                    $label,
                    $value
                );
                $index++;
            }
        }

        return $items;
    }

    /**
     * @return list<array<string, mixed>>|null
     */
    private function queryByColumn(string $column, int $id): ?array
    {
        $sql = sprintf(
            'SELECT * FROM %sUserdefined WHERE %s = :id',
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
