<?php

declare(strict_types=1);

namespace PhpDivingLog\Adapters\Web\Controller;

use PhpDivingLog\Repository\ShopRepository;

final readonly class ShopController
{
    public function __construct(private ShopRepository $shops)
    {
    }

    /**
     * @return array{shops:list<object>}
     */
    public function overview(): array
    {
        return ['shops' => $this->shops->list()];
    }

    /**
     * @return array{shop: object}|null
     */
    public function detail(int $id): ?array
    {
        $shop = $this->shops->findById($id);
        return $shop === null ? null : ['shop' => $shop];
    }
}
