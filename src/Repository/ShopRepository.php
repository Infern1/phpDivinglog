<?php

declare(strict_types=1);

namespace PhpDivingLog\Repository;

use PhpDivingLog\Model\Shop;
use PDO;

final readonly class ShopRepository
{
    public function __construct(private PDO $pdo, private string $tablePrefix)
    {
    }

    /**
     * @return list<Shop>
     */
    public function list(int $limit = 200): array
    {
        $sql = sprintf('SELECT * FROM %sShop ORDER BY ShopName LIMIT :limit', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return array_map([$this, 'mapShop'], $statement->fetchAll());
    }

    public function findById(int $id): ?Shop
    {
        $row = $this->queryByIdColumn('ShopID', $id);
        if (!is_array($row)) {
            $row = $this->queryByIdColumn('ID', $id);
        }

        return is_array($row) ? $this->mapShop($row) : null;
    }

    /**
     * @return array<string, mixed>|false
     */
    private function queryByIdColumn(string $column, int $id): array|false
    {
        $sql = sprintf('SELECT * FROM %sShop WHERE %s = :id', $this->tablePrefix, $column);

        try {
            $statement = $this->pdo->prepare($sql);
            $statement->bindValue(':id', $id, PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetch();
        } catch (\PDOException $exception) {
            $sqlState = $exception->errorInfo[0] ?? null;
            if ($sqlState === '42S22' || ($sqlState === 'HY000' && str_contains(strtolower($exception->getMessage()), 'no such column'))) {
                return false;
            }

            throw $exception;
        }
    }

    /**
     * @param array<string, mixed> $row
     */
    private function mapShop(array $row): Shop
    {
        return new Shop(
            (int) ($row['ShopID'] ?? $row['ID'] ?? 0),
            (int) ($row['CountryID'] ?? 0),
            (string) ($row['ShopName'] ?? ''),
            isset($row['ShopType']) ? (string) $row['ShopType'] : null,
            isset($row['City']) ? (string) $row['City'] : null,
            isset($row['ShopComment']) ? (string) $row['ShopComment'] : (isset($row['Comments']) ? (string) $row['Comments'] : null)
        );
    }
}
