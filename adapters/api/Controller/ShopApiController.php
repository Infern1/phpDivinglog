<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Api\Controller;

use PhpDivingLog\Repository\ShopRepository;

final readonly class ShopApiController
{
    public function __construct(private ShopRepository $shops)
    {
    }

    /**
     * @return array{data:list<object>}
     */
    public function list(): array
    {
        return ['data' => $this->shops->list()];
    }

    /**
     * @return array{data: object}|null
     */
    public function item(int $id): ?array
    {
        $shop = $this->shops->findById($id);

        return $shop === null ? null : ['data' => $shop];
    }
}
