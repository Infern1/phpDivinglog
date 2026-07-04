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
        $sql = sprintf('SELECT ShopID, CountryID, ShopName, ShopType, City, ShopComment FROM %sShop ORDER BY ShopName LIMIT :limit', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
        $statement->execute();

        return array_map([$this, 'mapShop'], $statement->fetchAll());
    }

    public function findById(int $id): ?Shop
    {
        $sql = sprintf('SELECT ShopID, CountryID, ShopName, ShopType, City, ShopComment FROM %sShop WHERE ShopID = :id', $this->tablePrefix);
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $row = $statement->fetch();

        return is_array($row) ? $this->mapShop($row) : null;
    }

    /**
     * @param array<string, mixed> $row
     */
    private function mapShop(array $row): Shop
    {
        return new Shop(
            (int) ($row['ShopID'] ?? 0),
            (int) ($row['CountryID'] ?? 0),
            (string) ($row['ShopName'] ?? ''),
            isset($row['ShopType']) ? (string) $row['ShopType'] : null,
            isset($row['City']) ? (string) $row['City'] : null,
            isset($row['ShopComment']) ? (string) $row['ShopComment'] : null
        );
    }
}
